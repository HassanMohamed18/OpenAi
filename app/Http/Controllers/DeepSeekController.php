<?php
namespace App\Http\Controllers;

use App\Services\DeepSeekService;
use Illuminate\Http\Request;

class DeepSeekController extends Controller
{
    protected $deepSeekService;

    public function __construct(DeepSeekService $deepSeekService)
    {
        $this->deepSeekService = $deepSeekService;
    }

    public function chat(Request $request)
    {
        // $data = $request->validate([
        //     'input' => 'required|string',
        // ]);

        $result = $this->deepSeekService->ask_deepseek('what is php');

        return response()->json($result);
    }
}
