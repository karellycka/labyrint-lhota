# ✅ Kód je na GitHub!

Repository: **https://github.com/karellycka/labyrint-lhota**

---

## 🚀 Další kroky - Railway.app deployment

### Krok 1: Jděte na Railway.app

https://railway.app

- Klikněte **"Login"**
- Vyberte **"Login with GitHub"**

---

### Krok 2: Nový projekt

- Klikněte **"New Project"**
- Vyberte **"Deploy from GitHub repo"**
- Najděte a vyberte: **`labyrint-lhota`**
- Railway začne automatický build (2-3 minuty)

---

### Krok 3: Přidejte MySQL databázi

V projektu:
- Klikněte **"+ New"**
- Vyberte **"Database"** → **"Add MySQL"**
- Počkejte 30 sekund na vytvoření

---

### Krok 4: Nastavte Environment Variables

Klikněte na **váš web service** (ne MySQL):
- Záložka **"Variables"**
- Klikněte **"+ New Variable"** → **"Add Reference"**

Přidejte tyto reference variables (propojí se automaticky):

```
APP_ENV = production
BASE_URL = (nechte prázdné, nastavíme později)

DB_HOST = ${{MySQL.MYSQLHOST}}
DB_PORT = ${{MySQL.MYSQLPORT}}
DB_NAME = ${{MySQL.MYSQLDATABASE}}
DB_USER = ${{MySQL.MYSQLUSER}}
DB_PASS = ${{MySQL.MYSQLPASSWORD}}
```

**TIP:** Reference variables najdete v dropdown menu - automaticky propojí MySQL credentials!

---

### Krok 5: Vygenerujte URL

- Záložka **"Settings"**
- Sekce **"Networking"** → **"Public Networking"**
- Klikněte **"Generate Domain"**
- Dostanete URL typu: `https://labyrint-lhota-production.up.railway.app`

---

### Krok 6: Aktualizujte BASE_URL

- Zkopírujte nově vygenerovanou URL
- Zpět do **"Variables"**
- Upravte `BASE_URL` na tuto URL
- Railway automaticky redeploy (~1 minuta)

---

### Krok 7: Import databáze

#### Možnost A: Railway CLI (nejjednodušší)

```bash
# Nainstalovat Railway CLI
npm install -g @railway/cli

# Login
railway login

# Připojit k projektu
railway link

# Import databáze
railway run mysql -u root -p$MYSQLPASSWORD $MYSQLDATABASE < database_export_infinityfree.sql
```

#### Možnost B: phpMyAdmin plugin

1. V Railway projektu: **"+ New"** → **"Template"**
2. Hledat: **"phpMyAdmin"**
3. Připojit k MySQL service
4. Otevřít phpMyAdmin
5. Import → `database_export_infinityfree.sql`

---

### Krok 8: TEST! 🎉

Otevřete vygenerovanou URL:

```
https://labyrint-lhota-production.up.railway.app
```

✅ Měli byste vidět fungující web!

---

## 🔄 Budoucí workflow:

```bash
# Lokální vývoj (MAMP + MySQL - beze změny)
# ... editace souborů ...

# Commit & push
git add .
git commit -m "Nová feature"
git push origin main

# Railway AUTOMATICKY deployuje! 🚀
# (Žádné FTP, žádné manuální kroky!)
```

---

## 📊 Monitoring:

Railway dashboard:
- **Logs** → Real-time application logs
- **Metrics** → CPU, Memory, Network usage
- **Deployments** → Historie všech deployů + rollback

---

## 🎯 Výhody tohoto setupu:

✅ **Git workflow** - profesionální development
✅ **Automatický deploy** - push = live
✅ **Real-time logs** - vidíte co se děje
✅ **Rollback** - jeden klik zpět
✅ **Lokální dev nezměněn** - MAMP funguje stejně
✅ **Free tier** - $5 credit/měsíc (stačí!)

---

## 🆘 Potřebujete pomoct?

Řekněte mi kde jste a pomohu vám!

**Běžné problémy:**
- Build failing? → Zkontrolujte Logs
- DB connection error? → Ověřte Variables
- 404 na všech stránkách? → Problém s .htaccess

---

Hodně štěstí! 🚂✨
