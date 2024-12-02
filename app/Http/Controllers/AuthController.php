<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        /**
         * validiamo i dati che arrivano con la request
         * mettiamo le rules di validazione
         */
        $fields = $request->validate([
            'name'=>'required|max:255',
            // la rule unique:users controlla che la mail passata non esista già nella tabella users
            'email'=>'required|email|unique:users',
            // la rule confirmed controlla che un valore di input sia uguale ad un altro, nella request deve arrivare un input field
            // con name password ed uno con lo stesso name e l'aggunta di _confirmation
            // cioè nel FE ci saranno 2 input: uno con name password ed uno con name password_confirmation
            // poi ci pensa laravel a controllare che matchino
            'password'=>'required|confirmed'
        ]);

        // se non viene superata la validation verrà ritornato un array con proprietà errors che ha come value un array
        // questo secondo array conterrà tante proprietà per ogni errore di validazione restituito
        // ogni proprietà ha valore array con messaggio o messaggi da restituire

        // superata la validation creo un nuovo utente coi dati arrivati
        $user = User::create($fields);

        // poi dobbiamo creare un modo di autenticazione
        // sanctum lo fa creando e gestendo dei token di accesso che lo user riceverà nella response
        // per accedere alle rotte che hanno bisogno di autenticazione per accedervi questo token deve essere inviato tramite header
        // esempio: Authorization Bearer 31|XvVfl0kjj1FSSSl6BYe0cFl2ZWTnbKIAATxwlWcD1f70baf5
        // per creare il token il Model User deve utilizzare il trait HasApiTokens
        // il metodo createToken genera un codice token random partendo da una stringa ad esempio dal nome dell'utente e lo salva hashato
        // nella tabella personal_access_tokens insieme all'id dell'utente che si è loggato, quel token corrisponde univocamente a quello user
        $token = $user->createToken($request->name);

        // dato che il token è hashato va ritornato non hashato, lo si fa chiamando la proprietà plainTextToken
        return [
            'user' => $user,
            'token' => $token->plainTextToken
        ];
    }

    public function login(Request $request) {
        $request->validate([
            // la rule exists:users cerca un utente che ha quella email, se non esiste lancia un errore
            'email'=>'required|email|exists:users',
            'password'=>'required'
        ]);

        // superata la validation cerco l'utente tramite email
        // dobbiamo prendere il primo valore perchè viene ritornato un array dal where
        $user = User::where('email', $request->email)->first();

        /**
         * Hash::check controlla che la password passata al momento del login e quella hashata hel DB siano uguali, il primo argomento è la
         *  password stringa (non hashata, quella che arriva con la request), il secondo argomento è la password dello user che è stato trovato
         * tramite email nel DB (la password nel DB è hashata)
         */
        if (!$user || !Hash::check($request->password, $user->password)) {
            /**
             * Questo return non va bene, torna un messaggio e non un errore             *
             * se infatti al momento del login inserisco una mail corretta ed una password sbagliata avrei  questo messaggio come risposta
             * il frontend non lo gestirebbe come un errore di validation, per farlo devo rispettare la struttura della response di laravel
             * è un array con la prima key errors con un value array con seconda key email o password con value altro array con messaggio
             */
            /*return [
                'message' => 'The provided credentials are incorrect.'
            ];*/
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

    /**
     * c'è bisogno del bearer token perchè nell'api.php alla fine della rotta è stato aggiunto il midlleware auth:sanctum
     * se non si fosse autenticati vorrebbe dire che non ci sarebbero tokens da eliminare e quindi avremmo un errore
     */
    public function logout(Request $request) {
        // è possibile accedere ai tokens di un utente tramite il tokens method fornito dal trait HasApiTokens
        // metodo basato sulle relazioni di Eloquent
        // elimino dal DB ogni tokens collegato ad un utente
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
