<?php

use App\Http\Controllers\ReplyController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [TicketController::class, 'create'])->name('tickets.create');
Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');


Route::middleware(['auth'])->group(function () {
    Route::get('/home', [TicketController::class, 'index'])->name('tickets.index');

    Route::get('/tickets/{reference_number}', [TicketController::class, 'show'])->name('tickets.show');
    
    Route::patch('/agent/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.updateStatus');
    
    Route::post('/tickets/{ticket}/replies', [ReplyController::class, 'store'])->name('replies.store');
});
Auth::routes();

