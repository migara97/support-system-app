@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Ticket #{{ $ticket->reference_number }}</h1>
    <p><strong>Customer Name:</strong> {{ $ticket->customer_name }}</p>
    <p><strong>Problem Description:</strong> {{ $ticket->problem_description }}</p>
    <p><strong>Email:</strong> {{ $ticket->email }}</p>
    <p><strong>Phone Number:</strong> {{ $ticket->phone_number }}</p>
    <p><strong>Status:</strong> {{ $ticket->status }}</p>

    <h2>Replies</h2>
    <div id="replies">
        @foreach($ticket->replies as $reply)
            <div class="card mb-2">
                <div class="card-body">
                    {{ $reply->reply_message }}
                </div>
            </div>
        @endforeach
    </div>

    @if($ticket->status != 'closed')
    <form id="reply-form" class="border p-2">
        @csrf
        <div class="form-group mb-2">
            <label for="reply_message">Your Reply</label>
            <textarea name="reply_message" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send Reply</button>
    </form>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('reply-form').addEventListener('submit', function(event) {
        event.preventDefault();
        let formData = new FormData(this);

        fetch('{{ route('replies.store', $ticket) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        }).then(response => response.json())
          .then(data => {
              let replyDiv = document.createElement('div');
              replyDiv.className = 'card mb-2';
              replyDiv.innerHTML = `<div class="card-body">${data.reply_message}</div>`;
              document.getElementById('replies').appendChild(replyDiv);
              document.getElementById('reply-form').reset();
          }).catch(error => {
              console.error('Error:', error);
          });
    });
</script>
@endsection
