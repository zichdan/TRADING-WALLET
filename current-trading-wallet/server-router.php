<?php
// Router for PHP built-in server - serves static files directly, routes everything else to app
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve static files directly
$filePath = __DIR__ . $uri;
if ($uri !== '/' && file_exists($filePath) && is_file($filePath)) {
    // Set proper MIME types
    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'webp' => 'image/webp',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'eot' => 'application/vnd.ms-fontobject',
        'otf' => 'font/otf',
        'map' => 'application/json',
        'json' => 'application/json',
        'txt' => 'text/plain',
        'xml' => 'application/xml',
        'html' => 'text/html',
        'mp4' => 'video/mp4',
        'webm' => 'video/webm',
    ];
    
    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
    }
    
    // Disable caching for development
    header('Cache-Control: no-cache, must-revalidate');
    
    readfile($filePath);
    return true;
}

// Route everything else to the Laravel application
require __DIR__ . '/index.php';
