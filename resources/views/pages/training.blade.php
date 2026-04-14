@extends('layouts.app')
@section('title', 'Training')
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
.wrap { max-width: 1200px; margin: 0 auto; padding: 0 1rem 3rem; }

/* TOP BAR */
.topbar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1rem 0; border-bottom: 2px solid var(--navy);
    margin-bottom: 2rem; gap: 1rem; flex-wrap: wrap;
}
.brand { display: flex; align-items: center; gap: 0.8rem; }
.brand-badge {
    width: 40px; height: 40px; background: var(--navy); color: white;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; font-weight: bold; border-radius: 6px;
}
.brand-name { font-size: 1.25rem; font-weight: bold; color: var(--navy); }
.brand-sub { font-size: 0.8rem; color: var(--muted); }
.status-chip {
    display: flex; align-items: center; gap: 0.5rem;
    padding: 0.4rem 0.9rem; border-radius: 999px;
    background: white; border: 1px solid var(--border);
    font-size: 0.85rem; color: var(--muted);
}
.online-dot {
    width: 8px; height: 8px; background: #2E7D32; border-radius: 50%;
    box-shadow: 0 0 0 2px rgba(46,125,50,0.25);
}

/* PAGE HEADER */
.page-header { margin-bottom: 2rem; text-align: center; }
.page-header-eyebrow {
    font-size: 0.85rem; font-weight: bold; color: var(--red);
    text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.6rem;
}
.page-header h1 {
    font-size: 1.7rem; font-weight: bold; line-height: 1.15;
    color: var(--navy); margin-bottom: 0.8rem;
}
@media (min-width: 576px) { .page-header h1 { font-size: 2rem; } }
.page-header h1 span { color: var(--red); }
.page-header p { font-size: 0.95rem; color: var(--text-light); max-width: 600px; margin: 0 auto; }

/* STAT STRIP */
.stat-strip {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 1rem; margin-bottom: 2rem;
}
.stat-card {
    background: white; border: 1px solid var(--border); border-radius: 8px;
    padding: 1rem 0.8rem; text-align: center; box-shadow: var(--shadow-sm);
}
.stat-label { font-size: 0.8rem; font-weight: bold; color: var(--muted); text-transform: uppercase; margin-bottom: 0.3rem; }
.stat-value { font-size: 1.6rem; font-weight: bold; color: var(--navy); line-height: 1; margin-bottom: 0.2rem; }
.stat-sub { font-size: 0.85rem; color: var(--muted); }

/* COMING SOON */
.coming-soon {
    display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;
    padding: 1rem 1.2rem; background: rgba(0,51,102,0.06);
    border: 1px solid rgba(0,51,102,0.18); border-radius: 8px; margin-bottom: 2rem;
}
.coming-soon-icon { font-size: 1.8rem; flex-shrink: 0; }
.coming-soon-title { font-size: 1.2rem; font-weight: bold; color: var(--navy); margin-bottom: 0.3rem; }
.coming-soon-desc { font-size: 0.9rem; color: var(--text-light); flex: 1; }
.coming-soon-chip {
    padding: 0.4rem 1rem; border-radius: 999px; background: var(--red);
    color: white; font-size: 0.85rem; font-weight: bold; white-space: nowrap;
}

