@extends('layouts.app')
@section('title', 'Event Support')
@section('content')

<style>
:root {
    --navy: #003366;
    --red: #C8102E;
    --white: #FFFFFF;
    --light: #F2F2F2;
    --text: #003366;
    --text-light: #1A1A1A;
    --muted: #4A4A4A;
    --border: #D0D0D0;
    --shadow-sm: 0 2px 8px rgba(0,51,102,0.06);
    --shadow-md: 0 4px 16px rgba(0,51,102,0.1);
    --transition: all 0.2s ease;
}

*, *::before, *::after { box-sizing: border-box; margin:0; padding:0; }
html { scroll-behavior: smooth; }
body {
    background: var(--light);
    color: var(--text);
    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
    font-size: 15px;
    line-height: 1.55;
    min-height: 100vh;
}
.wrap {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem 3rem;
}

/* TOP BAR */
.topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 0;
    border-bottom: 2px solid var(--navy);
    margin-bottom: 2rem;
    gap: 1rem;
    flex-wrap: wrap;
}
.brand { display: flex; align-items: center; gap: 0.8rem; }
.brand-badge {
    width: 40px; height: 40px;
    background: var(--navy);
    color: white;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; font-weight: bold;
    border-radius: 6px;
}
.brand-name { font-size: 1.25rem; font-weight: bold; color: var(--navy); }
.brand-sub { font-size: 0.8rem; color: var(--muted); }
.status-chip {
    display: flex; align-items: center; gap: 0.5rem;
    padding: 0.4rem 0.9rem;
    border-radius: 999px;
    background: white;
    border: 1px solid var(--border);
    font-size: 0.85rem;
    color: var(--muted);
}
.online-dot {
    width: 8px; height: 8px;
    background: #2E7D32;
    border-radius: 50%;
    box-shadow: 0 0 0 2px rgba(46,125,50,0.25);
}

/* PAGE HEADER */
.page-header { margin-bottom: 2rem; text-align: center; }
.page-header-eyebrow {
    font-size: 0.85rem;
    font-weight: bold;
    color: var(--red);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.6rem;
}
.page-header h1 {
    font-size: 1.7rem;
    font-weight: bold;
    line-height: 1.15;
    color: var(--navy);
    margin-bottom: 0.8rem;
}
@media (min-width: 576px) { .page-header h1 { font-size: 2rem; } }
.page-header h1 span { color: var(--red); }
.page-header p {
    font-size: 0.95rem;
    color: var(--text-light);
    max-width: 600px;
    margin: 0 auto;
}

/* STAT STRIP */
.stat-strip {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}
.stat-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 1rem 0.8rem;
    text-align: center;
    box-shadow: var(--shadow-sm);
}
.stat-label {
    font-size: 0.8rem;
    font-weight: bold;
    color: var(--muted);
    text-transform: uppercase;
    margin-bottom: 0.3rem;
}
.stat-value {
    font-size: 1.6rem;
    font-weight: bold;
    color: var(--navy);
    line-height: 1;
    margin-bottom: 0.2rem;
}
.stat-sub { font-size: 0.85rem; color: var(--muted); }

/* CONTENT GRID */
.content-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}
@media (min-width: 768px) { .content-grid { grid-template-columns: 1fr 300px; } }

/* INFO CARD */
.info-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    margin-bottom: 1.5rem;
}
.info-card:last-child { margin-bottom: 0; }
.card-head {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    padding: 1rem 1.2rem;
    background: var(--light);
    border-bottom: 1px solid var(--border);
}
.card-head-icon { font-size: 1.6rem; line-height: 1; }
.card-head-title {
    font-size: 1.15rem;
    font-weight: bold;
    color: var(--navy);
}
.card-head-sub {
    font-size: 0.85rem;
    color: var(--muted);
}
.card-body { padding: 1.2rem; }
.card-body p {
    font-size: 0.95rem;
    color: var(--text-light);
    margin-bottom: 1rem;
}
.card-body p:last-child { margin-bottom: 0; }

