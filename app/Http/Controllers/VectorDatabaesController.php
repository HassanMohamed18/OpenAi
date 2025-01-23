<?php

namespace App\Http\Controllers;

use App\Services\EmbeddingService;
use Illuminate\Http\Request;
use OpenAI;

class VectorDatabaesController extends Controller
{
    //
    protected $embeddingService;
    protected $client;

    public function __construct(EmbeddingService $embeddingService)
    {
        $this->embeddingService = $embeddingService;
        $this->client = OpenAI::client(env('OPENAI_API_KEY'));
    }

    public function insert_vectors()
    {
        $table = 'projects';
        $columns = ['project_name','project_type','total_units','available_units','launch_date','completion_date','status','price_range','price_range_SQ','description','project_size'];

        //$this->info("Processing table: {$table}, column: {$column}");

        // Call the service to process table data
        $this->embeddingService->processTableData($table, $columns);

       // $this->info("All rows from {$table}.{$column} processed successfully.");
    }
}
