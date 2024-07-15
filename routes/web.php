<?php

use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\Admin\UserController;

Route::get('/', [HomePageController::class, 'index']);
Auth::routes();
Route::group(['middleware' => ['auth']], function () {
    Route::resource('posts', PostController::class);
    Route::post('user/profile/update/{userId}', [ProfileController::class, 'updateProfile']);
});
Route::middleware([IsAdmin::class])->group(function () {
    Route::get('admin/home', [UserController::class,'admin']);
    Route::get('users', [UserController::class, 'users']);
    Route::get('admin/posts', [PostController::class, 'adminPosts']);
    Route::get('users-export', [UserController::class, 'export'])->name('users.export');
    Route::post('users-import', [UserController::class, 'import'])->name('users.import');
});
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('post/{slug}', [HomePageController::class, 'viewPost']);
Route::post('post/comment', [HomePageController::class, 'makeComment']);
Route::get('comments', [HomePageController::class, 'comments']);
Route::get('media/download/{postId}', [HomePageController::class, 'downloadMedia']);
