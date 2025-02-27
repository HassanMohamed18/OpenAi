<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GeminiController extends Controller
{
    public function chat(Request $request)
    {
        $userMessage = $request->input('message');

        if (!$userMessage) {
            return response()->json(['error' => 'Message is required'], 400);
        }

        $apiKey = env('GEMINI_API_KEY');
        $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=$apiKey";

        $response = Http::post($apiUrl, [
            'contents' => [
                ['parts' => [['text' => $userMessage]]]
            ]
        ]);

        $data = $response->json();
        $aiReply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Sorry, I could not generate a response.';

        return response()->json(['reply' => $aiReply]);
    }
}
