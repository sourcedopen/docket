<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketTypeController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    Route::resource('tickets', TicketController::class);
    Route::get('/tickets/create/{ticketType}', [TicketController::class, 'createWithType'])->name('tickets.create-with-type');
    Route::post('/tickets/{ticket}/comments', [CommentController::class, 'store'])->name('tickets.comments.store');
    Route::delete('/tickets/{ticket}/comments/{comment}', [CommentController::class, 'destroy'])->name('tickets.comments.destroy');

    Route::resource('contacts', ContactController::class);

    Route::resource('ticket-types', TicketTypeController::class)->except('show');
    Route::get('/ticket-types/{ticketType}/schema', [TicketTypeController::class, 'schema'])->name('ticket-types.schema');

    Route::get('/tags', fn () => view('tags.index'))->name('tags.index');
    Route::get('/activity', fn () => view('activity.index'))->name('activity.index');
});
