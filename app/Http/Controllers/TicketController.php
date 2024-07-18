<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{
    public function create()
    {
        return view('tickets.create');
    }

    public function index(Request $request)
    {
        $query = Ticket::query();

        if ($request->has('search')) {
            $query->where('customer_name', 'like', '%' . $request->search . '%');
        }

        $tickets = $query->paginate(10);

        if ($request->expectsJson()) {
            return response()->json($tickets->items());
        }

        return view('tickets.index', ['tickets' => $tickets]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'problem_description' => 'required|string',
            'email' => 'required|email',
            'phone_number' => 'required|string|max:15|regex:/^[0-9]{10,15}$/',
        ]);

        $ticket = Ticket::create([
            'customer_name' => $validated['customer_name'],
            'problem_description' => $validated['problem_description'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'reference_number' => Str::uuid(),
            'status' => 'pending',
        ]);

        Mail::to($ticket->email)->send(new \App\Mail\TicketCreated($ticket));

        return response()->json(['reference_number' => $ticket->reference_number]);
    }

    public function show($reference_number)
    {
        $ticket = Ticket::where('reference_number', $reference_number)->firstOrFail();
        return view('tickets.show', compact('ticket'));
    }

    public function updateStatus(Ticket $ticket)
    {
        $ticket->update(['status' => 'opened']);
        return response()->json(['status' => 'success']);
    }
}
