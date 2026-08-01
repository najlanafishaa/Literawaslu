<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Category;

$defaultCategories = [
    'Pemerintahan',
    'Novel',
    'Hukum Dan Undang-undang',
    'Motivasi',
    'Politik',
    'Sosial',
    'Demokrasi',
    'Keagamaan',
    'Sengketa Pemilu',
    'Riset Pilkada',
    'Akuntansi',
    'Skripsi',
    'Laporan Hasil Pengawasan'
];

echo "<h2>Memasukkan Kategori Default ke Database...</h2>";
echo "<pre style='background:#111; color:#0f0; padding:20px;'>";

foreach ($defaultCategories as $cat) {
    $existing = Category::where('name', $cat)->first();
    if (!$existing) {
        Category::create(['name' => $cat]);
        echo "✅ Ditambahkan: " . $cat . "\n";
    } else {
        echo "⚡ Sudah ada: " . $cat . "\n";
    }
}

echo "\nSELESAI! Semua kategori sudah masuk ke database (bisa dihapus via admin nanti).";
echo "</pre>";
echo "<p style='color:red'><strong>⚠️ JANGAN LUPA HAPUS file setup_categories.php ini setelah berhasil!</strong></p>";
