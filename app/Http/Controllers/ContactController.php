<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        $contacts = Contact::query()->orderBy('name')->paginate(20);

        return view('contacts.index', compact('contacts'));
    }

    public function create(): View
    {
        return view('contacts.create');
    }

    public function store(StoreContactRequest $request): RedirectResponse
    {
        $contact = DB::transaction(function () use ($request) {
            $contact = Contact::query()->create($request->validated());
            $this->handleMedia($request, $contact);

            return $contact;
        });

        return redirect()->route('contacts.show', $contact)->with('success', 'Contact created successfully.');
    }

    public function show(Contact $contact): View
    {
        $tickets = $contact->tickets()->with('ticketType')->latest()->paginate(10);
        $documents = $contact->getMedia('documents');

        return view('contacts.show', compact('contact', 'tickets', 'documents'));
    }

    public function edit(Contact $contact): View
    {
        $documents = $contact->getMedia('documents');

        return view('contacts.edit', compact('contact', 'documents'));
    }

    public function update(UpdateContactRequest $request, Contact $contact): RedirectResponse
    {
        DB::transaction(function () use ($request, $contact) {
            $contact->update($request->validated());
            $this->handleMedia($request, $contact);
        });

        return redirect()->route('contacts.show', $contact)->with('success', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        $contact->delete();

        return redirect()->route('contacts.index')->with('success', 'Contact deleted successfully.');
    }

    private function handleMedia(Request $request, Contact $contact): void
    {
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $contact->addMedia($file)->toMediaCollection('documents');
            }
        }
    }
}
