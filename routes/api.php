<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\BackupController;


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

//Users
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('users/login', [UserController::class, 'login']);
Route::post('users/create', [UserController::class, 'create']);
Route::post('users/forgot-password', [UserController::class, 'forgotPassword']);
Route::post('users/reset-password', [UserController::class, 'resetPassword']);

// Projects Archive
Route::get('/files/{id}/{filename}', [FilesController::class, 'download']);
Route::delete('/files/{id}/{filename}', [FilesController::class, 'removeFile']);
Route::apiResource('files', FilesController::class);
Route::get('projects/export', [ProjectController::class, 'export']);
Route::post('projects/import', [ProjectController::class, 'import']);
Route::apiResource('projects', ProjectController::class);


//backup & restore
Route::get('/backups', [BackupController::class, 'index']);
Route::post('/backups', [BackupController::class, 'store']);
Route::get('/backups/download/database/{key}', [BackupController::class, 'downloadDatabase']);
Route::get('/backups/download/storage/{key}', [BackupController::class, 'downloadStorage']);
Route::post('/backups/restore', [BackupController::class, 'restore']);
Route::delete('/backups/destroy', [BackupController::class, 'destroy']);