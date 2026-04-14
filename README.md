<div align="center">

![RAYNET UK](https://www.raynet-uk.net/technical/graphics/raynet-uk.gif)

<br>

# RAYNET CMS

### The complete web platform for RAYNET UK groups

<br>

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![License](https://img.shields.io/badge/License-GPL--2.0-003366?style=flat-square)](https://www.gnu.org/licenses/gpl-2.0.html)
[![RAYNET UK](https://img.shields.io/badge/RAYNET-UK%20Affiliated-C8102E?style=flat-square)](https://www.raynet-uk.net)
[![Modules](https://img.shields.io/badge/Module%20Repo-raynet--cms--modules-003366?style=flat-square&logo=github)](https://github.com/raynet-uk/raynet-cms-modules)

<br>

> **Built by RAYNET Liverpool — for every RAYNET UK group**
>
> *A professional, fully-featured website platform designed specifically for the needs of volunteer emergency communications groups. Replaces ageing static sites and generic WordPress installs with something purpose-built for RAYNET.*

<br>

---

</div>

## ✨ Features

<table>
<tr>
<td width="50%">

**🖥️ Content Management**
- Divi-style visual page builder
- Source code editor with syntax highlighting
- Automatic backups before every save
- Page URL management with auto-route generation
- One-click module install & update system

**👥 Member Management**
- Registration with admin approval workflow
- Callsign verification and formatting
- Role-based access control
- Member availability self-reporting
- Digital profile with avatar upload

</td>
<td width="50%">

**📅 Operations**
- Event scheduling with RSVPs
- Operator assignment and briefing system
- Live alert status widget
- Operational map (APRS, Meshtastic, weather, flood)
- DMR network dashboard and last heard log

**🎓 Training & Admin**
- Full LMS with courses, quizzes, certificates
- SCORM content support
- Committee readiness matrix & LRF
- SSO / OAuth 2.0 for connected tools
- Super admin panel with session management

</td>
</tr>
</table>

---

## 📋 Requirements

| Requirement | Version | Notes |
|-------------|---------|-------|
| PHP | 8.2 or higher | 8.2 or 8.3 recommended |
| MySQL / MariaDB | 5.7+ / 10.3+ | |
| Composer | Any recent version | |
| Web Server | Apache or Nginx | |
| SSL Certificate | Required | Free via Let's Encrypt |
| **Hosting** | **VPS recommended** | **cPanel shared hosting also supported** |

> ✅ **Works on cPanel shared hosting** — a VPS is recommended for best performance and control, but not required.

---

## 🚀 Installation

### Step 1 — Get the files

```bash
git clone https://github.com/raynet-uk/raynet-cms.git
cd raynet-cms
```

Or download the ZIP from GitHub and extract to your web root.

### Step 2 — Install dependencies

```bash
# Standard
composer install --no-dev --optimize-autoloader

# cPanel shared hosting (if composer isn't in PATH)
php composer.phar install --no-dev --optimize-autoloader
```

### Step 3 — Configure your environment

```bash
cp .env.example .env
```

Edit `.env` with your details:

```env
APP_URL=https://yourgroup.net
DB_DATABASE=your_database
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
MAIL_HOST=mail.yourgroup.net
MAIL_FROM_ADDRESS=noreply@yourgroup.net
```

### Step 4 — Finalise setup

```bash
php artisan key:generate
php artisan migrate --force
php artisan storage:link
```

### Step 5 — Run the installation wizard

Visit your domain in a browser. You'll be automatically redirected to `/install` where the wizard will guide you through:

| Step | What it does |
|------|-------------|
| **Group Details** | Name, callsign, region, group number, contact email |
| **Admin Account** | Creates your first administrator account |
| **Complete** | Finalises the installation and launches your site |

**That's it. Your RAYNET CMS site is live. 📻**

---

## 🏠 Hosting

**A VPS (Virtual Private Server) is recommended** — it gives you full control, better performance, and makes running scheduled tasks (queue workers, cron jobs) straightforward.

**cPanel shared hosting also works** and is what many RAYNET groups already have. Here's what you need to know for shared hosting:

**Point your domain to the `public/` folder**

In cPanel → Domains, set your document root to point at the `public/` subdirectory. If you can't change the document root, add this to your root `.htaccess`:

```apache
RewriteEngine On
RewriteRule ^(.*)$ public/$1 [L]
```

**Use `php composer.phar` if `composer` isn't available:**

```bash
php composer.phar install --no-dev
```

**Run artisan commands via SSH terminal — not from a browser.**

---

## 🔌 Module System

RAYNET CMS has a built-in module system — extend your site's functionality with one click.

```
Admin Panel → Module Manager → Upload Module or Check for Updates
```

Official modules are published to the **[RAYNET CMS Module Repository](https://github.com/raynet-uk/raynet-cms-modules)**.

Modules are versioned ZIP files with automatic update notifications — your site checks GitHub for new versions and lets you update with a single button click.

**Building a module?** See the [module repository README](https://github.com/raynet-uk/raynet-cms-modules#module-zip-structure) for the structure and `module.json` format.

---

## 🔄 Updating

```bash
git pull origin main
php artisan migrate --force
php artisan view:clear && php artisan cache:clear
```

---

## ⚙️ Configuration

All group-specific settings live in **Admin → Settings** — no code editing required:

- Group name, callsign, number and region
- Logo upload (PNG, JPG, SVG, WebP)
- Contact and support emails
- Site URL
- Email header injection

---

## 📄 Default Pages

RAYNET CMS ships with these pages ready for you to customise:

| Page | Description |
|------|-------------|
| **Home** | Landing page with alert status and upcoming events |
| **About** | Group information and history |
| **Training** | Training information and course links |
| **Event Support** | Public support request form |
| **Calendar** | Public events calendar with ICS export |
| **Privacy Notice** | GDPR-compliant template |
| **Cookie Policy** | Cookie consent policy |

All pages are fully editable via the visual builder or source editor.

---

## 👨‍💻 Developers

RAYNET CMS is developed and maintained by **RAYNET Liverpool** (Group 10/ME/179).

<table>
<tr>
<td align="center" width="50%">
<br>
<strong>Ian</strong><br>
<code>G4BDS</code><br>
<em>Developer</em><br>
<br>
</td>
<td align="center" width="50%">
<br>
<strong>Nathan</strong><br>
<code>M7NDN</code><br>
<em>Developer</em><br>
<br>
</td>
</tr>
</table>

---

## 🔗 Links

| | |
|--|--|
| 🌐 **RAYNET UK** | [raynet-uk.net](https://www.raynet-uk.net) |
| 📻 **RAYNET Liverpool** | [raynet-liverpool.net](https://raynet-liverpool.net) |
| 🔌 **Module Repository** | [github.com/raynet-uk/raynet-cms-modules](https://github.com/raynet-uk/raynet-cms-modules) |

---

## 📜 Licence

RAYNET CMS is open source software released under the **[GNU General Public Licence v2.0](https://www.gnu.org/licenses/gpl-2.0.html)**.

You are free to use, modify and distribute this software for any RAYNET UK group. We ask that improvements are contributed back to the project where possible.

---

<div align="center">

<br>

**RAYNET CMS** · Developed by RAYNET Liverpool · For every RAYNET UK group

*RAYNET — the Radio Amateurs' Emergency Network*

<br>

`73 de G4BDS & M7NDN 📻`

</div>
