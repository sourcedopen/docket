<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    Route::resource('tickets', \App\Http\Controllers\TicketController::class);
    Route::resource('contacts', \App\Http\Controllers\ContactController::class);
    Route::resource('ticket-types', \App\Http\Controllers\TicketTypeController::class);
    Route::get('/tags', fn () => view('tags.index'))->name('tags.index');
    Route::get('/activity', fn () => view('activity.index'))->name('activity.index');
});
