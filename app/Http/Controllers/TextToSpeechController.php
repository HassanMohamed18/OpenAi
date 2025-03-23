<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
class TextToSpeechController extends Controller
{
    //

    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('OPENAI_API_KEY'); // Store API key in .env
    }

    public function stream(Request $request)
    {
        // $url = "https://api.openai.com/v1/audio/speech";

        // // Send request to OpenAI (returning a stream)
        // $response = $this->client->post($url, [
        //     'headers' => [
        //         'Authorization' => "Bearer {$this->apiKey}",
        //         'Content-Type'  => 'application/json',
        //     ],
        //     'json' => [
        //         'model' => 'tts-1',
        //         'input' => $request->text,
        //         'voice' => 'alloy',
        //         'response_format' => 'mp3', // mp3, opus, etc.
        //     ],
        //     'stream' => true, // Enables streaming
        // ]);

        // OpenAI Chat Completion Streaming Request
        // $response = $this->client->post($url, [
        //     'headers' => [
        //         'Authorization' => "Bearer {$this->apiKey}",
        //         'Content-Type'  => 'application/json',
        //     ],
        //     'json' => [
        //         'model' => 'gpt-4o-mini-audio-preview',
        //         'modalities' => ['text', 'audio'],
        //         'audio' => ['voice' => 'alloy', 'format' => 'wav'],
        //         'messages' => [
        //             ['role' => 'system', 'content' => 'You are a helpful AI.'],
        //             ['role' => 'user', 'content' => 'can you tell me about dubai']
        //         ],
        //         //'stream' => true, // Enables real-time streaming
        //     ]
        // ]);

        $url = "https://api.openai.com/v1/chat/completions";

        $response = Http::withHeaders([
            'Authorization' => "Bearer $this->apiKey",
            'Content-Type'  => 'application/json',
        ])->withOptions(['stream' => true])
            ->post($url, [
                'model'      => 'gpt-4o-mini-audio-preview',
                'modalities' => ['text', 'audio'],
                'audio'      => ['voice' => 'alloy', 'format' => 'pcm16'],
                'messages'   => [
                    ['role' => 'system', 'content' => 'You are a helpful AI.'],
                    ['role' => 'user', 'content' => 'Can you tell me about Dubai?']
                ],
                'stream'     => true
            ]);

        //return $response->json();

        return new StreamedResponse(function () use ($response) {
            // if (!$response instanceof Response) {
            //     echo "Failed to connect to API.";
            //     return;
            // }

            $body = $response->body();

            if (empty($body)) {
                echo "No response received.";
                return;
            }

            // Process each line in the stream
            foreach (explode("\n", $body) as $line) {
                if (!empty($line) && str_starts_with($line, "data:")) {
                    $json = json_decode(substr($line, 5), true);
                    if (isset($json['choices'][0]['message']['audio']['data'])) {
                        echo base64_decode($json['choices'][0]['message']['audio']['data']);
                        ob_flush();
                        flush();
                    }
                }
            }
        }, 200, [
            'Content-Type'  => 'audio/pcm',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma'        => 'no-cache',
            'Expires'       => '0',
        ]);


        // Decode JSON response
        // $json = $response->json();

        // if (isset($json['choices'][0]['message']['audio']['data'])) {
        //     $audioData = base64_decode($json['choices'][0]['message']['audio']['data']);

        //     return response()->stream(function () use ($audioData) {
        //         echo $audioData;
        //         ob_flush();
        //         flush();
        //     }, 200, [
        //         'Content-Type'   => 'audio/wav',
        //         'Cache-Control'  => 'no-cache',
        //         'Connection'    => 'keep-alive',
        //     ]);
        // }

        // return $body = json_decode($response->getBody(), true);
        // $audio = $body['choices'][0]['message']['audio']['data'];
        // $audioData = base64_decode($audio);
        // return Response::make($audioData, 200, [
        //     'Content-Type' => 'audio/wav', // Adjust MIME type if needed
        //     'Content-Length' => strlen($audioData),
        //     'Accept-Ranges' => 'bytes',
        //     'Cache-Control' => 'no-cache, no-store, must-revalidate',
        //     'Pragma' => 'no-cache',
        //     'Expires' => '0',
        // ]);

        // return $body = json_decode($response->getBody(), true);
        // return new StreamedResponse(function () use ($response) {
        //     //$body = $response->getBody();
        //     $body = json_decode($response->getBody(), true);
        //     $audio = $body['choices'][0]['message']['audio']['data'];
        //     $audio = base64_decode($audio);
        //     while (!$audio->eof()) {
        //         echo $audio->read(1024); // Send in 1KB chunks
        //         ob_flush();
        //         flush();
        //     }
        // }, 200, [
        //     'Content-Type'  => "audio/mp3",
        //     'Cache-Control' => 'no-cache',
        //     'Connection'    => 'keep-alive',
        //     //'Transfer-Encoding' => 'chunked', // Enables real-time streaming
        // ]);
    }

    public function generateWavHeader($sampleRate, $numChannels, $bitsPerSample)
    {
        $byteRate = $sampleRate * $numChannels * ($bitsPerSample / 8);
        $blockAlign = $numChannels * ($bitsPerSample / 8);

        return pack('N', 0x52494646) .   // "RIFF"
            pack('V', 0) .           // Chunk size (placeholder)
            "WAVEfmt " .
            pack('V', 16) .          // Subchunk1 size (16 for PCM)
            pack('v', 1) .           // Audio format (1 = PCM)
            pack('v', $numChannels) . // Number of channels
            pack('V', $sampleRate) . // Sample rate
            pack('V', $byteRate) .   // Byte rate
            pack('v', $blockAlign) . // Block align
            pack('v', $bitsPerSample) . // Bits per sample
            "data" .
            pack('V', 0);            // Subchunk2 size (placeholder)
    }
    public function transcribeAudio(Request $request)
    {
        Log::info('Received audio file:', $request->all());

        $request->validate([
            'audio' => 'required|file|mimes:wav,mp3,m4a,ogg,flac,webm|max:10240',
        ]);

        $audioFile = $request->file('audio');
        $audioPath = $audioFile->getPathname();

        Log::info('Audio file stored at: ' . $audioPath);

        // Send request to OpenAI Whisper API
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        ])->attach(
            'file',
            file_get_contents($audioPath),
            'audio.webm'  // Use correct file format
        )->post('https://api.openai.com/v1/audio/transcriptions', [
            'model' => 'whisper-1',
            
            'response_format' => 'json',
        ]);

        $transcription = $response->json();
        Log::info('Transcription Response:', $transcription);

        return response()->json([
            'message' => 'Transcription successful',
            'transcription' => $transcription['text'] ?? 'Error transcribing audio',
        ]);
    }
    // public function transcribeAudio(Request $request)
    // {
    //     $request->validate([
    //         'audio' => 'required|file|mimes:wav,mp3,m4a,ogg,flac|max:10240', // Accept various formats
    //     ]);

    //     // Get uploaded file
    //     $audioFile = $request->file('audio');
    //     $audioPath = $audioFile->getPathname();

    //     // Send request to OpenAI's Whisper API
    //     $response = Http::withHeaders([
    //         'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
    //     ])->attach(
    //         'file', file_get_contents($audioPath), 'audio.wav'
    //     )->post('https://api.openai.com/v1/audio/transcriptions', [
    //         'model' => 'whisper-1',

    //     ]);

    //     // Get transcription response
    //     $transcription = $response->json();

    //     return response()->json([
    //         'message' => 'Transcription successful',
    //         'transcription' => $transcription['text'] ?? 'Error transcribing audio',
    //     ]);
    // }
}
