<?php

use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserAuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Klaviyo\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
 

// Auth Routes
Route::get('/users', [UserController::class, 'users']);
Route::get('users-export', [UserController::class, 'export'])->name('users.export');
Route::post('users-import', [UserController::class, 'import'])->name('users.import');

Route::post('/login', [UserAuthController::class, 'login']);
Route::post('/register', [UserAuthController::class, 'register']);
Route::post('/logout', [UserAuthController::class, 'logout'])->middleware('auth:sanctum');

// Posts Routes
Route::get('posts', [PostController::class, 'index']); 
Route::get('post/show', [PostController::class, 'show']); 
Route::post('post/store', [PostController::class, 'store']);
Route::delete('post/destroy', [PostController::class, 'destroy']);
Route::put('/post/update', [PostController::class, 'update']);
Route::post('post/create-comment', [PostController::class, 'createComment']);
Route::get('post/filter', [PostController::class, 'filterPost']);

// Route::resource('userpost', PostController::class);
Route::get('all/posts', [UserAuthController::class, 'posts']);


// klaviyo

Route::prefix('klaviyo')->group( function () {
    Route::get('all-profiles', [ProfileController::class, 'all_profiles']);
});