#!/bin/bash
# ============================================================
#  RAYNET CMS — Interactive Install Script
#  Run from your web root directory: bash install.sh
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
    for bin in \
        /usr/local/bin/ea-php84 \
        /usr/local/bin/ea-php83 \
        /usr/local/bin/ea-php82 \
        /usr/local/bin/ea-php81 \
        php8.4 php8.3 php8.2 php8.1 \
        /usr/local/bin/php \
        /usr/bin/php \
        php; do
        if [ -x "$bin" ] 2>/dev/null || command -v "$bin" &>/dev/null; then
            TEST=$("$bin" -r "echo PHP_VERSION;" 2>/dev/null)
            if [ -z "$TEST" ]; then continue; fi
            PHP_VER=$(echo "$TEST" | tr -d "\r\n ")
            MAJOR=$(echo "$PHP_VER" | cut -d. -f1 | tr -dc "0-9")
            MINOR=$(echo "$PHP_VER" | cut -d. -f2 | tr -dc "0-9")
            if [ -n "$MAJOR" ] && [ -n "$MINOR" ]; then
                if [ "$MAJOR" -ge 8 ] && [ "$MINOR" -ge 2 ]; then
                    PHP="$bin"
                    return 0
                fi
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

# ── Detect web server user ────────────────────────────────────
detect_web_user() {
    WEB_USER=$(ps aux 2>/dev/null | grep -E '(apache|nginx|www-data|nobody|httpd|nobody)' \
        | grep -v grep | head -1 | awk '{print $1}')
    # On cPanel the owner of public_html is usually the account user
    if [ -z "$WEB_USER" ] || [ "$WEB_USER" = "root" ]; then
        # Try to detect from the parent directory owner
        WEB_USER=$(stat -c '%U' "$(pwd)" 2>/dev/null || echo "")
    fi
    if [ -z "$WEB_USER" ] || [ "$WEB_USER" = "root" ]; then
        WEB_USER="nobody"
    fi
}

# ── Step 0: Preflight ─────────────────────────────────────────
preflight() {
    step "Pre-flight Checks"

    # Must not be run as root in production — but warn rather than block
    if [ "$(whoami)" = "root" ]; then
        warn "Running as root — files will be owned by root. We'll fix ownership after install."
    fi

    # PHP
    if detect_php; then
        ok "PHP $PHP_VER found ($PHP)"
    else
        fail "PHP 8.2+ not found. Cannot continue."
        exit 1
    fi

    # Extensions
    for ext in pdo pdo_mysql mbstring openssl curl zip fileinfo; do
        if $PHP -r "echo extension_loaded('$ext') ? 'yes' : 'no';" 2>/dev/null | grep -q "yes"; then
            ok "PHP extension: $ext"
        else
            warn "PHP extension missing: $ext (may cause issues)"
        fi
    done

    # .env
    if [ -f ".env" ]; then
        warn ".env already exists — values will be overwritten"
    elif [ -f ".env.example" ]; then
        ok ".env.example found"
    else
        fail "No .env.example found. Run this from the RAYNET CMS root directory."
        exit 1
    fi

    # Writable check (attempt to fix)
    for dir in storage bootstrap/cache; do
        mkdir -p "$dir" 2>/dev/null
        chmod -R 775 "$dir" 2>/dev/null
        if [ -w "$dir" ]; then
            ok "Directory writable: $dir"
        else
            warn "Cannot make $dir writable — will attempt chown after install"
        fi
    done
}

