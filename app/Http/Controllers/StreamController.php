<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StreamController extends Controller
{

    public function index()
    {

        //$messages  = collect($messages);
        return view('stream');
    }
    public function streamForm(Request $request)
    {
        // Validate the form input
        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);

        $message = $validated['message']; // Get the validated message

        // Return a stream response
        return response()->stream(function () use ($message) {
            // Set headers for SSE (Server-Sent Events)
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');

            // Set the delay (in seconds) between each character
            $delay = 0.5; // You can adjust the delay (in seconds)

            foreach (str_split($message) as $char) {
                // Send the character as a stream
                echo "data: " . json_encode(['message' => $char]) . "\n\n";
                flush(); // Ensure the message is sent immediately to the client

                // Sleep for the specified delay before sending the next character
                usleep($delay * 1000000); // Delay in microseconds (0.5 sec = 500000)
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
    }
}
