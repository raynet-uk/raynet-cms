{{-- Cookie consent banner + settings modal --}}
<div id="cookieBanner" class="cookie-banner" role="dialog" aria-label="Cookie consent" aria-modal="true" style="display:none;">
    <div class="cb-inner">
        <div class="cb-left">
            <div class="cb-logo">🍪</div>
            <div>
                <div class="cb-title">Cookie &amp; Tracking Notice</div>
                <div class="cb-text">
                    We use cookies and similar technologies. Some are essential for the site to work.
                    Others are used for analytics or are claimed under "legitimate interest" — you can
                    reject all non-essential uses, including legitimate interest claims.
                    <a href="/cookies" class="cb-link">Cookie Policy</a>
                </div>
            </div>
        </div>
        <div class="cb-actions">
            <button class="cb-btn cb-btn-settings" onclick="openCookieSettings()">⚙ Manage</button>
            <button class="cb-btn cb-btn-reject"   onclick="rejectAllCookies()">✕ Reject All</button>
            <button class="cb-btn cb-btn-accept"   onclick="acceptAllCookies()">✓ Accept All</button>
        </div>
    </div>
</div>

{{-- Cookie settings modal --}}
<div id="cookieModal" class="cm-overlay" role="dialog" aria-label="Cookie settings" aria-modal="true" style="display:none;">
    <div class="cm-dialog">
        <div class="cm-header">
            <div class="cm-header-left">
                <div class="cm-logo-block"><span>RAY<br>NET</span></div>
                <div>
                    <div class="cm-title">Cookie Preferences</div>
                    <div class="cm-sub">{{ \App\Helpers\RaynetSetting::groupName() }} · {{ \App\Helpers\RaynetSetting::siteUrl() }}</div>
                </div>
            </div>
            <button class="cm-close" onclick="closeCookieSettings()" aria-label="Close">✕</button>
        </div>

        <div class="cm-body">

            <div class="cm-intro">
                <p>We believe in full transparency. Below is every category of cookie and tracking technology we use or may use.
                You can turn off anything that is not strictly necessary — including cookies justified under
                <strong>"legitimate interest"</strong>, which you have the right to object to under UK GDPR.</p>
            </div>

            {{-- Category: Strictly Necessary --}}
            <div class="cm-category">
                <div class="cm-cat-head">
                    <div class="cm-cat-info">
                        <div class="cm-cat-name">Strictly Necessary</div>
                        <div class="cm-cat-desc">Required for the site to function. Cannot be disabled. Includes session management, CSRF security tokens, and login state.</div>
                    </div>
                    <div class="cm-toggle-wrap">
                        <div class="cm-toggle cm-toggle-always">
                            <span class="cm-toggle-knob"></span>
                        </div>
                        <span class="cm-always-label">Always on</span>
                    </div>
                </div>
                <div class="cm-cookies-list">
                    <div class="cm-cookie-row">
                        <span class="cm-cookie-name">raynet-session</span>
                        <span class="cm-cookie-desc">Laravel session cookie — keeps you logged in</span>
                        <span class="cm-cookie-duration">Session</span>
                    </div>
                    <div class="cm-cookie-row">
                        <span class="cm-cookie-name">XSRF-TOKEN</span>
                        <span class="cm-cookie-desc">Cross-site request forgery protection</span>
                        <span class="cm-cookie-duration">Session</span>
                    </div>
                    <div class="cm-cookie-row">
                        <span class="cm-cookie-name">timezone</span>
                        <span class="cm-cookie-desc">Stores your detected timezone for display</span>
                        <span class="cm-cookie-duration">1 year</span>
                    </div>
                </div>
            </div>

            {{-- Category: Functional --}}
            <div class="cm-category">
                <div class="cm-cat-head">
                    <div class="cm-cat-info">
                        <div class="cm-cat-name">Functional / Preferences</div>
                        <div class="cm-cat-desc">Remembers your preferences such as dismissed notices and locale settings. Not used for tracking.</div>
                    </div>
                    <div class="cm-toggle-wrap">
                        <div class="cm-toggle" id="toggle-functional" onclick="toggleCategory('functional')">
                            <span class="cm-toggle-knob"></span>
                        </div>
                        <span class="cm-toggle-status" id="status-functional">Off</span>
                    </div>
                </div>
                <div class="cm-cookies-list">
                    <div class="cm-cookie-row">
                        <span class="cm-cookie-name">session_locale</span>
                        <span class="cm-cookie-desc">Remembers your language preference</span>
                        <span class="cm-cookie-duration">Session</span>
                    </div>
                    <div class="cm-cookie-row">
                        <span class="cm-cookie-name">dismissed_broadcast_id</span>
                        <span class="cm-cookie-desc">Remembers which broadcast notices you've dismissed</span>
                        <span class="cm-cookie-duration">30 days</span>
                    </div>
                </div>
            </div>

            {{-- Category: Analytics --}}
            <div class="cm-category">
                <div class="cm-cat-head">
                    <div class="cm-cat-info">
                        <div class="cm-cat-name">Analytics</div>
                        <div class="cm-cat-desc">Helps us understand how the site is used so we can improve it. May include third-party tools such as Google Analytics if enabled by the administrator.</div>
                    </div>
                    <div class="cm-toggle-wrap">
                        <div class="cm-toggle" id="toggle-analytics" onclick="toggleCategory('analytics')">
                            <span class="cm-toggle-knob"></span>
                        </div>
                        <span class="cm-toggle-status" id="status-analytics">Off</span>
                    </div>
                </div>
                <div class="cm-cookies-list">
                    <div class="cm-cookie-row">
                        <span class="cm-cookie-name">_ga, _gid</span>
                        <span class="cm-cookie-desc">Google Analytics — page view and session tracking (if enabled)</span>
                        <span class="cm-cookie-duration">2 years</span>
                    </div>
                    <div class="cm-cookie-row">
                        <span class="cm-cookie-name">_gat</span>
                        <span class="cm-cookie-desc">Google Analytics — throttle request rate</span>
                        <span class="cm-cookie-duration">1 minute</span>
                    </div>
                </div>
            </div>

            {{-- Category: Legitimate Interest --}}
            <div class="cm-category cm-cat-li">
                <div class="cm-cat-head">
                    <div class="cm-cat-info">
                        <div class="cm-cat-name">
                            Legitimate Interest Claims
                            <span class="cm-li-badge">⚖ You can object</span>
                        </div>
                        <div class="cm-cat-desc">
                            Some third-party scripts loaded on this site (e.g. propagation data feeds, external resources)
                            may process data under a "legitimate interest" legal basis. Under UK GDPR Article 21, you have the
                            right to object to this processing at any time. Rejecting here will block these scripts from loading.
                        </div>
                    </div>
                    <div class="cm-toggle-wrap">
                        <div class="cm-toggle" id="toggle-legint" onclick="toggleCategory('legint')">
                            <span class="cm-toggle-knob"></span>
                        </div>
                        <span class="cm-toggle-status" id="status-legint">Off</span>
                    </div>
                </div>
                <div class="cm-li-notice">
                    <strong>⚖ Legitimate interest notice:</strong> You have the absolute right to object to processing
                    under legitimate interest. Toggling this off constitutes a formal objection under UK GDPR Art. 21.
                    We will not override your objection.
                </div>
                <div class="cm-cookies-list">
                    <div class="cm-cookie-row">
                        <span class="cm-cookie-name">signalsafe.uk/condx.js</span>
                        <span class="cm-cookie-desc">HF propagation data feed — may set storage or send IP to third party</span>
                        <span class="cm-cookie-duration">Session</span>
                    </div>
                    <div class="cm-cookie-row">
                        <span class="cm-cookie-name">radioid.net API</span>
                        <span class="cm-cookie-desc">Amateur radio ID lookup — callsign queries sent to third-party server</span>
                        <span class="cm-cookie-duration">Session</span>
                    </div>
                    <div class="cm-cookie-row">
                        <span class="cm-cookie-name">fonts.googleapis.com</span>
                        <span class="cm-cookie-desc">Google Fonts — IP address transmitted to Google on load</span>
                        <span class="cm-cookie-duration">1 year</span>
                    </div>
                </div>
            </div>

        </div>

        <div class="cm-footer">
            <div class="cm-footer-links">
                <a href="/cookies">Cookie Policy</a>
                <a href="/privacy">Privacy Policy</a>
                <span class="cm-footer-version">Last updated: {{ now()->format('d M Y') }}</span>
            </div>
            <div class="cm-footer-actions">
                <button class="cm-btn cm-btn-reject" onclick="rejectAllCookies()">✕ Reject All</button>
                <button class="cm-btn cm-btn-save"   onclick="savePreferences()">✓ Save Preferences</button>
                <button class="cm-btn cm-btn-accept" onclick="acceptAllCookies()">✓ Accept All</button>
            </div>
        </div>
    </div>
