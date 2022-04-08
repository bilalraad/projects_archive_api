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
Route::apiResource('projects', ProjectController::class);
Route::apiResource('files', FilesController::class);