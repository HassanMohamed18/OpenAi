<?php

namespace App\Http\Controllers;

use App\Services\EmbeddingService;
use Illuminate\Http\Request;
use OpenAI;

class ChatWithEmbeddingsController extends Controller
{
    protected $embeddingService;
    protected $client;

    public function __construct(EmbeddingService $embeddingService)
    {
        $this->embeddingService = $embeddingService;
        $this->client = OpenAI::client(env('OPENAI_API_KEY'));
    }

    public function chat(Request $request)
    {
        // $request->validate([
        //     'message' => 'required|string',
        // ]);

        // $userMessage = $request->input('message');
        $userMessage = 'i want to know the projects with highest units price';

        // // Step 1: Generate embeddings for the user message
        $userEmbedding = $this->embeddingService->generateEmbeddings($userMessage);

        // Step 2: Find the most relevant context from the database
         $relevantContext = $this->embeddingService->findRelevantContext($userEmbedding);

        $contextText = implode("\n", array_column($relevantContext, 'text'));

        //Step 3: Perform chat completion with context
        $response = $this->client->chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant that uses relevant stored context to answer questions.'],
                ['role' => 'system', 'content' => "Relevant context:\n" . $contextText],
                ['role' => 'user', 'content' => $userMessage],
            ],
            'max_tokens' => 100,
        ]);

        return response()->json([
            'message' => $response['choices'][0]['message']['content'],
        ]);
    }
}
