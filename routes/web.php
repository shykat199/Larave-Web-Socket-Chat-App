<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Models\User;
use App\Http\Controllers\GroupController;


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

Route::middleware('auth')->controller(ChatController::class)->group(function () {
    Route::post('/save-chat', 'saveChat')->name('save-chat');
    Route::post('/load-old-chat', 'loadOldChat')->name('load-old-chat');
    Route::post('/load-more-chat', 'loadMoreChat')->name('load-more-chat');
    Route::post('/start-typing/{id}/{id2}', 'startTyping')->name('startTyping');
    Route::post('/stop-typing/{id}/{id2}', 'stopTyping')->name('stopTyping');
    Route::get('/delete-chat/{id}', 'deleteChat')->name('deleteChat');
});

Route::middleware(['auth', 'verified'])->controller(GroupController::class)->group(function (){
    Route::get('/group','index')->name('group');
    Route::post('/add-group','store')->name('add-group');
});

require __DIR__ . '/auth.php';
