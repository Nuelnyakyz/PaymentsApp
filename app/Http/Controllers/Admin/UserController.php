<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_admin' => ['sometimes', 'boolean'],
        ]);

        $data['is_admin'] = (bool) ($data['is_admin'] ?? false);

        User::create($data);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_admin' => ['sometimes', 'boolean'],
        ]);

        $makeAdmin = (bool) ($data['is_admin'] ?? false);
        $currentUser = $request->user();
        $isPrimaryAdmin = $user->primary_admin;

        if ($isPrimaryAdmin) {
            $makeAdmin = true;
        }

        if ($user->id === $currentUser->id && !$makeAdmin) {
            return back()
                ->withErrors(['is_admin' => 'You cannot remove your own admin access.'])
                ->withInput($request->except(['password', 'password_confirmation']));
        }

        if ($user->is_admin && !$makeAdmin && !$this->hasOtherAdmins($user)) {
            return back()
                ->withErrors(['is_admin' => 'At least one admin account must remain.'])
                ->withInput($request->except(['password', 'password_confirmation']));
        }

        $user->fill([
            'name' => $isPrimaryAdmin ? $user->name : $data['name'],
            'email' => $isPrimaryAdmin ? $user->email : $data['email'],
            'is_admin' => $makeAdmin,
        ]);
        if (!empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['delete' => 'You cannot delete your own account.']);
        }

        if ($user->primary_admin) {
            return back()->withErrors(['delete' => 'The primary Admin account cannot be deleted.']);
        }

        if ($user->is_admin && !$this->hasOtherAdmins($user)) {
            return back()->withErrors(['delete' => 'Cannot delete the last admin account.']);
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User deleted successfully.');
    }

    private function hasOtherAdmins(User $excluding): bool
    {
        return User::where('is_admin', true)
            ->where('id', '!=', $excluding->id)
            ->exists();
    }

}
