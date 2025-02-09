<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenAISpeechService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SpeechController extends Controller
{
    protected OpenAISpeechService $speechService;

    public function __construct(OpenAISpeechService $speechService)
    {
        $this->speechService = $speechService;
    }

    

    public function streamSpeech(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'voice' => 'nullable|string',
        ]);

        $text = $request->input('text');
        $voice = $request->input('voice', 'alloy'); // Default voice

        $response = $this->speechService->speechStreamed($text, $voice);

        return new StreamedResponse(function () use ($response) {
            foreach ($response as $chunk) {
                echo $chunk;
                flush();
                ob_flush();
            }
        }, 200, [
            'Content-Type' => 'audio/mpeg',
        ]);
    }
}
