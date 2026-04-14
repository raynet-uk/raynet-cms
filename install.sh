#!/bin/bash
# ============================================================
#  RAYNET CMS — Interactive Install Script
#  Run from your public_html directory: bash install.sh
#  Developed by RAYNET Liverpool (G4BDS & M7NDN)
# ============================================================

set -e

# ── Colours ──────────────────────────────────────────────────
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m'

# ── Helpers ───────────────────────────────────────────────────
ok()   { echo -e "${GREEN}  ✓ $1${NC}"; }
fail() { echo -e "${RED}  ✗ $1${NC}"; }
info() { echo -e "${CYAN}  → $1${NC}"; }
warn() { echo -e "${YELLOW}  ⚠ $1${NC}"; }
step() { echo -e "\n${BOLD}${BLUE}━━━ $1 ━━━${NC}"; }
ask()  { echo -e "${YELLOW}  ? $1${NC}"; }

header() {
    clear
    echo -e "${BLUE}"
    echo "  ██████╗  █████╗ ██╗   ██╗███╗   ██╗███████╗████████╗"
    echo "  ██╔══██╗██╔══██╗╚██╗ ██╔╝████╗  ██║██╔════╝╚══██╔══╝"
    echo "  ██████╔╝███████║ ╚████╔╝ ██╔██╗ ██║█████╗     ██║   "
    echo "  ██╔══██╗██╔══██║  ╚██╔╝  ██║╚██╗██║██╔══╝     ██║   "
    echo "  ██║  ██║██║  ██║   ██║   ██║ ╚████║███████╗   ██║   "
    echo "  ╚═╝  ╚═╝╚═╝  ╚═╝   ╚═╝   ╚═╝  ╚═══╝╚══════╝   ╚═╝   "
    echo -e "${NC}"
    echo -e "${BOLD}  RAYNET CMS — Installation Script${NC}"
    echo -e "  Built by RAYNET Liverpool · G4BDS & M7NDN"
    echo -e "  For RAYNET UK affiliated groups"
    echo ""
    echo -e "  ${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo ""
}

# ── Detect PHP binary ─────────────────────────────────────────
detect_php() {
    for bin in php php8.3 php8.2 php82 /usr/local/bin/php /usr/bin/php; do
        if command -v "$bin" &>/dev/null; then
            PHP_VER=$("$bin" -r 'echo PHP_VERSION;' 2>/dev/null)
            MAJOR=$(echo "$PHP_VER" | cut -d. -f1)
            MINOR=$(echo "$PHP_VER" | cut -d. -f2)
            if [ "$MAJOR" -ge 8 ] && [ "$MINOR" -ge 2 ]; then
                PHP="$bin"
                return 0
            fi
        fi
    done
    return 1
}

# ── Detect Composer ───────────────────────────────────────────
detect_composer() {
    if command -v composer &>/dev/null; then
        COMPOSER="composer"
    elif [ -f "composer.phar" ]; then
        COMPOSER="$PHP composer.phar"
    elif [ -f "../composer.phar" ]; then
        COMPOSER="$PHP ../composer.phar"
    else
        COMPOSER=""
    fi
}

# ── Step 0: Pre-flight checks ─────────────────────────────────
preflight() {
    step "Pre-flight Checks"

    # PHP
    if detect_php; then
        ok "PHP $PHP_VER found ($PHP)"
    else
        fail "PHP 8.2+ not found. Install PHP 8.2 or higher and try again."
        exit 1
    fi

    # Extensions
    for ext in pdo pdo_mysql mbstring openssl curl zip fileinfo; do
        if $PHP -m 2>/dev/null | grep -qi "^$ext$"; then
            ok "PHP extension: $ext"
        else
            warn "PHP extension missing: $ext (may cause issues)"
        fi
    done

    # .env file
    if [ -f ".env" ]; then
        warn ".env file already exists — will use existing values"
        ENV_EXISTS=1
    elif [ -f ".env.example" ]; then
        ok ".env.example found"
        ENV_EXISTS=0
    else
        fail "No .env.example found. Make sure you're in the RAYNET CMS root directory."
        exit 1
    fi

    # Writable directories
    for dir in storage bootstrap/cache; do
        if [ -w "$dir" ]; then
            ok "Directory writable: $dir"
        else
            warn "Directory not writable: $dir — fixing permissions"
            chmod -R 775 "$dir" 2>/dev/null && ok "Fixed: $dir" || fail "Could not fix $dir — check permissions manually"
        fi
    done
}

