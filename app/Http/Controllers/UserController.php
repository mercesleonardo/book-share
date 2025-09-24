<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\{StoreUserRequest, UpdateUserRequest};
use App\Models\User;
use App\Notifications\InitialPasswordSetupNotification;
use Illuminate\Support\Facades\{Hash, Password, Storage};
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = UserRole::cases();
        $users = User::where('id', '!=', auth()->id())->orderBy('name')->paginate(15);

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = UserRole::cases();

        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'description' => $validated['description'] ?? null,
            'password'    => Hash::make(Str::random(32)),
            'role'        => UserRole::from($validated['role']),
        ]);

        // Gera token de reset manualmente para custom notification
        $token = Password::createToken($user);

        // Envia notificação customizada de configuração inicial
        $user->notify(new InitialPasswordSetupNotification($token));

        return redirect()->route('users.index')->with('success', __('User created. A password setup link was sent to the user.'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user  = User::findOrFail($id);
        $roles = UserRole::cases();

        return view('users.edit', compact('user', 'roles'));
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

        if (!empty($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
