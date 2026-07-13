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
<<<<<<< HEAD
        // 1. Create Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@literawaslu.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'phone' => '0811000000',
            'security_question' => 'Apa nama hewan favorit Anda?',
            'security_answer' => 'kucing',
        ]);

        // 2. Create Regular Admin (Petugas)
        User::create([
            'name' => 'Petugas Perpus',
            'email' => 'petugas@literawaslu.com',
            'password' => Hash::make('password'),
            'role' => 'petugas',
            'phone' => '0812000000',
            'security_question' => 'Siapa nama hewan peliharaan Anda?',
            'security_answer' => 'doggy',
        ]);

        // 3. Create Members
        $user1 = User::create([
            'name' => 'Ahmad Yani',
            'email' => 'ahmad@literawaslu.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'phone' => '0813000000',
            'security_question' => 'Siapa nama hewan peliharaan Anda?',
            'security_answer' => 'mimi',
        ]);
        $member1 = Member::create([
            'user_id' => $user1->id,
            'member_code' => 'MEM-001',
            'total_loans' => 4,
            'points' => 40,
            'borrow_limit' => 1,
        ]);

        $user2 = User::create([
            'name' => 'Budi Sudarsono',
            'email' => 'budi@literawaslu.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'phone' => '0814000000',
            'security_question' => 'Siapa nama hewan peliharaan Anda?',
            'security_answer' => 'mimi',
        ]);
        $member2 = Member::create([
            'user_id' => $user2->id,
            'member_code' => 'MEM-002',
            'total_loans' => 1,
            'points' => 10,
            'borrow_limit' => 1,
        ]);

        $user3 = User::create([
            'name' => 'Citra Lestari',
            'email' => 'citra@literawaslu.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'phone' => '0815000000',
            'security_question' => 'Siapa nama hewan peliharaan Anda?',
            'security_answer' => 'mimi',
        ]);
        $member3 = Member::create([
            'user_id' => $user3->id,
            'member_code' => 'MEM-003',
            'total_loans' => 0,
            'points' => 0,
            'borrow_limit' => 1,
        ]);
=======
        // 1. Create or update Super Admin
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@literawaslu.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('123'),
                'role' => 'super_admin',
            ]
        );

        // 2. Create or update Admin
        $admin = User::updateOrCreate(
            ['email' => 'petugas@literawaslu.com'],
            [
                'name' => 'Admin Perpus',
                'password' => Hash::make('123'),
                'role' => 'admin',
            ]
        );

        // 3. Create or update Users and member profiles
        $user1 = User::updateOrCreate(
            ['email' => 'ahmad@literawaslu.com'],
            [
                'name' => 'Ahmad Yani',
                'password' => Hash::make('123'),
                'role' => 'user',
            ]
        );
        $user1->member()->firstOrCreate(
            ['user_id' => $user1->id],
            [
                'member_code' => 'MEM-100001',
                'total_loans' => 4,
                'points' => 40,
                'borrow_limit' => 1,
                'is_verified' => true,
            ]
        );

        $user2 = User::updateOrCreate(
            ['email' => 'budi@literawaslu.com'],
            [
                'name' => 'Budi Sudarsono',
                'password' => Hash::make('123'),
                'role' => 'user',
            ]
        );
        $user2->member()->firstOrCreate(
            ['user_id' => $user2->id],
            [
                'member_code' => 'MEM-100002',
                'total_loans' => 1,
                'points' => 10,
                'borrow_limit' => 1,
                'is_verified' => true,
            ]
        );

        $user3 = User::updateOrCreate(
            ['email' => 'citra@literawaslu.com'],
            [
                'name' => 'Citra Lestari',
                'password' => Hash::make('123'),
                'role' => 'user',
            ]
        );
        $user3->member()->firstOrCreate(
            ['user_id' => $user3->id],
            [
                'member_code' => 'MEM-100003',
                'total_loans' => 0,
                'points' => 0,
                'borrow_limit' => 1,
                'is_verified' => true,
            ]
        );
