<?php
// Simple router for PHP built-in server to serve static assets correctly
// and forward everything else to index.php.

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');

// Public asset directories that should be served directly
$staticPrefixes = [
    '/assets/',
    '/core/assets/',
    '/storage/',
    '/core/storage/',
    '/vendor/',
    '/favicon.ico',
    '/robots.txt',
];

foreach ($staticPrefixes as $prefix) {
    if (strpos($uri, $prefix) === 0) {
        return false; // serve as static file
    }
}

// If request targets an existing file, let the server handle it
$requested = __DIR__ . DIRECTORY_SEPARATOR . ltrim($uri, '/\\');
if (is_file($requested)) {
    return false;
}

require __DIR__ . '/index.php';




