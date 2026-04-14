@extends('layouts.app')
@section('title', 'Privacy Notice — Liverpool RAYNET')
@section('content')

<style>
.privacy-hero {
    background: var(--navy);
    border-bottom: 4px solid var(--red);
    padding: 2.5rem 1.5rem;
    text-align: center;
    margin-bottom: 2rem;
}
.privacy-hero-eyebrow {
    font-size: 10px; font-weight: bold; color: rgba(255,255,255,.5);
    text-transform: uppercase; letter-spacing: .2em; margin-bottom: .5rem;
}
.privacy-hero-title { font-size: 26px; font-weight: bold; color: #fff; margin-bottom: .5rem; }
.privacy-hero-meta {
    display: flex; align-items: center; justify-content: center;
    gap: 1rem; flex-wrap: wrap; margin-top: .75rem;
}
.privacy-hero-chip {
    display: inline-flex; align-items: center; gap: .35rem;
    font-size: 11px; font-weight: bold; padding: .25rem .75rem;
    border: 1px solid rgba(255,255,255,.2); color: rgba(255,255,255,.7);
    text-transform: uppercase; letter-spacing: .06em;
}

.privacy-wrap { max-width: 860px; margin: 0 auto; padding: 0 1rem 4rem; }

/* Contact card */
.privacy-contact-card {
    background: var(--navy-faint); border: 1px solid var(--grey-mid);
    border-left: 4px solid var(--navy);
    padding: 1rem 1.25rem; margin-bottom: 1.5rem;
    display: flex; align-items: flex-start; gap: 1rem; flex-wrap: wrap;
}
.pcc-icon { font-size: 1.5rem; flex-shrink: 0; margin-top: 2px; }
.pcc-title { font-size: 13px; font-weight: bold; color: var(--navy); margin-bottom: .3rem; }
.pcc-detail { font-size: 12px; color: var(--text-mid); line-height: 1.7; }
.pcc-detail a { color: var(--red); text-decoration: none; font-weight: bold; }
.pcc-detail a:hover { text-decoration: underline; }
.ico-badge {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .4rem .9rem; border: 1px solid var(--grey-mid);
    background: #fff; font-size: 11px; font-weight: bold; color: var(--navy);
}

/* Table of contents */
.privacy-toc {
    background: #fff; border: 1px solid var(--grey-mid);
    border-left: 4px solid var(--navy);
    padding: 1.25rem 1.5rem; margin-bottom: 2rem;
    box-shadow: var(--shadow-sm);
}
.privacy-toc-title {
    font-size: 11px; font-weight: bold; text-transform: uppercase;
    letter-spacing: .12em; color: var(--navy); margin-bottom: .85rem;
    display: flex; align-items: center; gap: .5rem;
}
.privacy-toc-title::before { content: ''; width: 14px; height: 2px; background: var(--red); display: inline-block; }
.privacy-toc-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: .25rem; }
.privacy-toc-link {
    display: flex; align-items: center; gap: .5rem;
    font-size: 12px; font-weight: 500; color: var(--text-mid);
    text-decoration: none; padding: .3rem .5rem; transition: all .15s;
}
.privacy-toc-link:hover { color: var(--navy); background: var(--navy-faint); }
.privacy-toc-num { font-size: 10px; font-weight: bold; color: var(--text-muted); min-width: 20px; text-align: right; flex-shrink: 0; }

/* Sections */
.privacy-section {
    background: #fff; border: 1px solid var(--grey-mid);
    border-top: 3px solid var(--navy);
    margin-bottom: 1rem; box-shadow: var(--shadow-sm); overflow: hidden;
}
.privacy-section-head {
    padding: .85rem 1.25rem; background: var(--grey);
    border-bottom: 1px solid var(--grey-mid);
    display: flex; align-items: center; gap: .75rem;
    cursor: pointer; user-select: none; transition: background .12s;
}
.privacy-section-head:hover { background: var(--navy-faint); }
.psec-num {
    width: 26px; height: 26px; background: var(--navy); color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: bold; flex-shrink: 0;
}
.psec-title { font-size: 13px; font-weight: bold; color: var(--navy); text-transform: uppercase; letter-spacing: .05em; flex: 1; }
.psec-chevron { font-size: 10px; color: var(--text-muted); transition: transform .2s; flex-shrink: 0; }
.psec-chevron.open { transform: rotate(180deg); }

