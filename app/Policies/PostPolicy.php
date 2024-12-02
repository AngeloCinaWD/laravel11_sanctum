<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    // questa policy è stata creata di default quando ho creato il model perchè avevo aggiunto l'option -a
    // autorizzo a fare una determinata cosa solo se è rispettata una condizione
    // in questo caso un utente può modificare o eliminare un post solo se è suo
    // ritorna una Response
    public function modify(User $user, Post $post): Response
    {
        return $user->id === $post->user_id ? Response::allow() : Response::deny('You do not own this post');
    }

    // potrei creare una function che chiamo checkRole e consento o nego im base al ruolo
}