/* EVENT TYPE GRID */
.event-type-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
    margin-top: 1rem;
}
@media (min-width: 576px) { .event-type-grid { grid-template-columns: repeat(2, 1fr); } }
.event-type-item {
    display: flex;
    align-items: flex-start;
    gap: 0.8rem;
    padding: 1rem;
    background: var(--light);
    border-radius: 8px;
    border: 1px solid var(--border);
}
.event-type-icon { font-size: 1.8rem; flex-shrink: 0; margin-top: 0.2rem; }
.event-type-title {
    font-size: 1.05rem;
    font-weight: bold;
    color: var(--navy);
    margin-bottom: 0.3rem;
}
.event-type-desc { font-size: 0.9rem; color: var(--text-light); }

/* PROVIDE LIST */
.provide-list { display: flex; flex-direction: column; gap: 0.8rem; margin-top: 1rem; }
.provide-item {
    display: flex;
    align-items: flex-start;
    gap: 0.8rem;
    padding: 1rem;
    background: var(--light);
    border-radius: 8px;
    border: 1px solid var(--border);
}
.provide-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 0.3rem;
}
.provide-item-title {
    font-size: 1.05rem;
    font-weight: bold;
    color: var(--navy);
    margin-bottom: 0.3rem;
}
.provide-item-desc { font-size: 0.9rem; color: var(--text-light); }

/* SIDE COLUMN */
.side-col { display: flex; flex-direction: column; gap: 1.5rem; }

/* CTA CARD */
.cta-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--shadow-md);
}
.cta-card-body { padding: 1.5rem 1.2rem; text-align: center; }
.cta-badge {
    width: 56px;
    height: 56px;
    background: var(--red);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    border-radius: 10px;
    margin: 0 auto 1rem;
}
.cta-title {
    font-size: 1.25rem;
    font-weight: bold;
    color: var(--navy);
    margin-bottom: 0.6rem;
}
.cta-desc {
    font-size: 0.95rem;
    color: var(--text-light);
    margin-bottom: 1.2rem;
}
.cta-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.8rem 1.5rem;
    border-radius: 999px;
    background: var(--red);
    color: white;
    font-size: 0.95rem;
    font-weight: bold;
    text-decoration: none;
    transition: var(--transition);
}
.cta-btn:hover {
    background: #a00d25;
    transform: translateY(-2px);
}
.cta-divider { height: 1px; background: var(--border); margin: 1.2rem 0; }
.cta-note { font-size: 0.85rem; color: var(--muted); line-height: 1.4; }

/* LINK CARD */
.link-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}
.link-list { padding: 0.6rem; }
.link-item {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    padding: 1rem 1.2rem;
    border-radius: 8px;
    text-decoration: none;
    color: inherit;
    transition: var(--transition);
}
.link-item:hover { background: var(--light); }
.link-item-icon { font-size: 1.6rem; line-height: 1; flex-shrink: 0; }
.link-item-text {
    flex: 1;
    font-size: 1rem;
    font-weight: bold;
}
.link-item-sub { font-size: 0.85rem; color: var(--muted); }
.link-item-arrow {
    font-size: 1.2rem;
    color: var(--red);
    font-weight: bold;
}

/* STEPS */
.full-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--shadow-md);
    margin-bottom: 2rem;
}
.steps {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0;
}
@media (min-width: 768px) { .steps { grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); } }
.step {
    padding: 1.5rem 1.2rem;
    border-bottom: 1px solid var(--border);
    text-align: center;
}
.step:last-child { border-bottom: none; }
@media (min-width: 768px) {
    .step { border-bottom: none; border-right: 1px solid var(--border); }
    .step:last-child { border-right: none; }
}
.step-num {
    font-size: 2.2rem;
    font-weight: bold;
    color: var(--red);
    opacity: 0.25;
    margin-bottom: 0.6rem;
    line-height: 1;
}
.step-title {
    font-size: 1.1rem;
    font-weight: bold;
    color: var(--navy);
    margin-bottom: 0.5rem;
}
.step-desc {
    font-size: 0.9rem;
    color: var(--text-light);
}

