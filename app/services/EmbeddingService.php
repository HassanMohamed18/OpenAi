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
        //$json_data = json_encode($text);
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

    public function processTableData(string $table, array $columns, $columns_reference)
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
                    ->join('projects', 'properties.project_id', '=', 'projects.project_id')
                    ->select('projects.project_id', 'projects.project_name')
                    ->where('properties.property_id', '=', $row->$columns_reference)
                    ->get()->pluck('project_name');

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

    public function storeEmbeddings(string $table, array $columns)
    {

        //$records = DB::table($table)->select(array_merge(['project_id'], $columns))->get();
        //return $records;
        $records = [
            (object)[
                'id' => 1,
                'content' => 'Damac Lagoons - Costa Brava 2 is a residential project with 497 total units and 320 available units. The project was launched on 2021-02-01 and is expected to complete by 2025-07-29. The price range starts from AED 1,535,000 with a price per square meter of AED 1,200. The project size is 93,195.46 sq.mt. Experience Caribbean-inspired waterfront living with stunning views and world-class amenities.'
            ],
            (object)[
                'id' => 2,
                'content' => 'Binghatti Amber is a residential project with 726 total units and 650 available units. The project was launched on 2023-01-01 and is expected to complete by 2027-11-01. The price range starts from AED 577,000 with a price per square meter of AED 1,500. The project size is 54,010.40 sq.mt. Experience elegant living in Binghatti Amber, offering stunning apartments with modern finishes and a prime location in JVC. Enjoy world-class amenities and a convenient lifestyle in this vibrant community.'
            ],
            (object)[
                'id' => 3,
                'content' => 'Diamondz By Danube is a residential project with 1,219 total units and 950 available units. The project was launched on 2024-01-01 and is expected to complete by 2024-12-31. The price range starts from AED 1.12 M with a price per square meter of AED 1,700. The project size is 84,117.24 sq.mt. Experience unparalleled luxury with stunning apartments and world-class amenities in this iconic 62-story tower.'
            ],
            (object)[
                'id' => 4,
                'content' => 'The Bristol Emaar Beachfront is a residential project with 229 total units and 130 available units. The project was launched on 2024-07-01 and is expected to complete by 2029-09-30. The price range starts from AED 2.4 M with a price per square meter of AED 800. The project size is 67,430.36 sq.mt. Experience stunning apartments with breathtaking sea views in this iconic tower.'
            ],
            (object)[
                'id' => 5,
                'content' => 'Sobha Hartland - The Crest is a residential project with 1,518 total units and 1,002 available units. The project was launched on 2020-11-01 and is expected to complete by 2025-12-31. The price range starts from AED 1.1 Million with a price per square meter of AED 700. The project size is 121,044.96 sq.mt. Experience Caribbean-inspired luxury with stunning lagoon views and world-class amenities.'
            ]
        ];
        foreach ($records as $record) {
            // foreach ($columns as $column) {
            //     $content = $record->{$column};
                
            //     if (empty($content)) {
            //         continue; // Skip if the text for this column is empty
            //     }

                try {
                    $embedding = $this->generateEmbeddings($record->content);

                    $dataToInsert = [
                        // 'table_name' => 'projects',
                        // 'source_id' => $record->project_id, 
                        // 'column_name' => $column,
                        // 'content' => $content,
                        'values' => json_encode($embedding),
                        'metadata' => $record->content,

                    ];

                    if (!empty($dataToInsert)) {
                        DB::table('real_state_data')->insert($dataToInsert);
                    }
                } catch (\Exception $e) {
                    return $e->getMessage();
                   // logger()->error("Error processing row ID: {$record->project_id}, Column: {$column} - {$e->getMessage()}");
                }
            //}
        }
        return ['message' => 'Date Inserted Successfully'];
    }

    public function findRelevantContext(array $userEmbedding, int $limit = 5): array
    {
        $vectorData = DB::table('real_state_data')->get();

        $results = $vectorData->map(function ($item) use ($userEmbedding) {
            $storedEmbedding = json_decode($item->values, true);
            $similarity = $this->cosineSimilarity($userEmbedding, $storedEmbedding);

            return [
                'id' => $item->id,
                // 'table_name' => $item->table_name,
                // 'source_id' => $item->source_id,
                // 'column_name' => $item->column_name,
                'text' => $item->metadata,
                'similarity' => $similarity,
            ];
        });

        return $results
        ->sort(function ($a, $b) {
            return $b['similarity'] <=> $a['similarity']; // Descending order
        })->take($limit)->values()->toArray();
        
    }

    /**
     * Compute cosine similarity between two vectors.
     */
    private function cosineSimilarity(array $vecA, array $vecB): float
    {
        // $dotProduct = array_sum(array_map(fn($a, $b) => $a * $b, $vecA, $vecB));
        // $magnitudeA = sqrt(array_sum(array_map(fn($a) => $a ** 2, $vecA)));
        // $magnitudeB = sqrt(array_sum(array_map(fn($b) => $b ** 2, $vecB)));

        // return $dotProduct / ($magnitudeA * $magnitudeB);
        return array_sum(array_map(fn($a, $b) => $a * $b, $vecA, $vecB));
        // $distance = 0.0;

        // foreach ($vecA as $i => $valueA) {
        //     $distance += ($valueA - $vecB[$i]) ** 2;
        // }

        // return sqrt($distance);
    }

    
}
