<?php

namespace App\Services;

use GuzzleHttp\Client;

class GoogleSearchService
{
    protected $googleApiKey;
    protected $googleCseId;
    protected $apiUrl;
    protected $client;
    public function __construct()
    {
        
        $this->googleApiKey = env('GOOGLE_CSE_API_KEY');
        $this->googleCseId = env('GOOGLE_CSE_ID');
        $this->apiUrl = 'https://www.googleapis.com/customsearch/v1';
        $this->client = new Client();
    }
    public function web_search($query)
    {

        $searchResults = "";
        try {
            $response = $this->client->request('GET',$this->apiUrl, [
                'query' => [
                    'key' => $this->googleApiKey,
                    'cx' => $this->googleCseId,
                    'q' => $query,
                    'num' => 5, // Limit results to 5
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            //return $data;
            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    $searchResults .= "- " . $item['title'] . ": " . $item['snippet'] . "\n";
                }
            }
        } catch (\Exception $e) {
            $searchResults = "Web search failed. No relevant results found.";
        }

        return $searchResults ?: "No relevant web search results found.";
    }
}
