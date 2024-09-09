<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampusController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LikeController;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
//public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

//protected routes

Route::group(['middleware'=>'auth:sanctum'], function(){
    //user
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::patch('/user', [AuthController::class, 'update']);

    //event
    Route::get('/events', [EventController::class, 'index']); //all events
    Route::post('/events', [EventController::class, 'store']); //create an event
    Route::patch('/events/{event}', [EventController::class, 'update']); //update event
    Route::get('/events/{event}', [EventController::class, 'show']); //show event
    Route::delete('/events/{event}', [EventController::class, 'destroy']); //delete event

    //Comment
    Route::get('/events/{event}/comments', [CommentController::class, 'index']); //all comments of an event
    Route::post('/events/{event}/comments', [CommentController::class, 'store']); //// create comment
    Route::patch('/comments/{comment}', [CommentController::class, 'update']); // update comment
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']); // delete comment

    //Like
    Route::post('/events/{event}/likes', [LikeController::class, 'likeOrUnlike']); //like or unlike a post

    //campus
    Route::post('/campus/store', [CampusController::class, 'store']); //create a campus

    //roles
    Route::post('/roles/store', [AuthController::class, 'createRole']); //create a role

    //categories
    Route::post('/categories/store', [CategoryController::class, 'store']); //create a category
    Route::get('/categories', [CategoryController::class, 'index']); //get all categories

});

Route::post('/campus/store', [CampusController::class, 'store']); //create a campus
Route::post('/roles/store', [AuthController::class, 'createRole']); //create a role
Route::get('/categories', [CategoryController::class, 'index']); //get all categories
