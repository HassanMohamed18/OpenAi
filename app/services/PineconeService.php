<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use OpenAI;

class PineconeService
{
    protected $client;
    protected $pinecone;
    protected $apiKey;
    protected $indexName;
    protected $pineconeUrl;

    public function __construct()
    {
        // OpenAI Client
        $this->client = OpenAI::client(env('OPENAI_API_KEY'));

        // Pinecone Configuration
        $this->apiKey = env('PINECONE_API_KEY');
        // $this->indexName = env('PINECONE_INDEX');
        // $environment = env('PINECONE_ENVIRONMENT');

        $this->pineconeUrl = "https://realstate-24mn74j.svc.aped-4627-b74a.pinecone.io";
        $this->pinecone = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Api-Key' => $this->apiKey
            ]
        ]);
    }

    /**
     * Generate embedding using OpenAI
     */
    public function generateEmbedding(string $text)
    {
        //$json_data = json_encode($text);
        $response = $this->client->embeddings()->create([
            'model' => 'text-embedding-ada-002',
            'input' => $text,
        ]);

        return $response['data'][0]['embedding'] ?? null;
    }

    /**
     * Store vector in Pinecone
     */
    public function upsertVector(string $id, array $vector, array $metadata)
    {
        try {
            // $response = $this->pinecone->post("$this->pineconeUrl/vectors/upsert", [
            //     'json' => [
            //         'vectors' => [
            //             [
            //                 'id' => $id,
            //                 'values' => $vector,
            //                 'metadata' => $metadata
            //             ]
            //         ]
            //     ]
            // ]);
            $payload = [
                "vectors" => [
                    [
                        "id" => $id,
                        "values" => $vector,
                        "metadata" => $metadata
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Api-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("$this->pineconeUrl/vectors/upsert", $payload);

            return $response->json();

            //return true;
            // return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Search for similar vectors in Pinecone
     */
    public function queryVector(array $queryVector, $filters, int $topK)
    {
        try {
              $filter = $filters ? $this->convertToPineconeFilter($filters) : null;
            $response = $this->pinecone->post("$this->pineconeUrl/query", [
                'json' => [
                    'vector' => $queryVector,
                    'topK' => $topK,
                    //"filter" => ["available_units" => ['$eq' => "1002"]],
                    "filter" => $filter,
                    'includeMetadata' => true
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function parseNaturalLanguageFilters($userMessage)
    {

        //,price_range(float),price_per_sqm(float),project_size(float)
        // $systemPrompt = "You are a system that converts natural language queries into structured JSON filters compatible with Pinecone available metadata fields:available_units(integer), total_units(integer),launch_date(number),completion_date(number)";
        // $prompt = "Convert the following user query into a JSON filter compatible with Pinecone using  Unix timestamp for metadata date paramters:
        // Query: \"$userMessage\"
        // ";

        // $res = $this->client->chat()->create([ 
        //     'model' => 'gpt-4o',
        //     'messages' => [
        //         ['role' => 'system', 'content' => $systemPrompt],
        //         ['role' => 'user', 'content' => $prompt]
        //     ],
        //     'temperature' => 0,
        //     'max_tokens' => 100

        // ]);
        // Step 1: Translate User Query into English
        $translationPrompt = "make a question from the following query:
Query: \"$userMessage\"
Output:";

        $translationRes = $this->client->chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => 'You are an AI that accurately translates text into English while preserving meaning.'],
                ['role' => 'user', 'content' => $translationPrompt]
            ],
            'temperature' => 0,
            'max_tokens' => 200
        ]);

        $translatedQuery = $translationRes['choices'][0]['message']['content'] ?? $userMessage;
        // - property_name (string) 
        // - project_name (string) 
        // - zone_name (string) 
        // - property_type (string) 
        // - availability_status (string) 
        // - construction_status (string)
       // - project_id (integer) equals 1 for damac project
        // Step 2: Generate Filtered JSON from Translated Query
        $systemPrompt = "You are an AI that converts natural language queries into structured JSON filters compatible with Pinecone's metadata fields.
Ensure the following rules are strictly followed:

✅ Only use numeric values for numeric fields.
✅ Convert date-related values into Unix timestamps (seconds).
✅ Ignore conditions that try to filter a numeric field using a string.
✅ Ensure proper JSON formatting with no extra text.

### **Metadata Fields & Expected Data Types**
- available_units (integer) ✅ **Example:** {\"available_units\": {\"\$gte\": 10}}
- total_units (integer) ✅ **Example:** {\"total_units\": {\"\$lte\": 100}}
- launch_date (Unix timestamp) ✅ **Example:** {\"launch_date\": {\"\$gte\": 1672531200}}
- completion_date (Unix timestamp) ✅ **Example:** {\"completion_date\": {\"\$lte\": 1704067200}}
- bedrooms (integer) 
- bathrooms (integer) 


### **Examples of Invalid Filtering (Must Be Ignored)**
❌ **Query:** 'Find projects where available_units = \"damac\"'  
✅ **Expected Output:** `{}` (Invalid condition is ignored)

Return only a valid JSON object, nothing else.";

        $prompt = "Convert the following user query into a JSON filter:
Query: \"$translatedQuery\"";

        $res = $this->client->chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0,
            'max_tokens' => 100
        ]);

        $response = $res['choices'][0]['message']['content'] ?? "{}";

        //$response = $res['choices'][0]['message']['content'];
        $cleanedString = str_replace('json', '', $response);
        $jsonString = trim($cleanedString, "```");
        $filters = json_decode($jsonString, true);
        return['filters' => $filters , 'translatedQuery' => $translatedQuery];
    }

    private function convertToPineconeFilter($filters)
    {
        $pineconeFilters = [];

        foreach ($filters as $key => $value) {
            if (is_array($value)) {
                // foreach($value as $item => $value_){
                //     $pineconeFilters[$key] = [$item => $value_]; // Already structured as needed
                // }
                $pineconeFilters[$key] =  $value;
            } else {
                $pineconeFilters[$key] = ['$eq' => $value];
            }
        }

        return $pineconeFilters;
    }
}
