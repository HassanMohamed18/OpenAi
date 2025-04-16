<?php

namespace App\Http\Controllers\Base\AI;

use App\Http\Controllers\Base\Controller;
use App\Interface\Base\AI\AiConversationInterface;
use Illuminate\Http\Request;

class AiConversationController extends Controller
{
    protected $aiConversationInterface;
    protected $user_id;

    public function __construct(AiConversationInterface $aiConversationInterface)
    {
        $this->aiConversationInterface = $aiConversationInterface;
        $this->user_id = 1;
    }

    public function index()
    {
        $ai_conversations = $this->aiConversationInterface->getAllUserConversations($this->user_id);
        return response()->json([
            'message' => 'Conversations Retreived Successfully',
            'data' => $ai_conversations
        ], 200);
    }

    public function show(int $id)
    {

        $ai_conversation = $this->aiConversationInterface->getByID($id);
        if (!$ai_conversation) {
            return response()->json([
                'message' => 'Conversation Not Found',
            ], 404);
        }

        return response()->json([
            'message' => 'Conversation Retreived Successfully',
            'data' => $ai_conversation
        ], 200);
    }

    public function destroy(int $id)
    {

        $ai_conversation = $this->aiConversationInterface->deleteConversation($id);
        if (!$ai_conversation) {
            return response()->json([
                'message' => 'Conversation Not Found',
            ], 404);
        }

        return response()->json([
            'message' => 'Conversation Deleted Successfully',
        ], 204);
    }
}
