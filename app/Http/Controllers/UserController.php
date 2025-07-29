<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();
        $allUsers = User::with('roles', 'news')->get();
        
        // For Vue.js admin users page, always return Inertia response
        return Inertia::render('Admin/Users/Index', [
            'users' => $allUsers->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'bio' => $user->bio,
                    'image' => $user->image,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                                        'email_verified_at' => $user->email_verified_at,
                    'last_seen' => $user->last_seen,
                    'last_seen_human' => $user->lastSeenHuman(),
                    'is_online' => $user->isOnline(),
                    'news_count' => $user->news->count(),
                    'roles' => $user->roles->map(function ($role) {
                        return [
                            'id' => $role->id,
                            'name' => $role->name,
                        ];
                    }),
                ];
            }),
            'roles' => $roles,
            'currentUser' => auth()->user(),
        ]);
    }

    /**
     * Display Blade version (for legacy support)
     */
    public function manage()
    {
        $roles = Role::all();
        $allUsers = User::with(['roles'])
            ->withCount('news')
            ->get()
            ->map(function ($user) {
                $user->is_online = $user->isOnline();
                $user->last_seen_human = $user->lastSeenHuman();
                return $user;
            });

        // Always return Inertia response for Vue.js
        return \Inertia\Inertia::render('Admin/Users/Manage', [
            'allUsers' => $allUsers,
            'roles' => $roles,
            'currentUser' => auth()->user()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|exists:roles,id',
                'bio' => 'nullable|string|max:500',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'bio' => $request->bio,
            ];

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/images', $imageName);
                $userData['image'] = $imageName;
            }

            $user = User::create($userData);
            
            // Assign role
            $role = Role::findOrFail($request->role);
            $user->assignRole($role);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User berhasil dibuat.',
                ]);
            }

            return redirect()->route('admin.users.manage')->with('success', 'User berhasil dibuat.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        if (Auth::id() !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        return view('admin.profile', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        try {
            // Check if this is admin update or profile update
            if ($request->has('role') || auth()->user()->hasRole('Super Admin')) {
                // Admin update
                return $this->adminUpdate($request, $user);
            }

            // Profile update - only user can update their own profile
            if (Auth::id() !== $user->id) {
                abort(403, 'Unauthorized action.');
            }

            $request->validate([
                'name' => 'sometimes|string|string:max:255',
                'bio' => 'nullable|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:8|confirmed',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $data = [
                'name' => $request->name,
                'bio' => $request->bio,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $image->storeAs('public/images/', $image->hashName());

                Storage::delete('public/images/' . $user->image);

                $data['image'] = $image->hashName();
            }

            $user->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Successfully update the profile.',
                'redirect_url' => route('dashboard', $user->id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Admin update for user management
     */
    public function adminUpdate(Request $request, User $user)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:8|confirmed',
                'role' => 'nullable|exists:roles,id',
                'bio' => 'nullable|string|max:500',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'bio' => $request->bio,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/images', $imageName);
                
                // Delete old image
                if ($user->image) {
                    Storage::delete('public/images/' . $user->image);
                }
                
                $userData['image'] = $imageName;
            }

            $user->update($userData);
            
            // Update role if provided
            if ($request->filled('role')) {
                $role = Role::findOrFail($request->role);
                $user->syncRoles([$role]);
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User berhasil diperbarui.',
                ]);
            }

            return redirect()->route('admin.users.manage')->with('success', 'User berhasil diperbarui.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            if ($user->image) {
                Storage::delete('public/images/' . $user->image);
            }

            foreach ($user->news as $news) {
                if ($news->image) {
                    Storage::delete('public/images/' . $news->image);
                }
            }

            $user->news()->delete();
            $user->notifications()->delete();

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Successfully delete the user.',
                'redirect_url' => route('admin.users.manage')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function assignRole(Request $request, User $user)
    {
        try {
            $request->validate([
                'role' => 'required:exist:roles,id'
            ]);

            $roleId = $request->input('role');
            $role = Role::findOrFail($roleId);

            $user->syncRoles([$role]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully update the role.',
                'redirect_url' => route('admin.users.manage')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
