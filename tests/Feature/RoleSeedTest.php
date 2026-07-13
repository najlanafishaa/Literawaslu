<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleSeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_accounts_use_new_roles_and_password_123(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $superAdmin = \App\Models\User::where('email', 'admin@literawaslu.com')->first();
        $admin = \App\Models\User::where('email', 'petugas@literawaslu.com')->first();
        $user = \App\Models\User::where('email', 'ahmad@literawaslu.com')->first();

        $this->assertNotNull($superAdmin);
        $this->assertSame('super_admin', $superAdmin->role);
        $this->assertTrue(Hash::check('123', $superAdmin->password));

        $this->assertNotNull($admin);
        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check('123', $admin->password));

        $this->assertNotNull($user);
        $this->assertSame('user', $user->role);
        $this->assertTrue(Hash::check('123', $user->password));
    }
}
