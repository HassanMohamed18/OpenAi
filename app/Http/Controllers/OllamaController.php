<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OllamaController extends Controller
{
    public function chat(Request $request)
    {
        // Validate user input
        $request->validate([
            'message' => 'required|string'
        ]);

        // Define Ollama API URL
        $ollamaUrl = 'http://localhost:11434/api/generate'; // Ollama runs on port 11434 by default

        // Send the request to Ollama
        $response = Http::post($ollamaUrl, [
            'model' => 'mistral:latest', // Change model name if needed (e.g., llama3, codellama, etc.)
            'prompt' => $request->message,
            'num_predict' => 100,
            'stream' => false, // Set to true if you want streaming responses
        ]);

        // Return response as JSON
        return response()->json($response->json());
    }
}
