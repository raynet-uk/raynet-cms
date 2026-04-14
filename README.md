📘 Liverpool RAYNET — Laravel 12 Web Platform

Project README & Development Roadmap

This repository contains the next-generation Liverpool RAYNET web platform, built in Laravel 12 with a clean modular architecture, feature-branch workflow, and a long-term multi-milestone roadmap.

This document captures the full project structure, purpose of each branch, and the official milestone plan — so future development is always aligned, traceable, and organised.

⸻

🚀 1. Project Scope

The platform provides:
	•	Member login system with callsign authentication
	•	Admin panel (operators, events, roles, alert status)
	•	Member dashboard
	•	Events & calendar engine
	•	Propagation & space-weather engine
	•	Checkpoint magazine library
	•	KPI / status cards
	•	JSON & ICS feeds
	•	Future integration with SignalSafe services

Everything is developed using a strict feature-branch workflow.

⸻

🧭 2. Official Feature Branches

These branches exist (or must be created) one per subsystem.
No development is done directly on main.

Core Backend

Branch	Purpose
feature/propagation-engine	Space-weather ingestion, SignalSafe JSON, cron generation, Condx API
feature/status-block	RAYNET alert status cards & global banner
feature/member-dashboard	Fully featured member dashboard (cards, alerts, events, profile)
feature/admin-ui-refactor	Table redesign, navigation, admin UX improvements
feature/calendar-ics	ICS export engine, event feeds, calendar endpoints
feature/memberships	Membership tracking, renewal dates, roles, permissions

Publishing / Media

Branch	Purpose
feature/checkpoint-library	Checkpoint magazine upload, PDF viewer, archive
feature/newsletter-library	Newsletter system (HTML/PDF), archive, categories

Radio & Operational Tools

Branch	Purpose
feature/kpi-slider	Conversion of Concrete KPI blocks → Laravel components
feature/dmr-viewer	Live DMR talkgroup / hotspot viewer
feature/frequency-lists	Editable RAYNET frequency pages (HF/VHF/UHF + bandplans)
feature/call-out-cascade	Call-out engine, escalation tree, SMS/email broadcast


⸻

🧩 3. Eight-Milestone Development Plan

These are the canonical project milestones you approved — stored here for long-term reference.

Milestone 1 – Core Framework & Auth

✔ Laravel 12 installed
✔ Breeze login
✔ Email/callsign login
✔ Admin user model
✔ Basic home page

Milestone 2 – Events System (Core)

✔ Events CRUD
✔ Event types
✔ Slugs + multi-day support
✔ Export/import (CSV)
✔ Seeder for sample events

Milestone 3 – Status Engine (Completed)

✔ RAYNET alert status
✔ Global banner
✔ Admin control panel
✔ Status card component
✔ Live injection into homepage

Milestone 3.1 – RAYNET Alert Status Block (Addon)

✔ Status card
✔ Status colour logic
✔ Integration with admin settings

Milestone 3.2 – Frequency Block (Addon)

✔ Frequency list tables
✔ JSON structure for later dynamic updates

Milestone 4 – Member Dashboard Framework

✔ Member landing page
✔ Profile view
✔ Operator integration
✔ Auth middleware
(Enhancements ongoing under feature/member-dashboard)

Milestone 5 – Consent Forms + Policy Acknowledgements

Pending: digital signatures / audit log

Milestone 6 – UK Propagation Brief Engine

✔ Backend design
✔ Conversion map from Concrete CMS
🚧 Implemented in feature/kpi-slider & feature/propagation-engine

Milestone 7 – Space-Weather Slider Components

Kp slider, SFI, MUF, HF/VHF blocks → Laravel Blade components

Milestone 8 – Deployment & API Output

To Krystal.io with:
	•	Static asset build
	•	Queue worker
	•	Cron for propagation engine
	•	JSON+ICS public feeds

⸻

🗂 4. Branch → Milestone Mapping

Branch	Linked Milestones
feature/kpi-slider	M6, M7
feature/propagation-engine	M6, M8
feature/status-block	M3, M3.1
feature/member-dashboard	M4
feature/admin-ui-refactor	M4, M8
feature/calendar-ics	M2, M8
feature/memberships	M5
feature/checkpoint-library	Publishing system (future milestone)
feature/newsletter-library	Publishing system (future milestone)
feature/dmr-viewer	Future radio milestone
feature/frequency-lists	M3.2
feature/call-out-cascade	Future call-out milestone


⸻

📡 5. Propagation Block Conversion (Concrete → Laravel)

This maps how each old block becomes a Laravel component.

Concrete Block	Laravel Component	Branch
Kp Slider	<x-kpi-card>	feature/kpi-slider
SFI Meter	<x-kpi-card>	feature/kpi-slider
MUF(3000) Widget	<x-kpi-card> or <x-outlook-card>	feature/kpi-slider
HF Conditions	<x-outlook-card>	feature/kpi-slider
VHF Tropo Outlook	<x-outlook-card>	feature/kpi-slider
UK Propagation Brief	<x-condx-summary-card>	feature/propagation-engine


⸻

🌐 6. Required Environment Variables

CONDX_JSON_URL="https://signalsafe.uk/feeds/condx.json"
CONDX_CACHE_TTL=10
CONDX_SHOW_ADVANCED=false


⸻

🔧 7. Development Workflow

git checkout main
git pull
git checkout -b feature/my-task
# develop
git push -u origin feature/my-task
# open PR

Main is protected — no direct development permitted.

⸻

🧪 8. Testing
	•	PHPUnit for services
	•	Pest optional
	•	UI tested manually in local dev

More structured tests will be added in Milestone 8.

⸻

🚀 9. Deployment

Final deployment will target Krystal.io (Laravel + MySQL).
Production requires:
	•	php artisan optimize
	•	Queues enabled
	•	Cron for propagation engine
	•	Manual upload via GitHub Actions or SSH deploy script

⸻

🔐 10. Security Policy
	•	Admin guard separate from user guard
	•	Password expiry logic
	•	Forced password resets
	•	Operators and Users stored separately
	•	No direct S3 uploads without validation

⸻

📄 11. License

Internal Liverpool RAYNET development.
Not for external redistribution.

