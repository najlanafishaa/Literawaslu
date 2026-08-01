<?php
// Clear compiled view cache
$viewFiles = glob(__DIR__ . '/storage/framework/views/*.php');
$cleared = 0;
if ($viewFiles) {
    foreach ($viewFiles as $file) {
        if (is_file($file)) {
            unlink($file);
            $cleared++;
        }
    }
}

// Clear bootstrap cache
$cacheFiles = [
    __DIR__ . '/bootstrap/cache/config.php',
    __DIR__ . '/bootstrap/cache/routes-v7.php',
    __DIR__ . '/bootstrap/cache/packages.php',
    __DIR__ . '/bootstrap/cache/services.php',
    __DIR__ . '/bootstrap/cache/events.php',
];
foreach ($cacheFiles as $file) {
    if (file_exists($file)) {
        unlink($file);
    }
}

echo "<h2>✅ Cache Cleared!</h2>";
echo "<p>Deleted {$cleared} compiled view files.</p>";
echo "<p>Bootstrap cache cleared.</p>";
echo "<p style='color:red'><strong>⚠️ HAPUS file clear.php ini setelah digunakan!</strong></p>";
