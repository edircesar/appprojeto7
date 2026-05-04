<?php
// router.php for PHP built-in server
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$ext = pathinfo($path, PATHINFO_EXTENSION);

if ($path !== '/') {
    // If there is no extension, check if a .php file exists
    if ($ext === '') {
        $file = __DIR__ . $path . '.php';
        if (file_exists($file)) {
            require $file;
            return true;
        }
    }
}
return false; // Let PHP built-in server handle it (e.g. index.php, images, css)
