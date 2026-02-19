<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\TagController;
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

    Route::post('/tickets/{ticket}/reminders', [ReminderController::class, 'store'])->name('tickets.reminders.store');
    Route::put('/tickets/{ticket}/reminders/{reminder}', [ReminderController::class, 'update'])->name('tickets.reminders.update');
    Route::delete('/tickets/{ticket}/reminders/{reminder}', [ReminderController::class, 'destroy'])->name('tickets.reminders.destroy');

    Route::resource('contacts', ContactController::class);

    Route::resource('ticket-types', TicketTypeController::class)->except('show');
    Route::get('/ticket-types/{ticketType}/schema', [TicketTypeController::class, 'schema'])->name('ticket-types.schema');

    Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
    Route::post('/tags', [TagController::class, 'store'])->name('tags.store');
    Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');

    Route::get('/activity', [App\Http\Controllers\ActivityController::class, 'index'])->name('activity.index');

    Route::delete('/media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
});
