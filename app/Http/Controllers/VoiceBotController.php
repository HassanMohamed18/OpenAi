<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\VoiceBotStreamService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;


class VoiceBotController extends Controller
{
    protected $openAIService;

    public function __construct(VoiceBotStreamService $openAIService)
    {
        $this->openAIService = $openAIService;
    }
    public function index(){

        return view('voicestream');
}
    // Process real-time voice
    public function processVoice(Request $request)
    {
        try {
            // Validate audio file
            // $request->validate([
            //     'audio' => 'required|file|mimes:wav,mp3,m4a|max:10240'
            // ]);

            // Save audio file temporarily
            $audioPath = $request->file('audio')->store('audio', 'public');

            // Transcribe audio to text
            $transcribedText = $this->openAIService->transcribeAudio(storage_path("app/public/{$audioPath}"));

            // Get AI response from GPT-4
            $aiResponseStream = $this->openAIService->chatWithGPT($transcribedText);

            // Stream AI response to frontend
            return response()->stream(function () use ($aiResponseStream) {
                foreach ($aiResponseStream as $chunk) {
                    echo $chunk->choices[0]->delta->content ?? ''; // Output each streamed chunk
                    ob_flush();
                    flush();
                }
            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Generate Speech (TTS)
    public function generateSpeech(Request $request)
    {
        try {
            $request->validate([
                'text' => 'required|string'
            ]);

            $audioData = $this->openAIService->generateSpeech($request->input('text'));

            // return response($audioData)
            //     ->header('Content-Type', 'audio/mpeg')
            //     ->header('Content-Disposition', 'inline; filename="response.mp3"');
            return response()->json([
                'audio_url' => $audioData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
