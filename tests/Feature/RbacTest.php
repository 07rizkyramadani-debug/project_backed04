<?php

use App\Models\User;

test('guest diarahkan ke login saat membuka dashboard admin dan user', function () {
    $this->get('/admin/dashboard')->assertRedirect('/login');
    $this->get('/user/dashboard')->assertRedirect('/login');
});

test('register membuat user dengan role default user', function () {
    $this->post('/register', [
        'name' => 'Mahasiswa',
        'email' => 'mhs@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect();

    expect(User::where('email', 'mhs@example.com')->first()->role)->toBe('user');
});

test('user biasa bisa membuka user dashboard', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)->get('/user/dashboard')->assertOk();
});

test('user biasa mendapat 403 di admin dashboard', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)->get('/admin/dashboard')->assertForbidden();
});

test('admin bisa membuka admin dashboard', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get('/admin/dashboard')
        ->assertOk()
        ->assertSee('Statistik Pengguna');
});

test('helper isAdmin dan isUser bekerja', function () {
    expect(User::factory()->make(['role' => 'admin'])->isAdmin())->toBeTrue()
        ->and(User::factory()->make(['role' => 'user'])->isUser())->toBeTrue();
});

// ---------- Nilai plus: manajemen user ----------

test('admin bisa melihat daftar semua user', function () {
    $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Utama']);
    User::factory()->create(['role' => 'user', 'name' => 'Budi Santoso']);

    $this->actingAs($admin)
        ->get('/admin/users')
        ->assertOk()
        ->assertSee('Admin Utama')
        ->assertSee('Budi Santoso');
});

test('user biasa mendapat 403 di halaman kelola user', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)->get('/admin/users')->assertForbidden();
});

test('admin bisa mengubah role user lain', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $target = User::factory()->create(['role' => 'user']);

    $this->actingAs($admin)
        ->patch("/admin/users/{$target->id}/role", ['role' => 'admin'])
        ->assertSessionHas('status');

    expect($target->fresh()->role)->toBe('admin');
});

test('role yang tidak valid ditolak', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $target = User::factory()->create(['role' => 'user']);

    $this->actingAs($admin)
        ->patch("/admin/users/{$target->id}/role", ['role' => 'superadmin'])
        ->assertSessionHasErrors('role');

    expect($target->fresh()->role)->toBe('user');
});

test('admin tidak bisa mengubah role dirinya sendiri', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->patch("/admin/users/{$admin->id}/role", ['role' => 'user'])
        ->assertSessionHas('error');

    expect($admin->fresh()->role)->toBe('admin');
});

test('user biasa tidak bisa mengubah role', function () {
    $user = User::factory()->create(['role' => 'user']);
    $other = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->patch("/admin/users/{$other->id}/role", ['role' => 'admin'])
        ->assertForbidden();

    expect($other->fresh()->role)->toBe('user');
});

test('admin bisa menghapus user lain', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $target = User::factory()->create(['role' => 'user']);

    $this->actingAs($admin)
        ->delete("/admin/users/{$target->id}")
        ->assertSessionHas('status');

    $this->assertDatabaseMissing('users', ['id' => $target->id]);
});

test('admin tidak bisa menghapus dirinya sendiri', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->delete("/admin/users/{$admin->id}")
        ->assertSessionHas('error');

    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});

test('user bisa memperbarui profil sendiri', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->patch('/profile', ['name' => 'Nama Baru', 'email' => $user->email])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    expect($user->fresh()->name)->toBe('Nama Baru');
});
