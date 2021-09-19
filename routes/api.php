<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function(){
   Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
   Route::apiResource('posts', \App\Http\Controllers\Api\PostController::class);
   Route::post('/upload-post-image/{post}', [\App\Http\Controllers\Api\PostController::class, 'uploadImage']);
   Route::post('/remove-post-image/{post}', [\App\Http\Controllers\Api\PostController::class, 'removeImage']);
   Route::post('create-comment/{post}', [\App\Http\Controllers\Api\CommentController::class, 'store']);
   Route::delete('delete-comment/{comment}', [\App\Http\Controllers\Api\CommentController::class, 'destroy']);
});
