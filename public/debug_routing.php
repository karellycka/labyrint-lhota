<?php
// Debug routing - shows what Router receives

session_start();

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
echo "Original REQUEST_URI: {$_SERVER['REQUEST_URI']}\n";
echo "Parsed PATH: {$requestUri}\n\n";

// Simulate index.php processing
$basePath = '/labyrint';
if (strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
    echo "After removing basePath: {$requestUri}\n";
}

if (empty($requestUri) || $requestUri[0] !== '/') {
    $requestUri = '/' . $requestUri;
    echo "After ensuring /: {$requestUri}\n";
}

echo "\nAdmin check: " . (strpos($requestUri, '/admin') === 0 ? 'YES' : 'NO') . "\n";
echo "Method: {$_SERVER['REQUEST_METHOD']}\n";

// Test pattern matching
function testPattern($pattern, $uri) {
    $pattern = preg_replace('#:([a-zA-Z0-9_]+)#', '([^/]+)', $pattern);
    $pattern = str_replace('/', '\\/', $pattern);
    $regex = "#^{$pattern}$#";

    $match = preg_match($regex, $uri);
    echo "\nPattern: {$pattern}\n";
    echo "URI: {$uri}\n";
    echo "Regex: {$regex}\n";
    echo "Match: " . ($match ? 'YES' : 'NO') . "\n";
}

echo "\n=== Pattern Tests ===\n";
testPattern('/admin', '/admin');
testPattern('/admin/blog', '/admin/blog');
testPattern('/admin/blog/:id/edit', '/admin/blog/5/edit');
