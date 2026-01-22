# ⚡ Railway.app - Quick Start

**5 kroků k živému webu:**

---

## ✅ Checklist

### Krok 1: Git & GitHub (5 min)
```bash
cd /Users/karellycka/weby/labyrint
git init
git add .
git commit -m "Initial commit"
```

GitHub:
- [ ] Vytvořit repository na https://github.com/new
- [ ] Push lokální kód na GitHub

```bash
git remote add origin https://github.com/your-username/labyrint-web.git
git push -u origin main
```

---

### Krok 2: Railway.app účet (2 min)
- [ ] Jít na https://railway.app
- [ ] Login with GitHub
- [ ] Autorizovat přístup

---

### Krok 3: Deploy projekt (3 min)
- [ ] **"New Project"** → **"Deploy from GitHub repo"**
- [ ] Vybrat `labyrint-web` repository
- [ ] Počkat na build (2-3 min)

---

### Krok 4: Přidat MySQL (1 min)
- [ ] Kliknout **"+ New"** → **"Database"** → **"Add MySQL"**
- [ ] Počkat na vytvoření (30 sec)

---

### Krok 5: Nastavit Variables (2 min)
Web service → Variables → Přidat:

```bash
APP_ENV=production
BASE_URL=https://your-app.up.railway.app

# DB credentials - použít Reference Variables:
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_NAME=${{MySQL.MYSQLDATABASE}}
DB_USER=${{MySQL.MYSQLUSER}}
DB_PASS=${{MySQL.MYSQLPASSWORD}}
```

---

### Krok 6: Import DB (5 min)
```bash
# Nainstalovat Railway CLI
npm install -g @railway/cli

# Login & link
railway login
railway link

# Import databáze
railway run mysql -u root -p railway < database_export_infinityfree.sql
```

---

### Krok 7: Generate Domain & Test
- [ ] Settings → Networking → **"Generate Domain"**
- [ ] Otevřít URL → Měl by běžet web! 🎉

---

## 🚀 Budoucí updates:

```bash
# Editace souborů lokálně...
git add .
git commit -m "Update"
git push

# Railway automaticky deployuje! ✅
```

---

## 📊 Výhody:

| Co | InfinityFree | Railway.app |
|----|--------------|-------------|
| Deploy | FTP upload 😢 | Git push 🎉 |
| DB | Separate | Integrated ✅ |
| Logs | Žádné | Real-time ✅ |
| Workflow | Manuální | Automatický ✅ |

---

**Celkový čas:** ~15 minut
**Cena:** Zdarma ($5 credit/měsíc)
**Maintenance:** Žádný - jen git push!

---

📖 **Detailní návod:** `DEPLOYMENT_RAILWAY.md`
