#!/bin/bash
# ============================================================
#  RAYNET CMS — Interactive Install Script
#  Run from your RAYNET CMS directory: bash install.sh
#  Developed by RAYNET Liverpool (G4BDS & M7NDN)
# ============================================================

# ── Colours ──────────────────────────────────────────────────
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m'

ok()   { echo -e "${GREEN}  ✓ $1${NC}"; }
fail() { echo -e "${RED}  ✗ $1${NC}"; exit 1; }
info() { echo -e "${CYAN}  → $1${NC}"; }
warn() { echo -e "${YELLOW}  ⚠ $1${NC}"; }
step() { echo -e "\n${BOLD}${BLUE}━━━ $1 ━━━${NC}"; }
ask()  { echo -e "${YELLOW}  ? $1${NC}"; }

INSTALL_DIR=$(pwd)

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

# ── Detect PHP ────────────────────────────────────────────────
detect_php() {
    for bin in \
        /usr/local/bin/ea-php84 \
        /usr/local/bin/ea-php83 \
        /usr/local/bin/ea-php82 \
        /usr/local/bin/ea-php81 \
        php8.4 php8.3 php8.2 \
        /usr/local/bin/php \
        /usr/bin/php \
        php; do
        if [ -x "$bin" ] 2>/dev/null || command -v "$bin" &>/dev/null; then
            TEST=$("$bin" -r "echo PHP_VERSION;" 2>/dev/null)
            [ -z "$TEST" ] && continue
            PHP_VER=$(echo "$TEST" | tr -d "\r\n ")
            MAJOR=$(echo "$PHP_VER" | cut -d. -f1 | tr -dc "0-9")
            MINOR=$(echo "$PHP_VER" | cut -d. -f2 | tr -dc "0-9")
            if [ -n "$MAJOR" ] && [ -n "$MINOR" ] && [ "$MAJOR" -ge 8 ] && [ "$MINOR" -ge 2 ]; then
                PHP="$bin"
                return 0
            fi
        fi
    done
    return 1
}

# ── Detect account user (not root) ───────────────────────────
detect_account_user() {
    ACCOUNT_USER=""
    # Try parent directories to find the cPanel account username
    local dir="$INSTALL_DIR"
    for i in 1 2 3 4; do
        local owner
        owner=$(stat -c '%U' "$dir" 2>/dev/null)
        if [ -n "$owner" ] && [ "$owner" != "root" ] && [ "$owner" != "nobody" ]; then
            ACCOUNT_USER="$owner"
            return 0
        fi
        dir=$(dirname "$dir")
    done
    # Fallback: look for home directory owner
    if [ -d "/home" ]; then
        ACCOUNT_USER=$(ls /home | head -1)
    fi
}

# ── Run as account user if we're root ────────────────────────
run_as_user() {
    if [ "$(whoami)" = "root" ] && [ -n "$ACCOUNT_USER" ] && [ "$ACCOUNT_USER" != "root" ]; then
        su -s /bin/bash "$ACCOUNT_USER" -c "cd $INSTALL_DIR && $*" 2>&1 | grep -v "OPcache"
    else
        eval "$@" 2>&1 | grep -v "OPcache"
    fi
}

# ── Step 0: Fix ownership BEFORE anything else ────────────────
fix_ownership() {
    step "Fixing Ownership & Permissions"

    detect_account_user

    if [ -n "$ACCOUNT_USER" ] && [ "$ACCOUNT_USER" != "root" ]; then
        info "Account user detected: $ACCOUNT_USER"
        chown -R "$ACCOUNT_USER":"$ACCOUNT_USER" "$INSTALL_DIR" 2>/dev/null && \
            ok "Ownership set to $ACCOUNT_USER" || \
            warn "chown failed — continuing anyway"
    else
        warn "Could not detect account user — files may stay owned by root"
    fi

    chmod -R 755 "$INSTALL_DIR" 2>/dev/null
    mkdir -p storage/logs storage/framework/cache storage/framework/sessions \
             storage/framework/views storage/app/public bootstrap/cache 2>/dev/null
    chmod -R 775 storage bootstrap/cache 2>/dev/null && ok "Permissions set (755/775)"
    find storage -type f -exec chmod 664 {} \; 2>/dev/null
    ok "Ownership and permissions complete"
}

# ── Step 1: Preflight ─────────────────────────────────────────
preflight() {
    step "Pre-flight Checks"

    if ! detect_php; then
        fail "PHP 8.2+ not found. Install PHP 8.2 or higher and try again."
    fi
    ok "PHP $PHP_VER found ($PHP)"

    for ext in pdo pdo_mysql mbstring openssl curl zip fileinfo; do
        if $PHP -r "echo extension_loaded('$ext') ? 'yes' : 'no';" 2>/dev/null | grep -q "yes"; then
            ok "PHP extension: $ext"
        else
            warn "PHP extension possibly missing: $ext"
        fi
    done

    if [ ! -f ".env.example" ] && [ ! -f ".env" ]; then
        fail "No .env.example found. Run this from the RAYNET CMS root directory."
    fi
    ok "Directory looks correct"
}

