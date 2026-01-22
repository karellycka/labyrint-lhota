<?php
// Test routing logic

// Test convertPatternToRegex
function convertPatternToRegex(string $pattern): string
{
    $pattern = preg_replace('#:([a-zA-Z0-9_]+)#', '([^/]+)', $pattern);
    $pattern = str_replace('/', '\\/', $pattern);
    return "#^{$pattern}$#";
}

echo "Testing pattern conversion:\n";
echo "Pattern: /admin\n";
$regex = convertPatternToRegex('/admin');
echo "Regex: {$regex}\n";
echo "Match /admin: " . (preg_match($regex, '/admin') ? 'YES' : 'NO') . "\n";
echo "\n";

echo "Pattern: /admin/login\n";
$regex = convertPatternToRegex('/admin/login');
echo "Regex: {$regex}\n";
echo "Match /admin/login: " . (preg_match($regex, '/admin/login') ? 'YES' : 'NO') . "\n";
