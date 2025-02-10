<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatWithEmbeddingsController;
use App\Http\Controllers\DeepSeekController;
use App\Http\Controllers\googleSearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OpenAIController;
use App\Http\Controllers\PineconeController;
use App\Http\Controllers\SpeechController;
use App\Http\Controllers\VectorDatabaesController;
use App\Http\Controllers\VoiceBotController;
use App\Http\Controllers\VoiceChatController;
use App\Http\Controllers\VoiceSessionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::get('/chat', [ChatController::class, 'chat']);
Route::get('/test', function (){
    return "test";
});

Route::get('/chat_gpt', [OpenAIController::class, 'chat']);
Route::get('/ask_deepseek', [DeepSeekController::class, 'chat']);

Route::get('/vectors/store', [VectorDatabaesController::class, 'insert_vectors']);

Route::get('/vectors/search', [VectorDatabaesController::class, 'test_embeddings']);


Route::get('/pinecone/store', [PineconeController::class, 'store']);
Route::get('/pinecone/search', [PineconeController::class, 'search']);

Route::get('/google/search', [googleSearchController::class, 'google_search']);


//Route::post('/voicebot', [VoiceChatController::class, 'processAudio']);

Route::post('/voicebot', [VoiceBotController::class, 'processVoice']);
Route::post('/voicebot/speech', [VoiceBotController::class, 'generateSpeech']);



Route::post('/speech/stream', [SpeechController::class, 'streamSpeech']);

Route::post('/chat_voice', [VoiceSessionController::class, 'chatWithAI']);
