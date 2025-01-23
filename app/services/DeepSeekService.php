<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DeepSeekService
{
    protected $apiUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('deepseek_api_key');
        $this->apiUrl = 'https://api.deepseek.com/chat/completions';
    }

    public function ask_deepseek($ask)
    {

        $data = [
            "model" => "deepseek-chat",
            "messages" => [
                ["role" => "system", "content" => "You are a helpful assistant."],
                ["role" => "user", "content" => $ask]
            ],
            "stream" => false
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->apiUrl}", $data);

        if ($response->successful()) {
            return $response->json();
        }

        return $response->throw();

    }
}
