<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use OpenAI;

class VoiceBotService
{
    protected $client;
    public $apiKey;
    public function __construct()
    {
        $this->client = OpenAI::client(env('OPENAI_API_KEY'));
        $this->apiKey = env('OPENAI_API_KEY');
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

    // Generate AI response using GPT-4
    public function chatWithGPT($message)
    {
        $response = $this->client->chat()->create([
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful voice assistant.'],
                ['role' => 'user', 'content' => $message],
            ],
        ]);
        return $response['choices'][0]['message']['content'];
    }

    // Convert Text to Speech (TTS)
    // public function generateSpeech($text)
    // {
    //     $response = $this->client->audio()->speech([
    //         'model' => 'tts-1',
    //         'voice' => 'alloy',
    //         'input' => $text
    //     ]);

    //     // Save to public/audio/
    //      $filePath = public_path('audio/response.mp3');
    //     // $response = json_decode($response,true);
    //      file_put_contents($filePath, $response->getContent());

    //     return asset('audio/response.mp3');
    // }

    public function generateSpeech($text)
{
    $apiKey = 'your-api-key'; // Replace with your OpenAI API key
    $url = "https://api.openai.com/v1/audio/speech";

    // Make the API request
    $response = Http::withHeaders([
        "Authorization" => "Bearer ".$this->apiKey,
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
