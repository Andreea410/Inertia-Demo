<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizationRequest;
use App\Http\Resources\OrganizationCollection;
use App\Http\Resources\OrganizationResource;
use App\Models\Organization;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $organizations = Organization::query()
            ->withCount('contacts')
            ->filter([
                'search' => $request->query('search'),
                'trashed' => $request->query('trashed'),
            ])
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'component' => 'Organizations/Index',
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
                'organizations' => new OrganizationCollection($organizations),
            ],
            'url' => url()->current(),
            'version' => now()->timestamp,
            'encryptHistory' => false,
            'clearHistory' => false,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrganizationRequest $request)
    {
        $validated = $request->validated();
        $organization = Organization::create($validated);

        return $this->okResponse(
            new OrganizationResource($organization),
            'Organization created successfully.'
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization)
    {
        $organization->load('contacts');
        
        return response()->json([
            'component' => 'Organizations/Edit',
            'props' => [
                'errors' => new \stdClass(),
                'auth' => [
                    'user' => auth()->user(),
                ],
                'flash' => [
                    'success' => session('success'),
                    'error' => session('error'),
                ],
                'organization' => new OrganizationResource($organization),
            ],
            'url' => url()->current(),
            'version' => now()->timestamp,
            'encryptHistory' => false,
            'clearHistory' => false,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrganizationRequest $request, Organization $organization)
    {
        $validated = $request->validated();
        $organization->update($validated);

        return $this->okResponse(
            new OrganizationResource($organization->fresh()),
            'Organization updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization)
    {
        $organization->delete();

        return $this->okResponse(
            null,
            'Organization deleted successfully.'
        );
    }

    /**
     * Restore a soft-deleted resource.
     */
    public function restore($id)
    {
        $organization = Organization::withTrashed()->findOrFail($id);
        $organization->restore();

        return $this->okResponse(
            new OrganizationResource($organization->fresh()),
            'Organization restored successfully.'
        );
    }
}

