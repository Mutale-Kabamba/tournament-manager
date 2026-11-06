# 🏆 Tournament Manager (Laravel Web App)

A complete, production-ready Laravel application converted from the standalone Tournament Manager HTML app. Features real-time score tracking, balanced 2-games-per-team group stages, live standings, master pitch schedules, knockouts (semi-finals, 3rd place, finals), penalties, and admin PIN authorization with instant database persistence.

---

## ⚡ Quick Start (Run Locally Right Now)

Open PowerShell or your terminal in this directory:

```bash
cd "C:\Users\mukuk\.gemini\antigravity\scratch\tournament-manager"
php artisan serve
```

Visit: **[http://localhost:8000](http://localhost:8000)**

- **Public Live View:** Anyone accessing the URL gets real-time read-only scores and fixtures.
- **Admin PIN:** Default PIN is **`1234`** (Click **Admin Login** in the top right to unlock score inputs and match controls; you can change the PIN at any time).

---

## 🚀 How to Host This Online Right Now

### Option 1: Render.com (Free & Instant with Docker)
1. Push this folder to a GitHub repository.
2. In Render dashboard, click **New +** > **Blueprint** (or **Web Service**).
3. Connect your repository. Render will automatically detect `render.yaml` and `Dockerfile`.
4. Click **Deploy**. Your live tournament site will be online with HTTPS.

### Option 2: Railway.app
1. Go to [railway.app](https://railway.app), click **New Project** > **Deploy from GitHub repo**.
2. Railway detects the `Dockerfile` (or `Procfile`) and automatically deploys.

### Option 3: Docker / VPS / DigitalOcean / Linode
To run on any Linux or cloud server with Docker installed:

```bash
docker compose up -d
```
Your app will be running at `http://YOUR_SERVER_IP:8080`. SQLite database volume is automatically persisted.

### Option 4: Shared Hosting / cPanel / Traditional Apache / Nginx
1. Upload this project folder to your hosting server.
2. Set your web server's **Document Root** to the `public/` directory (e.g. `/home/user/public_html/tournament/public` or symlink `public_html` to `public`).
3. Make sure `storage/` and `bootstrap/cache/` are writable (`chmod -R 775 storage bootstrap/cache`).
4. Run migrations:
   ```bash
   php artisan migrate --force
   ```
5. Cache configurations for top performance:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

---

## ⚙️ Features & Architecture

- **Backend:** Laravel 13 with SQLite database out-of-the-box (zero database configuration needed, or switch to MySQL/Postgres in `.env`).
- **Real-Time Synchronization:** Admin score edits, team renames, and bracket seedings save to the Laravel database and automatically sync to all viewer screens via periodic background polling.
- **Offline / Pitch-side Resilience:** Keeps local cache so organizers and spectators at sports pitches don't lose view state during temporary connection drops.
- **Handwritten Roster Pre-loaded:**
  - **Boys Division (10 Teams):** 2 Groups of 5 (Anthony, Mumba, Frank, Eliza, Poniso in Group 1; Betty, Tasila, Kambenja, Wisdom, Thelma in Group 2). Each team plays exactly 2 games using a balanced cycle graph. Top 2 from each group advance to semi-finals.
  - **Girls Division (6 Teams):** Single league of 6 (Eugene, Gift, Edma, Gracious, Fredrick, Maureen). Top 4 advance to semi-finals (1st vs 4th, 2nd vs 3rd).
- **Master Pitch Schedule:** Chronological timeline with division and status filters, plus printable/PDF export.
- **PIN Protected:** Admin mode is secured by a server-side verified PIN (`1234` by default).

---

## 🧪 Testing

The test suite covers Blade rendering, API endpoints, PIN authentication, and state persistence:

```bash
php artisan test
```
All 8 automated tests pass.
