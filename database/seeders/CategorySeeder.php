<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Pemerintahan',
            'November',
            'Hukum dan Undang-Undang',
            'Motivasi',
            'Politik',
            'Sosial',
            'Demokrasi',
            'Keagamaan',
            'Sengketa Pemilu',
            'Riset Pilkada',
            'Akuntansi',
            'Skripsi',
            'Laporan Hasil Pengawasan',
            'Lainnya',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
