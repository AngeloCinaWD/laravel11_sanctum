<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// per utilizzare un progetto Laravel come rest api
// php artisan install:api
// è stato installato laravel sanctum, ci aiuta a generare dei token per proteggere le rotte API e gestire l'autenticazione
// laravel sanctum utilizza API tokens session, quindi genera una migration per la tabella personal_access_tokens_table
// ci mette a disposizione uno speciale middleware che controlla se si è autenticati o no per poter accedere alla rotta: middleware('auth:sanctum');

// nomi che utilizza laravel quando crea i metodi automaticamente per le rotte API:

// index -> è all (GET)
// show -> ritorna il singolo (GET con id come path variable)
// store -> crea uno nuovo (POST con i dati dell'istanza da creare)
// update -> modifica uno (PUT|PATCH con dati dell'oggetto da modificare)
// destroy -> elimina uno (DELETE con id dell'istanza da eliminare)

// NELL'HEADER DELLE REQUESTS CI DEVE ESSERE LA KEY Accept CON VALUE application/json (https://laravel.com/docs/11.x/sanctum#spa-authentication)

// questa rotta viene creata di default, è comoda perchè mi restituisce tutte le informazioni di un utente
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// questa ha come url solo api
//Route::get('/', function() {
//    return ['ciao'];
//});

// per creare una CRUD abbiamo bisogno di varie cose
// utilizziamo il comando php artisan make:model con l'option -a (o --all), questa option  mi andrà a generare model, migration, seeder, factory, policy,form request ed importante il resource controller, questo però ha i metodi create ed edit di cui non abbiamo bisogno (sono quelli che renderizzano un form), quindi o li andiamo ad eliminare a mano o utilizziamo il flag --api (questo è quello con tutti i metodi index, show, store, update e destroy)
// il comnado è php artisan make:model Post -a --api
// una volta compilato il model e la migration runno le migrations php artisan migrate
// per chiamare le rotte per posts, tutte insieme senza doverle scrivere una per una (come nell'immagine del readme), utilizzo il metodo apiResource (cioè in questo modo avrò una rotta per lo show, una per l'update, una per il destroy, una per l'index ed una per lo store, con già i metodi http assegnati e che puntano ai rispettivi metodi nel controller)
// il metodo resource è simile, ma non è per le API e quindi punta anche alle rotte edit e create
Route::apiResource('posts', PostController::class);

/**
 * per gestire l'autenticazione tramite sanctum creo un controller che conterrà questi 3 metodi: register, login e logout
 * php artisan make:controller AuthController
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// devo essere loggato per poter effettuare il logout
// il middleware auth:sanctum fornito da sanctum controlla che nell'header della request sia presente un bearer token
// da quello ricava di quale utente si tratta
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
