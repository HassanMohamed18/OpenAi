<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class googleSearchController extends Controller
{
    //
    protected $googleApiKey;
    protected $googleCseId;
    protected $client;
    public function __construct()
    {
        
        $this->googleApiKey = env('GOOGLE_CSE_API_KEY');
        $this->googleCseId = env('GOOGLE_CSE_ID');
        $this->client = new Client();
    }
    public function google_search()
    {
    $query = "top 5 damac projects";

        $searchResults = "";
        try {
            $response = $this->client->request('GET', 'https://www.googleapis.com/customsearch/v1', [
                'query' => [
                    'key' => $this->googleApiKey,
                    'cx' => $this->googleCseId,
                    'q' => $query,
                    //'num' => 5, // Limit results to 5
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            //return $data;
            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    $searchResults .= "- " . $item['title'] . ": " . $item['snippet'] . "\n".
                    "  [Read more](" . $item['link'] . ")\n\n";
                }
            }
        } catch (\Exception $e) {
            $searchResults = "Web search failed. No relevant results found.";
        }

        return $searchResults ?: "No relevant web search results found.";
    }
}