.privacy-section-body {
    padding: 1.25rem 1.5rem;
    font-size: 13px; color: var(--text-mid); line-height: 1.75;
    display: none;
}
.privacy-section-body.open { display: block; }
.privacy-section-body p { margin-bottom: .85rem; }
.privacy-section-body p:last-child { margin-bottom: 0; }
.privacy-section-body ul { margin: .65rem 0 .85rem 0; display: flex; flex-direction: column; gap: .3rem; }
.privacy-section-body li {
    display: flex; align-items: flex-start; gap: .6rem;
    font-size: 13px; color: var(--text-mid); line-height: 1.6;
}
.privacy-section-body li::before { content: '·'; color: var(--red); font-weight: bold; flex-shrink: 0; margin-top: 1px; }
.privacy-section-body strong { color: var(--navy); font-weight: bold; }
.privacy-section-body a { color: var(--red); text-decoration: none; font-weight: bold; }
.privacy-section-body a:hover { text-decoration: underline; }

/* Info / warn boxes */
.priv-info {
    display: flex; align-items: flex-start; gap: .65rem;
    padding: .75rem 1rem; margin: .85rem 0;
    background: var(--navy-faint); border: 1px solid rgba(0,51,102,.2);
    border-left: 3px solid var(--navy);
    font-size: 12px; color: var(--text-mid); line-height: 1.6;
}
.priv-warn {
    display: flex; align-items: flex-start; gap: .65rem;
    padding: .75rem 1rem; margin: .85rem 0;
    background: #fffbeb; border: 1px solid #f59e0b;
    border-left: 3px solid #f59e0b;
    font-size: 12px; color: #78350f; line-height: 1.6;
}
.priv-box-icon { font-size: 14px; flex-shrink: 0; margin-top: 1px; }

/* Retention table */
.retention-table { width: 100%; border-collapse: collapse; font-size: 12px; margin: .75rem 0; }
.retention-table thead { background: var(--navy); }
.retention-table th {
    padding: .5rem .85rem; text-align: left;
    font-size: 10px; font-weight: bold; text-transform: uppercase;
    letter-spacing: .1em; color: rgba(255,255,255,.7);
}
.retention-table tbody tr { border-bottom: 1px solid var(--grey-mid); transition: background .1s; }
.retention-table tbody tr:last-child { border-bottom: none; }
.retention-table tbody tr:hover { background: var(--navy-faint); }
.retention-table td { padding: .55rem .85rem; vertical-align: top; color: var(--text-mid); }
.retention-table td:first-child { font-weight: 600; color: var(--text); }
.retention-table td:last-child { color: var(--text-muted); white-space: nowrap; }
.retention-wrap { overflow-x: auto; }

/* Page footer */
.privacy-page-footer {
    text-align: center; padding: 1.5rem 0 0;
    font-size: 11px; color: var(--text-muted);
    border-top: 1px solid var(--grey-mid); margin-top: 1.5rem;
}
.privacy-page-footer a { color: var(--navy); font-weight: bold; text-decoration: none; }
.privacy-page-footer a:hover { color: var(--red); }

@media(max-width:600px) {
    .privacy-hero { padding: 1.75rem 1rem; }
    .privacy-hero-title { font-size: 20px; }
    .privacy-section-body { padding: 1rem; }
    .privacy-toc-grid { grid-template-columns: 1fr; }
    .retention-table td:last-child { white-space: normal; }
}
</style>

{{-- Hero --}}
<div class="privacy-hero">
    <div class="privacy-hero-eyebrow">Liverpool RAYNET · Legal</div>
    <h1 class="privacy-hero-title">🔒 Privacy Notice &amp; Data Protection Policy</h1>
    <div class="privacy-hero-meta">
        <span class="privacy-hero-chip">📅 Last updated: 17 March 2026</span>
        <span class="privacy-hero-chip">🏛 UK GDPR · DPA 2018 · PECR</span>
    </div>
</div>

