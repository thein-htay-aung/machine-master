<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:superadmin');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with(['role', 'department'])->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::where('name', '!=', 'superadmin')->orderBy('name')->get();
        $departments = Department::where('name', '!=', 'System')->orderBy('name')->get();

        return view('users.create', compact('roles', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'required|exists:departments,id',
        ]);

        $password = Str::random(8);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'role_id' => $request->role_id,
            'department_id' => $request->department_id,
            'status' => true,
        ]);

        // Send email with password and verification link
        Mail::to($user->email)->send(new \App\Mail\UserCreated($user, $password));

        return redirect()->route('users.index')->with('success', 'User created and email sent.');
    }

    /**
     * Send an email notification to the user.
     */
    public function sendEmail(User $user)
    {
        try {
            Mail::to($user->email)->send(new \App\Mail\UserCreated($user, null));
            return redirect()->route('users.show', $user)->with('success', 'Email sent to the user.');
        } catch (\Throwable $e) {
            return redirect()->route('users.show', $user)->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['role', 'department']);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('users.edit', compact('user', 'roles', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'required|exists:departments,id',
        ]);

        $user->update($request->only(['name', 'email', 'role_id', 'department_id']));

        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    /**
     * Enable the user.
     */
    public function enable(User $user)
    {
        $user->update(['status' => true]);
        return redirect()->route('users.index')->with('success', 'User enabled.');
    }

    /**
     * Disable the user.
     */
    public function disable(User $user)
    {
        $user->update(['status' => false]);
        return redirect()->route('users.index')->with('success', 'User disabled.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
}
