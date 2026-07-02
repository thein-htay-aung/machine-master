<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesPlantOptions;
use App\Models\Department;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use ResolvesPlantOptions;

    public function __construct()
    {
        $this->middleware('role:superadmin')->except(['editPassword', 'updatePassword']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with(['role', 'department', 'plant'])->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::whereIn('name', ['viewer', 'editor', 'admin'])->orderBy('name')->get();
        $departments = Department::where('name', '!=', 'System')->orderBy('name')->get();
        $plants = $this->userSelectablePlants();
        $defaultPlantId = $this->defaultUserPlantId();

        return view('users.create', compact('roles', 'departments', 'plants', 'defaultPlantId'));
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
            'plant_id' => ['required', $this->userPlantValidationRule()],
        ]);

        $password = Str::random(8);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'role_id' => $request->role_id,
            'department_id' => $request->department_id,
            'plant_id' => $request->plant_id,
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
        $user->load(['role', 'department', 'plant']);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roleNames = $user->isSuperAdmin()
            ? ['viewer', 'editor', 'admin', 'superadmin']
            : ['viewer', 'editor', 'admin'];
        $roles = Role::whereIn('name', $roleNames)->orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $plants = $this->userSelectablePlants();
        $defaultPlantId = $this->defaultUserPlantId($user->plant_id);

        return view('users.edit', compact('user', 'roles', 'departments', 'plants', 'defaultPlantId'));
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
            'plant_id' => ['required', $this->userPlantValidationRule()],
            'status' => 'required|boolean',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'department_id' => $request->department_id,
            'plant_id' => $request->plant_id,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    public function sendResetLink(User $user)
    {
        $status = Password::sendResetLink(['email' => $user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            $user->update(['status' => false]);
        }

        return redirect()->route('users.edit', $user)->with($status === Password::RESET_LINK_SENT ? ['success' => 'Password reset email sent. User has been disabled until they reset their password.'] : ['error' => __($status)]);
    }

    public function editPassword()
    {
        return view('users.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Password updated successfully. Please log in again.');
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
     * Disable the specified user instead of removing audit history.
     */
    public function destroy(User $user)
    {
        $user->update(['status' => false]);
        return redirect()->route('users.index')->with('success', 'User disabled.');
    }

    private function userSelectablePlants()
    {
        return \App\Models\Plant::whereIn('name', ['All', 'WTY', 'SLB'])
            ->orderByRaw("CASE WHEN name = 'All' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();
    }

    private function userSelectablePlantIds(): array
    {
        return $this->userSelectablePlants()->pluck('id')->all();
    }

    private function userPlantValidationRule()
    {
        return Rule::exists('plants', 'id')->where(fn ($query) => $query->whereIn('id', $this->userSelectablePlantIds()));
    }

    private function defaultUserPlantId(?int $currentPlantId = null): ?int
    {
        return $currentPlantId;
    }

}
