<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$routes = app('router')->getRoutes();
$found = [];

foreach ($routes as $route) {
    $uri = $route->uri();
    if (str_contains($uri, 'bulk') || str_contains($uri, 'books')) {
        $found[] = [
            'method' => implode('|', $route->methods()),
            'uri'    => '/' . $uri,
            'name'   => $route->getName() ?? '-',
        ];
    }
}

echo '<h2>Routes yang mengandung "books" atau "bulk":</h2>';
echo '<table border="1" cellpadding="8" style="border-collapse:collapse; font-family:monospace;">';
echo '<tr><th>Method</th><th>URI</th><th>Name</th></tr>';
foreach ($found as $r) {
    $highlight = str_contains($r['uri'], 'bulk') ? 'background:yellow;' : '';
    echo "<tr style='{$highlight}'><td>{$r['method']}</td><td>{$r['uri']}</td><td>{$r['name']}</td></tr>";
}
echo '</table>';

if (empty($found)) {
    echo '<p style="color:red;"><strong>❌ Tidak ada route books/bulk yang terdaftar!</strong></p>';
}

echo '<p style="color:red; margin-top:20px;"><strong>⚠️ HAPUS file ini setelah digunakan!</strong></p>';
