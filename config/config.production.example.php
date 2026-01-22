<?php

/**
 * Production Configuration Example for InfinityFree
 *
 * INSTRUKCE:
 * 1. Zkopírujte tento soubor jako config.local.php
 * 2. Vyplňte skutečné hodnoty (DB credentials z InfinityFree)
 * 3. NIKDY necommitujte config.local.php do gitu
 */

return [
    // Environment
    'ENVIRONMENT' => 'production', // DŮLEŽITÉ: production pro produkci

    // Base URL - nahraďte vaší skutečnou doménou z InfinityFree
    'BASE_URL' => 'https://your-domain.infinityfreeapp.com',
    // nebo pokud máte vlastní doménu:
    // 'BASE_URL' => 'https://labyrint.cz',

    // Database - VYPLŇTE údaje z InfinityFree cPanel > MySQL Databases
    'DB_HOST' => 'sql123.infinityfree.com', // Hostname z InfinityFree
    'DB_PORT' => '3306', // Výchozí MySQL port
    'DB_NAME' => 'epiz_12345678_labyrint', // Název databáze z InfinityFree
    'DB_USER' => 'epiz_12345678', // Uživatelské jméno z InfinityFree
    'DB_PASS' => 'your-database-password', // Heslo databáze z InfinityFree
];
