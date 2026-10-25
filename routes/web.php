<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\PlannerController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/doctor', [DoctorController::class, 'store'])->name('doctor.store');

Route::get('/planner', [PlannerController::class, 'show'])->name('planner.show');

Route::get('/operators/{operator}', [OperatorController::class, 'show'])->name('operators.show');
Route::post('/operators/{operator}/comments', [CommentController::class, 'store'])->name('comments.store');
Route::post('/operators/{operator}/favorite', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
