<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class HttpService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('askyourdatabase_api_key');
        $this->baseUrl = 'https://api.openai.com/v1';
    }

    public function askgpt($prompt, $model = 'gpt-4o-mini', $maxTokens = 100)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type'  => 'application/json',
        ])->post("https://www.askyourdatabase.com/api/chatbot/v2/session", [
            'model'       => $model,
            'prompt'      => $prompt,
            /*'max_tokens'  => $maxTokens,
            'temperature' => 0.7,*/
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return $response->throw();
    }
}
