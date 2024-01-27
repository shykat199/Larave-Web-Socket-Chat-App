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
    Route::get('/all-group','allGroup')->name('all-group');
    Route::get('/get-group-information/{slug}','getGroupInformation')->name('get-group-information');
    Route::post('/add-group','store')->name('add-group');
    Route::post('/send-group-request','sendGroupRequest')->name('send-group-request');
    Route::get('/get-group-request-list/{id}','getGroupRequestList')->name('get-group-request-list');
    Route::post('/update-group-request-list','updateGroupRequestList')->name('update-group-request-list');

    Route::post('/save-group-chat','saveGroupChat')->name('save-group-chat');
    Route::get('/check-group-user-access','checkGroupUserAccess')->name('check-group-user-access');
    Route::get('/load-group-old-chat','loadGroupOldChat')->name('load-group-old-chat');
});

require __DIR__ . '/auth.php';
