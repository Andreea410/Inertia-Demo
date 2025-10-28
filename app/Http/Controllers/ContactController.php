<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        $contacts = Contact::query()
            ->with('organization')
            ->orderByName()
            ->filter([
                'search' => $request->query('search'),
                'trashed' => $request->query('trashed'),
            ])
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'component' => 'Contacts/Index',
            'props' => [
                'errors' => new \stdClass(),
                'auth' => [
                    'user' => auth()->user(),
                ],
                'flash' => [
                    'success' => session('success'),
                    'error' => session('error'),
                ],
                'filters' => [
                    'search' => $request->query('search'),
                    'trashed' => $request->query('trashed'),
                ],
                'contacts' => new ContactCollection($contacts),
            ],
            'url' => url()->current(),
            'version' => now()->timestamp,
            'encryptHistory' => false,
            'clearHistory' => false,
        ]);
    }


    public function store(ContactRequest $request)
    {
        $validated = $request->validated();
        $contact = Contact::create($validated);

        return $this->okResponse(
            new ContactResource($contact->load('organization')),
            'Contact created successfully.'
        );
    }

    public function show(Contact $contact)
    {
        $contact->load('organization');

        return $this->okResponse(new ContactResource($contact));
    }

    public function update(ContactRequest $request, Contact $contact)
    {
        $validated = $request->validated();
        $contact->update($validated);

        return $this->okResponse(
            new ContactResource($contact->fresh()->load('organization')),
            'Contact updated successfully.'
        );
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return $this->okResponse(
            null,
            'Contact deleted successfully.'
        );
    }

    public function restore($id)
    {
        $contact = Contact::withTrashed()->findOrFail($id);
        $contact->restore();

        return $this->okResponse(
            new ContactResource($contact->fresh()->load('organization')),
            'Contact restored successfully.'
        );
    }
}

