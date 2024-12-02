<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
     use HasFactory;

     /**
      * le proprietà del modello (i campi della tabella) vanno inseriti in un array di una property protected $fillable
     */
     protected $fillable = [
         'title',
         'body'
     ];

     // relazione one to many (Inverse) verso user
     // più post possono avere lo stesso utente
     public function user() {
         return $this->belongsTo(User::class);
     }
}
