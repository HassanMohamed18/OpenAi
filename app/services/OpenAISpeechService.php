<?php
namespace App\Services;

use OpenAI;

class OpenAISpeechService
{
    protected  $openAI;

    public function __construct()
    {
        $this->openAI = OpenAI::client(env('OPENAI_API_KEY'));
    }

    public function speechStreamed(string $text, string $voice = 'alloy', string $model = 'tts-1')
    {
        $parameters = [
            'model' => $model,
            'input' => $text,
            'voice' => $voice,
        ];

        return $this->openAI->audio()->speechStreamed($parameters);
    }
}
