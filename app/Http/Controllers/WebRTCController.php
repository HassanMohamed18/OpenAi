<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WebRTCController extends Controller
{
    public function getSessionToken()
    {
        $apiKey = env('OPENAI_API_KEY');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type'  => 'application/json',
        ])->post('https://api.openai.com/v1/realtime/sessions', [
            "model" => "gpt-4o-mini-realtime-preview-2024-12-17",
            "modalities" => ["text"],         
        ]);

        // $response = Http::withHeaders([
        //     'Authorization' => 'Bearer ' . $apiKey,
        //     'Content-Type'  => 'application/json',
        // ])->post('https://api.openai.com/v1/chat/completions', [
        //     "model" => "gpt-4o-audio-preview",
        //     "modalities" => ["text", "audio"],
        //     "audio" => ["voice" => "alloy" ,"format" => "wav"],
        //     "messages" => [
        //             //['role' => 'system', 'content' => 'You are a helpful assistant who can interact with databases.'],
        //             ['role' => 'user', 'content' => 'tell me about php'],
        //         ],
        // ]);
        
        //$response->json();

         //$item = $responseData->session->update(['event_id' => "dfsdf"]);

        // Extract text response
        //$textResponse = $responseData['choices'][0]['message']['content'] ?? 'No response';

        // Extract audio URL
      // return $audioUrl = $responseData['choices'][0]['message']['audio']['id'];
        
        return response()->json($response->json());
    }
}

