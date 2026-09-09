<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(): View
    {
        $users = User::with('roles')
            ->orderByRaw("CASE WHEN id IN (SELECT model_id FROM model_has_roles WHERE role_id IN (SELECT id FROM roles WHERE name = 'admin')) THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();

        return view('users.index', [
            'users' => $users,
        ]);
    }

    public function create(): View
    {
        return view('users.create', [
            'roles' => Role::whereIn('name', ['admin', 'engineer', 'manager'])->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in(['admin', 'engineer', 'manager'])],
            'status' => ['required', Rule::in(array_keys(User::STATUSES))],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => $validated['status'],
        ]);

        $user->syncRoles($validated['role']);

        ActivityLogger::log(
            'user.created',
            'users',
            "Created {$validated['role']} account for {$user->name}",
            $user,
            ['role' => $validated['role']],
        );

        return redirect()->route('users.index')
            ->with('success', 'User account created successfully.');
    }

    public function quickCreate(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'min:8'],
                'role' => ['required', Rule::in(['admin', 'engineer', 'manager'])],
                'status' => ['required', Rule::in(array_keys(User::STATUSES))],
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'status' => $validated['status'],
            ]);

            $user->syncRoles($validated['role']);

            ActivityLogger::log(
                'user.created',
                'users',
                "Created {$validated['role']} account for {$user->name}",
                $user,
                ['role' => $validated['role']],
            );

            return response()->json([
                'success' => true,
                'message' => 'Engineer added successfully.',
                'engineer' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function edit(User $user): View
    {
        return view('users.edit', [
            'user' => $user->load('roles'),
            'roles' => Role::whereIn('name', ['admin', 'engineer', 'manager'])->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in(['admin', 'engineer', 'manager'])],
            'status' => ['required', Rule::in(array_keys(User::STATUSES))],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'status' => $validated['status'],
        ]);

        if (! empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles($validated['role']);

        ActivityLogger::log(
            'user.updated',
            'users',
            "Updated account for {$user->name}",
            $user,
            ['role' => $validated['role']],
        );

        return redirect()->route('users.index')
            ->with('success', 'User account updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        ActivityLogger::log(
            'user.deleted',
            'users',
            "Deleted account for {$name}",
        );

        return redirect()->route('users.index')
            ->with('success', 'User account deleted successfully.');
    }
}
