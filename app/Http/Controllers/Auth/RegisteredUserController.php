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
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // [PERBAIKAN 1] Menambahkan semua field dari form ke validasi
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'full_name' => ['required', 'string', 'max:255'], // Wajib diisi sesuai skema DB
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'job_title' => ['nullable', 'string', 'max:255'], // Boleh kosong
            'gender' => ['nullable', 'string', 'in:male,female,other'], // Boleh kosong
            'birth_date' => ['nullable', 'date'], // Boleh kosong
            'profile_picture' => ['nullable', 'image', 'max:2048'], // Boleh kosong, maks 2MB
        ]);

        // Menangani upload file jika ada
        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->store('profile-pictures', 'public');
        }

        // [PERBAIKAN 2] Menambahkan semua field ke dalam User::create
        $user = User::create([
            'name' => $request->name,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'job_title' => $request->job_title,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'profile_picture' => $profilePicturePath,
        ]);

        event(new Registered($user));

        Auth::login($user);

        // [PERBAIKAN 3] Mengarahkan pengguna ke dashboard setelah berhasil
        return redirect()->route('user.dashboard');
    }
}
