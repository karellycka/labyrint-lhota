# 🚂 Deployment na Railway.app

**Vše na jednom místě:** Web hosting + MySQL databáze + Automatický deployment z Gitu!

---

## 🎯 Proč Railway.app?

✅ **$5 credit měsíčně zdarma** (stačí na menší web)
✅ **Git deploy** - push to main = auto deploy
✅ **MySQL included** - žádná externí databáze
✅ **Real-time logs** - vidíte co se děje
✅ **Automatic SSL** - HTTPS z boxu
✅ **Environment variables** - bezpečná konfigurace

---

## 📋 Co budete potřebovat:

- [ ] Účet na Railway.app (sign up přes GitHub)
- [ ] GitHub účet (pro Git repository)
- [ ] 10 minut času

---

## 🚀 Krok 1: Příprava Git repository

### 1.1 Inicializujte Git (pokud ještě nemáte)

```bash
cd /Users/karellycka/weby/labyrint
git init
git add .
git commit -m "Initial commit - Škola Labyrint"
```

### 1.2 Vytvořte GitHub repository

1. Jděte na https://github.com/new
2. Název: `labyrint-web` (nebo cokoliv)
3. Private nebo Public (doporučuji Private)
4. **NEVYTVÁŘEJTE** README, .gitignore (už máme)
5. Klikněte **"Create repository"**

### 1.3 Pushněte na GitHub

```bash
git remote add origin https://github.com/your-username/labyrint-web.git
git branch -M main
git push -u origin main
```

---

## 🏗️ Krok 2: Vytvoření projektu na Railway.app

### 2.1 Sign Up / Login

1. Jděte na https://railway.app
2. Klikněte **"Login"** → **"Login with GitHub"**
3. Autorizujte Railway přístup k GitHub

### 2.2 Nový projekt

1. Klikněte **"New Project"**
2. Vyberte **"Deploy from GitHub repo"**
3. Vyberte váš repository: `labyrint-web`
4. Railway začne build (chvíli počkejte)

---

## 🗄️ Krok 3: Přidání MySQL databáze

### 3.1 Přidat MySQL service

1. V projektu klikněte **"+ New"**
2. Vyberte **"Database"** → **"Add MySQL"**
3. Railway vytvoří MySQL instanci (pár sekund)

### 3.2 Poznamenejte si credentials

1. Klikněte na **MySQL** service
2. Záložka **"Variables"**
3. Uvidíte:
   ```
   MYSQLHOST=mysql.railway.internal
   MYSQLPORT=3306
   MYSQLDATABASE=railway
   MYSQLUSER=root
   MYSQLPASSWORD=abc123xyz...
   ```

---

## ⚙️ Krok 4: Konfigurace Environment Variables

### 4.1 Nastavte proměnné pro web aplikaci

1. Klikněte na **váš web service** (ne MySQL)
2. Záložka **"Variables"**
3. Klikněte **"+ New Variable"**
4. Přidejte tyto proměnné:

```bash
# Application
APP_ENV=production
BASE_URL=https://your-app.up.railway.app  # Railway vám dá URL

# Database - POUŽIJTE hodnoty z MySQL service!
DB_HOST=mysql.railway.internal
DB_PORT=3306
DB_NAME=railway
DB_USER=root
DB_PASS=<zkopírujte MYSQLPASSWORD z MySQL service>
```

**TIP:** Railway má funkci **"Reference Variables"** - můžete přímo propojit MySQL credentials:
```
DB_HOST = ${{MySQL.MYSQLHOST}}
DB_PORT = ${{MySQL.MYSQLPORT}}
DB_NAME = ${{MySQL.MYSQLDATABASE}}
DB_USER = ${{MySQL.MYSQLUSER}}
DB_PASS = ${{MySQL.MYSQLPASSWORD}}
```

---

## 📊 Krok 5: Import databáze

### 5.1 Připojení k Railway MySQL

Railway MySQL není přímo přístupný zvenčí. Máme 2 možnosti:

#### Možnost A: Použít Railway CLI (doporučuji)

```bash
# Nainstalovat Railway CLI
npm install -g @railway/cli

# Login
railway login

# Připojit se k projektu
railway link

# Otevřít MySQL shell
railway run mysql -h mysql.railway.internal -u root -p railway
```

Pak v MySQL shellu:
```sql
source /cesta/k/database_export_infinityfree.sql
```

#### Možnost B: Použít phpMyAdmin plugin

1. V Railway projektu klikněte **"+ New"**
2. Vyberte **"Template"** → Hledejte **"phpMyAdmin"**
3. Připojte k MySQL service
4. Otevřete phpMyAdmin
5. Import → `database_export_infinityfree.sql`

### 5.2 Nebo nahrát přes lokální script

Vytvořím vám helper script:

