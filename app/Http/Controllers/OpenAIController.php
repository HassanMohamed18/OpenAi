<?php

namespace App\Http\Controllers;

use App\Services\HttpService;
use Illuminate\Http\Request;

class OpenAIController extends Controller
{
    protected $HttpService;

    public function __construct(HttpService $HttpService)
    {
        $this->HttpService = $HttpService;
    }

    public function chat(Request $request)
    {
        // $request->validate([
        //     'prompt' => 'required|string',
        // ]);

        // $prompt = $request->input('prompt');
        $response = $this->HttpService->AskGpt('i want to know the projects in dubai area');

        return response()->json($response);
    }
}
