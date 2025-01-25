<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use OpenAI;

class EmbeddingService
{
    protected $openai;

    public function __construct()
    {
        $this->openai = OpenAI::client(env('OPENAI_API_KEY'));
    }

    /**
     * Generate embeddings for a given text.
     */
    public function generateEmbeddings(string $text): array
    {
        $response = $this->openai->embeddings()->create([
            'model' => 'text-embedding-ada-002', // Optimized for embeddings
            'input' => $text,
        ]);

        return $response['data'][0]['embedding'];
    }

    /**
     * Process rows from a specified table and column, and store embeddings in the vector_data table.
     */
    // public function processTableData(string $table, array $columns, int $chunkSize = 100)
    // {

    //     DB::table($table)
    //         ->select(array_merge(['project_id'], $columns)) // Select the id and all specified columns
    //         ->where(function ($query) use ($columns) {
    //             foreach ($columns as $column) {
    //                 $query->orWhereNotNull($column); // Ensure at least one column has a value
    //             }
    //         })
    //         ->orderBy('project_id')
    //         ->chunk($chunkSize, function ($rows) use ($columns) {
    //             $dataToInsert = [];

    //             foreach ($rows as $row) {
    //                 // foreach ($columns as $column) {
    //                 //     $text = $row->{$column};

    //                 //     if (empty($text)) {
    //                 //         continue; // Skip if the text for this column is empty
    //                 //     }
    //                 $textParts = [];
    //                 foreach ($columns as $column) {
    //                     $value = $row->{$column} ?? ''; // Get column value or empty string
    //                     if (!empty($value)) {
    //                         $textParts[] = "{$column}: {$value}"; // Format as "column_name: value"
    //                     }
    //                 }
    //                 $text = implode(', ', $textParts); // Join all parts with a comma

    //                 // Skip processing if the concatenated text is empty
    //                 if (empty($text)) {
    //                     continue;
    //                 }


    //                 try {
    //                     $embedding = $this->generateEmbeddings($text);

    //                     $dataToInsert[] = [
    //                         'source_table' => 'projects', // Reference back to the original table row
    //                         //'column_name' => $column,
    //                         'text' => $text,
    //                         'embedding' => json_encode($embedding),
    //                         'created_at' => now(),
    //                         'updated_at' => now(),
    //                     ];

    //                     if (!empty($dataToInsert)) {
    //                         DB::table('vector_data')->insert($dataToInsert);
    //                     }


    //                 } catch (\Exception $e) {
    //                     logger()->error("Error processing row ID: {$row->project_id}, Column: {$column} - {$e->getMessage()}");
    //                 }
    //             //}

    //             }
    //             return ['suces' => 'true','message' => 'runnnnnn'];
    //         });
    // }

    public function processTableData(string $table, array $columns,$columns_reference)
    {
        try {
            // Fetch all rows from the table
            $rows = DB::table($table)
                ->select(array_merge([$columns_reference], $columns)) // Select project_id and specified columns
                ->where(function ($query) use ($columns) {
                    foreach ($columns as $column) {
                        $query->orWhereNotNull($column); // Ensure at least one column has a value
                    }
                })
                ->orderBy($columns_reference)
                ->get();

            $dataToInsert = [];
                // $project_name = DB::table($table)
                // ->join('projects','properties.project_id','=','projects.project_id')
                // ->select('projects.project_id','projects.project_name')
                // ->where('properties.property_id','=',1)
                // ->get()->pluck('project_name');
                //  $project_name;
            $reference = 'project_name';

                //return $textParts[] = "{$reference}: {$project_name[0]}";
            // Process each row
            foreach ($rows as $row) {
                $textParts = [];
                foreach ($columns as $column) {
                    $value = $row->{$column} ?? ''; // Get column value or empty string
                    if (!empty($value)) {
                        $textParts[] = "{$column}: {$value}"; // Format as "column_name: value"
                    }
                }
                $project_name = DB::table($table)
                ->join('projects','properties.project_id','=','projects.project_id')
                ->select('projects.project_id','projects.project_name')
                ->where('properties.property_id','=',$row->$columns_reference)
                ->get()->pluck('project_name');
                 $project_name;
                $textParts[] = "{$reference}: {$project_name[0]}";
                $text = implode(', ', $textParts); // Join all parts with a comma

                // Skip processing if the concatenated text is empty
                if (empty($text)) {
                    continue;
                }

                try {
                    // Generate embeddings for the text
                    $embedding = $this->generateEmbeddings($text);

                    $dataToInsert[] = [
                        'source_table' => 'projects', // Reference back to the original table row
                        'text' => $text,
                        'embedding' => json_encode($embedding),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                } catch (\Exception $e) {
                    logger()->error("Error processing row ID: {$row->$columns_reference} - {$e->getMessage()}");
                }
            }

            // Insert data into vector_data table
            if (!empty($dataToInsert)) {
                DB::table('vector_data')->insert($dataToInsert);
            }

            return ['success' => true, 'message' => 'Data processed successfully'];
        } catch (\Exception $e) {
            logger()->error("Error processing table data: {$e->getMessage()}");
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function findRelevantContext(array $userEmbedding, int $limit = 4): array
    {
        $vectorData = DB::table('vector_data')->get();

        $results = $vectorData->map(function ($item) use ($userEmbedding) {
            $storedEmbedding = json_decode($item->embedding, true);
            $similarity = $this->cosineSimilarity($userEmbedding, $storedEmbedding);

            return [
                'id' => $item->id,
                'text' => $item->text,
                'similarity' => $similarity,
            ];
        });

        return $results
            ->sortByDesc('similarity')
            ->take($limit)
            ->toArray();
    }

    /**
     * Compute cosine similarity between two vectors.
     */
    private function cosineSimilarity(array $vecA, array $vecB): float
    {
        $dotProduct = array_sum(array_map(fn($a, $b) => $a * $b, $vecA, $vecB));
        $magnitudeA = sqrt(array_sum(array_map(fn($a) => $a ** 2, $vecA)));
        $magnitudeB = sqrt(array_sum(array_map(fn($b) => $b ** 2, $vecB)));

        return $dotProduct / ($magnitudeA * $magnitudeB);
    }



}
