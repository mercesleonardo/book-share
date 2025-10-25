<?php

use App\Http\Controllers\Auth\Google\{CallBackController, RedirectController};
use App\Http\Controllers\{CategoryController, CommentController, ModerationController, PostController, ProfileController, RatingController, UserController};
use App\Http\Controllers\{DashboardController, HomeController, PostPublicController};
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/auth/google/redirect', RedirectController::class)->name('auth.google.redirect');
Route::get('/auth/google/callback', CallBackController::class)->name('auth.google.callback');

// Rota pública para visualização de posts
Route::get('/posts/{post:slug}', [PostPublicController::class, 'show'])->name('posts.show');

// Rota pública para avaliações (requer autenticação)
Route::post('/posts/{post:slug}/ratings', [RatingController::class, 'store'])->name('posts.ratings.store')->middleware('auth');

Route::prefix('admin')->middleware(['auth', 'verified'])->name('admin.')->group(function () {

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

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
