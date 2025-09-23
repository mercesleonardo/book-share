<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\{StoreUserRequest, UpdateUserRequest};
use App\Models\User;
use Illuminate\Support\Facades\{Hash, Storage};

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = UserRole::cases();
        $users = User::orderBy('name')->paginate(10);

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => UserRole::from($validated['role']),
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $user      = User::findOrFail($id);
        $validated = $request->validated();

        $validated['role'] = UserRole::from($validated['role']);
        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if (!empty($user->profile_photo) && isset($data['profile_photo'])) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
