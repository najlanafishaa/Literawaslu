<?php
echo "<h2>🔍 Laravel Deployment Diagnostics</h2>";
echo "<pre style='font-family:monospace; background:#111; color:#0f0; padding:20px; font-size:13px;'>";

$base = __DIR__;

$checks = [
    'resources/'              => is_dir($base . '/resources'),
    'resources/views/'        => is_dir($base . '/resources/views'),
    'resources/views/auth/'   => is_dir($base . '/resources/views/auth'),
    'resources/views/auth/login.blade.php' => file_exists($base . '/resources/views/auth/login.blade.php'),
    'vendor/'                 => is_dir($base . '/vendor'),
    'bootstrap/cache/'        => is_dir($base . '/bootstrap/cache'),
    'storage/framework/views/' => is_dir($base . '/storage/framework/views'),
    '.env'                    => file_exists($base . '/.env'),
];

echo "📁 File & Folder Checks:\n";
echo str_repeat('-', 50) . "\n";
foreach ($checks as $path => $exists) {
    $icon = $exists ? '✅' : '❌ MISSING';
    echo str_pad($path, 40) . " $icon\n";
}

echo "\n📁 Isi folder resources/ (jika ada):\n";
echo str_repeat('-', 50) . "\n";
if (is_dir($base . '/resources')) {
    $dirs = scandir($base . '/resources');
    foreach ($dirs as $d) {
        if ($d !== '.' && $d !== '..') {
            echo "  - resources/$d\n";
        }
    }
    if (is_dir($base . '/resources/views')) {
        $vdirs = scandir($base . '/resources/views');
        foreach ($vdirs as $d) {
            if ($d !== '.' && $d !== '..') {
                echo "    - resources/views/$d\n";
            }
        }
    }
} else {
    echo "  ❌ Folder resources/ TIDAK ADA!\n";
}

echo "\n📄 Storage writable check:\n";
echo str_repeat('-', 50) . "\n";
$storagePaths = [
    'storage/framework/views',
    'storage/framework/sessions',
    'storage/framework/cache',
    'storage/logs',
    'bootstrap/cache',
];
foreach ($storagePaths as $path) {
    $full = $base . '/' . $path;
    $w = is_writable($full) ? '✅ writable' : '❌ NOT writable';
    $e = is_dir($full) ? '' : ' (not exists)';
    echo str_pad($path, 40) . " $w$e\n";
}

echo "\n🔑 PHP Version: " . PHP_VERSION . "\n";
echo "</pre>";
