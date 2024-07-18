@extends('layouts.app')

@section('content')
<div class="container border">
    <h1 class="my-4">Create Ticket</h1>
    <form id="ticket-form" class="needs-validation mb-4" novalidate>
        @csrf
        <div id="success-message" class="alert alert-success mt-3" style="display: none;">
            <p>Ticket created successfully. Reference number: <span id="reference-number"></span></p>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="customer_name">Customer Name</label>
                <input type="text" name="customer_name" class="form-control" required>
                <div class="invalid-feedback">Customer name is required.</div>
            </div>
            <div class="form-group col-md-6">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" required>
                <div class="invalid-feedback">Valid email is required.</div>
            </div>
        </div>
        <div class="form-group">
            <label for="problem_description">Problem Description</label>
            <textarea name="problem_description" class="form-control" rows="5" required></textarea>
            <div class="invalid-feedback">Problem description is required.</div>
        </div>
        <div class="form-group mb-2">
            <label for="phone_number">Phone Number</label>
            <input type="text" name="phone_number" class="form-control" required>
            <div class="invalid-feedback">Phone number is required.</div>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
    
</div>

@section('scripts')
<script>
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();

    document.getElementById('ticket-form').addEventListener('submit', function(event) {
        event.preventDefault();
        if (this.checkValidity() === false) {
            event.stopPropagation();
            return;
        }

        let formData = new FormData(this);

        fetch('{{ route('tickets.store') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        }).then(response => response.json())
          .then(data => {
              document.getElementById('success-message').style.display = 'block';
              document.getElementById('reference-number').textContent = data.reference_number;
              document.getElementById('ticket-form').reset();
              document.getElementById('ticket-form').classList.remove('was-validated');
          }).catch(error => {
              console.error('Error:', error);
          });
    });
</script>
@endsection
@endsection
