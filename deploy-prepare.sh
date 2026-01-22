#!/bin/bash

##################################################
# Deployment Preparation Script
# Připraví všechny soubory pro nahrání na InfinityFree
##################################################

set -e  # Exit on error

echo "🚀 Příprava deployment balíčku pro InfinityFree..."
echo ""

# Cleanup old deployment
if [ -d "DEPLOY_PACKAGE" ]; then
    echo "🗑️  Mažu starý deployment balíček..."
    rm -rf DEPLOY_PACKAGE
fi

# Create deployment structure
echo "📁 Vytvářím strukturu..."
mkdir -p DEPLOY_PACKAGE/htdocs
mkdir -p DEPLOY_PACKAGE/root/app
mkdir -p DEPLOY_PACKAGE/root/config
mkdir -p DEPLOY_PACKAGE/root/database
mkdir -p DEPLOY_PACKAGE/root/storage/cache
mkdir -p DEPLOY_PACKAGE/root/storage/logs
mkdir -p DEPLOY_PACKAGE/root/storage/sessions

# Copy htdocs files (from public/)
echo "📦 Kopíruji htdocs (public)..."
cp public/index.php DEPLOY_PACKAGE/htdocs/
cp -r public/assets DEPLOY_PACKAGE/htdocs/
cp -r public/uploads DEPLOY_PACKAGE/htdocs/

# Copy production .htaccess
echo "🔧 Kopíruji production .htaccess..."
cp public/.htaccess.production DEPLOY_PACKAGE/htdocs/.htaccess

# Copy app/
echo "📦 Kopíruji app/..."
cp -r app/* DEPLOY_PACKAGE/root/app/

# Copy config/
echo "📦 Kopíruji config/..."
cp config/config.php DEPLOY_PACKAGE/root/config/
cp config/database.php DEPLOY_PACKAGE/root/config/
cp config/routes.php DEPLOY_PACKAGE/root/config/
cp config/config.production.example.php DEPLOY_PACKAGE/root/config/

# Copy database/
echo "📦 Kopíruji database/..."
cp -r database/* DEPLOY_PACKAGE/root/database/

# Copy storage/ structure
echo "📦 Kopíruji storage/..."
touch DEPLOY_PACKAGE/root/storage/cache/.gitkeep
touch DEPLOY_PACKAGE/root/storage/logs/.gitkeep
touch DEPLOY_PACKAGE/root/storage/sessions/.gitkeep

# Create config.local.php template
echo "📝 Vytvářím config.local.php šablonu..."
cat > DEPLOY_PACKAGE/root/config/config.local.php << 'EOL'
<?php

/**
 * Production Configuration for InfinityFree
 *
 * INSTRUKCE: Vyplňte skutečné hodnoty z InfinityFree!
 */

return [
    // Environment - PONECHTE production!
    'ENVIRONMENT' => 'production',

    // Base URL - ZMĚŇTE na vaši doménu!
    'BASE_URL' => 'https://labyrint.ct.ws',

    // Database - VYPLŇTE údaje z InfinityFree cPanel > MySQL Databases
    'DB_HOST' => 'sql212.infinityfree.com',
    'DB_PORT' => '3306',
    'DB_NAME' => 'if0_40970521_labyrint',
    'DB_USER' => 'if0_40970521',
    'DB_PASS' => 'fwYvUlMkZQ2Hi',
];
EOL

# Create deployment instructions
echo "📄 Vytvářím DEPLOY_INSTRUCTIONS.txt..."
cat > DEPLOY_PACKAGE/DEPLOY_INSTRUCTIONS.txt << 'EOL'
╔════════════════════════════════════════════════════════════╗
║  DEPLOYMENT INSTRUCTIONS - InfinityFree                    ║
╚════════════════════════════════════════════════════════════╝

Tento balíček obsahuje 2 složky připravené k nahrání:

📁 htdocs/     → Nahrajte do /htdocs/ na serveru
📁 root/       → Nahrajte do / (root) na serveru


═══════════════════════════════════════════════════════════
 KROK 1: Připojte se k FTP
═══════════════════════════════════════════════════════════

Host: ftpupload.net
User: [vaše FTP username z InfinityFree]
Pass: [vaše FTP heslo]
Port: 21


═══════════════════════════════════════════════════════════
 KROK 2: Nahrajte složku htdocs/
═══════════════════════════════════════════════════════════

1. V FTP klientovi přejděte do /htdocs/
2. Nahrajte OBSAH složky DEPLOY_PACKAGE/htdocs/:
   - index.php
   - .htaccess
   - assets/
   - uploads/

⚠️  PŘEPIŠTE existující soubory pokud existují!


═══════════════════════════════════════════════════════════
 KROK 3: Nahrajte složku root/
═══════════════════════════════════════════════════════════

1. V FTP klientovi přejděte do / (root, jeden level NAD htdocs)
2. Nahrajte OBSAH složky DEPLOY_PACKAGE/root/:
   - app/
   - config/
   - database/
   - storage/

Finální struktura na serveru:
/
├── htdocs/
│   ├── index.php
│   ├── .htaccess
│   ├── assets/
│   └── uploads/
├── app/
├── config/
│   └── config.local.php  ← ZKONTROLUJTE!
├── database/
└── storage/


═══════════════════════════════════════════════════════════
 KROK 4: Zkontrolujte config.local.php
═══════════════════════════════════════════════════════════

1. V FTP otevřete /config/config.local.php
2. Zkontrolujte, že obsahuje SPRÁVNÉ údaje:
   ✓ BASE_URL = https://labyrint.ct.ws
   ✓ DB credentials z InfinityFree


═══════════════════════════════════════════════════════════
 KROK 5: Import databáze
═══════════════════════════════════════════════════════════

1. InfinityFree cPanel > phpMyAdmin
2. Vyberte vaši databázi
3. Import > database_export_infinityfree.sql (v kořenu projektu)
4. Spusťte import


═══════════════════════════════════════════════════════════
 KROK 6: Nastavte permissions
═══════════════════════════════════════════════════════════

V FTP nastavte:
- /htdocs/uploads/     → 755 nebo 777
- /storage/            → 755
- /storage/logs/       → 755
- /storage/cache/      → 755


═══════════════════════════════════════════════════════════
 KROK 7: Test webu
═══════════════════════════════════════════════════════════

Otevřete: https://labyrint.ct.ws

✅ Měli byste vidět homepage!


═══════════════════════════════════════════════════════════
 HOTOVO! 🎉
═══════════════════════════════════════════════════════════

EOL

# Create ZIP archive
echo "🗜️  Vytvářím ZIP archiv..."
cd DEPLOY_PACKAGE
zip -r ../labyrint-deployment.zip htdocs/ root/ DEPLOY_INSTRUCTIONS.txt -q
cd ..

echo ""
echo "✅ HOTOVO!"
echo ""
echo "📦 Vytvořené soubory:"
echo "   - DEPLOY_PACKAGE/          (složka s připravenými soubory)"
echo "   - labyrint-deployment.zip  (ZIP archiv)"
echo ""
echo "🚀 Další kroky:"
echo "   1. Otevřete DEPLOY_PACKAGE/DEPLOY_INSTRUCTIONS.txt"
echo "   2. Nebo použijte labyrint-deployment.zip"
echo "   3. Nahrajte podle instrukcí na InfinityFree"
echo ""
