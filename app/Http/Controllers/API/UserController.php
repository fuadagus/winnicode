<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Location;

class UserController extends Controller
{
    // List all users with their related locations and news
    public function index()
    {
        return response()->json(
            User::with(['locations', 'news'])->get()
        );
    }

    // Store a new user
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255|unique:users',
            'password'  => 'required|string|min:8',
            'role'      => ['required', Rule::in(['admin', 'editor', 'user'])],
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'location_ids' => 'array',
            'location_ids.*' => 'exists:locations,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        if ($request->has('location_ids')) {
            $user->locations()->sync($request->location_ids);
        }

        return response()->json($user->load('locations'), 201);
    }

    // Show one user
    public function show($id)
    {
        $user = User::with(['locations', 'news'])->findOrFail($id);
        return response()->json($user);
    }

    // Update user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'      => 'sometimes|string|max:255',
            'email'     => ['sometimes', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'  => 'sometimes|string|min:8',
            'role'      => ['sometimes', Rule::in(['admin', 'editor', 'user'])],
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'location_ids' => 'array',
            'location_ids.*' => 'exists:locations,id',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        if ($request->has('location_ids')) {
            $user->locations()->sync($request->location_ids);
        }

        return response()->json($user->load('locations'));
    }

    // Delete user
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->locations()->detach(); // optional
        $user->delete();

        return response()->json(null, 204);
    }
}
