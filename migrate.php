<?php
// Load autoloader dan app
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

// Boot aplikasi untuk bisa pakai artisan
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<h2>🚀 Menjalankan Database Migrations...</h2>";
echo "<pre style='background:#111; color:#0f0; padding:20px;'>";

try {
    // Jalankan php artisan migrate --force
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    echo \Illuminate\Support\Facades\Artisan::output();
    echo "\n\n✅ MIGRATION SELESAI!\n";
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage();
}

echo "</pre>";
echo "<p style='color:red'><strong>⚠️ JANGAN LUPA HAPUS file migrate.php ini setelah berhasil!</strong></p>";
