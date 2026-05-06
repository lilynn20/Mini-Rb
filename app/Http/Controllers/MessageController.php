<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $partnerIds = Message::query()
            ->where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->get(['sender_id', 'receiver_id'])
            ->map(fn ($m) => $m->sender_id === $userId ? $m->receiver_id : $m->sender_id)
            ->unique()
            ->values();

        $conversations = $partnerIds->map(function ($otherId) use ($userId) {
            $other = User::find($otherId);
            if (!$other) return null;

            $lastMessage = Message::where(function ($q) use ($userId, $otherId) {
                $q->where('sender_id', $userId)->where('receiver_id', $otherId);
            })->orWhere(function ($q) use ($userId, $otherId) {
                $q->where('sender_id', $otherId)->where('receiver_id', $userId);
            })->latest()->first();

            $unread = Message::where('sender_id', $otherId)
                ->where('receiver_id', $userId)
                ->whereNull('read_at')
                ->count();

            return [
                'user' => [
                    'id'         => $other->id,
                    'name'       => $other->name,
                    'avatar_url' => $other->avatar ? Storage::disk('s3')->url($other->avatar) : null,
                ],
                'last_message' => $lastMessage ? [
                    'content'    => $lastMessage->content,
                    'is_mine'    => $lastMessage->sender_id === $userId,
                    'created_at' => $lastMessage->created_at,
                ] : null,
                'unread' => $unread,
            ];
        })->filter()->sortByDesc(fn ($c) => $c['last_message']['created_at'] ?? null)->values();

        return response()->json($conversations);
    }

    public function conversation(Request $request, User $user)
    {
        $userId = $request->user()->id;

        Message::where('sender_id', $user->id)
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::query()
            ->where(function ($q) use ($userId, $user) {
                $q->where('sender_id', $userId)->where('receiver_id', $user->id);
            })
            ->orWhere(function ($q) use ($userId, $user) {
                $q->where('sender_id', $user->id)->where('receiver_id', $userId);
            })
            ->orderBy('created_at')
            ->get()
            ->map(fn ($m) => [
                'id'         => $m->id,
                'content'    => $m->content,
                'is_mine'    => $m->sender_id === $userId,
                'created_at' => $m->created_at,
                'read_at'    => $m->read_at,
            ]);

        return response()->json([
            'partner' => [
                'id'         => $user->id,
                'name'       => $user->name,
                'avatar_url' => $user->avatar ? Storage::disk('s3')->url($user->avatar) : null,
            ],
            'messages' => $messages,
        ]);
    }

    public function store(Request $request, User $user)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Vous ne pouvez pas vous envoyer un message.'], 422);
        }

        $message = Message::create([
            'sender_id'   => $request->user()->id,
            'receiver_id' => $user->id,
            'content'     => $validated['content'],
        ]);

        return response()->json([
            'id'         => $message->id,
            'content'    => $message->content,
            'is_mine'    => true,
            'created_at' => $message->created_at,
            'read_at'    => null,
        ], 201);
    }

    public function unreadCount(Request $request)
    {
        $count = Message::where('receiver_id', $request->user()->id)
            ->whereNull('read_at')
            ->count();

        return response()->json(['unread' => $count]);
    }
}
