<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use OpenAI;

class OpenAIService
{
    protected $client;

    public function __construct()
    {
        $this->client = OpenAI::client(env('OPENAI_API_KEY'));
    }

    public function processUserQuery(string $userAsk)
    {
        // Fetch table metadata
        try {

            // $tables = DB::select("SELECT TABLE_NAME
            //                     FROM information_schema.tables 
            //                     WHERE table_schema = 'realstate' 
            //                     and TABLE_NAME 
            //                     IN('areas','locations','projects','units');");
            // $tableNames = array_map('current', $tables);

            // // Provide schema to GPT-4
            // $schemaInfo = [];
            // foreach ($tableNames as $table) {
            //     $columns = DB::select("SHOW COLUMNS FROM {$table}");
            //     $schemaInfo[$table] = array_map(function ($column) {
            //         return $column->Field . ' (' . $column->Type . ')';
            //     }, $columns);
            // }

            // // Prepare GPT-4 prompt
            // $prompt = "You are connected to a MySQL database with the following schema:\n\n";
            // foreach ($schemaInfo as $table => $columns) {
            //     $prompt .= "Table: {$table}\nColumns: " . implode(', ', $columns) . "\n\n";
            // }
            //  $prompt .= "User query: {$userAsk}\nTranslate the entire question to english and Provide only a SQL query to answer this translated english question using matching technique with varchar columns type without explaination and translated question.";

            // $spacing_removed = str_replace(' ', '', $userAsk);
            // return strlen(trim($spacing_removed));

            // $jsonPath = storage_path('app/data.json'); // Path to your JSON file
            // $jsonData = json_decode(file_get_contents($jsonPath), true);

            // $jsonContent = json_encode($jsonData);
            // //Call GPT-4

            // $response = $this->client->chat()->create([
            //     'model' => 'gpt-4o-mini',
            //     'messages' => [
            //         ['role' => 'system', 'content' => 'You are an assistant that answers questions based on JSON data.'],
            //         ['role' => 'user', 'content' => "Here is the data: $jsonContent"],
            //         ['role' => 'user', 'content' => 'i want to know the price of the pc'],
            //     ],
            //     'max_tokens' => 50,
            // ]);



            $response = $this->client->chat()->create([
                'model' => 'gpt-4o',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant who can interact with databases.'],
                    ['role' => 'user', 'content' => $userAsk],
                ],
                'max_tokens' => 20,
            ]);

            // // Extract GPT-4's response
             return $gptResponse =  $response['choices'][0]['message']['content'] ?? 'No response';
            $sqlQuery = $this->extractSQLQuery($gptResponse);
            if ($sqlQuery) {
                return $this->executeSQL($sqlQuery);
            }
        } catch (\Exception $e) {

            return ['error' => $e->getMessage()];
        }
    }
    public function extractSQLQuery(string $response)
    {
        // Extract the SQL query from GPT-4's response using a regex or pattern matching
        preg_match('/SELECT.*?;/is', $response, $matches);
        return $matches[0] ?? null;
    }

    public function executeSQL(string $query)
    {
        try {
            $result = DB::select($query);
            return $result;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
