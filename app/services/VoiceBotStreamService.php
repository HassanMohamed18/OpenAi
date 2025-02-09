<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

use OpenAI;

class VoiceBotStreamService
{
    protected $client;

    public function __construct()
    {
        $this->client = OpenAI::client(env('OPENAI_API_KEY'));
    }

    // Convert Speech to Text (Whisper API)
    public function transcribeAudio($audioPath)
    {
        $audioFile = fopen($audioPath, 'r');
        $response = $this->client->audio()->transcribe([
            'file' => $audioFile,
            'model' => 'whisper-1'
        ]);
        return $response->text;
    }

    // Get AI response from GPT-4o
    public function chatWithGPT($message)
    {
        $response = $this->client->chat()->createStreamed([
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful AI assistant.'],
                ['role' => 'user', 'content' => $message],
            ],
            'stream' => true // Enable streaming
        ]);

        return $response;
    }

    // Convert Text to Speech (TTS)
    public function generateSpeech($text)
    {
        // $response = $this->client->audio()->speech()->create([
        //     'model' => 'tts-1',
        //     'voice' => 'alloy',
        //     'input' => $text
        // ]);
        $apiKey = env('OPENAI_API_KEY'); // Replace with your OpenAI API key
        $url = "https://api.openai.com/v1/audio/speech";
    
        // Make the API request
        $response = Http::withHeaders([
            "Authorization" => "Bearer ".$apiKey,
            "Content-Type"  => "application/json"
        ])->post($url, [
            "model" => "tts-1",
            "voice" => "alloy",
            "input" => $text
        ]);
    
        // Check for errors
        if ($response->failed()) {
            return response()->json(['error' => 'Failed to generate speech', 'response' => $response->body()], $response->status());
        }

        $directory = public_path('audio'); // Path to "public/audio/"
    $filePath = $directory . '/response.mp3';

    // Ensure the "audio" directory exists
    if (!File::exists($directory)) {
        File::makeDirectory($directory, 0755, true); // Create directory if not exists
    }

    // Save the audio response to a file
    file_put_contents($filePath, $response->body());

   // return true;
    // Return the URL to the saved audio file
   return asset('audio/response.mp3');
    }
}
