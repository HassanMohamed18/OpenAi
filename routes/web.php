<?php

use App\Http\Controllers\ChatWithEmbeddingsController;
use App\Http\Controllers\ChatWithMySqlDatabaseController;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\VoiceBotController;
use App\Http\Controllers\VoiceChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/chat_ai', [ChatWithEmbeddingsController::class,'index'])->name('chat.index');
Route::get('/chat_stream', [ChatWithEmbeddingsController::class, 'chat'])->name('chat.store');

//Route::get('/chat_stream', [ChatWithMySqlDatabaseController::class, 'chat'])->name('chat.store');


// Route::get('/stream', [StreamController::class, 'index'])->name('chat.index');
// Route::get('/stream_message', [StreamController::class, 'streamForm'])->name('chat.streamForm');


Route::get('/voicebot', [VoiceChatController::class, 'index'])->name('voice.index');

Route::get('/voicebot_stream', [VoiceBotController::class, 'index'])->name('voicebot.index');


Route::get('/speech', function () {
    return view('speech');
});
