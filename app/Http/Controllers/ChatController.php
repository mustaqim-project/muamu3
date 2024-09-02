<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'receiver_id' => 'required|exists:users,id'
        ]);

        $chat = Chat::create([
            'message' => $validated['message'],
            'sender_id' => Auth::id(),
            'receiver_id' => $validated['receiver_id'],
        ]);

        return response()->json($chat);
    }

    public function getMessages($receiver_id)
    {
        $messages = Chat::where(function ($query) use ($receiver_id) {
            $query->where('sender_id', Auth::id())
                  ->where('receiver_id', $receiver_id);
        })->orWhere(function ($query) use ($receiver_id) {
            $query->where('sender_id', $receiver_id)
                  ->where('receiver_id', Auth::id());
        })->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }
}
