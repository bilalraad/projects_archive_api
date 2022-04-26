<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FilesController;






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

Route::post('users/login', [UserController::class, 'login']);
Route::post('users/create', [UserController::class, 'create']);

Route::get('/files/{id}/{filename}', [FilesController::class, 'download']);
Route::delete('/files/{id}/{filename}', [FilesController::class, 'removeFile']);
Route::apiResource('projects', ProjectController::class);
Route::apiResource('files', FilesController::class);

// Route::group(['middleware' => 'auth:sanctum'], function () {
    //All the routes that belongs to the group goes here
//     Route::get('dashboard', function () {
//     });
// });