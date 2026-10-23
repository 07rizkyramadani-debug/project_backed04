<?php

namespace Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Auth\Requests\UpdateUserRoleRequest;

class UserManagementController extends Controller
{
    /**
     * Daftar semua user (khusus admin).
     */
    public function index(): View
    {
        $users = User::orderBy('name')->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Ubah role user lain. Admin tidak boleh mengubah role dirinya sendiri
     * agar tidak terkunci dari sistem.
     */
    public function updateRole(UpdateUserRoleRequest $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->with('error', 'Anda tidak dapat mengubah role akun sendiri.');
        }

        $user->update(['role' => $request->validated('role')]);

        return back()->with('status', "Role {$user->name} diubah menjadi {$user->role}.");
    }

    /**
     * Hapus user lain. Admin tidak boleh menghapus akunnya sendiri.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return back()->with('status', "User {$user->name} berhasil dihapus.");
    }
}
