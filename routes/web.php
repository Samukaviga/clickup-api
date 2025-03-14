<?php

use App\Http\Controllers\ClickUpController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/clickup/workspaces', [ClickUpController::class, 'getWorkspaces']);
Route::get('/clickup/create-task', [ClickUpController::class, 'createTask']);

Route::get('/clickup/spaces', [ClickUpController::class, 'getSpaces']);

Route::get('/clickup/folder', [ClickUpController::class, 'getFolders']);

Route::get('/clickup/list', [ClickUpController::class, 'getLists']);

Route::get('/clickup/task', [ClickUpController::class, 'getTasks']);

Route::get('/clickup/members', [ClickUpController::class, 'getMembers']);


Route::get('/teste', [ClickUpController::class, 'teste']);



require __DIR__.'/auth.php';