>>>>>>> origin/pr-1

        // 4. Create or update Books
        $book1 = Book::updateOrCreate(
            ['barcode' => '9786020333175'],
            [
                'title' => 'Laskar Pelangi',
                'author' => 'Andrea Hirata',
                'publisher' => 'Bentang Pustaka',
                'year' => 2005,
                'category' => 'Fiksi',
                'stock' => 5,
                'available_stock' => 5,
                'is_available' => true,
            ]
        );

        $book2 = Book::updateOrCreate(
            ['barcode' => '9786020523315'],
            [
                'title' => 'Bumi Manusia',
                'author' => 'Pramoedya Ananta Toer',
                'publisher' => 'Lentera Dipantara',
                'year' => 1980,
                'category' => 'Sejarah',
                'stock' => 3,
                'available_stock' => 3,
                'is_available' => true,
            ]
        );

        $book3 = Book::updateOrCreate(
            ['barcode' => '9789792238419'],
            [
                'title' => 'Perahu Kertas',
                'author' => 'Dee Lestari',
                'publisher' => 'Bentang Pustaka',
                'year' => 2009,
                'category' => 'Romantis',
                'stock' => 4,
                'available_stock' => 3,
                'is_available' => true,
            ]
        );

        $book4 = Book::updateOrCreate(
            ['barcode' => '9786020822341'],
            [
                'title' => 'Pulang',
                'author' => 'Tere Liye',
                'publisher' => 'Republika',
                'year' => 2015,
                'category' => 'Fiksi',
                'stock' => 5,
                'available_stock' => 5,
                'is_available' => true,
            ]
        );

        $book5 = Book::updateOrCreate(
            ['barcode' => '9786022207321'],
            [
                'title' => 'Negeri 5 Menara',
                'author' => 'Ahmad Fuadi',
                'publisher' => 'Gramedia Pustaka Utama',
                'year' => 2009,
                'category' => 'Inspiratif',
                'stock' => 2,
                'available_stock' => 2,
                'is_available' => true,
            ]
        );

        $book6 = Book::updateOrCreate(
            ['barcode' => '9786020633123'],
            [
                'title' => 'Filosofi Teras',
                'author' => 'Henry Manampiring',
                'publisher' => 'Kompas',
                'year' => 2018,
                'category' => 'Self-Help',
                'stock' => 3,
                'available_stock' => 3,
                'is_available' => true,
            ]
        );

        // 5. Create or update Borrow Transactions
        Borrow::firstOrCreate(
            [
                'member_id' => $user1->member->id,
                'book_id' => $book3->id,
                'borrow_date' => '2026-06-25',
            ],
            [
                'due_date' => '2026-07-02',
                'return_date' => null,
                'status' => 'borrowed',
            ]
        );

        Borrow::firstOrCreate(
            [
                'member_id' => $user1->member->id,
                'book_id' => $book1->id,
                'borrow_date' => '2026-06-10',
            ],
            [
                'due_date' => '2026-06-17',
                'return_date' => '2026-06-15',
                'status' => 'returned',
            ]
        );

        Borrow::firstOrCreate(
            [
                'member_id' => $user2->member->id,
                'book_id' => $book2->id,
                'borrow_date' => '2026-06-18',
            ],
            [
                'due_date' => '2026-06-25',
                'return_date' => '2026-06-26',
                'status' => 'returned',
            ]
        );

        Borrow::firstOrCreate(
            [
                'member_id' => $user1->member->id,
                'book_id' => $book4->id,
                'borrow_date' => '2026-05-01',
            ],
            [
                'due_date' => '2026-05-08',
                'return_date' => '2026-05-07',
                'status' => 'returned',
            ]
        );

        Borrow::firstOrCreate(
            [
                'member_id' => $user1->member->id,
                'book_id' => $book5->id,
                'borrow_date' => '2026-05-15',
            ],
            [
                'due_date' => '2026-05-22',
                'return_date' => '2026-05-20',
                'status' => 'returned',
            ]
        );
    }
}
