<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

/*
 * i metodi index, show, update, store e destroy sono stati generati dall'ption --api passata al comando php artisan make:model
 * vanno implementati manualmente
 */
class PostController extends Controller implements HasMiddleware
{
    // per utilizzare la funzione middleware dobbiamo implementare l'interface HasMiddleware
    public static function middleware()
    {
        // attraverso il middleware di sanctum rendo le rotte accessibili solo se loggati, quindi se nella request c'è il bearer token
        // posso gestire le eccezioni alla regola indicando il nome del metodo in un array dell aproprietà except
        // $request->user() funziona solo se l'utente è loggato
        // il middleware trova l'utente attraverso il token presente nell'header della request
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }


    /**
     * Display a listing of the resource.
     */
    public function index(): Collection
    {
        return Post::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required'
        ]);

        /**
         * il metodo  di eloquent create() accetta un array da cui ricavare l'istanza e salvarla nel db
        */
        //        return Post::create($fields);

        // il $post che creo lo devo assegnare ad un utente
        // prendo l'utente loggato, chiamo la collection posts di cui è owner ed in questa creo ed aggiungo il nuovo post
        return $request->user()->posts()->create($fields);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return $post;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        // per controllare se un utente può fare o no una cosa utilizzo le Policies
        // creo delle guards che controllano delle condizioni e consentono o no di fare una cosa
        // utilizzo la Gate facade ed il metodo authorize che controlla le abilities, le guards, il nome che ho dato ale metodo nella policy
        // il secondo argomento è quello di cui ha bisogno l'ability
        // nel caso della modify si ha bisogno dello User (che viene automaticamente passato) e del Post che deve essere modificato
        // potrei controllare il ruolo con la policy
        Gate::authorize('modify', $post);

        $fields = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required'
        ]);

        $post->update($fields);

        return $post;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        Gate::authorize('modify', $post);

        $post->delete();

        return ['message' => 'post deleted'];
    }
}
