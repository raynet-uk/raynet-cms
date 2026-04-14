@extends('layouts.app')
@section('title', 'Cookie Policy — Liverpool RAYNET')
@section('content')

<style>
.cookies-wrap { max-width: 800px; margin: 0 auto; padding: 2rem 1rem 4rem; }
.cookies-hero {
    background: #003366;
    border-bottom: 4px solid #C8102E;
    padding: 2.5rem 1.5rem;
    text-align: center;
    margin-bottom: 2.5rem;
}
.cookies-hero-eyebrow { font-size: 10px; font-weight: bold; color: rgba(255,255,255,.5); text-transform: uppercase; letter-spacing: .18em; margin-bottom: .5rem; }
.cookies-hero-title { font-size: 28px; font-weight: bold; color: #fff; margin-bottom: .5rem; }
.cookies-hero-sub { font-size: 13px; color: rgba(255,255,255,.6); }

.policy-card { background: #fff; border: 1px solid #dce4ee; border-top: 3px solid #003366; padding: 1.75rem; margin-bottom: 1.5rem; box-shadow: 0 1px 4px rgba(0,31,64,.07); }
.policy-card h2 { font-size: 15px; font-weight: bold; color: #003366; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 1rem; padding-bottom: .6rem; border-bottom: 1px solid #e8eef5; display: flex; align-items: center; gap: .5rem; }
.policy-card p { font-size: 13px; color: #2d4a6b; line-height: 1.7; margin-bottom: .85rem; }
.policy-card p:last-child { margin-bottom: 0; }
.policy-card ul { margin: .75rem 0 .75rem 1.25rem; display: flex; flex-direction: column; gap: .4rem; }
.policy-card li { font-size: 13px; color: #2d4a6b; line-height: 1.6; }
.policy-card strong { color: #001f40; }

.cookie-table-wrap { overflow-x: auto; margin-top: 1rem; }
.cookie-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.cookie-table thead { background: #002244; border-bottom: 2px solid #C8102E; }
.cookie-table th { padding: .5rem .85rem; text-align: left; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: .1em; color: rgba(255,255,255,.7); white-space: nowrap; }
.cookie-table tbody tr { border-bottom: 1px solid #e8eef5; transition: background .1s; }
.cookie-table tbody tr:hover { background: #f8fafc; }
.cookie-table tbody tr:last-child { border-bottom: none; }
.cookie-table td { padding: .6rem .85rem; vertical-align: top; color: #2d4a6b; }
.cookie-table td:first-child { font-family: monospace; font-size: 12px; font-weight: bold; color: #003366; white-space: nowrap; }
.cat-badge { display: inline-flex; align-items: center; font-size: 10px; font-weight: bold; padding: 1px 7px; border: 1px solid; text-transform: uppercase; letter-spacing: .04em; white-space: nowrap; }
.cat-necessary  { background: #e8eef5; border-color: rgba(0,51,102,.25); color: #003366; }
.cat-functional { background: #f0f8ff; border-color: rgba(2,136,209,.25); color: #0277bd; }
.cat-analytics  { background: #f5f3ff; border-color: rgba(91,33,182,.25); color: #5b21b6; }
.cat-legint     { background: #fffbeb; border-color: rgba(245,158,11,.4); color: #92400e; }

.li-box { background: #fffbeb; border: 1px solid #f59e0b; border-left: 3px solid #f59e0b; padding: 1rem; margin: 1rem 0; font-size: 13px; color: #78350f; line-height: 1.6; }
.li-box strong { color: #92400e; }

.manage-btn-wrap { text-align: center; padding: 1.5rem; background: #f2f5f9; border: 1px solid #dce4ee; margin-bottom: 1.5rem; }
.manage-btn-wrap p { font-size: 13px; color: #6b7f96; margin-bottom: .85rem; }
.manage-btn {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .6rem 1.4rem;
    background: #003366; border: 1px solid #003366; color: #fff;
    font-family: Arial, sans-serif; font-size: 13px; font-weight: bold;
    cursor: pointer; text-transform: uppercase; letter-spacing: .07em;
    transition: all .12s;
}
.manage-btn:hover { background: #004080; }

.last-updated { text-align: center; font-size: 11px; color: #9aa3ae; margin-top: 2rem; font-family: monospace; }
</style>

<div class="cookies-hero">
    <div class="cookies-hero-eyebrow">Liverpool RAYNET · Legal</div>
    <div class="cookies-hero-title">🍪 Cookie Policy</div>
    <div class="cookies-hero-sub">Last updated {{ now()->format('d F Y') }} · Applies to raynet-liverpool.net</div>
</div>

<div class="cookies-wrap">

    <div class="manage-btn-wrap">
        <p>You can review and change your cookie preferences at any time.</p>
        <button class="manage-btn" onclick="openCookieSettings()">⚙ Manage My Cookie Preferences</button>
    </div>

    <div class="policy-card">
        <h2>📋 What Are Cookies?</h2>
        <p>Cookies are small text files placed on your device when you visit a website. They allow the site to remember information about your visit and can be used for various purposes including keeping you logged in, remembering preferences, and gathering analytics data.</p>
        <p>We also use related technologies including local storage and session storage which work similarly to cookies.</p>
    </div>

    <div class="policy-card">
        <h2>⚖ Your Rights Under UK GDPR</h2>
        <p>Under the UK General Data Protection Regulation (UK GDPR) and the Privacy and Electronic Communications Regulations (PECR), you have the following rights regarding cookies:</p>
        <ul>
            <li><strong>Right to consent or refuse</strong> — You can accept or reject non-essential cookies.</li>
            <li><strong>Right to object to legitimate interest processing</strong> — Under Article 21 UK GDPR, you can formally object to any processing carried out under a "legitimate interest" legal basis. We must comply unless we can demonstrate compelling legitimate grounds that override your interests.</li>
            <li><strong>Right to withdraw consent</strong> — You can change your preferences at any time using the "Manage Preferences" button above.</li>
            <li><strong>Right to erasure</strong> — You can clear cookies at any time via your browser settings.</li>
        </ul>
        <p>We do not use dark patterns, pre-ticked boxes, or any mechanism designed to nudge you toward accepting cookies.</p>
    </div>

    <div class="policy-card">
        <h2>🍪 Cookies We Use</h2>
        <div class="cookie-table-wrap">
            <table class="cookie-table">
                <thead>
                    <tr>
                        <th>Cookie / Technology</th>
                        <th>Category</th>
                        <th>Purpose</th>
                        <th>Duration</th>
                        <th>Provider</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>raynet-session</td>
                        <td><span class="cat-badge cat-necessary">Necessary</span></td>
                        <td>Laravel session — keeps you logged in and stores flash messages</td>
                        <td>Session</td>
                        <td>First party</td>
                    </tr>
                    <tr>
                        <td>XSRF-TOKEN</td>
                        <td><span class="cat-badge cat-necessary">Necessary</span></td>
                        <td>Cross-site request forgery (CSRF) protection token</td>
                        <td>Session</td>
                        <td>First party</td>
                    </tr>
                    <tr>
                        <td>timezone</td>
                        <td><span class="cat-badge cat-necessary">Necessary</span></td>
                        <td>Stores your local timezone so dates display correctly</td>
                        <td>1 year</td>
                        <td>First party</td>
                    </tr>
                    <tr>
                        <td>session_locale</td>
                        <td><span class="cat-badge cat-functional">Functional</span></td>
                        <td>Remembers your language / locale preference</td>
                        <td>Session</td>
                        <td>First party</td>
                    </tr>
                    <tr>
                        <td>rn_cookie_consent</td>
                        <td><span class="cat-badge cat-necessary">Necessary</span></td>
                        <td>Stores your cookie consent choices (localStorage)</td>
                        <td>1 year</td>
                        <td>First party</td>
                    </tr>
                    <tr>
                        <td>_ga, _gid, _gat</td>
                        <td><span class="cat-badge cat-analytics">Analytics</span></td>
                        <td>Google Analytics — page view tracking and session analysis (only loaded with consent)</td>
                        <td>Up to 2 years</td>
                        <td>Google LLC</td>
                    </tr>
                    <tr>
                        <td>signalsafe.uk/condx.js</td>
                        <td><span class="cat-badge cat-legint">Leg. Interest</span></td>
                        <td>Third-party HF propagation data feed — your IP may be transmitted to their servers on load</td>
                        <td>Session</td>
                        <td>SignalSafe UK</td>
                    </tr>
                    <tr>
                        <td>radioid.net API</td>
                        <td><span class="cat-badge cat-legint">Leg. Interest</span></td>
                        <td>Amateur radio callsign lookups — callsign strings sent to RadioID servers</td>
                        <td>Session</td>
                        <td>RadioID.net</td>
                    </tr>
                    <tr>
                        <td>fonts.googleapis.com</td>
                        <td><span class="cat-badge cat-legint">Leg. Interest</span></td>
                        <td>Google Fonts — transmits your IP to Google to serve font files</td>
                        <td>1 year</td>
                        <td>Google LLC</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="policy-card" style="border-top-color:#f59e0b;">
        <h2>⚖ Legitimate Interest — Your Right to Object</h2>
        <div class="li-box">
            <strong>⚖ Important:</strong> Some third-party technologies on this site process data under a "legitimate interest" legal basis rather than requiring your explicit consent. Under <strong>UK GDPR Article 21</strong>, you have an absolute right to object to this processing. We respect that right and will not override it.
        </div>
        <p>When you use our cookie preference centre and toggle off "Legitimate Interest Claims", we treat this as a formal objection under Article 21 UK GDPR. We will not load those third-party resources for your session.</p>
        <p>The third parties claiming legitimate interest on this site are:</p>
        <ul>
            <li><strong>SignalSafe UK</strong> — propagation data feed (legitimate interest: operational data provision)</li>
            <li><strong>RadioID.net</strong> — callsign lookup API (legitimate interest: amateur radio identification services)</li>
            <li><strong>Google LLC (Fonts)</strong> — web font delivery (legitimate interest: website performance)</li>
        </ul>
        <p>You can object to these at any time via the preference centre. Your objection is recorded in your browser's local storage.</p>
    </div>

    <div class="policy-card">
        <h2>🔧 How to Control Cookies</h2>
        <p>In addition to our preference centre, you can control cookies through your browser:</p>
        <ul>
            <li><strong>Chrome:</strong> Settings → Privacy and Security → Cookies and other site data</li>
            <li><strong>Firefox:</strong> Settings → Privacy &amp; Security → Cookies and Site Data</li>
            <li><strong>Safari:</strong> Preferences → Privacy → Manage Website Data</li>
            <li><strong>Edge:</strong> Settings → Cookies and site permissions → Cookies and site data</li>
        </ul>
        <p>Note that blocking all cookies will prevent you from logging in to the members area.</p>
    </div>

    <div class="policy-card">
        <h2>📬 Contact</h2>
        <p>If you have questions about our use of cookies or wish to exercise your data rights, please contact the Liverpool RAYNET Group Controller via the <a href="{{ route('request-support') }}" style="color:#003366;font-weight:bold;">support request form</a> or via RAYNET-UK.</p>
        <p>For complaints about how we handle your data, you can contact the UK Information Commissioner's Office (ICO) at <a href="https://ico.org.uk" target="_blank" style="color:#003366;font-weight:bold;">ico.org.uk</a>.</p>
    </div>

    <p class="last-updated">Cookie Policy v1.0 · Liverpool RAYNET (Group 10/ME/179) · Affiliated to RAYNET-UK</p>

</div>

@endsection