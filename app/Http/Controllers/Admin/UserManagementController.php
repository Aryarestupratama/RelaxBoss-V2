<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    /**
     * Menampilkan halaman utama manajemen user dengan tabulasi.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Ambil tab aktif dari URL, defaultnya 'all'
        $activeTab = $request->query('tab', 'all');

        // Filter query berdasarkan tab yang aktif
        if ($activeTab !== 'all') {
            $query->where('role', $activeTab);
        }

        $users = $query->latest()->paginate(10);

        return view('admin.users.index', compact('users', 'activeTab'));
    }

    /**
     * Menyimpan user baru yang dibuat dari modal.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:user,admin,psikolog'],
        ]);

        User::create([
            'name' => $request->name,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    /**
     * Mengupdate data user yang diedit dari modal.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'in:user,admin,psikolog'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->name = $request->name;
        $user->full_name = $request->full_name;
        $user->email = $request->email;
        $user->role = $request->role;

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Menghapus user.
     */
    public function destroy(User $user)
    {
        // Tambahkan proteksi agar tidak bisa menghapus diri sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
