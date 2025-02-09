<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\VoiceBotResponse;
use App\Services\VoiceBotService;

class VoiceChatController extends Controller
{
    protected $voiceBotService;

    public function __construct(VoiceBotService $voiceBotService)
    {
        $this->voiceBotService = $voiceBotService;
    }


    public function index(){

            return view('voicebot');
    }
    public function processAudio(Request $request)
    {
        // $request->validate([
        //     'audio' => 'required|file|mimes:mp3,wav',
        // ]);

        // Store the uploaded audio
        //$audioPath = $request->file('audio')->store('uploads', 'public');
        $audioPath = 'uploads/Recording (3).m4a';
        // Transcribe the audio
        $text = $this->voiceBotService->transcribeAudio(storage_path("app/public/{$audioPath}"));

        // Get GPT response
        $gptResponse = $this->voiceBotService->chatWithGPT($text);

        // Generate speech from GPT response
        $speechUrl = $this->voiceBotService->generateSpeech($gptResponse);

        // Broadcast response via WebSockets
        //broadcast(new VoiceBotResponse($gptResponse, $speechUrl));

        return response()->json([
            'text' => $gptResponse,
            'audio_url' => $speechUrl
        ]);
    }
}
