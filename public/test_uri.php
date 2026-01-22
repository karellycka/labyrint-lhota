<?php
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
echo "Raw REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "Parsed requestUri: " . $requestUri . "\n";

$basePath = '/labyrint';
if (strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}

echo "After basePath removal: " . $requestUri . "\n";
echo "Starts with /admin? " . (strpos($requestUri, '/admin') === 0 ? 'YES' : 'NO') . "\n";