/* HEX TRAINING WHEEL */
.wheel-body {
    padding: 1.5rem 1rem 1rem;
    display: flex; flex-direction: column; align-items: center;
}
#trainingWheel { width: 100%; max-width: 620px; }
.wheel-legend {
    display: flex; gap: 1.5rem; flex-wrap: wrap;
    justify-content: center; margin-top: 0.75rem;
}
.wleg { display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; font-weight: bold; color: var(--navy); }
.wleg-sw { width: 14px; height: 14px; border-radius: 2px; flex-shrink: 0; }
.wheel-info {
    width: 100%; max-width: 620px; min-height: 48px;
    margin-top: 0.75rem; padding: 0.6rem 1rem;
    text-align: center; border-top: 1px solid var(--border);
}
.wheel-info-title { font-size: 0.9rem; font-weight: bold; color: #9aa3ae; transition: color 0.2s; }
.wheel-info-desc { font-size: 0.8rem; color: var(--muted); margin-top: 3px; line-height: 1.4; }

/* PILLAR GRID (wheel footer) */
.pillar-grid-wheel {
    display: grid; grid-template-columns: repeat(3, 1fr);
    border-top: 1px solid var(--border);
}
@media (max-width: 580px) { .pillar-grid-wheel { grid-template-columns: 1fr; } }
.pcol {
    padding: 1rem 1.2rem;
    border-right: 1px solid var(--border);
}
.pcol:last-child { border-right: none; }
@media (max-width: 580px) {
    .pcol { border-right: none; border-bottom: 1px solid var(--border); }
    .pcol:last-child { border-bottom: none; }
}
.pcol-lbl { font-size: 0.7rem; font-weight: bold; text-transform: uppercase; letter-spacing: 0.1em; color: #9aa3ae; margin-bottom: 0.2rem; }
.pcol-name { font-size: 0.95rem; font-weight: bold; color: var(--navy); margin-bottom: 0.25rem; }
.pcol-cores { font-size: 0.78rem; color: var(--muted); margin-bottom: 0.5rem; }
.pcol-hr { height: 1px; background: var(--border); margin: 0.5rem 0; }
.pbags { display: flex; flex-wrap: wrap; gap: 4px; }
.pbf {
    font-size: 0.7rem; font-weight: bold; padding: 2px 7px;
    border-radius: 999px; background: #e8eef5;
    border: 1px solid var(--navy); color: var(--navy);
}
.pba { font-size: 0.7rem; font-weight: bold; padding: 2px 7px; border-radius: 999px; background: var(--red); color: white; }

/* CONTENT GRID */
.content-grid { display: grid; grid-template-columns: 1fr; gap: 1.5rem; }
@media (min-width: 768px) { .content-grid { grid-template-columns: 1fr 300px; } }

/* INFO CARD */
.info-card {
    background: white; border: 1px solid var(--border); border-radius: 8px;
    overflow: hidden; box-shadow: var(--shadow-sm); margin-bottom: 1.5rem;
}
.info-card:last-child { margin-bottom: 0; }
.card-head {
    display: flex; align-items: center; gap: 0.8rem;
    padding: 1rem 1.2rem; background: var(--light); border-bottom: 1px solid var(--border);
}
.card-head-icon { font-size: 1.6rem; line-height: 1; }
.card-head-title { font-size: 1.15rem; font-weight: bold; color: var(--navy); }
.card-head-sub { font-size: 0.85rem; color: var(--muted); }
.card-body { padding: 1.2rem; }
.card-body p { font-size: 0.95rem; color: var(--text-light); margin-bottom: 1rem; }
.card-body p:last-child { margin-bottom: 0; }

/* TOPIC GRID */
.topic-grid { display: grid; grid-template-columns: 1fr; gap: 1rem; margin-top: 1rem; }
@media (min-width: 576px) { .topic-grid { grid-template-columns: repeat(2, 1fr); } }
.topic-item {
    display: flex; align-items: flex-start; gap: 0.8rem;
    padding: 1rem; background: var(--light); border-radius: 8px; border: 1px solid var(--border);
}
.topic-icon { font-size: 1.8rem; flex-shrink: 0; margin-top: 0.2rem; }
.topic-title { font-size: 1.05rem; font-weight: bold; color: var(--navy); margin-bottom: 0.3rem; }
.topic-desc { font-size: 0.9rem; color: var(--text-light); }

/* CAP LIST */
.cap-list { display: flex; flex-direction: column; gap: 0.8rem; margin-top: 1rem; }
.cap-item {
    display: flex; align-items: flex-start; gap: 0.8rem;
    padding: 1rem; background: var(--light); border-radius: 8px; border: 1px solid var(--border);
}
.cap-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; margin-top: 0.3rem; }
.cap-item-title { font-size: 1.05rem; font-weight: bold; color: var(--navy); margin-bottom: 0.3rem; }
.cap-item-desc { font-size: 0.9rem; color: var(--text-light); }

/* NOTICE CARD */
.notice-card { background: white; border: 1px solid var(--border); border-radius: 8px; overflow: hidden; box-shadow: var(--shadow-sm); }
.notice-card-body { padding: 1.5rem 1.2rem; text-align: center; }
.notice-badge {
    width: 56px; height: 56px; background: var(--navy); color: white;
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; border-radius: 10px; margin: 0 auto 1rem;
}
.notice-title { font-size: 1.3rem; font-weight: bold; color: var(--navy); margin-bottom: 0.6rem; }
.notice-desc { font-size: 0.95rem; color: var(--text-light); margin-bottom: 1.2rem; }
.notice-divider { height: 1px; background: var(--border); margin: 1.2rem 0; }
.notice-row { display: flex; align-items: flex-start; gap: 0.8rem; font-size: 0.9rem; margin-bottom: 0.8rem; text-align: left; }
.notice-row:last-child { margin-bottom: 0; }
.notice-row-icon { font-size: 1.6rem; flex-shrink: 0; margin-top: 0.2rem; }
.notice-row-text strong { color: var(--navy); }

/* LINK CARD */
.link-card { background: white; border: 1px solid var(--border); border-radius: 8px; overflow: hidden; box-shadow: var(--shadow-sm); }
.link-list { padding: 0.6rem; }
.link-item {
    display: flex; align-items: center; gap: 0.8rem;
    padding: 1rem 1.2rem; border-radius: 8px;
    text-decoration: none; color: inherit; transition: var(--transition);
}
.link-item:hover { background: var(--light); }
.link-item-icon { font-size: 1.6rem; line-height: 1; flex-shrink: 0; }
.link-item-text { flex: 1; font-size: 1rem; font-weight: bold; }
.link-item-sub { font-size: 0.85rem; color: var(--muted); }
.link-item-arrow { font-size: 1.2rem; color: var(--red); font-weight: bold; }

/* PATHWAY STEPS */
.full-card { background: white; border: 1px solid var(--border); border-radius: 8px; overflow: hidden; box-shadow: var(--shadow-md); margin-bottom: 2rem; }
.steps { display: grid; grid-template-columns: 1fr; gap: 0; }
@media (min-width: 768px) { .steps { grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); } }
.step { padding: 1.5rem 1.2rem; border-bottom: 1px solid var(--border); text-align: center; }
.step:last-child { border-bottom: none; }
@media (min-width: 768px) {
    .step { border-bottom: none; border-right: 1px solid var(--border); }
    .step:last-child { border-right: none; }
}
.step-num { font-size: 2.2rem; font-weight: bold; color: var(--red); opacity: 0.25; margin-bottom: 0.6rem; line-height: 1; }
.step-title { font-size: 1.1rem; font-weight: bold; color: var(--navy); margin-bottom: 0.5rem; }
.step-desc { font-size: 0.9rem; color: var(--text-light); }
</style>