```bash
# deploy-db.sh
railway run php -r "
\$config = require 'config/database.php';
\$pdo = new PDO(
    \"mysql:host={\$config['host']};dbname={\$config['database']}\",
    \$config['username'],
    \$config['password']
);
\$sql = file_get_contents('database_export_infinityfree.sql');
\$pdo->exec(\$sql);
echo 'Database imported!';
"
```

---

## 🔧 Krok 6: Deploy!

### 6.1 Railway automaticky builduje

Railway už začal build hned po připojení repository. Sledujte:

1. Záložka **"Deployments"**
2. Sledujte build log
3. Čekejte na **"✓ Deployment successful"**

### 6.2 Získejte URL

1. Záložka **"Settings"**
2. Sekce **"Networking"**
3. Klikněte **"Generate Domain"**
4. Dostanete URL typu: `https://labyrint-production-abc123.up.railway.app`

### 6.3 Aktualizujte BASE_URL

1. Zkopírujte novou URL
2. Záložka **"Variables"**
3. Upravte `BASE_URL` na novou URL
4. Railway auto-redeploy

---

## ✅ Krok 7: Test webu

Otevřete vygenerovanou URL v prohlížeči:

```
https://labyrint-production-abc123.up.railway.app
```

**Měli byste vidět:**
- ✅ Homepage se načte
- ✅ Styly fungují
- ✅ Obrázky se zobrazují
- ✅ Menu funguje
- ✅ Přepínání jazyků

**Admin:**
```
https://labyrint-production-abc123.up.railway.app/admin
```

---

## 🔍 Debugging (pokud něco nefunguje)

### Zobrazit logy

1. Railway dashboard → váš web service
2. Záložka **"Logs"**
3. Sledujte real-time output

### Běžné problémy

**Problém: 500 Error**
- Zkontrolujte Logs
- Zkontrolujte DB credentials v Variables

**Problém: Database connection failed**
- Ověřte `DB_*` proměnné
- Zkontrolujte že MySQL service běží

**Problém: Chybí styly**
- Zkontrolujte `BASE_URL` v Variables

---

## 🔄 Workflow pro budoucí změny

### Lokální vývoj → Production

```bash
# 1. Pracujete lokálně (MAMP + MySQL)
# ... editace souborů ...

# 2. Commit changes
git add .
git commit -m "Přidána nová feature"

# 3. Push to GitHub
git push origin main

# 4. Railway AUTOMATICKY deployuje! 🎉
# (Žádné FTP, žádné manuální upload!)
```

### Sledování deployu

1. Railway dashboard → Deployments
2. Sledujte build v reálném čase
3. Po dokončení - hotovo!

---

## 💰 Costs & Limits (Free Tier)

Railway Free Tier:
- **$5 credit měsíčně**
- Typicky stačí na:
  - 1 web aplikace (malá-střední traffic)
  - 1 MySQL databáze (několik GB)
  - ~500 hodin běhu měsíčně

**Odhad pro váš web:**
- Web: ~$3-4/měsíc
- MySQL: ~$1-2/měsíc
- **Celkem: vejde se do free tieru! ✅**

---

## 🎯 Vlastní doména (volitelné)

### Připojení labyrint.cz

1. Railway → Settings → Networking
2. Klikněte **"Custom Domain"**
3. Zadejte: `labyrint.cz` a `www.labyrint.cz`
4. Railway vám dá CNAME record
5. U vašeho registrátora domény:
   ```
   Type: CNAME
   Host: @
   Value: [hodnota z Railway]

   Type: CNAME
   Host: www
   Value: [hodnota z Railway]
   ```
6. Počkejte 24-48h na DNS propagaci
7. SSL certifikát se vygeneruje automaticky!

---

## 📞 Podpora

**Railway.app:**
- Docs: https://docs.railway.app
- Discord: https://discord.gg/railway
- Status: https://status.railway.app

**Běžné otázky:**
- Build failing? → Zkontrolujte Dockerfile
- DB connection? → Ověřte Variables
- 404 errors? → Zkontrolujte .htaccess v public/

---

## ✨ Výhody oproti InfinityFree

| Feature | InfinityFree | Railway.app |
|---------|-------------|-------------|
| Deploy | ❌ FTP upload | ✅ Git push |
| Database | ❌ Separate | ✅ Integrated |
| Logs | ❌ Hidden | ✅ Real-time |
| SSL | ⚠️ Manual | ✅ Automatic |
| Environment | ❌ Files | ✅ Variables |
| Rollback | ❌ Manual | ✅ One click |
| Updates | ❌ Re-upload | ✅ Git push |

---

## 🎉 Hotovo!

Váš web běží na Railway.app s:
- ✅ Automatickým deploymentem z Gitu
- ✅ MySQL databází
- ✅ Real-time logy
- ✅ HTTPS
- ✅ Zdarma (free tier)

**Příští update:**
```bash
git add .
git commit -m "Update"
git push
# → Railway auto-deploy! 🚀
```

---

Vytvořeno: 2026-01-22
Platforma: Railway.app
Projekt: Škola Labyrint
Stack: PHP 8.1, Apache, MySQL
