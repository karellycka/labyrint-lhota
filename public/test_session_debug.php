<?php
// Debug session for admin
session_start();

header('Content-Type: application/json');

echo json_encode([
    'session_id' => session_id(),
    'session_data' => $_SESSION,
    'is_logged_in' => isset($_SESSION['user_id']),
    'user_id' => $_SESSION['user_id'] ?? null,
    'username' => $_SESSION['username'] ?? null,
    'cookies' => $_COOKIE
], JSON_PRETTY_PRINT);
