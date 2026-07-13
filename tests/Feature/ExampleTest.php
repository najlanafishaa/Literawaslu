<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(302);
    }

    public function test_super_admin_can_access_history_reports_and_settings(): void
    {
        $admin = \App\Models\User::create([
            'name' => 'Super Admin Test',
            'email' => 'admintest@literawaslu.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'super_admin'
        ]);

        $response = $this->actingAs($admin)->get('/admin/borrows/history');
        $response->assertStatus(200);

        $response2 = $this->actingAs($admin)->get('/reports');
        $response2->assertStatus(200);

        $response3 = $this->actingAs($admin)->get('/admin/settings');
        $response3->assertStatus(200);
    }

    public function test_unverified_member_cannot_checkout_books(): void
    {
        $petugas = \App\Models\User::create([
            'name' => 'Petugas Test',
            'email' => 'petugastest@literawaslu.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'petugas'
        ]);

        $memberUser = \App\Models\User::create([
            'name' => 'Unverified Member',
            'email' => 'unverified@literawaslu.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'member'
        ]);

        $member = \App\Models\Member::create([
            'user_id' => $memberUser->id,
            'member_code' => 'MEM-TEST01',
            'total_loans' => 0,
            'points' => 0,
            'borrow_limit' => 1,
            'is_verified' => false
        ]);

        $book = \App\Models\Book::create([
            'barcode' => 'BAR-TEST01',
            'title' => 'Test Book',
            'author' => 'Test Author',
            'publisher' => 'Test Publisher',
            'year' => 2026,
            'category' => 'Test Category',
            'stock' => 1,
            'available_stock' => 1,
            'is_available' => true
        ]);

        $response = $this->actingAs($petugas)->post('/borrows/checkout', [
            'member_code' => 'MEM-TEST01',
            'barcode' => 'BAR-TEST01'
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('borrows', [
            'member_id' => $member->id,
            'book_id' => $book->id
        ]);
    }

    public function test_verified_member_can_checkout_books(): void
    {
        $petugas = \App\Models\User::create([
            'name' => 'Petugas Test',
            'email' => 'petugastest@literawaslu.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'petugas'
        ]);

        $memberUser = \App\Models\User::create([
            'name' => 'Verified Member',
            'email' => 'verified@literawaslu.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'member'
        ]);

        $member = \App\Models\Member::create([
            'user_id' => $memberUser->id,
            'member_code' => 'MEM-TEST02',
            'total_loans' => 0,
            'points' => 0,
            'borrow_limit' => 1,
            'is_verified' => true
        ]);

        $book = \App\Models\Book::create([
            'barcode' => 'BAR-TEST02',
            'title' => 'Test Book 2',
            'author' => 'Test Author 2',
            'publisher' => 'Test Publisher 2',
            'year' => 2026,
            'category' => 'Test Category 2',
            'stock' => 1,
            'available_stock' => 1,
            'is_available' => true
        ]);

        $response = $this->actingAs($petugas)->post('/borrows/checkout', [
            'member_code' => 'MEM-TEST02',
            'barcode' => 'BAR-TEST02'
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('borrows', [
            'member_id' => $member->id,
            'book_id' => $book->id,
            'status' => 'borrowed'
        ]);
    }

    public function test_user_can_view_and_edit_profile()
    {
        $user = \App\Models\User::create([
            'name' => 'Original Name',
            'email' => 'original@gmail.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'member'
        ]);

        $response = $this->actingAs($user)->get('/profile');
        $response->assertStatus(200);
        $response->assertSee('Original Name');

        $file = \Illuminate\Http\UploadedFile::fake()->create('avatar.png', 100);

        $response = $this->actingAs($user)->post('/profile', [
            'name' => 'Updated Name',
            'email' => 'updated@gmail.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
            'avatar' => $file
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@gmail.com'
        ]);
        
        $updatedUser = $user->fresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newpassword', $updatedUser->password));
        $this->assertNotNull($updatedUser->avatar);
        $this->assertTrue(file_exists(public_path($updatedUser->avatar)));

        // Clean up
        @unlink(public_path($updatedUser->avatar));
    }

    public function test_super_admin_can_manually_register_member()
    {
        $superAdmin = \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'sa@literawaslu.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'super_admin'
        ]);

        $response = $this->actingAs($superAdmin)->post('/admin/members', [
            'name' => 'Manual Member',
            'email' => 'manualmember@gmail.com',
            'password' => 'password'
        ]);

        $response->assertRedirect(route('members.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Manual Member',
            'email' => 'manualmember@gmail.com',
            'role' => 'member'
        ]);

        $user = \App\Models\User::where('email', 'manualmember@gmail.com')->first();
        $this->assertDatabaseHas('members', [
            'user_id' => $user->id,
            'is_verified' => true
        ]);
    }

    public function test_super_admin_can_create_officer_and_super_admin()
    {
        $superAdmin = \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'sa2@literawaslu.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'super_admin'
        ]);

        // Create Petugas
        $response = $this->actingAs($superAdmin)->post('/admin/officers', [
            'name' => 'New Petugas',
            'email' => 'newpetugas@literawaslu.com',
            'password' => 'password',
            'role' => 'petugas'
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'name' => 'New Petugas',
            'email' => 'newpetugas@literawaslu.com',
            'role' => 'petugas'
        ]);

        // Create Super Admin
        $response = $this->actingAs($superAdmin)->post('/admin/officers', [
            'name' => 'New Super Admin',
            'email' => 'newsa@literawaslu.com',
            'password' => 'password',
            'role' => 'super_admin'
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'name' => 'New Super Admin',
            'email' => 'newsa@literawaslu.com',
            'role' => 'super_admin'
        ]);
    }

    public function test_super_admin_can_demote_super_admin_to_officer()
    {
        $superAdmin1 = \App\Models\User::create([
            'name' => 'SA One',
            'email' => 'saone@literawaslu.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'super_admin'
        ]);

        $superAdmin2 = \App\Models\User::create([
            'name' => 'SA Two',
            'email' => 'satwo@literawaslu.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'super_admin'
        ]);

        // SA One demotes SA Two to petugas
        $response = $this->actingAs($superAdmin1)->put("/admin/officers/{$superAdmin2->id}", [
            'name' => 'SA Two Updated',
            'email' => 'satwo@literawaslu.com',
            'role' => 'petugas'
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('petugas', $superAdmin2->fresh()->role);
        $this->assertEquals('SA Two Updated', $superAdmin2->fresh()->name);
    }

    public function test_unverified_member_redirected_to_unverified_page()
    {
        $user = \App\Models\User::create([
            'name' => 'Unverified Member',
            'email' => 'unverified@gmail.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'member'
        ]);

        $member = \App\Models\Member::create([
            'user_id' => $user->id,
            'member_code' => 'MEM-999999',
            'is_verified' => false
        ]);

        // Attempting to visit dashboard redirects to /unverified
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertRedirect(route('unverified'));

        // Can access /unverified page
        $response = $this->actingAs($user)->get('/unverified');
        $response->assertStatus(200);
        $response->assertSee('Menunggu Verifikasi Akun');
    }

    public function test_verified_member_can_access_dashboard()
    {
        $user = \App\Models\User::create([
            'name' => 'Verified Member',
            'email' => 'verifiedmember@gmail.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'member'
        ]);

        $member = \App\Models\Member::create([
            'user_id' => $user->id,
            'member_code' => 'MEM-888888',
            'is_verified' => true
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);

        // Accessing /unverified redirects to /dashboard
        $response = $this->actingAs($user)->get('/unverified');
        $response->assertRedirect(route('dashboard'));
    }

    public function test_admin_dashboard_shows_unverified_members()
    {
        $superAdmin = \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'sa_dash@literawaslu.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'super_admin'
        ]);

        $unverifiedUser = \App\Models\User::create([
            'name' => 'John Doe',
            'email' => 'johndoe@gmail.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'member'
        ]);

        $member = \App\Models\Member::create([
            'user_id' => $unverifiedUser->id,
            'member_code' => 'MEM-777777',
            'is_verified' => false
        ]);

        $response = $this->actingAs($superAdmin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Anggota Menunggu Verifikasi');
        $response->assertSee('John Doe');
        $response->assertSee('MEM-777777');
    }

    public function test_admin_can_reject_unverified_member()
    {
        $superAdmin = \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'sa_reject@literawaslu.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'super_admin'
        ]);

        $unverifiedUser = \App\Models\User::create([
            'name' => 'Jane Smith',
            'email' => 'janesmith@gmail.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'member'
        ]);

        $member = \App\Models\Member::create([
            'user_id' => $unverifiedUser->id,
            'member_code' => 'MEM-666666',
            'is_verified' => false
        ]);

        $response = $this->actingAs($superAdmin)->post("/admin/members/{$member->id}/reject");
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $unverifiedUser->id]);
        $this->assertDatabaseMissing('members', ['id' => $member->id]);
    }
}