/* EXPECTATIONS TABLE */
.expect-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}
.expect-table th, .expect-table td {
    padding: 0.8rem 1rem;
    text-align: left;
    border-bottom: 1px solid var(--border);
}
.expect-table th {
    background: var(--light);
    font-weight: bold;
    color: var(--navy);
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.03em;
}
.expect-table tr:last-child td { border-bottom: none; }
.expect-table tr:hover td { background: rgba(0,51,102,0.03); }
.tag {
    display: inline-block;
    padding: 0.3rem 0.8rem;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: bold;
}
.tag-green { background: rgba(46,125,50,0.12); color: #2E7D32; border: 1px solid rgba(46,125,50,0.3); }
.tag-yellow { background: rgba(200,140,0,0.12); color: #c47f00; border: 1px solid rgba(200,140,0,0.3); }
.tag-blue { background: rgba(0,51,102,0.12); color: var(--navy); border: 1px solid rgba(0,51,102,0.3); }
</style>

<div class="wrap">

    <nav class="topbar">
        <div class="brand">
            <div class="brand-badge">📡</div>
            <div>
                <div class="brand-name">{{ \App\Helpers\RaynetSetting::groupName() }}</div>
                <div class="brand-sub">Zone 10 · {{ \App\Helpers\RaynetSetting::groupRegion() }} · Group 179</div>
            </div>
        </div>
        <div class="status-chip">
            <div class="online-dot"></div>
            <span>Accepting Event Requests – Free Support</span>
        </div>
    </nav>

    <header class="page-header">
        <div class="page-header-eyebrow">// Event Support</div>
        <h1>Radio Communications for<br>Your <span>Event</span></h1>
        <p>{{ \App\Helpers\RaynetSetting::groupName() }} provides free volunteer radio support for public events across {{ \App\Helpers\RaynetSetting::groupRegion() }} — marathons, sportives, charity walks, festivals, and more — ensuring reliable links for marshals, welfare, medical, and command teams independent of mobile networks.</p>
    </header>

    <div class="stat-strip">
        <div class="stat-card">
            <div class="stat-label">Cost</div>
            <div class="stat-value">Free</div>
            <div class="stat-sub">Voluntary – no charge</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Notice</div>
            <div class="stat-value">4+ weeks</div>
            <div class="stat-sub">Ideal planning lead time</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Coverage</div>
            <div class="stat-value">{{ \App\Helpers\RaynetSetting::groupRegion() }}</div>
            <div class="stat-sub">City & surrounding</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Operators</div>
            <div class="stat-value">30+</div>
            <div class="stat-sub">Trained volunteers</div>
        </div>
    </div>

    <div class="content-grid">
        <div>
            <div class="info-card">
                <div class="card-head">
                    <div class="card-head-icon">🏁</div>
                    <div>
                        <div class="card-head-title">Events We Support</div>
                        <div class="card-head-sub">Types we regularly assist</div>
                    </div>
                </div>
                <div class="card-body">
                    <p>We work with organisers and safety teams to deliver independent radio comms — essential when mobile networks are congested or unavailable.</p>
                    <div class="event-type-grid">
                        <div class="event-type-item">
                            <div class="event-type-icon">🏃</div>
                            <div>
                                <div class="event-type-title">Road Races & Marathons</div>
                                <div class="event-type-desc">Marshal coordination, sweep teams, medical links.</div>
                            </div>
                        </div>
                        <div class="event-type-item">
                            <div class="event-type-icon">🚴</div>
                            <div>
                                <div class="event-type-title">Cycling Sportives</div>
                                <div class="event-type-desc">Route coverage, feed stations, incident reporting.</div>
                            </div>
                        </div>
                        <div class="event-type-item">
                            <div class="event-type-icon">🥾</div>
                            <div>
                                <div class="event-type-title">Charity Walks & Hikes</div>
                                <div class="event-type-desc">Welfare checks, participant tracking.</div>
                            </div>
                        </div>
                        <div class="event-type-item">
                            <div class="event-type-icon">🎪</div>
                            <div>
                                <div class="event-type-title">Festivals & Outdoor Events</div>
                                <div class="event-type-desc">Security, medical, welfare, management nets.</div>
                            </div>
                        </div>
                        <div class="event-type-item">
                            <div class="event-type-icon">🚣</div>
                            <div>
                                <div class="event-type-title">Water-Based Events</div>
                                <div class="event-type-desc">Shore-to-vessel safety comms.</div>
                            </div>
                        </div>
                        <div class="event-type-item">
                            <div class="event-type-icon">🏟️</div>
                            <div>
                                <div class="event-type-title">Community & Civic Events</div>
                                <div class="event-type-desc">Resilience for parades & public gatherings.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-card">
                <div class="card-head">
                    <div class="card-head-icon">📻</div>
                    <div>
                        <div class="card-head-title">What We Provide</div>
                        <div class="card-head-sub">Equipment & services</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="provide-list">
                        <div class="provide-item">
                            <div class="provide-dot" style="background:var(--navy);"></div>
                            <div>
                                <div class="provide-item-title">Dedicated Radio Net</div>
                                <div class="provide-item-desc">Private managed network covering your event area.</div>
                            </div>
                        </div>
                        <div class="provide-item">
                            <div class="provide-dot" style="background:var(--red);"></div>
                            <div>
                                <div class="provide-item-title">Operators at Key Points</div>
                                <div class="provide-item-desc">Positioned at marshal posts, medical, start/finish etc.</div>
                            </div>
                        </div>
                        <div class="provide-item">
                            <div class="provide-dot" style="background:var(--navy);"></div>
                            <div>
                                <div class="provide-item-title">Self-Contained Equipment</div>
                                <div class="provide-item-desc">Battery-powered radios — no venue power needed.</div>
                            </div>
                        </div>
                        <div class="provide-item">
                            <div class="provide-dot" style="background:var(--red);"></div>
                            <div>
                                <div class="provide-item-title">Event Log & Debrief</div>
                                <div class="provide-item-desc">Full record kept; summary available post-event.</div>
                            </div>
                        </div>
                        <div class="provide-item">
                            <div class="provide-dot" style="background:var(--navy);"></div>
                            <div>
                                <div class="provide-item-title">Emergency Services Liaison</div>
                                <div class="provide-item-desc">Links to police/ambulance if in your safety plan.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="side-col">
            <div class="cta-card">
                <div class="cta-card-body">
                    <div class="cta-badge">📋</div>
                    <div class="cta-title">Request Support</div>
                    <div class="cta-desc">Quick form — we’ll contact you to discuss needs. Free voluntary service.</div>
                    <a href="{{ route('request-support') }}" class="cta-btn">Submit Request →</a>
                    <div class="cta-divider"></div>
                    <div class="cta-note">Submit 4+ weeks ahead for best coordination. Earlier for big events.</div>
                </div>
            </div>

            <div class="link-card">
                <div class="card-head">
                    <div class="card-head-icon">🔗</div>
                    <div>
                        <div class="card-head-title">Related Pages</div>
                    </div>
                </div>
                <div class="link-list">
                    <a href="{{ route('request-support') }}" class="link-item">
                        <div class="link-item-icon">📋</div>
                        <div>
                            <div class="link-item-text">Request Support</div>
                            <div class="link-item-sub">Submit event details</div>
                        </div>
                        <div class="link-item-arrow">→</div>
                    </a>
                    <a href="{{ route('about') }}" class="link-item">
                        <div class="link-item-icon">📡</div>
                        <div>
                            <div class="link-item-text">About RAYNET</div>
                            <div class="link-item-sub">Who we are & capabilities</div>
                        </div>
                        <div class="link-item-arrow">→</div>
                    </a>
                    <a href="{{ route('training') }}" class="link-item">
                        <div class="link-item-icon">🎓</div>
                        <div>
                            <div class="link-item-text">Training & Nets</div>
                            <div class="link-item-sub">Exercises & schedule</div>
                        </div>
                        <div class="link-item-arrow">→</div>
                    </a>
                    <a href="https://www.raynet-uk.net" target="_blank" rel="noopener" class="link-item">
                        <div class="link-item-icon">🌐</div>
                        <div>
                            <div class="link-item-text">RAYNET-UK</div>
                            <div class="link-item-sub">National organisation</div>
                        </div>
                        <div class="link-item-arrow">↗</div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="full-card">
        <div class="card-head">
            <div class="card-head-icon">🔄</div>
            <div>
                <div class="card-head-title">How to Request Support</div>
                <div class="card-head-sub">From enquiry to deployment</div>
            </div>
        </div>
        <div class="steps">
            <div class="step">
                <div class="step-num">01</div>
                <div class="step-title">Submit Request</div>
                <div class="step-desc">Use our form: date, location, size, comms needs.</div>
            </div>
            <div class="step">
                <div class="step-num">02</div>
                <div class="step-title">Initial Discussion</div>
                <div class="step-desc">Controller contacts you to review layout & requirements.</div>
            </div>
            <div class="step">
                <div class="step-num">03</div>
                <div class="step-title">Planning & Briefing</div>
                <div class="step-desc">Comms plan created; frequencies & positions shared.</div>
            </div>
            <div class="step">
                <div class="step-num">04</div>
                <div class="step-title">Event Day</div>
                <div class="step-desc">Operators arrive early, radio checks, net active.</div>
            </div>
            <div class="step">
                <div class="step-num">05</div>
                <div class="step-title">Stand Down & Debrief</div>
                <div class="step-desc">Operators released; log available, lessons noted.</div>
            </div>
        </div>
    </div>

    <div class="full-card">
        <div class="card-head">
            <div class="card-head-icon">✅</div>
            <div>
                <div class="card-head-title">What to Expect</div>
                <div class="card-head-sub">Responsibilities summary</div>
            </div>
        </div>
        <table class="expect-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Detail</th>
                    <th>Provided by</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Radio Equipment</td>
                    <td>Handhelds, mobiles, batteries, antennas</td>
                    <td><span class="tag tag-green">RAYNET</span></td>
                </tr>
                <tr>
                    <td>Licensed Operators</td>
                    <td>Valid Ofcom licence holders</td>
                    <td><span class="tag tag-green">RAYNET</span></td>
                </tr>
                <tr>
                    <td>Comms Plan</td>
                    <td>Frequencies, callsigns, positions, escalation</td>
                    <td><span class="tag tag-green">RAYNET</span></td>
                </tr>
                <tr>
                    <td>Briefing Pack</td>
                    <td>Route maps, contacts, timings, key personnel</td>
                    <td><span class="tag tag-yellow">Organiser</span></td>
                </tr>
                <tr>
                    <td>Marshal Positions</td>
                    <td>Access & location details for radio posts</td>
                    <td><span class="tag tag-yellow">Organiser</span></td>
                </tr>
                <tr>
                    <td>Risk Assessment</td>
                    <td>Event safety docs shared pre-event</td>
                    <td><span class="tag tag-yellow">Organiser</span></td>
                </tr>
                <tr>
                    <td>Cost</td>
                    <td>No charge – voluntary organisation</td>
                    <td><span class="tag tag-blue">Free</span></td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

@endsection