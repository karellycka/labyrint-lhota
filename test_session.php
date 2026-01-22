<?php
session_start();

echo "Session ID: " . session_id() . "\n";
echo "Session data: " . print_r($_SESSION, true) . "\n";
echo "Is logged in: " . (isset($_SESSION['user_id']) ? 'YES (ID: ' . $_SESSION['user_id'] . ')' : 'NO') . "\n";
