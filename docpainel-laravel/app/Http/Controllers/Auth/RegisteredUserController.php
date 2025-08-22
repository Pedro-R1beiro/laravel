<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create($role = 'client'): View
    {
        if (!in_array($role, ['client', 'sub-adm', 'adm'])) {
            abort(404);
        }

        return view('auth.register', ['role' => $role]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $role = !in_array($request->role, ['client', 'sub-adm', 'adm']) ? 'client' : $request->role;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        if (in_array($role, ['sub-adm', 'adm'])) {
            $rules['secret_password'] = ['required'];
        }

        $request->validate($rules);


        if (in_array($role, ['sub-adm', 'adm'])) {
            $passwords = [
                'adm' => env('ADM_SECRET'),
                'sub-adm' => env('SUBADM_SECRET')
            ];

            if ($request->secret_password !== $passwords[$role]) {
                return back()->withErrors(['secret_password' => 'Invalid secret password']);
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($role);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