# ── Step 1: Environment setup ─────────────────────────────────
setup_env() {
    step "Environment Configuration"

    if [ "$ENV_EXISTS" = "0" ]; then
        cp .env.example .env
        ok "Created .env from .env.example"
    fi

    echo ""
    info "Please provide your site details (press Enter to skip optional fields):"
    echo ""

    ask "Site URL (e.g. https://yourgroup.net):"
    read -r APP_URL
    APP_URL=${APP_URL:-https://example.com}

    ask "Database host (default: localhost):"
    read -r DB_HOST
    DB_HOST=${DB_HOST:-localhost}

    ask "Database name:"
    read -r DB_DATABASE

    ask "Database username:"
    read -r DB_USERNAME

    ask "Database password:"
    read -rs DB_PASSWORD
    echo ""

    ask "Mail host (e.g. mail.yourgroup.net):"
    read -r MAIL_HOST

    ask "Mail from address (e.g. noreply@yourgroup.net):"
    read -r MAIL_FROM

    ask "Mail username (leave blank if same as from address):"
    read -r MAIL_USER
    MAIL_USER=${MAIL_USER:-$MAIL_FROM}

    ask "Mail password:"
    read -rs MAIL_PASS
    echo ""

    # Write values to .env
    sed -i "s|APP_URL=.*|APP_URL=$APP_URL|g" .env
    sed -i "s|DB_HOST=.*|DB_HOST=$DB_HOST|g" .env
    sed -i "s|DB_DATABASE=.*|DB_DATABASE=$DB_DATABASE|g" .env
    sed -i "s|DB_USERNAME=.*|DB_USERNAME=$DB_USERNAME|g" .env
    sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=$DB_PASSWORD|g" .env
    [ -n "$MAIL_HOST" ] && sed -i "s|MAIL_HOST=.*|MAIL_HOST=$MAIL_HOST|g" .env
    [ -n "$MAIL_FROM" ] && sed -i "s|MAIL_FROM_ADDRESS=.*|MAIL_FROM_ADDRESS=\"$MAIL_FROM\"|g" .env
    [ -n "$MAIL_USER" ] && sed -i "s|MAIL_USERNAME=.*|MAIL_USERNAME=$MAIL_USER|g" .env
    [ -n "$MAIL_PASS" ] && sed -i "s|MAIL_PASS=.*|MAIL_PASS=$MAIL_PASS|g" .env

    ok ".env configured"
}

# ── Step 2: Composer ──────────────────────────────────────────
install_composer_deps() {
    step "Installing PHP Dependencies"

    detect_composer

    if [ -z "$COMPOSER" ]; then
        info "Composer not found — downloading composer.phar..."
        curl -sS https://getcomposer.org/installer | $PHP
        COMPOSER="$PHP composer.phar"
        ok "composer.phar downloaded"
    else
        ok "Composer found: $COMPOSER"
    fi

    info "Running composer install (this may take a minute)..."
    $COMPOSER install --no-dev --optimize-autoloader --no-interaction 2>&1 | tail -5
    ok "Composer dependencies installed"
}

# ── Step 3: Application key ───────────────────────────────────
generate_key() {
    step "Application Key"

    CURRENT_KEY=$(grep "^APP_KEY=" .env | cut -d= -f2)
    if [ -n "$CURRENT_KEY" ] && [ "$CURRENT_KEY" != "" ]; then
        warn "APP_KEY already set — skipping key generation"
    else
        $PHP artisan key:generate --force
        ok "Application key generated"
    fi
}

# ── Step 4: Database ──────────────────────────────────────────
run_migrations() {
    step "Database Setup"

    info "Testing database connection..."
    if $PHP artisan db:show &>/dev/null 2>&1; then
        ok "Database connection successful"
    else
        warn "Could not verify connection — attempting migration anyway"
    fi

    info "Running migrations..."
    $PHP artisan migrate --force 2>&1
    ok "Database migrations complete"
}

# ── Step 5: Storage link ──────────────────────────────────────
setup_storage() {
    step "Storage Link"

    if [ -L "public/storage" ]; then
        warn "Storage link already exists — removing and relinking"
        rm public/storage
    fi

    $PHP artisan storage:link
    ok "Storage link created"
}

# ── Step 6: Permissions ───────────────────────────────────────
fix_permissions() {
    step "File Permissions"

    chmod -R 775 storage bootstrap/cache 2>/dev/null && ok "storage/ permissions set to 775" || warn "Could not set storage permissions"
    find storage -type f -exec chmod 664 {} \; 2>/dev/null && ok "storage file permissions set to 664"

    # Try to detect the web user
    WEB_USER=$(ps aux | grep -E '(apache|nginx|www-data|nobody|httpd)' | grep -v grep | head -1 | awk '{print $1}')
    if [ -n "$WEB_USER" ] && [ "$WEB_USER" != "root" ]; then
        CURRENT_USER=$(whoami)
        chown -R "$CURRENT_USER":"$WEB_USER" storage bootstrap/cache 2>/dev/null && \
            ok "Ownership set to $CURRENT_USER:$WEB_USER" || \
            warn "Could not set ownership — may need manual adjustment"
    fi
}

# ── Step 7: Cache clear ───────────────────────────────────────
clear_caches() {
    step "Clearing Caches"
    $PHP artisan route:clear  && ok "Routes cleared"
    $PHP artisan view:clear   && ok "Views cleared"
    $PHP artisan config:clear && ok "Config cleared"
    $PHP artisan cache:clear  && ok "Cache cleared"
}

# ── Step 8: Cron job reminder ─────────────────────────────────
cron_reminder() {
    step "Scheduled Tasks (Optional)"
    echo ""
    info "To enable scheduled tasks (recommended), add this cron job in cPanel:"
    echo ""
    WEBROOT=$(pwd)
    echo -e "  ${YELLOW}* * * * * cd $WEBROOT && $PHP artisan schedule:run >> /dev/null 2>&1${NC}"
    echo ""
    info "In cPanel: Cron Jobs → Add New Cron Job → paste the above"
    echo ""
}

# ── Final summary ─────────────────────────────────────────────
summary() {
    step "Installation Complete"
    echo ""
    echo -e "  ${GREEN}${BOLD}RAYNET CMS is ready!${NC}"
    echo ""
    echo -e "  ${CYAN}Next steps:${NC}"
    echo -e "  1. Visit ${BOLD}$APP_URL${NC} in your browser"
    echo -e "  2. The installation wizard will guide you through the final setup"
    echo -e "  3. You'll set your group name, callsign, and create your admin account"
    echo ""
    echo -e "  ${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo ""
    echo -e "  Built by RAYNET Liverpool · G4BDS & M7NDN"
    echo -e "  73 de RAYNET Liverpool 📻"
    echo ""
}

# ── Main ──────────────────────────────────────────────────────
main() {
    header

    echo -e "  This script will install RAYNET CMS on your server."
    echo -e "  It will set up your .env, install dependencies, run migrations,"
    echo -e "  and prepare the site for the web-based installation wizard."
    echo ""
    echo -e "  ${YELLOW}Make sure you are running this from your web root directory.${NC}"
    echo ""
    ask "Ready to start? (y/N)"
    read -r CONFIRM
    if [[ ! "$CONFIRM" =~ ^[Yy]$ ]]; then
        echo "  Aborted."
        exit 0
    fi

    preflight
    setup_env
    install_composer_deps
    generate_key
    run_migrations
    setup_storage
    fix_permissions
    clear_caches
    cron_reminder
    summary
}

main