# ── Step 2: Environment ───────────────────────────────────────
setup_env() {
    step "Environment Configuration"

    if [ ! -f ".env" ]; then
        cp .env.example .env
        ok "Created .env from .env.example"
    else
        warn ".env already exists — updating values"
    fi

    echo ""
    info "Enter your site details:"
    echo ""

    ask "Site URL (e.g. https://yourgroup.net):"
    read -r APP_URL
    APP_URL=${APP_URL:-https://example.com}
    # Strip trailing spaces
    APP_URL=$(echo "$APP_URL" | tr -d '[:space:]')

    ask "Database host [localhost]:"
    read -r DB_HOST
    DB_HOST=${DB_HOST:-localhost}

    ask "Database name:"
    read -r DB_DATABASE

    ask "Database username:"
    read -r DB_USERNAME

    ask "Database password:"
    read -rs DB_PASSWORD
    echo ""

    ask "Mail host (optional, e.g. mail.yourgroup.net):"
    read -r MAIL_HOST

    ask "Mail from address (optional, e.g. noreply@yourgroup.net):"
    read -r MAIL_FROM

    ask "Mail password (optional):"
    read -rs MAIL_PASS
    echo ""

    # Write to .env
    sed -i "s|APP_URL=.*|APP_URL=$APP_URL|g"             .env
    sed -i "s|DB_HOST=.*|DB_HOST=$DB_HOST|g"             .env
    sed -i "s|DB_DATABASE=.*|DB_DATABASE=$DB_DATABASE|g" .env
    sed -i "s|DB_USERNAME=.*|DB_USERNAME=$DB_USERNAME|g" .env
    sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=$DB_PASSWORD|g" .env
    [ -n "$MAIL_HOST" ] && sed -i "s|MAIL_HOST=.*|MAIL_HOST=$MAIL_HOST|g" .env
    [ -n "$MAIL_FROM" ] && sed -i "s|MAIL_FROM_ADDRESS=.*|MAIL_FROM_ADDRESS=\"$MAIL_FROM\"|g" .env
    [ -n "$MAIL_PASS" ] && sed -i "s|MAIL_PASSWORD=.*|MAIL_PASSWORD=$MAIL_PASS|g" .env

    # Clear APP_KEY so we generate a fresh one
    sed -i "s|APP_KEY=.*|APP_KEY=|g" .env

    ok ".env configured"
}

# ── Step 3: Composer ──────────────────────────────────────────
install_deps() {
    step "Installing PHP Dependencies"

    # Always download composer.phar using the detected PHP binary
    # Never rely on system 'composer' as it may use a broken PHP
    info "Downloading composer.phar using $PHP..."
    curl -sS https://getcomposer.org/installer | $PHP -- --quiet 2>/dev/null
    if [ ! -f "composer.phar" ]; then
        fail "Failed to download composer.phar. Check curl is available and try again."
    fi
    COMPOSER="$PHP composer.phar"
    ok "composer.phar ready"

    info "Running composer install (this may take 1-2 minutes)..."

    # Run as account user if we are root, to avoid root-owned vendor/
    if [ "$(whoami)" = "root" ] && [ -n "$ACCOUNT_USER" ] && [ "$ACCOUNT_USER" != "root" ]; then
        su -s /bin/bash "$ACCOUNT_USER" -c             "cd $INSTALL_DIR && $COMPOSER install --no-dev --optimize-autoloader --no-interaction 2>&1"             | grep -v "OPcache" | grep -v "^$" | tail -8
    else
        $COMPOSER install --no-dev --optimize-autoloader --no-interaction 2>&1             | grep -v "OPcache" | grep -v "^$" | tail -8
    fi

    if [ ! -d "vendor" ]; then
        fail "vendor/ directory not created — composer install failed."
    fi
    ok "Dependencies installed (vendor/ created)"
}

# ── Step 4: App key ───────────────────────────────────────────
generate_key() {
    step "Application Key"

    if [ ! -f "vendor/autoload.php" ]; then
        fail "vendor/autoload.php missing — composer did not complete successfully."
    fi

    $PHP artisan key:generate --force 2>&1 | grep -v "OPcache"

    KEY=$(grep "^APP_KEY=" .env | cut -d= -f2)
    if [ -z "$KEY" ]; then
        fail "APP_KEY is still empty after key:generate. Check PHP and artisan are working."
    fi
    ok "Application key generated"
}

# ── Step 5: Database ──────────────────────────────────────────
run_migrations() {
    step "Database Setup"

    info "Running migrations..."
    $PHP artisan migrate --force 2>&1 | grep -v "OPcache"
    ok "Migrations complete"
}

# ── Step 6: Storage ───────────────────────────────────────────
setup_storage() {
    step "Storage Link"

    rm -f public/storage 2>/dev/null
    $PHP artisan storage:link 2>&1 | grep -v "OPcache"
    ok "Storage link created"
}

# ── Step 7: Final permissions ─────────────────────────────────
final_permissions() {
    step "Final Permissions"

    if [ -n "$ACCOUNT_USER" ] && [ "$ACCOUNT_USER" != "root" ]; then
        chown -R "$ACCOUNT_USER":"$ACCOUNT_USER" "$INSTALL_DIR" 2>/dev/null && \
            ok "Final ownership set to $ACCOUNT_USER" || \
            warn "Could not set final ownership"
    fi
    chmod -R 775 storage bootstrap/cache 2>/dev/null
    find storage -type f -exec chmod 664 {} \; 2>/dev/null
    ok "Permissions finalised"
}

# ── Step 8: Cache clear ───────────────────────────────────────
clear_caches() {
    step "Clearing Caches"
    $PHP artisan route:clear  2>&1 | grep -v "OPcache" && ok "Routes cleared"
    $PHP artisan view:clear   2>&1 | grep -v "OPcache" && ok "Views cleared"
    $PHP artisan config:clear 2>&1 | grep -v "OPcache" && ok "Config cleared"
    $PHP artisan cache:clear  2>&1 | grep -v "OPcache" && ok "Cache cleared"
}

# ── Step 9: Document root ─────────────────────────────────────
setup_docroot() {
    step "Web Server Configuration"

    PUBLIC_DIR="$INSTALL_DIR/public"
    PARENT_DIR=$(dirname "$INSTALL_DIR")
    FOLDER_NAME=$(basename "$INSTALL_DIR")

    echo ""
    info "Your document root must point to:"
    echo -e "  ${BOLD}$PUBLIC_DIR${NC}"
    echo ""
    info "In cPanel → Domains → set Document Root to the above path."
    echo ""

    # Auto-create .htaccess in parent if writable
    if [ -d "$PARENT_DIR" ] && [ -w "$PARENT_DIR" ]; then
        ask "Auto-create redirect .htaccess in parent directory? (y/N)"
        read -r AUTO_HT
        if [[ "$AUTO_HT" =~ ^[Yy]$ ]]; then
            cat > "$PARENT_DIR/.htaccess" << HTEOF
RewriteEngine On
RewriteRule ^(.*)$ ${FOLDER_NAME}/public/\$1 [L]
HTEOF
            # Fix ownership on the .htaccess
            [ -n "$ACCOUNT_USER" ] && chown "$ACCOUNT_USER":"$ACCOUNT_USER" "$PARENT_DIR/.htaccess" 2>/dev/null
            ok "Created $PARENT_DIR/.htaccess → redirects to $FOLDER_NAME/public/"
        fi
    fi
}

# ── Step 10: Cron ─────────────────────────────────────────────
cron_reminder() {
    step "Scheduled Tasks (Optional)"
    echo ""
    echo -e "  ${YELLOW}* * * * * cd $INSTALL_DIR && $PHP artisan schedule:run >> /dev/null 2>&1${NC}"
    echo ""
    info "Add the above line to cPanel → Cron Jobs"
    echo ""
}

# ── Summary ───────────────────────────────────────────────────
summary() {
    step "Installation Complete"
    echo ""
    echo -e "  ${GREEN}${BOLD}RAYNET CMS is ready!${NC}"
    echo ""
    echo -e "  ${CYAN}Next steps:${NC}"
    echo -e "  1. Make sure your domain's document root is set to:"
    echo -e "     ${BOLD}$INSTALL_DIR/public${NC}"
    echo -e "  2. Visit ${BOLD}$APP_URL${NC}"
    echo -e "  3. Complete the setup wizard (group name, callsign, admin account)"
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

    echo -e "  This script will fully install RAYNET CMS with no manual steps."
    echo -e "  It handles dependencies, database, permissions, and document root."
    echo ""
    echo -e "  ${YELLOW}Run this from your RAYNET CMS root directory.${NC}"
    echo ""
    ask "Ready to start? (y/N)"
    read -r CONFIRM
    if [[ ! "$CONFIRM" =~ ^[Yy]$ ]]; then
        echo "  Aborted."
        exit 0
    fi

    fix_ownership    # Fix ownership FIRST before anything else
    preflight
    setup_env
    install_deps     # Composer runs as account user to avoid root-owned vendor/
    generate_key
    run_migrations
    seed_roles
    setup_storage
    final_permissions
    clear_caches
    setup_docroot
    cron_reminder
    summary
}

main