<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        $fields = $request->validate([
            'name'=>'required|max:255',
            'email'=>'required|email|unique:users',
            'password'=>'required|confirmed'
        ]);

        $user = User::create($fields);

        $token = $user->createToken($request->name);

        return [
            'user' => $user,
            'token' => $token->plainTextToken
        ];
    }

    public function login(Request $request) {
        $request->validate([
            'email'=>'required|email|exists:users',
            'password'=>'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
//            Questo return non va bene, torna un messaggio e non un errore
//            se infatti al momento del login inserisco una mail corretta ed una password sbagliata avrei  questo messaggio come risposta
//            il frontend non lo gestirebbe come un errore di validation, per farlo devo rispettare la struttura della response di laravel
//            è un array con la prima key errors con un value array con seconda key email o password con value altro array con messaggio
//            return [
//                'message' => 'The provided credentials are incorrect.'
//            ];
            return [
                'errors' => ['email' => ['The provided credentials are incorrect.']]
            ];
        }

        $token = $user->createToken($user->name);

        return [
            'user' => $user,
            'token' => $token->plainTextToken
        ];
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();

        return [
            'message' => 'You are logged out.'
        ];
    }

    public function prova(Request $request) {


        return [
            'message' => 'You are logged out.'
        ];
    }
}