<div class="wrap">

    <nav class="topbar">
        <div class="brand">
            <div class="brand-badge">📡</div>
            <div>
                <div class="brand-name">Liverpool RAYNET</div>
                <div class="brand-sub">Zone 10 · Merseyside · Group 179</div>
            </div>
        </div>
        <div class="status-chip">
            <div class="online-dot"></div>
            <span>Regular Training Programme — Ready to Deploy</span>
        </div>
    </nav>

    <header class="page-header">
        <div class="page-header-eyebrow">// Training & Exercises</div>
        <h1>Keeping Operators<br><span>Ready to Deploy</span></h1>
        <p>Liverpool RAYNET delivers structured training in radio procedure, net control, digital modes, mapping, JESIP interoperability, and resilient deployment — through on-air nets, classroom sessions, and practical field exercises.</p>
    </header>

    <div class="stat-strip">
        <div class="stat-card">
            <div class="stat-label">Format</div>
            <div class="stat-value">3-Way</div>
            <div class="stat-sub">On-air · Classroom · Field</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Core Topics</div>
            <div class="stat-value">6</div>
            <div class="stat-sub">Across three training pillars</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Standard</div>
            <div class="stat-value">JESIP</div>
            <div class="stat-sub">Joint Emergency Services Interoperability</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Practice Nets</div>
            <div class="stat-value">Regular</div>
            <div class="stat-sub">Scheduled on-air sessions</div>
        </div>
    </div>

    <div class="coming-soon">
        <div class="coming-soon-icon">🚧</div>
        <div>
            <div class="coming-soon-title">Training Portal Coming Soon</div>
            <div class="coming-soon-desc">A dedicated online portal with full training plan, resources, and current schedule will be available here soon. In the meantime, contact us for the latest net/exercise details.</div>
        </div>
        <div class="coming-soon-chip">In Development</div>
    </div>

    {{-- ── OPERATIONAL TRAINING FRAMEWORK WHEEL ── --}}
    <div class="info-card" style="margin-bottom:2rem;">
        <div class="card-head">
            <div style="display:flex;align-items:center;justify-content:center;width:28px;height:28px;flex-shrink:0;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#003366" stroke-width="1.5" stroke-linejoin="round">
                    <polygon points="12,2 21.5,7 21.5,17 12,22 2.5,17 2.5,7"/>
                </svg>
            </div>
            <div>
                <div class="card-head-title">Operational training framework</div>
                <div class="card-head-sub">Six core competencies — three pillars — foundation and advanced levels</div>
            </div>
        </div>

        <div class="wheel-body">
            <svg id="trainingWheel" viewBox="0 0 680 680" xmlns="http://www.w3.org/2000/svg"></svg>

            <div class="wheel-legend">
                <div class="wleg">
                    <div class="wleg-sw" style="background:#003366;"></div>
                    Core competency
                </div>
                <div class="wleg">
                    <div class="wleg-sw" style="background:#e8eef5;border:1.5px solid #003366;"></div>
                    Foundation
                </div>
                <div class="wleg" style="color:#C8102E;">
                    <div class="wleg-sw" style="background:#C8102E;"></div>
                    Advanced
                </div>
            </div>

            <div class="wheel-info">
                <div class="wheel-info-title" id="wiT">Hover over any section to explore the framework</div>
                <div class="wheel-info-desc" id="wiS"></div>
            </div>
        </div>

        <div class="pillar-grid-wheel">
            <div class="pcol">
                <div class="pcol-lbl">Pillar A</div>
                <div class="pcol-name">Robust Coordination</div>
                <div class="pcol-cores">Core 1 · Mapping &amp; navigation &nbsp;·&nbsp; Core 4 · JESIP principles</div>
                <div class="pcol-hr"></div>
                <div class="pbags">
                    <span class="pbf">Positioning</span>
                    <span class="pbf">Awareness</span>
                    <span class="pba">Position reporting</span>
                    <span class="pba">Joint coordination</span>
                </div>
            </div>
            <div class="pcol">
                <div class="pcol-lbl">Pillar B</div>
                <div class="pcol-name">Resilient Systems</div>
                <div class="pcol-cores">Core 2 · Power &amp; deployment &nbsp;·&nbsp; Core 5 · Digital Comms</div>
                <div class="pcol-hr"></div>
                <div class="pbags">
                    <span class="pbf">Deployment</span>
                    <span class="pbf">Connectivity</span>
                    <span class="pba">Power sustainment</span>
                    <span class="pba">Digital systems</span>
                </div>
            </div>
            <div class="pcol">
                <div class="pcol-lbl">Pillar C</div>
                <div class="pcol-name">Radio Communications</div>
                <div class="pcol-cores">Core 3 · Radio procedure &nbsp;·&nbsp; Core 6 · Net control</div>
                <div class="pcol-hr"></div>
                <div class="pbags">
                    <span class="pbf">Clarity</span>
                    <span class="pbf">Discipline</span>
                    <span class="pba">Message control</span>
                    <span class="pba">Net control</span>
                </div>
            </div>
        </div>
    </div>
    {{-- ── END FRAMEWORK WHEEL ── --}}

    <div class="content-grid">
        <div>
            <div class="info-card">
                <div class="card-head">
                    <div class="card-head-icon">📚</div>
                    <div>
                        <div class="card-head-title">Core Training Topics</div>
                        <div class="card-head-sub">Key areas covered in our programme</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="topic-grid">
                        <div class="topic-item">
                            <div class="topic-icon">🎙️</div>
                            <div>
                                <div class="topic-title">Radio Procedure</div>
                                <div class="topic-desc">Phonetic alphabet, prowords, net discipline, message handling, and voice procedure to RAYNET-UK standards.</div>
                            </div>
                        </div>
                        <div class="topic-item">
                            <div class="topic-icon">🗼</div>
                            <div>
                                <div class="topic-title">Net Control</div>
                                <div class="topic-desc">Running a net, traffic management, logging, priority/emergency handling.</div>
                            </div>
                        </div>
                        <div class="topic-item">
                            <div class="topic-icon">🗺️</div>
                            <div>
                                <div class="topic-title">Mapping & Navigation</div>
                                <div class="topic-desc">OS grid references, six-figure coords, sketching, position reporting in field ops.</div>
                            </div>
                        </div>
                        <div class="topic-item">
                            <div class="topic-icon">💻</div>
                            <div>
                                <div class="topic-title">Digital Comms</div>
                                <div class="topic-desc">APRS tracking, Winlink email-over-radio, packet, JS8Call for resilient data links.</div>
                            </div>
                        </div>
                        <div class="topic-item">
                            <div class="topic-icon">🤝</div>
                            <div>
                                <div class="topic-title">JESIP Principles</div>
                                <div class="topic-desc">Joint working: co-location, clear communication, coordination, shared risk understanding, situational awareness.</div>
                            </div>
                        </div>
                        <div class="topic-item">
                            <div class="topic-icon">🔋</div>
                            <div>
                                <div class="topic-title">Power & Deployment</div>
                                <div class="topic-desc">Battery/solar management, antenna setup, vehicle/fixed/portable operation.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-card">
                <div class="card-head">
                    <div class="card-head-icon">⚡</div>
                    <div>
                        <div class="card-head-title">Training Formats</div>
                        <div class="card-head-sub">Three complementary methods to build readiness</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="cap-list">
                        <div class="cap-item">
                            <div class="cap-dot" style="background:var(--navy);"></div>
                            <div>
                                <div class="cap-item-title">On-Air Practice Nets</div>
                                <div class="cap-item-desc">Regular scheduled nets on our calling frequency to practise procedure, message passing, and emergency handling from home/portable stations.</div>
                            </div>
                        </div>
                        <div class="cap-item">
                            <div class="cap-dot" style="background:var(--red);"></div>
                            <div>
                                <div class="cap-item-title">Classroom Sessions</div>
                                <div class="cap-item-desc">Theory-focused presentations/discussions on RAYNET doctrine, JESIP, licensing, and lessons from real activations.</div>
                            </div>
                        </div>
                        <div class="cap-item">
                            <div class="cap-dot" style="background:var(--navy);"></div>
                            <div>
                                <div class="cap-item-title">Practical Field Exercises</div>
                                <div class="cap-item-desc">Hands-on deployments across Merseyside: rapid setup, portable ops, navigation, team coordination.</div>
                            </div>
                        </div>
                        <div class="cap-item">
                            <div class="cap-dot" style="background:var(--red);"></div>
                            <div>
                                <div class="cap-item-title">Inter-Group & National Exercises</div>
                                <div class="cap-item-desc">Participation in RAYNET-UK regional/national scenarios with other groups and partner agencies.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="side-col">
            <div class="notice-card">
                <div class="notice-card-body">
                    <div class="notice-badge">🎓</div>
                    <div class="notice-title">Interested in Joining?</div>
                    <div class="notice-desc">Training is open to licensed amateur radio operators keen to support emergency communications. No prior RAYNET experience required — we welcome Foundation licence holders and above.</div>
                    <div class="notice-divider"></div>
                    <div class="notice-row">
                        <div class="notice-row-icon">🪪</div>
                        <div class="notice-row-text"><strong>Licence Required</strong> — Valid Ofcom amateur licence (Foundation+) needed to operate.</div>
                    </div>
                    <div class="notice-row">
                        <div class="notice-row-icon">📻</div>
                        <div class="notice-row-text"><strong>Equipment</strong> — A basic 2m handheld is sufficient to start. We offer guidance on suitable gear.</div>
                    </div>
                    <div class="notice-row">
                        <div class="notice-row-icon">📬</div>
                        <div class="notice-row-text"><strong>Get in Touch</strong> — Contact us via the members' area or form to express interest — we'll guide you through the next steps.</div>
                    </div>
                </div>
            </div>

            <div class="link-card" style="margin-top:1.5rem;">
                <div class="card-head">
                    <div class="card-head-icon">🔗</div>
                    <div>
                        <div class="card-head-title">Related Pages</div>
                    </div>
                </div>
                <div class="link-list">
                    <a href="{{ route('about') }}" class="link-item">
                        <div class="link-item-icon">📡</div>
                        <div>
                            <div class="link-item-text">About RAYNET</div>
                            <div class="link-item-sub">Who we are & capabilities</div>
                        </div>
                        <div class="link-item-arrow">→</div>
                    </a>
                    <a href="{{ route('event-support') }}" class="link-item">
                        <div class="link-item-icon">🏁</div>
                        <div>
                            <div class="link-item-text">Event Support</div>
                            <div class="link-item-sub">How we assist events</div>
                        </div>
                        <div class="link-item-arrow">→</div>
                    </a>
                    <a href="{{ route('members') }}" class="link-item">
                        <div class="link-item-icon">👥</div>
                        <div>
                            <div class="link-item-text">Members' Area</div>
                            <div class="link-item-sub">Operator resources & hub</div>
                        </div>
                        <div class="link-item-arrow">→</div>
                    </a>
                    <a href="https://www.raynet-uk.net" target="_blank" rel="noopener" class="link-item">
                        <div class="link-item-icon">🌐</div>
                        <div>
                            <div class="link-item-text">RAYNET-UK</div>
                            <div class="link-item-sub">National training & doctrine</div>
                        </div>
                        <div class="link-item-arrow">↗</div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="full-card">
        <div class="card-head">
            <div class="card-head-icon">🚀</div>
            <div>
                <div class="card-head-title">Pathway to Deployment Readiness</div>
                <div class="card-head-sub">From joining to active operational support</div>
            </div>
        </div>
        <div class="steps">
            <div class="step">
                <div class="step-num">01</div>
                <div class="step-title">Hold a Licence</div>
                <div class="step-desc">Obtain a valid Ofcom amateur radio licence (Foundation, Intermediate or Full). RSGB Foundation is ideal for newcomers.</div>
            </div>
            <div class="step">
                <div class="step-num">02</div>
                <div class="step-title">Join Liverpool RAYNET</div>
                <div class="step-desc">Register with the group, complete RAYNET-UK membership, and gain access to callout list and training resources.</div>
            </div>
            <div class="step">
                <div class="step-num">03</div>
                <div class="step-title">Attend Practice Nets</div>
                <div class="step-desc">Join scheduled nets to build confidence in procedure, discipline, and message handling under guidance.</div>
            </div>
            <div class="step">
                <div class="step-num">04</div>
                <div class="step-title">Complete Core Training</div>
                <div class="step-desc">Cover radio procedure, mapping, JESIP, digital comms, and deployment skills via classroom and field sessions.</div>
            </div>
            <div class="step">
                <div class="step-num">05</div>
                <div class="step-title">Deploy Operationally</div>
                <div class="step-desc">Once assessed ready, join activations and events alongside experienced operators as part of the team.</div>
            </div>
        </div>
    </div>

