<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\{IndexUserRequest, StoreUserRequest, UpdateUserRequest};
use App\Models\User;
use App\Notifications\InitialPasswordSetupNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\{Hash, Password, Storage};
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexUserRequest $request): View
    {
        $this->authorize('viewAny', User::class);

        $roles = UserRole::cases();
        $users = User::withTrashed()->where('id', '!=', auth()->id());

        $validated = $request->validated();

        if (!empty($validated['name'])) {
            $name = $validated['name'];
            $users->where('name', 'like', "%{$name}%");
        }

        if (!empty($validated['role'])) {
            $users->where('role', $validated['role']);
        }

        $users = $users->orderBy('name')->paginate(15)->withQueryString();

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', User::class);
        $roles = UserRole::cases();

        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $validated = $request->validated();

        $user = User::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'description' => $validated['description'] ?? null,
            'password'    => Hash::make(Str::random(32)),
            'role'        => UserRole::from($validated['role']),
        ]);

        // Generate reset token manually for custom notification
        $token = Password::createToken($user);

        // Send notification to set initial password
        $user->notify(new InitialPasswordSetupNotification($token));

        return redirect()->route('users.index')->with('success', __('users.created'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        $this->authorize('update', $user);
        $roles = UserRole::cases();

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $validated = $request->validated();

        $validated['role'] = UserRole::from($validated['role']);
        $user->update($validated);

        return redirect()->route('users.index')->with('success', __('users.updated'));
    }

    /**
    * Restore the specified resource from storage.
    */
    public function restore(User $user): RedirectResponse
    {
        $this->authorize('restore', $user);
        $user->restore();

        return redirect()->route('users.index')->with('success', __('users.restored'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        if (!empty($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', __('users.deleted'));
    }
}
