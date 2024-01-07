<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Models\User;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $allUsers = User::where('id', '!=', \Illuminate\Support\Facades\Auth::user()->id)->get();
    return view('dashboard', compact('allUsers'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::controller(ChatController::class)->group(function () {
    Route::post('/save-chat', 'saveChat')->name('save-chat');
    Route::post('/load-old-chat', 'loadOldChat')->name('load-old-chat');
    Route::post('/load-more-chat', 'loadMoreChat')->name('load-more-chat');
});

require __DIR__ . '/auth.php';