<div class="privacy-wrap">

    {{-- Contact card --}}
    <div class="privacy-contact-card">
        <span class="pcc-icon">👤</span>
        <div style="flex:1;">
            <div class="pcc-title">Data Controller</div>
            <div class="pcc-detail">
                <strong>Ian Jones</strong> · Group Controller, Liverpool RAYNET Group<br>
                Email: <a href="mailto:GC.liverpool@raynet-uk.net">GC.liverpool@raynet-uk.net</a> &nbsp;·&nbsp;
                Website: <a href="https://raynet-liverpool.net">raynet-liverpool.net</a>
            </div>
        </div>
        <div style="flex-shrink:0;display:flex;align-items:center;">
            <span class="ico-badge">🏛 Registered with the ICO</span>
        </div>
    </div>

    {{-- Table of contents --}}
    <div class="privacy-toc">
        <div class="privacy-toc-title">Contents</div>
        <div class="privacy-toc-grid">
            <a href="#s1"  class="privacy-toc-link"><span class="privacy-toc-num">1.</span> Introduction</a>
            <a href="#s2"  class="privacy-toc-link"><span class="privacy-toc-num">2.</span> Who We Are</a>
            <a href="#s3"  class="privacy-toc-link"><span class="privacy-toc-num">3.</span> Separate Role of RAYNET-UK</a>
            <a href="#s4"  class="privacy-toc-link"><span class="privacy-toc-num">4.</span> Scope of This Policy</a>
            <a href="#s5"  class="privacy-toc-link"><span class="privacy-toc-num">5.</span> Personal Data We Collect</a>
            <a href="#s6"  class="privacy-toc-link"><span class="privacy-toc-num">6.</span> Where We Get Personal Data From</a>
            <a href="#s7"  class="privacy-toc-link"><span class="privacy-toc-num">7.</span> Why We Use Personal Data</a>
            <a href="#s8"  class="privacy-toc-link"><span class="privacy-toc-num">8.</span> Lawful Bases for Processing</a>
            <a href="#s9"  class="privacy-toc-link"><span class="privacy-toc-num">9.</span> Special Category Data</a>
            <a href="#s10" class="privacy-toc-link"><span class="privacy-toc-num">10.</span> Live Location &amp; APRS</a>
            <a href="#s11" class="privacy-toc-link"><span class="privacy-toc-num">11.</span> Photographs &amp; Publicity</a>
            <a href="#s12" class="privacy-toc-link"><span class="privacy-toc-num">12.</span> Emergency Contact Details</a>
            <a href="#s13" class="privacy-toc-link"><span class="privacy-toc-num">13.</span> Finance &amp; Reimbursement</a>
            <a href="#s14" class="privacy-toc-link"><span class="privacy-toc-num">14.</span> Who We Share Data With</a>
            <a href="#s15" class="privacy-toc-link"><span class="privacy-toc-num">15.</span> Processors &amp; Third-Party Services</a>
            <a href="#s16" class="privacy-toc-link"><span class="privacy-toc-num">16.</span> International Transfers</a>
            <a href="#s17" class="privacy-toc-link"><span class="privacy-toc-num">17.</span> Retention</a>
            <a href="#s18" class="privacy-toc-link"><span class="privacy-toc-num">18.</span> Cookies &amp; Analytics</a>
            <a href="#s19" class="privacy-toc-link"><span class="privacy-toc-num">19.</span> Operational Emails</a>
            <a href="#s20" class="privacy-toc-link"><span class="privacy-toc-num">20.</span> Security</a>
            <a href="#s21" class="privacy-toc-link"><span class="privacy-toc-num">21.</span> Data Breaches</a>
            <a href="#s22" class="privacy-toc-link"><span class="privacy-toc-num">22.</span> DPIAs</a>
            <a href="#s23" class="privacy-toc-link"><span class="privacy-toc-num">23.</span> Your Rights</a>
            <a href="#s24" class="privacy-toc-link"><span class="privacy-toc-num">24.</span> If You Don't Provide Data</a>
            <a href="#s25" class="privacy-toc-link"><span class="privacy-toc-num">25.</span> Automated Decision-Making</a>
            <a href="#s26" class="privacy-toc-link"><span class="privacy-toc-num">26.</span> Review of This Policy</a>
            <a href="#s27" class="privacy-toc-link"><span class="privacy-toc-num">27.</span> Complaints</a>
        </div>
    </div>

    {{-- Section 1 --}}
    <div class="privacy-section" id="s1">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">1</div>
            <div class="psec-title">Introduction</div>
            <span class="psec-chevron open">▼</span>
        </div>
        <div class="privacy-section-body open">
            <p>Liverpool RAYNET Group is a voluntary emergency communications organisation. We support events, exercises, incidents, training, and community resilience activity. In doing so, we handle personal data relating to adult members, adult volunteers, and adult third-party contacts.</p>
            <p>We are committed to processing personal data lawfully, fairly, securely, and transparently in accordance with the <strong>UK General Data Protection Regulation</strong>, the <strong>Data Protection Act 2018</strong>, and the <strong>Privacy and Electronic Communications Regulations</strong> where applicable.</p>
            <p>This document serves as both Liverpool RAYNET Group's privacy notice and its general data protection policy.</p>
            <div class="priv-info"><span class="priv-box-icon">ℹ️</span><span>This policy is intended for adult members and adult third parties only. It is not intended for children.</span></div>
        </div>
    </div>

    {{-- Section 2 --}}
    <div class="privacy-section" id="s2">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">2</div>
            <div class="psec-title">Who We Are</div>
            <span class="psec-chevron">▼</span>
        </div>
        <div class="privacy-section-body">
            <p>Liverpool RAYNET Group, acting through its Management Committee, is the data controller for the personal data covered by this policy.</p>
            <p><strong>Data Controller contact:</strong><br>
            Ian Jones, Group Controller<br>
            Email: <a href="mailto:GC.liverpool@raynet-uk.net">GC.liverpool@raynet-uk.net</a><br>
            Website: <a href="https://raynet-liverpool.net">raynet-liverpool.net</a></p>
            <p>Liverpool RAYNET Group is registered with the Information Commissioner's Office.</p>
        </div>
    </div>

    {{-- Section 3 --}}
    <div class="privacy-section" id="s3">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">3</div>
            <div class="psec-title">Separate Role of RAYNET-UK</div>
            <span class="psec-chevron">▼</span>
        </div>
        <div class="privacy-section-body">
            <p>RAYNET-UK operates its own website, email system, and member management system under its own control. <strong>RAYNET-UK is therefore a separate data controller</strong> for that processing. Members should refer to RAYNET-UK's own privacy information for the way RAYNET-UK collects, stores, and uses personal data through its own systems.</p>
            <div class="priv-info"><span class="priv-box-icon">📋</span><span>Liverpool RAYNET Group does not provide identifiable member data to RAYNET-UK as part of routine annual reporting. It provides only aggregated information such as total hours and total events where required.</span></div>
        </div>
    </div>

    {{-- Section 4 --}}
    <div class="privacy-section" id="s4">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">4</div>
            <div class="psec-title">Scope of This Policy</div>
            <span class="psec-chevron">▼</span>
        </div>
        <div class="privacy-section-body">
            <p>This policy applies to personal data processed by Liverpool RAYNET Group in connection with:</p>
            <ul>
                <li>membership administration at group level</li>
                <li>training and learning administration</li>
                <li>event, exercise, and incident coordination</li>
                <li>operational communications and deployment</li>
                <li>finance and expense reimbursement</li>
                <li>photographs and publicity</li>
                <li>website cookies, analytics, embedded media, and social-media plugins</li>
            </ul>
        </div>
    </div>

    {{-- Section 5 --}}
    <div class="privacy-section" id="s5">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">5</div>
            <div class="psec-title">Personal Data We Collect</div>
            <span class="psec-chevron">▼</span>
        </div>
        <div class="privacy-section-body">
            <p>Liverpool RAYNET Group may collect, store, and use the following categories of personal data about members and volunteers:</p>
            <ul>
                <li>name</li>
                <li>callsign</li>
                <li>DMR ID</li>
                <li>postal address</li>
                <li>email address</li>
                <li>telephone number</li>
                <li>date of birth</li>
                <li>emergency contact details</li>
                <li>vehicle details, including vehicle registration where relevant</li>
                <li>qualifications and training records</li>
                <li>attendance records</li>
                <li>deployment logs</li>
                <li>incident notes and SITREPs</li>
                <li>health or welfare information where operationally necessary</li>
                <li>photographs</li>
                <li>APRS location data and live location data</li>
            </ul>
            <p>We may also hold limited personal data about adult third parties such as marshals, first aiders, welfare staff, organisers, and agency contacts, including their name, email address, and mobile number where needed for event or operational contact.</p>
            <p>We may also process finance-related personal data, including payer name, transaction reference, amount paid, expense claims, invoices, and bank details used for reimbursement.</p>
            <div class="priv-warn"><span class="priv-box-icon">⚠</span><span>Where our website uses cookies, analytics, embedded media, or social-media plugins, technical data may also be processed through those tools where consent has been given. See Section 18 for details.</span></div>
        </div>
    </div>

    {{-- Section 6 --}}
    <div class="privacy-section" id="s6">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">6</div>
            <div class="psec-title">Where We Get Personal Data From</div>
            <span class="psec-chevron">▼</span>
        </div>
        <div class="privacy-section-body">
            <p>We normally collect personal data directly from the individual concerned.</p>
            <p>We may also receive limited personal data from RAYNET-UK where this is relevant to group administration or operations.</p>
        </div>
    </div>

    {{-- Section 7 --}}
    <div class="privacy-section" id="s7">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">7</div>
            <div class="psec-title">Why We Use Personal Data</div>
            <span class="psec-chevron">▼</span>
        </div>
        <div class="privacy-section-body">
            <p>Liverpool RAYNET Group uses personal data for the following purposes:</p>
            <ul>
                <li>to maintain group-level membership and contact records</li>
                <li>to contact members for operational deployment, event duties, meetings, and training</li>
                <li>to administer qualifications, competence, attendance, and learning records</li>
                <li>to coordinate events, exercises, and emergency communications activity</li>
                <li>to maintain deployment logs, incident notes, and SITREPs</li>
                <li>to manage insurance-related and governance-related records</li>
                <li>to support welfare and safe deployment decisions</li>
                <li>to reimburse legitimate expenses and maintain finance records</li>
                <li>to manage website and training-platform access</li>
                <li>to publish photographs and publicity material where lawful</li>
                <li>to operate location tracking during authorised events, exercises, incidents, or deployments</li>
                <li>to comply with legal, insurance, safeguarding, and safety obligations</li>
            </ul>
        </div>
    </div>

    {{-- Section 8 --}}
    <div class="privacy-section" id="s8">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">8</div>
            <div class="psec-title">Lawful Bases for Processing</div>
            <span class="psec-chevron">▼</span>
        </div>
        <div class="privacy-section-body">
            <p>Liverpool RAYNET Group relies on different lawful bases depending on the activity.</p>
            <ul>
                <li><strong>Contract</strong> — for core group membership administration that is objectively necessary to run a member's participation in the Group.</li>
                <li><strong>Legitimate interests</strong> — for routine operational coordination, event administration, training records, attendance, qualifications, deployment records, limited sharing for events, finance administration, and similar day-to-day group functions.</li>
                <li><strong>Legal obligation</strong> — for compliance with legal obligations, insurance requirements, safeguarding issues, and health and safety duties where applicable.</li>
                <li><strong>Consent</strong> — for public-relations and social-media photographs of identifiable individuals used as featured or promotional images.</li>
                <li><strong>Consent</strong> — for non-essential cookies, analytics, embedded videos, and social-media plugins on the website.</li>
            </ul>
            <div class="priv-info"><span class="priv-box-icon">ℹ️</span><span>For health or welfare information, Liverpool RAYNET Group will use such information only where operationally necessary and with clear prior notice. Where practicable, explicit consent will be sought. In urgent situations involving serious risk to life or safety, Liverpool RAYNET Group may process or disclose relevant information where another lawful basis or condition applies.</span></div>
        </div>
    </div>

    {{-- Section 9 --}}
    <div class="privacy-section" id="s9">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">9</div>
            <div class="psec-title">Special Category Data</div>
            <span class="psec-chevron">▼</span>
        </div>
        <div class="privacy-section-body">
            <p>Health and welfare information is <strong>special category personal data</strong> and is handled only where genuinely necessary, with access limited to those who need to know.</p>
            <p>Liverpool RAYNET Group will collect and use such information only where it is operationally necessary, proportionate, and lawful to do so.</p>
        </div>
    </div>

    {{-- Section 10 --}}
    <div class="privacy-section" id="s10">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">10</div>
            <div class="psec-title">Live Location &amp; APRS Tracking</div>
            <span class="psec-chevron">▼</span>
        </div>
        <div class="privacy-section-body">
            <p>Liverpool RAYNET Group may use APRS and live location tracking, including through SARstuff, during authorised events, exercises, incidents, and deployments.</p>
            <p>Location tracking is used only for operational coordination, safety, welfare, and insurance-related purposes. It is <strong>not</strong> used for routine private monitoring outside authorised activities.</p>
            <p>Location data and track logs may be visible to authorised personnel with a genuine operational need to know.</p>
        </div>
    </div>

    {{-- Section 11 --}}
    <div class="privacy-section" id="s11">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">11</div>
            <div class="psec-title">Photographs &amp; Publicity</div>
            <span class="psec-chevron">▼</span>
        </div>
        <div class="privacy-section-body">
            <p>Liverpool RAYNET Group may use photographs for membership administration, identification, public relations, website content, and social-media content.</p>
            <p>For featured, posed, or clearly identifiable promotional images of members or other individuals, Liverpool RAYNET Group will seek explicit consent where appropriate.</p>
            <p>For general crowd shots or scene-setting images taken at public events, Liverpool RAYNET Group may rely on legitimate interests where use of the image is proportionate, people would reasonably expect such photography, clear notice is given, and the image is not used in a way likely to cause unfairness, harm, or distress.</p>
            <div class="priv-info"><span class="priv-box-icon">📷</span><span>Consent for publicity photographs may be withdrawn. Withdrawal will not affect use already made before the withdrawal, but Liverpool RAYNET Group will stop future use where required.</span></div>
        </div>
    </div>

    {{-- Section 12 --}}
    <div class="privacy-section" id="s12">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">12</div>
            <div class="psec-title">Emergency Contact Details</div>
            <span class="psec-chevron">▼</span>
        </div>
        <div class="privacy-section-body">
            <p>Members may provide emergency contact details for operational safety purposes. <strong>Members are responsible for ensuring that the emergency contact knows that their details have been provided</strong> to Liverpool RAYNET Group for that purpose.</p>
            <p>Emergency contact details are used only where relevant to welfare, safety, or urgent operational need.</p>
        </div>
    </div>

    {{-- Section 13 --}}
    <div class="privacy-section" id="s13">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">13</div>
            <div class="psec-title">Finance &amp; Reimbursement Data</div>
            <span class="psec-chevron">▼</span>
        </div>
        <div class="privacy-section-body">
            <p>Liverpool RAYNET Group processes limited finance-related personal data in order to record payments, maintain an audit trail, and reimburse legitimate expenses.</p>
            <p>This may include payer name, transaction reference, amount paid, bank details for reimbursement, expense claims, and invoices.</p>
        </div>
    </div>

    {{-- Section 14 --}}
    <div class="privacy-section" id="s14">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">14</div>
            <div class="psec-title">Who We Share Personal Data With</div>
            <span class="psec-chevron">▼</span>
        </div>
        <div class="privacy-section-body">
            <p>Liverpool RAYNET Group shares personal data only where there is a lawful basis and a genuine operational, administrative, safety, insurance, or legal need to do so. We may share personal data with:</p>
            <ul>
                <li><strong>SARstuff</strong> — for operational management, incident coordination, location tracking, and operational records</li>
                <li><strong>Moodle</strong> — for learning administration, course access, attendance, assessment results, certificates, learning progress, forum posts, and assignments</li>
                <li><strong>Google Workspace</strong> — where used as a document repository for membership lists, attendance records, training records, deployment logs, incident reports, finance records, photographs, and committee papers</li>
                <li><strong>Website and hosting providers</strong> — where necessary to run raynet-liverpool.net and its supporting services</li>
                <li><strong>Event organisers, local authorities, emergency services, and partner agencies</strong> — where operationally necessary, usually limited to first name and callsign, and in some cases mobile number or vehicle registration</li>
                <li><strong>Insurers, safeguarding contacts, regulators, or law enforcement</strong> — where disclosure is required or justified by law, safeguarding duty, insurance need, or urgent risk to life or safety</li>
            </ul>
            <div class="priv-info"><span class="priv-box-icon">🔒</span><span>Liverpool RAYNET Group does not sell personal data and does not share personal data for marketing purposes.</span></div>
        </div>
    </div>

    {{-- Section 15 --}}
    <div class="privacy-section" id="s15">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">15</div>
            <div class="psec-title">Processors &amp; Third-Party Services</div>
            <span class="psec-chevron">▼</span>
        </div>
        <div class="privacy-section-body">
            <p>Liverpool RAYNET Group uses third-party platforms and services including SARstuff, Moodle, Google Workspace, website hosting, and related tools.</p>
            <p>Liverpool RAYNET Group expects providers handling personal data on its behalf to process it securely and appropriately under applicable contractual terms and data protection law.</p>
        </div>
    </div>

    {{-- Section 16 --}}
    <div class="privacy-section" id="s16">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">16</div>
            <div class="psec-title">International Transfers</div>
            <span class="psec-chevron">▼</span>
        </div>
        <div class="privacy-section-body">
            <p>To the best of Liverpool RAYNET Group's knowledge, its data is normally stored in the UK. However, some service providers may process personal data outside the UK.</p>
            <p>Where personal data is transferred outside the UK, Liverpool RAYNET Group will seek to ensure that appropriate safeguards are in place, such as recognised contractual safeguards or other lawful transfer mechanisms required by UK data protection law.</p>
        </div>
    </div>

    {{-- Section 17 --}}
    <div class="privacy-section" id="s17">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">17</div>
            <div class="psec-title">Retention</div>
            <span class="psec-chevron">▼</span>
        </div>
        <div class="privacy-section-body">
            <p>Liverpool RAYNET Group keeps personal data only for as long as necessary for the purpose for which it was collected. Current retention periods are:</p>
            <div class="retention-wrap">
                <table class="retention-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Retention Period</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Membership records</td><td>18 months after leaving, or 6 months after death</td></tr>
                        <tr><td>Qualifications records</td><td>Same as membership records</td></tr>
                        <tr><td>Health or welfare details</td><td>Same as membership records, unless shorter period appropriate</td></tr>
                        <tr><td>Attendance records</td><td>3 years</td></tr>
                        <tr><td>Moodle learning records</td><td>18 months after the member leaves</td></tr>
                        <tr><td>Deployment logs, incident notes, and SITREPs</td><td>6 years</td></tr>
                        <tr><td>APRS and live location data</td><td>3 months after the relevant event or deployment</td></tr>
                        <tr><td>Emergency contact details</td><td>3 months after the relevant event, unless part of current membership records</td></tr>
                        <tr><td>Third-party contact details for events</td><td>18 months</td></tr>
                        <tr><td>Third-party contacts in SARstuff operational logs</td><td>Lifetime of the relevant operational log (SARstuff arrangements)</td></tr>
                        <tr><td>Payment records, expense claims, invoices, and reimbursement records</td><td>6 years from end of relevant financial year</td></tr>
                        <tr><td>Standalone reimbursement bank details</td><td>Only for as long as needed for active reimbursement</td></tr>
                        <tr><td>Membership ID photographs</td><td>Same as membership records</td></tr>
                        <tr><td>Public-relations and social-media photographs</td><td>Until consent withdrawn or image no longer needed, subject to annual review</td></tr>
                    </tbody>
                </table>
            </div>
            <p style="margin-top:.75rem;">Where retention periods expire, records will be securely deleted, destroyed, or anonymised as appropriate.</p>
        </div>
    </div>

    {{-- Section 18 --}}
    <div class="privacy-section" id="s18">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">18</div>
            <div class="psec-title">Website Cookies, Analytics &amp; Embedded Media</div>
            <span class="psec-chevron">▼</span>
        </div>
        <div class="privacy-section-body">
            <p>Raynet-Liverpool.net uses cookies and similar technologies. Non-essential cookies, analytics tools, embedded videos, and social-media plugins that collect personal data or store or access information on a user's device will be activated only with the user's consent through our cookie consent tool. Users will have the option to reject non-essential technologies.</p>
            <p>Liverpool RAYNET Group does not use its website for unsolicited direct marketing.</p>
            <div class="priv-info"><span class="priv-box-icon">🍪</span><span>For full details of every cookie we use, including your right to object to legitimate interest claims under UK GDPR Article 21, please see our <a href="{{ route('cookies') }}">Cookie Policy</a>.</span></div>
            <p>
                <button onclick="openCookieSettings()"
                        style="display:inline-flex;align-items:center;gap:.4rem;padding:.45rem 1.1rem;background:var(--navy);border:none;color:#fff;font-family:var(--font);font-size:12px;font-weight:bold;cursor:pointer;text-transform:uppercase;letter-spacing:.07em;transition:background .12s;">
                    ⚙ Manage Cookie Preferences
                </button>
            </p>
        </div>
    </div>

    {{-- Section 19 --}}
    <div class="privacy-section" id="s19">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">19</div>
            <div class="psec-title">Operational Emails &amp; Communications</div>
            <span class="psec-chevron">▼</span>
        </div>
        <div class="privacy-section-body">
            <p>Liverpool RAYNET Group sends operational emails, calls to meetings, and event details. It does not use personal data for unsolicited marketing, fundraising promotion, or general commercial advertising.</p>
        </div>
    </div>

    {{-- Section 20 --}}
    <div class="privacy-section" id="s20">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">20</div>
            <div class="psec-title">Security</div>
            <span class="psec-chevron">▼</span>
        </div>
        <div class="privacy-section-body">
            <p>Liverpool RAYNET Group applies technical and organisational measures to protect personal data. These include access controls, password protection, secure storage, sensible limitation of access, member awareness of responsible data handling, and secure disposal of records when they are no longer needed.</p>
            <p>Access to personal data is restricted to those with a genuine operational or administrative need to know.</p>
        </div>
    </div>

    {{-- Section 21 --}}
    <div class="privacy-section" id="s21">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">21</div>
            <div class="psec-title">Data Breaches</div>
            <span class="psec-chevron">▼</span>
        </div>
        <div class="privacy-section-body">
            <p>Any member who becomes aware of a personal data breach, suspected breach, loss, or unauthorised disclosure must report it promptly to the Group Controller.</p>
            <p>Liverpool RAYNET Group will assess the incident, record it, and where legally required notify the ICO without undue delay.</p>
        </div>
    </div>

    {{-- Section 22 --}}
    <div class="privacy-section" id="s22">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">22</div>
            <div class="psec-title">Data Protection Impact Assessments</div>
            <span class="psec-chevron">▼</span>
        </div>
        <div class="privacy-section-body">
            <p>Liverpool RAYNET Group will carry out a data protection impact assessment where a new technology, tracking function, database, or other change is likely to create a high risk to individuals' rights and freedoms.</p>
        </div>
    </div>

    {{-- Section 23 --}}
    <div class="privacy-section" id="s23">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">23</div>
            <div class="psec-title">Your Rights</div>
            <span class="psec-chevron">▼</span>
        </div>
        <div class="privacy-section-body">
            <p>Under UK data protection law, you may have the right to:</p>
            <ul>
                <li>request access to your personal data</li>
                <li>request correction of inaccurate or incomplete personal data</li>
                <li>request erasure in certain circumstances</li>
                <li>request restriction of processing in certain circumstances</li>
                <li>object to processing in certain circumstances</li>
                <li>request data portability where applicable</li>
                <li>withdraw consent at any time where consent is the lawful basis</li>
                <li>complain to the Information Commissioner's Office</li>
            </ul>
            <p>Requests should be sent to: <a href="mailto:GC.liverpool@raynet-uk.net">GC.liverpool@raynet-uk.net</a></p>
        </div>
    </div>

    {{-- Section 24 --}}
    <div class="privacy-section" id="s24">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">24</div>
            <div class="psec-title">If You Do Not Provide Personal Data</div>
            <span class="psec-chevron">▼</span>
        </div>
        <div class="privacy-section-body">
            <p>Membership is administered through RAYNET-UK rather than by Liverpool RAYNET Group itself. However, if a member does not provide the personal data needed for local administration, training, deployment, reimbursement, emergency contact, or operational safety, Liverpool RAYNET Group may be unable to deploy that member, enrol them on training, contact them in an emergency, or reimburse legitimate expenses.</p>
        </div>
    </div>

    {{-- Section 25 --}}
    <div class="privacy-section" id="s25">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">25</div>
            <div class="psec-title">Automated Decision-Making</div>
            <span class="psec-chevron">▼</span>
        </div>
        <div class="privacy-section-body">
            <p>Liverpool RAYNET Group does not carry out solely automated decision-making or profiling about individuals.</p>
        </div>
    </div>

    {{-- Section 26 --}}
    <div class="privacy-section" id="s26">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">26</div>
            <div class="psec-title">Review of This Policy</div>
            <span class="psec-chevron">▼</span>
        </div>
        <div class="privacy-section-body">
            <p>This policy will be reviewed at least annually, or sooner if Liverpool RAYNET Group's processing activities, systems, or legal obligations change.</p>
        </div>
    </div>

    {{-- Section 27 --}}
    <div class="privacy-section" id="s27">
        <div class="privacy-section-head" onclick="toggleSection(this)">
            <div class="psec-num">27</div>
            <div class="psec-title">Complaints</div>
            <span class="psec-chevron">▼</span>
        </div>
        <div class="privacy-section-body">
            <p>Liverpool RAYNET Group will try to resolve any privacy or data-protection concern fairly and promptly. If you are dissatisfied with the way your personal data has been handled, you may complain to the <strong>Information Commissioner's Office</strong>.</p>
            <div class="priv-info">
                <span class="priv-box-icon">🏛</span>
                <div>
                    Information Commissioner's Office<br>
                    Website: <a href="https://ico.org.uk" target="_blank" rel="noopener">ico.org.uk</a><br>
                    Helpline: 0303 123 1113
                </div>
            </div>
        </div>
    </div>

    {{-- Page footer --}}
    <div class="privacy-page-footer">
        <p>Liverpool RAYNET Group (Group 10/ME/179) · Affiliated to RAYNET-UK · Volunteer emergency communications for Merseyside</p>
        <p style="margin-top:.3rem;">Privacy Notice v1.0 · Draft · Last updated 17 March 2026 ·
            <a href="{{ route('cookies') }}">Cookie Policy</a>
        </p>
    </div>

</div>

<script>
function toggleSection(head) {
    const body    = head.nextElementSibling;
    const chevron = head.querySelector('.psec-chevron');
    const isOpen  = body.classList.toggle('open');
    if (chevron) chevron.classList.toggle('open', isOpen);
}

// Smooth scroll for TOC links
document.querySelectorAll('.privacy-toc-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (!target) return;
        // Open the section if it's closed
        const body    = target.querySelector('.privacy-section-body');
        const head    = target.querySelector('.privacy-section-head');
        const chevron = target.querySelector('.psec-chevron');
        if (body && !body.classList.contains('open')) {
            body.classList.add('open');
            if (chevron) chevron.classList.add('open');
        }
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});
</script>

@endsection