<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
use App\Models\Member;
use App\Models\Borrow;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@literawaslu.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'security_question' => 'Apa nama hewan favorit Anda?',
            'security_answer' => 'kucing',
        ]);

        // 2. Create Regular Admin (Petugas)
        User::create([
            'name' => 'Petugas Perpus',
            'email' => 'petugas@literawaslu.com',
            'password' => Hash::make('password'),
            'role' => 'petugas',
            'security_question' => 'Siapa nama hewan peliharaan Anda?',
            'security_answer' => 'doggy',
        ]);

        // Data dummy buku, member, dan transaksi dihapus sesuai permintaan

    }
}