</div>

<style>
/* ════════════════════════════════════════
   COOKIE BANNER
════════════════════════════════════════ */
.cookie-banner {
    position: fixed;
    bottom: 0; left: 0; right: 0;
    z-index: 9000;
    background: var(--navy, #003366);
    border-top: 3px solid var(--red, #C8102E);
    box-shadow: 0 -4px 24px rgba(0,0,0,.25);
    animation: slideUp .35s ease;
}
@keyframes slideUp { from { transform: translateY(100%); opacity: 0; } to { transform: none; opacity: 1; } }

.cb-inner {
    max-width: 1320px;
    margin: 0 auto;
    padding: .85rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
    flex-wrap: wrap;
}
.cb-left {
    display: flex;
    align-items: flex-start;
    gap: .85rem;
    flex: 1;
    min-width: 0;
}
.cb-logo { font-size: 1.3rem; flex-shrink: 0; margin-top: 2px; }
.cb-title { font-size: 13px; font-weight: bold; color: #fff; margin-bottom: .2rem; text-transform: uppercase; letter-spacing: .06em; }
.cb-text { font-size: 12px; color: rgba(255,255,255,.7); line-height: 1.55; }
.cb-link { color: rgba(255,255,255,.85); font-weight: bold; text-underline-offset: 2px; }
.cb-link:hover { color: #fff; }

.cb-actions {
    display: flex;
    align-items: center;
    gap: .5rem;
    flex-shrink: 0;
    flex-wrap: wrap;
}
.cb-btn {
    padding: .45rem 1.1rem;
    font-family: Arial, sans-serif;
    font-size: 12px;
    font-weight: bold;
    cursor: pointer;
    text-transform: uppercase;
    letter-spacing: .06em;
    border: 1px solid;
    transition: all .12s;
    white-space: nowrap;
}
.cb-btn-settings { background: transparent; border-color: rgba(255,255,255,.3); color: rgba(255,255,255,.8); }
.cb-btn-settings:hover { border-color: rgba(255,255,255,.6); color: #fff; }
.cb-btn-reject { background: transparent; border-color: rgba(200,16,46,.6); color: #fca5a5; }
.cb-btn-reject:hover { background: rgba(200,16,46,.15); border-color: #C8102E; color: #fff; }
.cb-btn-accept { background: #1a7a3c; border-color: #1a7a3c; color: #fff; }
.cb-btn-accept:hover { background: #145c2e; }

@media(max-width:700px) {
    .cb-inner { padding: .85rem 1rem; }
    .cb-actions { width: 100%; justify-content: flex-end; }
}

/* ════════════════════════════════════════
   COOKIE MODAL OVERLAY
════════════════════════════════════════ */
.cm-overlay {
    position: fixed;
    inset: 0;
    z-index: 9100;
    background: rgba(0,0,0,.65);
    display: flex;
    align-items: flex-end;
    justify-content: center;
    padding: 0;
    animation: fadeIn .2s ease;
}
@media(min-width:640px) { .cm-overlay { align-items: center; padding: 1rem; } }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

.cm-dialog {
    background: #fff;
    width: 100%;
    max-width: 680px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    border-top: 4px solid #C8102E;
    box-shadow: 0 20px 60px rgba(0,0,0,.4);
    animation: slideUpModal .3s ease;
}
@keyframes slideUpModal { from { transform: translateY(20px); opacity: 0; } to { transform: none; opacity: 1; } }

/* Header */
.cm-header {
    background: #003366;
    padding: .85rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.cm-header-left { display: flex; align-items: center; gap: .85rem; }
.cm-logo-block {
    background: #C8102E;
    width: 36px; height: 36px;
    display: flex; align-items: center; justify-content: center;
    font-size: 9px; font-weight: bold; color: #fff;
    letter-spacing: .06em; text-align: center; line-height: 1.2; text-transform: uppercase;
    flex-shrink: 0;
}
.cm-title { font-size: 14px; font-weight: bold; color: #fff; letter-spacing: .04em; text-transform: uppercase; }
.cm-sub { font-size: 10px; color: rgba(255,255,255,.5); margin-top: 1px; text-transform: uppercase; letter-spacing: .05em; }
.cm-close {
    background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2);
    color: rgba(255,255,255,.8); font-size: 14px; font-weight: bold;
    width: 30px; height: 30px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all .12s; flex-shrink: 0;
    font-family: Arial, sans-serif;
}
.cm-close:hover { background: rgba(255,255,255,.2); color: #fff; }

/* Body */
.cm-body {
    overflow-y: auto;
    flex: 1;
    padding: 0;
}
.cm-intro {
    padding: 1rem 1.25rem;
    background: #e8eef5;
    border-bottom: 1px solid #dce4ee;
    font-size: 12px;
    color: #2d4a6b;
    line-height: 1.6;
}

/* Category */
.cm-category {
    border-bottom: 1px solid #e8eef5;
}
.cm-category:last-child { border-bottom: none; }
.cm-cat-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem 1.25rem .75rem;
}
.cm-cat-info { flex: 1; min-width: 0; }
.cm-cat-name {
    font-size: 13px;
    font-weight: bold;
    color: #003366;
    margin-bottom: .3rem;
    display: flex;
    align-items: center;
    gap: .5rem;
    flex-wrap: wrap;
}
.cm-cat-desc { font-size: 12px; color: #6b7f96; line-height: 1.55; }

.cm-cat-li .cm-cat-head { background: #fff8f0; }
.cm-cat-li .cm-cat-name { color: #92400e; }
.cm-li-badge {
    display: inline-flex;
    font-size: 9px;
    font-weight: bold;
    padding: 1px 7px;
    background: #fffbeb;
    border: 1px solid #f59e0b;
    color: #92400e;
    text-transform: uppercase;
    letter-spacing: .05em;
    align-self: center;
}
.cm-li-notice {
    margin: 0 1.25rem .75rem;
    padding: .65rem .85rem;
    background: #fffbeb;
    border: 1px solid #f59e0b;
    border-left: 3px solid #f59e0b;
    font-size: 11px;
    color: #78350f;
    line-height: 1.55;
}

/* Toggle */
.cm-toggle-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .3rem;
    flex-shrink: 0;
}
.cm-toggle {
    width: 42px; height: 24px;
    border-radius: 12px;
    background: #dce4ee;
    position: relative;
    cursor: pointer;
    transition: background .2s;
    border: 1px solid #c8d4e0;
    flex-shrink: 0;
}
.cm-toggle.on { background: #003366; border-color: #003366; }
.cm-toggle-always { background: #1a7a3c !important; border-color: #1a7a3c !important; cursor: not-allowed; }
.cm-toggle-knob {
    position: absolute;
    top: 3px; left: 3px;
    width: 16px; height: 16px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,.2);
    transition: transform .2s;
}
.cm-toggle.on .cm-toggle-knob,
.cm-toggle-always .cm-toggle-knob { transform: translateX(18px); }
.cm-toggle-status {
    font-size: 10px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: #9aa3ae;
}
.cm-toggle.on + .cm-toggle-status { color: #003366; }
.cm-always-label { font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: .08em; color: #1a7a3c; }

/* Cookies list */
.cm-cookies-list {
    padding: 0 1.25rem .85rem;
    display: flex;
    flex-direction: column;
    gap: .3rem;
}
.cm-cookie-row {
    display: flex;
    align-items: center;
    gap: .65rem;
    padding: .4rem .65rem;
    background: #f8fafc;
    border: 1px solid #e8eef5;
    font-size: 11px;
    flex-wrap: wrap;
}
.cm-cookie-name {
    font-weight: bold;
    color: #003366;
    font-family: monospace;
    font-size: 11px;
    white-space: nowrap;
    flex-shrink: 0;
}
.cm-cookie-desc { color: #6b7f96; flex: 1; min-width: 0; }
.cm-cookie-duration {
    font-size: 10px;
    font-weight: bold;
    color: #9aa3ae;
    background: #e8eef5;
    padding: 1px 6px;
    white-space: nowrap;
    flex-shrink: 0;
    font-family: monospace;
}

/* Footer */
.cm-footer {
    padding: .85rem 1.25rem;
    border-top: 1px solid #dce4ee;
    background: #f2f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
    flex-shrink: 0;
}
.cm-footer-links {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}
.cm-footer-links a {
    font-size: 11px;
    font-weight: bold;
    color: #003366;
    text-decoration: none;
}
.cm-footer-links a:hover { color: #C8102E; text-decoration: underline; }
.cm-footer-version { font-size: 10px; color: #9aa3ae; font-family: monospace; }
.cm-footer-actions { display: flex; gap: .5rem; flex-wrap: wrap; }
.cm-btn {
    padding: .42rem 1rem;
    font-family: Arial, sans-serif;
    font-size: 11px;
    font-weight: bold;
    cursor: pointer;
    text-transform: uppercase;
    letter-spacing: .06em;
    border: 1px solid;
    transition: all .12s;
    white-space: nowrap;
}
.cm-btn-reject { background: transparent; border-color: rgba(200,16,46,.4); color: #C8102E; }
.cm-btn-reject:hover { background: #fdf0f2; border-color: #C8102E; }
.cm-btn-save { background: #003366; border-color: #003366; color: #fff; }
.cm-btn-save:hover { background: #004080; }
.cm-btn-accept { background: #1a7a3c; border-color: #1a7a3c; color: #fff; }
.cm-btn-accept:hover { background: #145c2e; }

@media(max-width:500px) {
    .cm-footer { flex-direction: column; align-items: stretch; }
    .cm-footer-actions { justify-content: stretch; }
    .cm-btn { flex: 1; text-align: center; justify-content: center; }
    .cm-cat-head { flex-direction: column; }
    .cm-toggle-wrap { flex-direction: row; align-items: center; }
}
</style>

<script>
/* ════════════════════════════════════════
   Cookie consent logic
════════════════════════════════════════ */
const COOKIE_KEY    = 'rn_cookie_consent';
const COOKIE_EXPIRY = 365; // days

const DEFAULTS = {
    necessary:   true,   // always
    functional:  false,
    analytics:   false,
    legint:      false,  // legitimate interest — off by default
};

function getCookieConsent() {
    const raw = localStorage.getItem(COOKIE_KEY);
    if (!raw) return null;
    try { return JSON.parse(raw); } catch { return null; }
}

function setCookieConsent(prefs) {
    prefs.necessary  = true; // always true
    prefs.timestamp  = new Date().toISOString();
    prefs.version    = '1.0';
    localStorage.setItem(COOKIE_KEY, JSON.stringify(prefs));
    applyConsent(prefs);
    hideBanner();
    closeModal();
}

function applyConsent(prefs) {
    // Fire GTM / analytics only if consented
    if (prefs.analytics && typeof gtag === 'function') {
        gtag('consent', 'update', { analytics_storage: 'granted' });
    }
    // Dispatch event so other scripts can listen
    window.dispatchEvent(new CustomEvent('cookieConsentUpdated', { detail: prefs }));
}

function showBanner() {
    const el = document.getElementById('cookieBanner');
    if (el) el.style.display = 'block';
}
function hideBanner() {
    const el = document.getElementById('cookieBanner');
    if (el) el.style.display = 'none';
}

function acceptAllCookies() {
    setCookieConsent({ necessary: true, functional: true, analytics: true, legint: true });
}

function rejectAllCookies() {
    setCookieConsent({ necessary: true, functional: false, analytics: false, legint: false });
}

function openCookieSettings() {
    hideBanner();
    const existing = getCookieConsent() || DEFAULTS;
    // Reflect current state on toggles
    ['functional','analytics','legint'].forEach(cat => {
        setToggleState(cat, existing[cat] || false);
    });
    const modal = document.getElementById('cookieModal');
    if (modal) modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeCookieSettings() {
    closeModal();
    // If no consent yet, show banner again
    if (!getCookieConsent()) showBanner();
}

function closeModal() {
    const modal = document.getElementById('cookieModal');
    if (modal) modal.style.display = 'none';
    document.body.style.overflow = '';
}

function toggleCategory(cat) {
    const toggle = document.getElementById('toggle-' + cat);
    const isOn   = toggle.classList.contains('on');
    setToggleState(cat, !isOn);
}

function setToggleState(cat, on) {
    const toggle = document.getElementById('toggle-' + cat);
    const status = document.getElementById('status-' + cat);
    if (!toggle || !status) return;
    toggle.classList.toggle('on', on);
    status.textContent = on ? 'On' : 'Off';
    status.style.color  = on ? '#003366' : '#9aa3ae';
}

function savePreferences() {
    const prefs = {
        necessary:  true,
        functional: document.getElementById('toggle-functional')?.classList.contains('on') || false,
        analytics:  document.getElementById('toggle-analytics')?.classList.contains('on')  || false,
        legint:     document.getElementById('toggle-legint')?.classList.contains('on')     || false,
    };
    setCookieConsent(prefs);
    // Brief confirmation
    const saveBtn = document.querySelector('.cm-btn-save');
    if (saveBtn) {
        const orig = saveBtn.textContent;
        saveBtn.textContent = '✓ Saved!';
        saveBtn.style.background = '#1a7a3c';
        setTimeout(() => { saveBtn.textContent = orig; saveBtn.style.background = ''; }, 1500);
    }
}

// Close modal on backdrop click
document.getElementById('cookieModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeCookieSettings();
});

// Escape key
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        if (document.getElementById('cookieModal')?.style.display !== 'none') {
            closeCookieSettings();
        }
    }
});

// On load — show banner if no consent yet
document.addEventListener('DOMContentLoaded', () => {
    const consent = getCookieConsent();
    if (!consent) {
        showBanner();
    } else {
        applyConsent(consent);
    }
});

// Expose a global for the "Manage cookies" footer link
window.openCookieSettings = openCookieSettings;
</script>