<?php

namespace App\Http\Controllers;

//use App\Services\EmbeddingService;

use App\Services\GoogleSearchService;
use App\Services\PineconeService;
use Illuminate\Http\Request;
use OpenAI;

class ChatWithEmbeddingsController extends Controller
{
    protected $embeddingService;
    protected $googleSearchService;
    protected $client;
    private $UserId;
    public $topK;
    public function __construct(PineconeService $embeddingService, GoogleSearchService $googleSearchService)
    {
        $this->embeddingService = $embeddingService;
        $this->googleSearchService = $googleSearchService;
        $this->topK = 20;
        $this->client = OpenAI::client(env('OPENAI_API_KEY'));
        //$this->UserId = auth()->id() ?? 'guest'; // Retrieve authenticated user ID or set to 'guest' for unauthenticated users.
    }

    public function index()
    {
        $messages = (object) [
            (object) ['user_message' => 'Hello AI', 'gpt_response' => 'Hellow my friend'],
            (object) ['user_message' => 'Hello AI', 'gpt_response' => 'Hellow my friend'],
            (object) ['user_message' => 'Hello AI', 'gpt_response' => 'Hellow my friend'],
            (object) ['user_message' => 'Hello AI', 'gpt_response' => 'Hellow my friend'],

        ];
        //$messages  = collect($messages);
        return view('chat', compact('messages'));
    }

    public function chat(Request $request)
    {
        // $request->validate([
        //     'message' => 'required|string',
        // ]);

        $userMessage = $request->input('message');
        // $validated = $request->validate([
        //     'message' => 'required|string',
        // ]);

        // $userMessage = $validated['message'];

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');

        // // Step 2: Start streaming
        // echo "data: " . json_encode(['message' => 'Streaming started...']) . "\n\n";
        // flush();

        // Step 1: Generate embeddings for the user message
        //$userEmbedding = $this->embeddingService->generateEmbeddings($userMessage);
        // $response = $this->client->embeddings()->create([
        //     'model' => 'text-embedding-ada-002', // Optimized for embeddings
        //     'input' => $userMessage,
        // ]);

        // $userEmbedding =  $response['data'][0]['embedding'];
        // // Step 2: Find the most relevant context from the database
        //  $relevantContext = $this->embeddingService->findRelevantContext($userEmbedding);

        // //$contextText = implode("\n", array_column($relevantContext, 'text'));


        //$userMessage = 'give me information about the projects';
        //$topk = 20;
        $filters = $this->embeddingService->parseNaturalLanguageFilters($userMessage);
        $filter = $filters['filters'];
        $translatedQuery = $filters['translatedQuery'];
        $filter = count($filter) > 0 ? $filter : null;
        $userEmbedding = $this->embeddingService->generateEmbedding($translatedQuery);

        if (!$userEmbedding) {
            return response()->json(['error' => 'Embedding generation failed'], 500);
        }
        // Perform search in Pinecone
        $RelativeContext = $this->embeddingService->queryVector($userEmbedding, $filter, $this->topK);
        $matches = $RelativeContext['matches'];
        $relative_context = '';
        foreach ($matches as $match) {
            $relative_context = $relative_context . $match['metadata']['content'] . ',';
        }

        //$searchResults = $this->googleSearchService->web_search($userMessage);

        return response()->stream(function () use ($userMessage, $relative_context) {

            // Step 2: Start streaming
            $delay = 0.5; // You can adjust the delay (in seconds)

            // Step 3: OpenAI API with streaming enabled
            $stream = $this->client->chat()->createStreamed([
                'model' => 'gpt-4o',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant that uses stored relative context to answer questions and provides a relevant recommendation at the end of your response.'],
                    ['role' => 'system', 'content' => "Relevant context:\n" . $relative_context],
                    ['role' => 'user', 'content' => $userMessage],
                    ['role' => 'system', 'content' => 'After answering the user’s question, provide a relevant recommendation based on the topic discussed.']
                ],
                // 'messages' => [
                //     ['role' => 'system', 'content' => 'You are a helpful assistant uses stored relative context to answer questions.'],
                //     ['role' => 'system', 'content' => "Relevant context:\n" .$relative_context],
                // //     ['role' => 'system', 'content' => 'You are a helpful assistant that provides answers using both stored relative context and your general knowledge. Ensure that your responses equally prioritize both sources of information.'],
                // //     ['role' => 'system', 'content' => "Relevant stored context:\n" . $relative_context],
                // //     ['role' => 'system', 'content' => "Reminder: When answering, ensure that half of your response is based on the provided context and the other half on your general knowledge."],
                // //['role' => 'user', 'content' => 'Explains in detail'],  
                // ['role' => 'user', 'content' => $userMessage],

                //  ],
                // 'messages' => [
                //     ['role' => 'system', 'content' => 'You are an AI assistant that combines stored context, general knowledge, and real-time web search results equally to generate accurate and well-balanced answers.'],
                //     ['role' => 'system', 'content' => "Relevant stored context:\n" . $relative_context],
                //     ['role' => 'system', 'content' => "Web search results:\n" . $searchResults],
                //     ['role' => 'user', 'content' => "Please ensure the answer is based equally on stored context, general knowledge, and web search results. Here is my query:\n" . $userMessage],
                // ],
                // 'messages' => [
                //     ['role' => 'system', 'content' => 'You are a helpful assistant that answers user questions by integrating stored relative context, general knowledge, and web search results equally. Ensure that the response remains within the scope of the stored relative context.'],
                //     ['role' => 'system', 'content' => "Relevant stored context (primary scope):\n" . $relative_context],
                //     ['role' => 'system', 'content' => "Web search results (real-time insights):\n" . $searchResults],
                //     ['role' => 'system', 'content' => "General knowledge (pre-trained information): GPT-4o’s internal knowledge base will be used but must align strictly with the stored context."],
                //     // ['role' => 'user', 'content' => $userMessage],
                //     ['role' => 'user', 'content' => "Please ensure the answer is based equally on stored context, general knowledge, and web search results. Here is my query:\n" . $userMessage],
                // ],
                'max_tokens' => 1000,
                //'temperature ' => 0.7,
                'stream' => true,
                //'user' => (string)$this->UserId
            ]);

            // Step 4: Stream chunks to the client
            foreach ($stream as $response) {
                $chunk = $response->choices[0]->delta->content ?? '';
                if ($chunk) {
                    echo "data: " . json_encode(['message' => $chunk]) . "\n\n";
                    flush();     // Ensure the message is sent immediately to the client
                    ob_flush();
                    usleep($delay * 100000);  // Sleep for the specified delay before sending the next chunk
                }
            }

            // Step 5: End the streaming
            echo "data: " . json_encode(['done' => true]) . "\n\n";
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
    }
}
