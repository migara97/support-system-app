<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ReplyController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'reply_message' => 'required|string',
        ]);

        $reply = Reply::create([
            'ticket_id' => $ticket->id,
            'reply_message' => $validated['reply_message'],
        ]);

        Mail::to($ticket->email)->send(new \App\Mail\TicketReplied($reply));

        return response()->json(['reply_message' => $reply->reply_message]);
    }
}