# ── Step 1: Environment ───────────────────────────────────────
setup_env() {
    step "Environment Configuration"

    if [ ! -f ".env" ]; then
        cp .env.example .env
        ok "Created .env from .env.example"
    fi

    echo ""
    info "Enter your site details (press Enter for defaults where shown):"
    echo ""

    ask "Site URL (e.g. https://yourgroup.net):"
    read -r APP_URL
    APP_URL=${APP_URL:-https://example.com}

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

    ask "Mail host (e.g. mail.yourgroup.net) [optional]:"
    read -r MAIL_HOST

    ask "Mail from address (e.g. noreply@yourgroup.net) [optional]:"
    read -r MAIL_FROM

    ask "Mail username [leave blank if same as from address]:"
    read -r MAIL_USER
    MAIL_USER=${MAIL_USER:-$MAIL_FROM}

    ask "Mail password [optional]:"
    read -rs MAIL_PASS
    echo ""

    # Write to .env
    sed -i "s|APP_URL=.*|APP_URL=$APP_URL|g"           .env
    sed -i "s|DB_HOST=.*|DB_HOST=$DB_HOST|g"           .env
    sed -i "s|DB_DATABASE=.*|DB_DATABASE=$DB_DATABASE|g" .env
    sed -i "s|DB_USERNAME=.*|DB_USERNAME=$DB_USERNAME|g" .env
    sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=$DB_PASSWORD|g" .env
    [ -n "$MAIL_HOST" ] && sed -i "s|MAIL_HOST=.*|MAIL_HOST=$MAIL_HOST|g" .env
    [ -n "$MAIL_FROM" ] && sed -i "s|MAIL_FROM_ADDRESS=.*|MAIL_FROM_ADDRESS=\"$MAIL_FROM\"|g" .env
    [ -n "$MAIL_USER" ] && sed -i "s|MAIL_USERNAME=.*|MAIL_USERNAME=$MAIL_USER|g" .env
    [ -n "$MAIL_PASS" ] && sed -i "s|MAIL_PASSWORD=.*|MAIL_PASSWORD=$MAIL_PASS|g" .env

    ok ".env configured"
}

# ── Step 2: Composer ──────────────────────────────────────────
install_deps() {
    step "Installing PHP Dependencies"

    detect_composer

    if [ -z "$COMPOSER" ]; then
        info "Downloading composer.phar..."
        curl -sS https://getcomposer.org/installer | $PHP
        COMPOSER="$PHP composer.phar"
        ok "composer.phar downloaded"
    else
        ok "Composer found: $COMPOSER"
    fi

    info "Running composer install (this may take a minute)..."
    $COMPOSER install --no-dev --optimize-autoloader --no-interaction 2>&1 | grep -v "OPcache" | tail -5
    ok "Dependencies installed"
}

# ── Step 3: App key ───────────────────────────────────────────
generate_key() {
    step "Application Key"

    CURRENT_KEY=$(grep "^APP_KEY=" .env | cut -d= -f2 | tr -d '"')
    if [ -n "$CURRENT_KEY" ] && [ "$CURRENT_KEY" != "" ]; then
        warn "APP_KEY already set — skipping"
    else
        $PHP artisan key:generate --force 2>&1 | grep -v "OPcache"
        ok "Application key generated"
    fi
}

# ── Step 4: Database ──────────────────────────────────────────
run_migrations() {
    step "Database Setup"

    info "Running migrations..."
    $PHP artisan migrate --force 2>&1 | grep -v "OPcache"
    ok "Migrations complete"
}

# ── Step 5: Storage link ──────────────────────────────────────
setup_storage() {
    step "Storage Link"

    if [ -L "public/storage" ]; then
        rm public/storage
    fi
    $PHP artisan storage:link 2>&1 | grep -v "OPcache"
    ok "Storage link created"
}

# ── Step 6: Fix ownership & permissions ───────────────────────
fix_permissions() {
    step "File Permissions & Ownership"

    INSTALL_DIR=$(pwd)
    detect_web_user

    # Determine the account user from directory ownership
    ACCOUNT_USER=$(stat -c '%U' "$INSTALL_DIR" 2>/dev/null || echo "$(whoami)")
    if [ "$ACCOUNT_USER" = "root" ]; then
        # Try parent directory
        ACCOUNT_USER=$(stat -c '%U' "$(dirname $INSTALL_DIR)" 2>/dev/null || echo "nobody")
    fi

    info "Setting ownership to $ACCOUNT_USER..."
    chown -R "$ACCOUNT_USER":"$ACCOUNT_USER" . 2>/dev/null && \
        ok "Ownership set to $ACCOUNT_USER" || \
        warn "Could not set ownership — files may be owned by root"

    chmod -R 755 . 2>/dev/null
    chmod -R 775 storage bootstrap/cache 2>/dev/null && \
        ok "Directory permissions set (755/775)"
    find storage -type f -exec chmod 664 {} \; 2>/dev/null && \
        ok "File permissions set (664)"

    ok "Permissions complete"
}

# ── Step 7: Cache clear ───────────────────────────────────────
clear_caches() {
    step "Clearing Caches"
    $PHP artisan route:clear  2>&1 | grep -v "OPcache" && ok "Routes cleared"
    $PHP artisan view:clear   2>&1 | grep -v "OPcache" && ok "Views cleared"
    $PHP artisan config:clear 2>&1 | grep -v "OPcache" && ok "Config cleared"
    $PHP artisan cache:clear  2>&1 | grep -v "OPcache" && ok "Cache cleared"
}

# ── Step 8: Document root hint ────────────────────────────────
docroot_hint() {
    step "Web Server Configuration"

    INSTALL_DIR=$(pwd)
    PUBLIC_DIR="$INSTALL_DIR/public"

    echo ""
    info "Your document root must point to the public/ folder:"
    echo ""
    echo -e "  ${BOLD}$PUBLIC_DIR${NC}"
    echo ""
    info "In cPanel → Domains → find your domain → set Document Root to:"
    echo -e "  ${YELLOW}$PUBLIC_DIR${NC}"
    echo ""
    info "Or add this to your domain root .htaccess:"
    echo -e "  ${YELLOW}RewriteEngine On"
    echo -e "  RewriteRule ^(.*)$ public/\$1 [L]${NC}"
    echo ""

    # Try to auto-detect if there's a parent .htaccess we can write
    PARENT_DIR=$(dirname "$INSTALL_DIR")
    if [ -d "$PARENT_DIR" ] && [ -w "$PARENT_DIR" ]; then
        ask "Auto-create .htaccess in parent directory to redirect to public/? (y/N)"
        read -r AUTO_HTACCESS
        if [[ "$AUTO_HTACCESS" =~ ^[Yy]$ ]]; then
            FOLDER_NAME=$(basename "$INSTALL_DIR")
            cat > "$PARENT_DIR/.htaccess" << HTEOF
RewriteEngine On
RewriteRule ^(.*)$ ${FOLDER_NAME}/public/\$1 [L]
HTEOF
            ok "Created $PARENT_DIR/.htaccess → redirects to $FOLDER_NAME/public/"
        fi
    fi
}

# ── Step 9: Cron ──────────────────────────────────────────────
cron_reminder() {
    step "Scheduled Tasks (Optional)"
    echo ""
    INSTALL_DIR=$(pwd)
    echo -e "  ${YELLOW}* * * * * cd $INSTALL_DIR && $PHP artisan schedule:run >> /dev/null 2>&1${NC}"
    echo ""
    info "Add the above to cPanel → Cron Jobs"
    echo ""
}

# ── Final summary ─────────────────────────────────────────────
summary() {
    step "Installation Complete"
    echo ""
    echo -e "  ${GREEN}${BOLD}RAYNET CMS is ready!${NC}"
    echo ""
    echo -e "  ${CYAN}Next steps:${NC}"
    echo -e "  1. Make sure your domain points to: ${BOLD}$(pwd)/public${NC}"
    echo -e "  2. Visit ${BOLD}$APP_URL${NC} in your browser"
    echo -e "  3. The installation wizard will guide you through group setup"
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
    echo -e "  It will configure your environment, install dependencies,"
    echo -e "  run migrations, set permissions, and prepare the install wizard."
    echo ""
    echo -e "  ${YELLOW}Run this from your RAYNET CMS root directory.${NC}"
    echo ""
    ask "Ready to start? (y/N)"
    read -r CONFIRM
    if [[ ! "$CONFIRM" =~ ^[Yy]$ ]]; then
        echo "  Aborted."
        exit 0
    fi

    preflight
    setup_env
    install_deps
    generate_key
    run_migrations
    setup_storage
    fix_permissions
    clear_caches
    docroot_hint
    cron_reminder
    summary
}

main