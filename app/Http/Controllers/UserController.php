<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->orderByName()
            ->filter([
                'search' => $request->query('search'),
                'role' => $request->query('role'),
                'trashed' => $request->query('trashed'),
            ])
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'component' => 'Users/Index',
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
                    'role' => $request->query('role'),
                    'trashed' => $request->query('trashed'),
                ],
                'users' => UserResource::collection($users),
            ],
            'url' => url()->current(),
            'version' => now()->timestamp,
            'encryptHistory' => false,
            'clearHistory' => false,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return response()->json([
            'component' => 'Users/Edit',
            'props' => [
                'errors' => new \stdClass(),
                'auth' => [
                    'user' => auth()->user(),
                ],
                'flash' => [
                    'success' => session('success'),
                    'error' => session('error'),
                ],
                'user' => new UserResource($user),
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
    public function store(UserRequest $request)
    {
        $validated = $request->validated();

        // Hash password if provided
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        // Handle photo upload if provided
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('photos');
        }

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        $validated = $request->validated();

        // Hash password if provided
        if (isset($validated['password']) && $validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Handle photo upload if provided
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo && file_exists(public_path($user->photo))) {
                unlink(public_path($user->photo));
            }
            $validated['photo'] = $request->file('photo')->store('photos');
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data' => new UserResource($user->fresh()),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ]);
    }

    /**
     * Restore a soft-deleted resource.
     */
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return response()->json([
            'success' => true,
            'message' => 'User restored successfully.',
            'data' => new UserResource($user->fresh()),
        ]);
    }
}

