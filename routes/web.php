<?php

use App\Http\Controllers\{CategoryController, CommentController, ModerationController, PostController, ProfileController, RatingController, UserController};
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', \App\Http\Controllers\DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('users', UserController::class)->except(['show']);
    Route::patch('users/{user}/restore', [UserController::class, 'restore'])->name('users.restore')->withTrashed();

    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('posts', PostController::class);
    Route::post('posts/{post}/ratings', [RatingController::class, 'store'])->name('posts.ratings.store');
    // Comments
    Route::post('comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::patch('posts/{post}/approve', [ModerationController::class, 'approve'])->middleware('throttle:moderation')->name('posts.approve');
    Route::patch('posts/{post}/reject', [ModerationController::class, 'reject'])->middleware('throttle:moderation')->name('posts.reject');
});

require __DIR__ . '/auth.php';
