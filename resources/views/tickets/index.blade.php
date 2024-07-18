@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Pending Tickets</h1>
    <input type="text" id="search" class="form-control" placeholder="Search by customer name...">
    <table class="table mt-4">
        <thead>
            <tr>
                <th>Reference Number</th>
                <th>Customer Name</th>
                <th>Problem Description</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="ticket-list">
            @foreach($tickets as $ticket)
                <tr @if($ticket->status == 'pending') class="table-warning" @endif>
                    <td>{{ $ticket->reference_number }}</td>
                    <td>{{ $ticket->customer_name }}</td>
                    <td>{{ $ticket->problem_description }}</td>
                    <td>{{ $ticket->status }}</td>
                    <td>
                        <a href="{{ route('tickets.show', $ticket->reference_number) }}" class="btn btn-primary">View</a>
                        @if($ticket->status == 'pending')
                            <button class="btn btn-success update-status" data-id="{{ $ticket->id }}">Mark as Opened</button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $tickets->links() }}
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('search').addEventListener('input', function(event) {
        let query = event.target.value;

        fetch(`/home?search=${query}`, {
            headers: {
                'Accept': 'application/json',
            }
        }).then(response => response.json())
          .then(data => {
              let ticketList = document.getElementById('ticket-list');
              ticketList.innerHTML = '';
              data.forEach(ticket => {
                  let row = document.createElement('tr');
                  if (ticket.status === 'pending') row.classList.add('table-warning');
                  row.innerHTML = `
                      <td>${ticket.reference_number}</td>
                      <td>${ticket.customer_name}</td>
                      <td>${ticket.problem_description}</td>
                      <td>${ticket.status}</td>
                      <td>
                          <a href="/tickets/${ticket.reference_number}" class="btn btn-primary">View</a>
                          ${ticket.status === 'pending' ? `<button class="btn btn-success update-status" data-id="${ticket.id}">Mark as Opened</button>` : ''}
                      </td>
                  `;
                  ticketList.appendChild(row);
              });
          }).catch(error => {
              console.error('Error:', error);
          });
    });

    document.getElementById('ticket-list').addEventListener('click', function(event) {
        if (event.target.classList.contains('update-status')) {
            let ticketId = event.target.getAttribute('data-id');
            // console.log('Ticket ID:', ticketId);

            fetch(`/agent/tickets/${ticketId}/status`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ status: 'opened' })
            }).then(response => {
                return response.json();
            }).then(data => {
                // console.log('Response data:', data);
                if (data.status === 'success') {
                    event.target.closest('tr').classList.remove('table-warning');
                    event.target.remove();
                } else {
                    console.error('Failed to update status:', data);
                }
            }).catch(error => {
                console.error('Error:', error);
            });
        }
    });
</script>
@endsection