</div>

<script>
(function () {
    const svg = document.getElementById('trainingWheel');
    if (!svg) return;

    const wiT = document.getElementById('wiT');
    const wiS = document.getElementById('wiS');

    const NAVY = '#003366', NAVYFAINT = '#e8eef5', RED = '#C8102E', REDDARK = '#9e0b23';
    const CX = 340, CY = 340;
    const C_R = 52, I_R = 54, I_D = 145, O_R = 42, O_D = 262;

    const rad = d => d * Math.PI / 180;
    const at  = (a, d) => ({ x: CX + d * Math.cos(rad(a)), y: CY + d * Math.sin(rad(a)) });
    const hp  = (cx, cy, R) => Array.from({ length: 6 }, (_, i) => {
        const a = rad(30 + i * 60);
        return `${(cx + R * Math.cos(a)).toFixed(1)},${(cy + R * Math.sin(a)).toFixed(1)}`;
    }).join(' ');

    const NS = 'http://www.w3.org/2000/svg';
    const mk = (t, a, p) => {
        const e = document.createElementNS(NS, t);
        for (const [k, v] of Object.entries(a)) e.setAttribute(k, v);
        p && p.appendChild(e);
        return e;
    };
    const tx = (p, x, y, s, f, sz, w, op) => {
        const t = mk('text', {
            x, y,
            'text-anchor': 'middle', 'dominant-baseline': 'middle',
            fill: f || '#fff', 'font-family': 'Arial,sans-serif',
            'font-size': sz || '12', 'font-weight': w || 'normal',
            'pointer-events': 'none',
            ...(op ? { opacity: op } : {})
        }, p);
        t.textContent = s;
        return t;
    };
    const mtx = (p, cx, cy, ls, opts = {}) => {
        const lh = opts.lh || 14, tot = (ls.length - 1) * lh;
        ls.forEach((l, i) => tx(p, cx, cy - tot / 2 + i * lh, l, opts.fill || '#fff', opts.sz || '12', opts.w || 'normal'));
    };

    const setInfo = (t, s) => {
        wiT.textContent = t;
        wiT.style.color = '#003366';
        wiT.style.fontWeight = 'bold';
        wiS.textContent = s || '';
    };
    const clr = () => {
        wiT.textContent = 'Hover over any section to explore the framework';
        wiT.style.color = '#9aa3ae';
        wiT.style.fontWeight = 'normal';
        wiS.textContent = '';
    };

    /* Guide rings */
    [90, 200, 305].forEach(r => mk('circle', {
        cx: CX, cy: CY, r, fill: 'none',
        stroke: NAVYFAINT, 'stroke-width': '1', 'stroke-dasharray': '4,8', opacity: '.5'
    }, svg));

    /* Spokes */
    for (let a = -90; a < 270; a += 30) {
        const e = at(a, 308);
        mk('line', {
            x1: CX, y1: CY, x2: e.x.toFixed(1), y2: e.y.toFixed(1),
            stroke: NAVYFAINT, 'stroke-width': '.8', 'stroke-dasharray': '3,8', opacity: '.35'
        }, svg);
    }

    /* ── DATA ── */
    const INNER = [
        { a: -90, n: '1', ls: ['Mapping &', 'navigation'],  tt: 'Core 1 — Mapping & navigation',  ts: 'OS grid references, position reporting, field navigation and sketching' },
        { a: -30, n: '2', ls: ['Power &', 'deployment'],    tt: 'Core 2 — Power & deployment',    ts: 'Battery and solar management, antenna setup, portable and vehicle operations' },
        { a:  30, n: '3', ls: ['Radio', 'procedure'],       tt: 'Core 3 — Radio procedure',       ts: 'Phonetic alphabet, prowords, net discipline and message handling' },
        { a:  90, n: '4', ls: ['JESIP', 'principles'],      tt: 'Core 4 — JESIP principles',      ts: 'Joint working, co-location, communication and shared situational awareness' },
        { a: 150, n: '5', ls: ['Digital', 'comms'],         tt: 'Core 5 — Digital Comms',         ts: 'APRS, Winlink, JS8Call, packet — resilient data link operation' },
        { a: 210, n: '6', ls: ['Net', 'control'],           tt: 'Core 6 — Net control',           ts: 'Running a net, traffic management, emergency handling and logging' },
    ];

    const OUTER = [
        { a: -90, ls: ['Positioning'],          F: 1, tt: 'Foundation — Positioning',        ts: 'Basic location awareness and OS grid reference reporting' },
        { a: -60, ls: ['Position', 'reporting'], F: 0, tt: 'Advanced — Position reporting',   ts: 'Dynamic position reporting during active multi-agency operations' },
        { a: -30, ls: ['Deployment'],            F: 1, tt: 'Foundation — Deployment',         ts: 'Core portable station deployment and setup skills' },
        { a:   0, ls: ['Power', 'sustainment'],  F: 0, tt: 'Advanced — Power sustainment',    ts: 'Extended operations power management and resilience planning' },
        { a:  30, ls: ['Clarity'],               F: 1, tt: 'Foundation — Clarity',            ts: 'Clear, disciplined voice procedure and on-air conduct' },
        { a:  60, ls: ['Message', 'control'],    F: 0, tt: 'Advanced — Message control',      ts: 'Formal message handling, proformas, precedence and routing' },
        { a:  90, ls: ['Awareness'],             F: 1, tt: 'Foundation — Awareness',          ts: 'Situational awareness in joint agency and multi-team environments' },
        { a: 120, ls: ['Joint', 'coordination'], F: 0, tt: 'Advanced — Joint coordination',   ts: 'Multi-agency interoperability and operational coordination' },
        { a: 150, ls: ['Connectivity'],          F: 1, tt: 'Foundation — Connectivity',       ts: 'Basic digital mode understanding and data link operation' },
        { a: 180, ls: ['Digital', 'systems'],    F: 0, tt: 'Advanced — Digital systems',      ts: 'Full deployment of APRS, Winlink and JS8Call suite' },
        { a: 210, ls: ['Discipline'],            F: 1, tt: 'Foundation — Discipline',         ts: 'Net discipline, listening procedure and traffic management' },
        { a: 240, ls: ['Net', 'control'],        F: 0, tt: 'Advanced — Net control',          ts: 'Full net control operation under operational pressure' },
    ];

    /* Outer ring */
    OUTER.forEach(d => {
        const p = at(d.a, O_D);
        const g = mk('g', { style: 'cursor:pointer' }, svg);
        mk('polygon', {
            points: hp(p.x, p.y, O_R),
            fill: d.F ? NAVYFAINT : RED,
            stroke: d.F ? NAVY : REDDARK,
            'stroke-width': '1.5'
        }, g);
        mtx(g, p.x, p.y, d.ls, { fill: d.F ? NAVY : '#fff', sz: '11', w: 'bold', lh: 13 });
        const on  = () => { g.style.opacity = '.82'; setInfo(d.tt, d.ts); };
        const off = () => { g.style.opacity = '1';   clr(); };
        g.addEventListener('mouseenter', on);
        g.addEventListener('mouseleave', off);
        g.addEventListener('touchstart', e => { e.preventDefault(); on(); }, { passive: false });
        g.addEventListener('touchend', off);
    });

    /* Inner ring */
    INNER.forEach(d => {
        const p = at(d.a, I_D);
        const g = mk('g', { style: 'cursor:pointer' }, svg);
        mk('polygon', { points: hp(p.x, p.y, I_R), fill: NAVY, stroke: RED, 'stroke-width': '2' }, g);
        tx(g, p.x, p.y, d.n, 'rgba(255,255,255,0.09)', '52', 'bold');
        mtx(g, p.x, p.y, d.ls, { fill: '#fff', sz: '12', w: 'bold', lh: 14 });
        const on  = () => { g.style.opacity = '.82'; setInfo(d.tt, d.ts); };
        const off = () => { g.style.opacity = '1';   clr(); };
        g.addEventListener('mouseenter', on);
        g.addEventListener('mouseleave', off);
        g.addEventListener('touchstart', e => { e.preventDefault(); on(); }, { passive: false });
        g.addEventListener('touchend', off);
    });

    /* Centre hex */
    const cg = mk('g', {}, svg);
    mk('polygon', { points: hp(CX, CY, C_R), fill: NAVY, stroke: RED, 'stroke-width': '3' }, cg);
    tx(cg, CX, CY - 7, 'RAYNET',   '#fff', '13', 'bold');
    tx(cg, CX, CY + 8, 'training', 'rgba(255,255,255,0.55)', '11');

})();
</script>

@endsection