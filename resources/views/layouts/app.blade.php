<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ \App\Helpers\RaynetSetting::groupName() }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#003366">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --navy: #003366; --navy-mid: #004080; --navy-faint: #e8eef5;
            --red: #C8102E; --red-faint: #fdf0f2;
            --white: #FFFFFF; --light: #F2F2F2; --grey: #F2F2F2;
            --grey-mid: #dde2e8; --grey-dark: #9aa3ae;
            --text: #003366; --text-light: #1A1A1A; --text-mid: #2d4a6b; --text-muted: #6b7f96;
            --muted: #4A4A1A; --border: #D0D0D0;
            --green: #1a6b3c; --green-bg: #eef7f2;
            --shadow-sm: 0 2px 8px rgba(0,51,102,0.06);
            --shadow-md: 0 4px 16px rgba(0,51,102,0.13);
            --transition: all 0.2s ease; --nav-height: 60px;
            --font: Arial, 'Helvetica Neue', Helvetica, sans-serif;
        }
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: var(--font); background: var(--light); color: var(--text); line-height: 1.55; min-height: 100vh; display: flex; flex-direction: column; }
        .site-shell { flex: 1; display: flex; flex-direction: column; }
        .impersonate-bar { background: #7c2d00; border-bottom: 3px solid #ea580c; display: flex; align-items: center; justify-content: space-between; padding: .55rem 1.5rem; gap: 1rem; flex-wrap: wrap; box-shadow: 0 3px 12px rgba(0,0,0,.4); position: sticky; top: 0; z-index: 1100; }
        .impersonate-bar-left { display: flex; align-items: center; gap: .75rem; }
        .impersonate-bar-icon { width: 28px; height: 28px; background: #ea580c; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; }
        .impersonate-bar-text { font-size: 13px; font-weight: bold; color: #fed7aa; letter-spacing: .02em; }
        .impersonate-bar-text em { color: #fdba74; font-style: normal; }
        .impersonate-bar-sub { font-size: 11px; color: #fb923c; margin-top: 1px; }
        .btn-exit-impersonate { padding: .4rem 1.1rem; background: #ea580c; border: 1px solid #c2410c; color: white; font-family: var(--font); font-size: 11px; font-weight: bold; cursor: pointer; text-transform: uppercase; letter-spacing: .08em; transition: all .12s; white-space: nowrap; flex-shrink: 0; }
        .btn-exit-impersonate:hover { background: #c2410c; border-color: #9a3412; }
        .admin-message-bar { background: #1e3a5f; border-bottom: 3px solid #3b82f6; display: flex; align-items: center; justify-content: space-between; padding: .55rem 1.5rem; gap: 1rem; flex-wrap: wrap; position: sticky; top: 0; z-index: 1090; }
        .admin-message-bar-left { display: flex; align-items: center; gap: .75rem; }
        .admin-message-bar-icon { width: 28px; height: 28px; background: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; }
        .admin-message-bar-text { font-size: 13px; font-weight: bold; color: #bfdbfe; }
        .admin-message-bar-text span { font-weight: normal; color: #dbeafe; }
        .btn-dismiss-message { padding: .4rem 1.1rem; background: #3b82f6; border: 1px solid #2563eb; color: white; font-family: var(--font); font-size: 11px; font-weight: bold; cursor: pointer; text-transform: uppercase; letter-spacing: .08em; transition: all .12s; white-space: nowrap; flex-shrink: 0; }
        .btn-dismiss-message:hover { background: #2563eb; }
        .broadcast-bar { background: #14532d; border-bottom: 3px solid #22c55e; display: flex; align-items: center; justify-content: space-between; padding: .55rem 1.5rem; gap: 1rem; flex-wrap: wrap; position: sticky; top: 0; z-index: 1080; }
        .broadcast-bar-left { display: flex; align-items: center; gap: .75rem; }
        .broadcast-bar-icon { width: 28px; height: 28px; background: #22c55e; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; }
        .broadcast-bar-text { font-size: 13px; font-weight: bold; color: #bbf7d0; }
        .broadcast-bar-text span { font-weight: normal; color: #dcfce7; }
        .btn-dismiss-broadcast { padding: .4rem 1.1rem; background: #22c55e; border: 1px solid #16a34a; color: white; font-family: var(--font); font-size: 11px; font-weight: bold; cursor: pointer; text-transform: uppercase; letter-spacing: .08em; transition: all .12s; white-space: nowrap; flex-shrink: 0; }
        .btn-dismiss-broadcast:hover { background: #16a34a; }
        .navbar { position: sticky; top: 0; z-index: 200; height: var(--nav-height); background: white; border-bottom: 2px solid var(--navy); box-shadow: var(--shadow-sm); }
        .nav-container { max-width: 1280px; margin: 0 auto; height: 100%; padding: 0 1rem; display: flex; align-items: center; justify-content: space-between; }
        .brand { display: flex; align-items: center; text-decoration: none; }
        .brand-tagline { font-size: 9px; font-weight: 700; letter-spacing: .18em; text-transform: uppercase; color: var(--red); font-family: var(--font); line-height: 1; padding-left: 2px; white-space: nowrap; }
        .brand-logo { height: 38px; width: auto; }
        .nav-main { display: flex; align-items: center; gap: 1.2rem; }
        .nav-main a { color: var(--muted); font-size: 0.95rem; font-weight: 500; text-decoration: none; padding: 0.4rem 0.6rem; transition: color 0.2s ease; }
        .nav-main a:hover, .nav-main a.active { color: var(--red); }
        .header-controls { display: flex; align-items: center; gap: 0.6rem; }
        .bell-wrap { position: relative; }
        .bell-btn { width: 38px; height: 38px; border-radius: 50%; background: var(--light); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 17px; color: var(--navy); transition: background .15s; position: relative; flex-shrink: 0; }
        .bell-btn:hover { background: var(--navy-faint); border-color: var(--navy); }
        .bell-badge { position: absolute; top: -5px; right: -5px; min-width: 18px; height: 18px; border-radius: 999px; background: var(--red); border: 2px solid white; display: none; align-items: center; justify-content: center; font-size: 10px; font-weight: bold; color: white; padding: 0 4px; line-height: 1; font-family: var(--font); }
        .bell-badge.visible { display: flex; }
        .notif-panel { display: none; position: absolute; top: calc(100% + 10px); right: 0; width: 320px; background: var(--white); border: 1px solid var(--grey-mid); border-top: 3px solid var(--navy); box-shadow: var(--shadow-md); z-index: 500; }
        .notif-panel.open { display: block; }
        .notif-panel-head { padding: .65rem 1rem; background: var(--grey); border-bottom: 1px solid var(--grey-mid); display: flex; align-items: center; justify-content: space-between; }
        .notif-panel-title { font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: .1em; color: var(--navy); }
        .notif-mark-all { font-size: 10px; font-weight: bold; color: var(--text-muted); background: none; border: none; cursor: pointer; padding: 0; text-transform: uppercase; letter-spacing: .05em; font-family: var(--font); }
        .notif-mark-all:hover { color: var(--navy); }
        .notif-list { max-height: 340px; overflow-y: auto; }
        .notif-item { display: flex; align-items: flex-start; gap: .75rem; padding: .75rem 1rem; border-bottom: 1px solid var(--grey-mid); transition: background .1s; cursor: default; }
        .notif-item:last-child { border-bottom: none; }
        .notif-item:hover { background: var(--navy-faint); }
        .notif-item.unread { background: #f0f5ff; }
        .notif-item.unread:hover { background: #e4edff; }
        .notif-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--red); flex-shrink: 0; margin-top: 5px; }
        .notif-dot.read { background: var(--grey-mid); }
        .notif-text { flex: 1; }
        .notif-text strong { color: var(--navy); display: block; font-size: 12px; margin-bottom: 2px; }
        .notif-body { font-size: 12px; color: var(--text-mid); line-height: 1.5; }
        .notif-time { font-size: 10px; color: var(--text-muted); margin-top: 3px; }
        .notif-empty { padding: 2rem 1rem; text-align: center; font-size: 12px; color: var(--text-muted); }
        .notif-empty-icon { font-size: 1.75rem; opacity: .25; margin-bottom: .5rem; }
        .notif-footer { padding: .6rem 1rem; border-top: 1px solid var(--grey-mid); background: var(--grey); text-align: center; }
        .notif-footer a { font-size: 11px; font-weight: bold; color: var(--navy); text-decoration: none; text-transform: uppercase; letter-spacing: .07em; }
        .notif-footer a:hover { color: var(--red); }
        .avatar-wrap { position: relative; }
        .avatar-btn { display: flex; align-items: center; gap: .5rem; background: var(--light); border: 1px solid var(--border); padding: .3rem .65rem .3rem .3rem; border-radius: 999px; cursor: pointer; transition: background .15s, border-color .15s; font-family: var(--font); }
        .avatar-btn:hover { background: var(--navy-faint); border-color: var(--navy); }
        .avatar-circle { width: 32px; height: 32px; border-radius: 50%; background: var(--navy); border: 2px solid rgba(0,51,102,.15); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold; color: #fff; flex-shrink: 0; text-transform: uppercase; }
        .avatar-btn-text { display: flex; flex-direction: column; align-items: flex-start; line-height: 1.2; }
        .avatar-name { font-size: 13px; font-weight: 600; color: var(--navy); white-space: nowrap; max-width: 110px; overflow: hidden; text-overflow: ellipsis; }
        .avatar-callsign { font-size: 10px; font-weight: 700; color: var(--text-muted); font-family: var(--font); letter-spacing: .06em; }
        .avatar-chevron { font-size: 9px; color: var(--grey-dark); margin-left: 2px; transition: transform .2s; display: inline-block; }
        .avatar-btn[aria-expanded="true"] .avatar-chevron { transform: rotate(180deg); }
        .avatar-dropdown { display: none; position: absolute; top: calc(100% + 10px); right: 0; width: 270px; background: var(--white); border: 1px solid var(--grey-mid); box-shadow: 0 8px 32px rgba(0,31,64,.18), 0 2px 8px rgba(0,31,64,.08); z-index: 500; overflow: hidden; }
        .avatar-dropdown.open { display: block; animation: ddFadeIn .15s ease; }
        @keyframes ddFadeIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:none; } }
        .avatar-dd-user { padding: 1rem; background: var(--navy); display: flex; align-items: center; gap: .75rem; position: relative; }
        .avatar-dd-circle { width: 42px; height: 42px; border-radius: 50%; background: rgba(255,255,255,.15); border: 2px solid rgba(255,255,255,.25); display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: bold; color: #fff; flex-shrink: 0; text-transform: uppercase; }
        .avatar-dd-user-info { flex: 1; min-width: 0; }
        .avatar-dd-name { font-size: 13px; font-weight: bold; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .avatar-dd-callsign { font-size: 11px; font-weight: 700; color: rgba(255,255,255,.6); font-family: var(--font); letter-spacing: .06em; margin-top: 1px; }
        .avatar-dd-email { font-size: 11px; color: rgba(255,255,255,.45); margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .avatar-dd-callsign-badge { position: absolute; top: .65rem; right: .75rem; font-size: 9px; font-weight: bold; padding: 2px 7px; background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2); color: rgba(255,255,255,.75); font-family: var(--font); letter-spacing: .07em; text-transform: uppercase; }
        .avatar-dd-status { display: flex; align-items: center; gap: .5rem; padding: .4rem 1rem; background: var(--navy-faint); border-bottom: 1px solid var(--grey-mid); font-size: 11px; }
        .avatar-dd-status-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--green); flex-shrink: 0; box-shadow: 0 0 0 2px rgba(26,107,60,.2); }
        .avatar-dd-status-text { color: var(--green); font-weight: bold; font-size: 10px; text-transform: uppercase; letter-spacing: .08em; }
        .avatar-dd-role-chip { margin-left: auto; font-size: 9px; font-weight: bold; padding: 1px 6px; text-transform: uppercase; letter-spacing: .06em; }
        .avatar-dd-menu { padding: .35rem 0; }
        .avatar-dd-item { display: flex; align-items: center; gap: .65rem; padding: .55rem 1rem; font-size: 13px; font-weight: 500; color: var(--text-mid); text-decoration: none; transition: background .1s, color .1s; cursor: pointer; font-family: var(--font); background: none; border: none; width: 100%; text-align: left; }
        .avatar-dd-item:hover { background: var(--navy-faint); color: var(--navy); }
        .avatar-dd-item-icon { width: 20px; text-align: center; font-size: 14px; flex-shrink: 0; }
        .avatar-dd-item-arrow { margin-left: auto; font-size: 11px; color: var(--grey-dark); opacity: 0; transition: opacity .1s, transform .1s; }
        .avatar-dd-item:hover .avatar-dd-item-arrow { opacity: 1; transform: translateX(2px); }
        .avatar-dd-item.danger { color: var(--red); }
        .avatar-dd-item.danger:hover { background: var(--red-faint); }
        .avatar-dd-item-elevated { color: var(--navy); font-weight: 600; }
        .avatar-dd-item-elevated:hover { background: var(--navy-faint); }
        .avatar-dd-divider { height: 1px; background: var(--grey-mid); margin: .35rem 0; display: flex; align-items: center; position: relative; }
        .avatar-dd-divider-label { position: absolute; left: 1rem; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: .12em; color: var(--text-muted); background: var(--white); padding: 0 .4rem 0 0; }
        .avatar-dd-footer { padding: .5rem 1rem; background: var(--grey); border-top: 1px solid var(--grey-mid); font-size: 10px; color: var(--text-muted); text-align: center; letter-spacing: .04em; }
        .btn-pill { padding: 0.5rem 1.1rem; border-radius: 999px; font-size: 0.9rem; font-weight: 600; text-decoration: none; transition: all 0.2s ease; border: 1px solid transparent; display: inline-flex; align-items: center; }
        .btn-pill:hover { transform: translateY(-1px); }
        .btn-member { background: var(--navy); color: white; }
        .btn-member:hover { background: #002244; }
        .btn-register { background: var(--red); color: white; }
        .btn-register:hover { background: #a00d25; }
        .nav-impersonate-pill { display: inline-flex; align-items: center; gap: .4rem; padding: .3rem .85rem; border-radius: 999px; background: #ea580c; border: 1px solid #c2410c; color: white; font-size: 0.8rem; font-weight: bold; letter-spacing: .04em; white-space: nowrap; }
        .nav-impersonate-pill .pip { width: 7px; height: 7px; border-radius: 50%; background: #fed7aa; animation: pulse-pip 1.8s ease-in-out infinite; flex-shrink: 0; }
        @keyframes pulse-pip { 0%, 100% { opacity: 1; } 50% { opacity: .3; } }
        .hamburger { display: none; flex-direction: column; gap: 5px; width: 36px; height: 36px; background: transparent; border: none; cursor: pointer; padding: 8px; position: relative; }
        .hamburger span { width: 24px; height: 2.5px; background: var(--navy); border-radius: 2px; transition: all 0.3s ease; display: block; }
        .hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .hamburger.open span:nth-child(2) { opacity: 0; }
        .hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }
        .hamburger-badge { position: absolute; top: 0; right: 0; min-width: 16px; height: 16px; border-radius: 999px; background: var(--red); border: 2px solid white; font-size: 9px; font-weight: bold; color: white; display: flex; align-items: center; justify-content: center; padding: 0 3px; line-height: 1; font-family: var(--font); pointer-events: none; }
        .mobile-menu { display: none; flex-direction: column; background: white; border-top: 1px solid var(--border); padding: 1rem 1.2rem; box-shadow: var(--shadow-sm); max-height: calc(100dvh - var(--nav-height) - 10px); overflow-y: auto; -webkit-overflow-scrolling: touch; }
        .mobile-menu.open { display: flex; }
        .mobile-menu-user { display: flex; align-items: center; gap: .75rem; padding: .85rem 0; border-bottom: 1px solid var(--border); margin-bottom: .5rem; }
        .mobile-avatar-circle { width: 42px; height: 42px; border-radius: 50%; background: var(--navy); display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: bold; color: #fff; flex-shrink: 0; text-transform: uppercase; }
        .mobile-user-name { font-size: 14px; font-weight: bold; color: var(--navy); }
        .mobile-user-role { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
        .mobile-nav-section { margin-bottom: .5rem; }
        .mobile-nav-label { font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: .12em; color: var(--text-muted); padding: .4rem 1rem .2rem 0; }
        .mobile-menu a, .mobile-dd-btn { display: flex; align-items: center; gap: .65rem; padding: 0.8rem 1rem; border-radius: 6px; font-size: 0.95rem; font-weight: 500; color: var(--muted); text-decoration: none; transition: all 0.2s; margin: 0.2rem 0; font-family: var(--font); background: none; border: none; width: 100%; text-align: left; cursor: pointer; }
        .mobile-menu a:hover, .mobile-dd-btn:hover { background: var(--light); color: var(--navy); }
        .mobile-menu a.active { background: var(--light); color: var(--red); font-weight: 600; }
        .mobile-menu-icon { font-size: 14px; width: 20px; text-align: center; flex-shrink: 0; }
        .mobile-dd-btn.danger { color: var(--red); }
        .mobile-dd-btn.danger:hover { background: var(--red-faint); }
        .mobile-exit-impersonate { display: flex; align-items: center; gap: .6rem; padding: .75rem 1rem; background: #7c2d00; border: 1px solid #c2410c; border-radius: 6px; color: #fed7aa; font-size: 0.9rem; font-weight: bold; cursor: pointer; width: 100%; font-family: var(--font); text-align: left; margin-top: .5rem; }
        .mobile-exit-impersonate:hover { background: #9a3412; }
        .mobile-notif-section { margin-bottom: .5rem; }
        .mobile-notif-toggle { display: flex; align-items: center; justify-content: space-between; padding: 0.8rem 1rem; border-radius: 6px; font-size: 0.95rem; font-weight: 500; color: var(--muted); background: none; border: none; width: 100%; font-family: var(--font); cursor: pointer; text-align: left; transition: all .2s; }
        .mobile-notif-toggle:hover { background: var(--light); color: var(--navy); }
        .mobile-notif-toggle-left { display: flex; align-items: center; gap: .65rem; }
        .mobile-notif-badge { background: var(--red); color: #fff; font-size: 10px; font-weight: bold; padding: 1px 7px; border-radius: 999px; min-width: 20px; text-align: center; }
        .mobile-notif-body { background: var(--light); border: 1px solid var(--border); border-radius: 6px; margin-bottom: .5rem; overflow: hidden; }
        .mobile-notif-body .notif-item { border-bottom-color: var(--border); }
        .content-wrap { flex: 1; max-width: 1280px; margin: 0 auto; padding: 1.5rem 1rem 2rem; width: 100%; }
        .footer { border-top: 2px solid var(--navy); background: white; padding: 1.5rem 1rem; font-size: .9rem; color: var(--muted); text-align: center; }
        .footer-inner { max-width: 1280px; margin: 0 auto; }
        .footer a { color: var(--red); text-decoration: none; }
        .footer a:hover { text-decoration: underline; }
        .credits { margin-top: .8rem; font-size: .85rem; }
        .alert-banner { background: linear-gradient(to right, var(--red), #002244); border-bottom: 1px solid #001122; color: white; padding: .6rem 1rem; font-size: .9rem; }
        .alert-inner { max-width: 1280px; margin: 0 auto; display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; }
        .alert-badge { padding: .3rem .8rem; border-radius: 999px; background: rgba(255,255,255,.2); font-weight: bold; font-size: .85rem; }
        @media (max-width: 920px) { .nav-main { display: none; } }
        @media (max-width: 768px) { .header-controls { display: none; } .hamburger { display: flex; } }
        @media (min-width: 769px) { .mobile-menu { display: none !important; } }
        @media (max-width: 480px) { .nav-container { padding: 0 1rem; } .brand-logo { height: 32px; } .content-wrap { padding: 1rem .8rem; } .impersonate-bar { padding: .5rem 1rem; } .impersonate-bar-sub { display: none; } .admin-message-bar, .broadcast-bar { padding: .5rem 1rem; } .notif-panel { width: calc(100vw - 2rem); right: -1rem; } }

        /* ── ADMIN SIDEBAR ── */
        .rn-sidebar { width:240px; background:#002244; display:flex; flex-direction:column; position:fixed; top:60px; left:0; bottom:0; z-index:90; overflow-y:auto; overflow-x:hidden; scrollbar-width:thin; scrollbar-color:rgba(255,255,255,.08) transparent; transition:transform .2s; }
        .rn-sidebar::-webkit-scrollbar { width:3px; }
        .rn-sidebar::-webkit-scrollbar-thumb { background:rgba(255,255,255,.1); }
        .sb-brand { display:flex; align-items:center; gap:.65rem; padding:.9rem .85rem; border-bottom:1px solid rgba(255,255,255,.08); flex-shrink:0; }
        .sb-logo { width:32px; height:32px; background:#C8102E; flex-shrink:0; display:flex; align-items:center; justify-content:center; }
        .sb-logo span { font-size:7px; font-weight:bold; color:#fff; text-align:center; line-height:1.2; text-transform:uppercase; letter-spacing:.04em; }
        .sb-brand-site { font-size:11px; font-weight:bold; color:#fff; text-transform:uppercase; letter-spacing:.04em; line-height:1.2; }
        .sb-brand-sub { font-size:9px; color:rgba(255,255,255,.35); text-transform:uppercase; letter-spacing:.08em; margin-top:2px; }
        .sb-nav { flex:1; padding:.4rem 0; }
        .sb-section-label { font-size:9px; font-weight:bold; text-transform:uppercase; letter-spacing:.15em; color:rgba(255,255,255,.28); padding:.85rem .85rem .3rem; display:flex; align-items:center; gap:.4rem; }
        .sb-section-label::after { content:''; flex:1; height:1px; background:rgba(255,255,255,.06); }
        .sb-item { display:flex; align-items:center; gap:.55rem; padding:.48rem .85rem; color:rgba(255,255,255,.6); text-decoration:none; font-size:12px; font-weight:500; cursor:pointer; transition:background .12s,color .12s; border-left:3px solid transparent; white-space:nowrap; }
        .sb-item:hover { background:rgba(255,255,255,.07); color:#fff; }
        .sb-item.active { background:rgba(255,255,255,.1); color:#fff; border-left-color:#C8102E; }
        .sb-icon { font-size:13px; width:17px; flex-shrink:0; text-align:center; }
        .sb-badge { margin-left:auto; font-size:9px; font-weight:bold; background:#C8102E; color:#fff; min-width:17px; height:15px; padding:0 4px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; }
        .sb-group-toggle { display:flex; align-items:center; gap:.55rem; padding:.48rem .85rem; color:rgba(255,255,255,.6); font-size:12px; font-weight:500; cursor:pointer; transition:background .12s,color .12s; border-left:3px solid transparent; user-select:none; }
        .sb-group-toggle:hover { background:rgba(255,255,255,.07); color:#fff; }
        .sb-group-toggle.open { color:#fff; }
        .sb-chevron { margin-left:auto; font-size:8px; transition:transform .2s; color:rgba(255,255,255,.28); }
        .sb-chevron.open { transform:rotate(180deg); }
        .sb-subnav { display:none; background:rgba(0,0,0,.12); }
        .sb-subnav.open { display:block; }
        .sb-subitem { display:flex; align-items:center; gap:.5rem; padding:.38rem .85rem .38rem 2.1rem; color:rgba(255,255,255,.48); text-decoration:none; font-size:11.5px; transition:background .12s,color .12s; border-left:3px solid transparent; }
        .sb-subitem:hover { background:rgba(255,255,255,.05); color:rgba(255,255,255,.82); }
        .sb-subitem.active { color:#fff; border-left-color:#C8102E; background:rgba(200,16,46,.14); }
        .sb-divider { height:1px; background:rgba(255,255,255,.06); margin:.35rem 0; }
        .sb-footer { padding:.7rem .85rem; border-top:1px solid rgba(255,255,255,.08); flex-shrink:0; }
        .sb-logout { display:flex; align-items:center; gap:.5rem; width:100%; padding:.38rem .65rem; background:rgba(200,16,46,.14); border:1px solid rgba(200,16,46,.22); color:rgba(255,100,100,.88); font-size:11px; font-family:Arial,sans-serif; font-weight:bold; cursor:pointer; letter-spacing:.04em; text-transform:uppercase; transition:background .12s; }
        .sb-logout:hover { background:rgba(200,16,46,.28); }
        .rn-admin-main { margin-left:240px; padding:1.5rem 1.5rem 3rem; min-width:0; background:#f4f5f7; min-height:calc(100vh - var(--nav-height)); }
        .sb-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:89; }
        .sb-overlay.open { display:block; }
        .sb-mobile-toggle { display:none; background:none; border:none; cursor:pointer; font-size:18px; color:#003366; padding:.3rem .5rem; margin-right:.5rem; }
        @media(max-width:900px) {
            .rn-sidebar { transform:translateX(-100%); z-index:500; }
            .rn-sidebar.open { transform:translateX(0); }
            .rn-admin-main { margin-left:0; padding:1rem 1rem 2rem; }
            .sb-mobile-toggle { display:block; }
        }
    </style>
    @php $headerCode = \App\Models\Setting::get('header_code', ''); @endphp
    @if($headerCode) {!! $headerCode !!} @endif
</head>
<body class="{{ session('original_admin_id') ? 'is-impersonating' : '' }}">

@php
    $isAdminPage = request()->is('admin/*') || str_starts_with(request()->route()?->getName() ?? '', 'admin.');
    $currentRoute = request()->route()?->getName() ?? '';
    $isSuperAdmin = auth()->user()?->is_super_admin ?? false;
    $adminPendingCount = 0;
    try { $adminPendingCount = \App\Models\User::where('registration_pending', true)->count(); } catch(\Throwable $e) {}
@endphp

<div class="site-shell">

@if (session('original_admin_id'))
<div class="impersonate-bar" role="alert" id="impersonateBar">
    <div class="impersonate-bar-left">
        <div class="impersonate-bar-icon">👤</div>
        <div>
            <div class="impersonate-bar-text">⚠ Admin impersonation active — viewing as <em>{{ auth()->user()->name }}</em> <span style="color:#fb923c;font-weight:normal;font-size:12px;">({{ auth()->user()->email }})</span></div>
            <div class="impersonate-bar-sub">Actions taken now affect this member's account. Exit to return to your admin session.</div>
        </div>
    </div>
    <form method="POST" action="{{ route('admin.impersonate.exit') }}">
        @csrf
        <button type="submit" class="btn-exit-impersonate">✕ Exit impersonation</button>
    </form>
</div>
@endif

@auth
    @if (auth()->user()->admin_message)
    <div class="admin-message-bar" role="alert" id="adminMessageBar">
        <div class="admin-message-bar-left">
            <div class="admin-message-bar-icon">📩</div>
            <div><div class="admin-message-bar-text">Message from an administrator: <span>{{ auth()->user()->admin_message }}</span></div></div>
        </div>
        <form method="POST" action="{{ route('message.dismiss') }}">
            @csrf
            <button type="submit" class="btn-dismiss-message">✕ Dismiss</button>
        </form>
    </div>
    @endif
    @php
        $broadcastMsg  = \App\Models\Setting::get('broadcast_message', '');
        $broadcastId   = (int) \App\Models\Setting::get('broadcast_message_id', 0);
        $showBroadcast = $broadcastMsg && $broadcastId > 0 && auth()->user()->dismissed_broadcast_id !== $broadcastId;
    @endphp
    @if ($showBroadcast)
    <div class="broadcast-bar" role="alert" id="broadcastBar">
        <div class="broadcast-bar-left">
            <div class="broadcast-bar-icon">📢</div>
            <div><div class="broadcast-bar-text">Notice from {{ \App\Helpers\RaynetSetting::groupName() }}: <span>{{ $broadcastMsg }}</span></div></div>
        </div>
        <form method="POST" action="{{ route('message.dismiss-broadcast') }}">
            @csrf
            <button type="submit" class="btn-dismiss-broadcast">✕ Dismiss</button>
        </form>
    </div>
    @endif
@endauth

<?php $alertStatus = \App\Models\AlertStatus::query()->first(); ?>

<nav class="navbar" id="mainNavbar">
    <div class="nav-container">

        @if($isAdminPage)
        <button class="sb-mobile-toggle" onclick="toggleSidebar()" title="Toggle admin menu">☰</button>
        @endif

        @php
            $siteLogo    = \App\Models\Setting::get('site_logo_path', '');
            $siteLogoUrl = $siteLogo ? \Illuminate\Support\Facades\Storage::url($siteLogo) : asset('images/raynet-uk-liverpool-banner.png');
            $siteTagline = \App\Models\Setting::get('site_tagline', 'Robust, Resilient, Radio');
            $siteName    = \App\Models\Setting::get('site_name', \App\Helpers\RaynetSetting::groupName());
        @endphp
        <a href="{{ route('home') }}" class="brand">
            <div style="display:flex;flex-direction:column;gap:2px;align-items:center;">
                <img src="{{ $siteLogoUrl }}" alt="{{ $siteName }}" class="brand-logo">
                <span class="brand-tagline">{{ $siteTagline }}</span>
            </div>
        </a>

        @if(!$isAdminPage)
        <div class="nav-main">
            <a href="{{ route('home') }}"            class="{{ request()->routeIs('home')            ? 'active' : '' }}">Home</a>
            <a href="{{ route('about') }}"           class="{{ request()->routeIs('about')           ? 'active' : '' }}">About</a>
            <a href="{{ route('event-support') }}"   class="{{ request()->routeIs('event-support')   ? 'active' : '' }}">Event Support</a>
            <a href="{{ route('request-support') }}" class="{{ request()->routeIs('request-support') ? 'active' : '' }}">Request Support</a>
            <a href="{{ route('data-dashboard') }}"  class="{{ request()->routeIs('data-dashboard')  ? 'active' : '' }}">Data Dashboard</a>
            <a href="{{ route('training') }}"        class="{{ request()->routeIs('training')        ? 'active' : '' }}">Training</a>
            @auth
            <a href="{{ route('ops-map') }}"         class="{{ request()->routeIs('ops-map')         ? 'active' : '' }}">Ops Map</a>
            @endauth
        </div>
        @else
        <div style="font-size:12px;color:var(--text-muted);margin-left:.5rem;">
            <a href="{{ route('admin.dashboard') }}" style="color:var(--navy);font-weight:bold;text-decoration:none;">Admin</a>
            @if($currentRoute !== 'admin.dashboard')
            <span style="margin:0 .4rem;color:var(--grey-dark)">›</span>
            <span>@yield('title', 'Admin')</span>
            @endif
        </div>
        @endif

        <div class="header-controls">
            @auth
            @php
                $user = auth()->user();
                $initials = strtoupper(substr($user->name, 0, 1));
                $isImpersonating = session('original_admin_id') !== null;
                $isSuperAdminUser = $user->isSuperAdmin();
                $isAdmin      = $user->isAdmin();
                $isCommittee  = $user->isCommittee();
                $currentRole  = $user->getRoleNames()->first() ?? 'member';
                $roleChipStyle = match($currentRole) {
                    'super-admin' => 'background:rgba(91,33,182,.15);border:1px solid rgba(91,33,182,.35);color:#7c3aed;',
                    'admin'       => 'background:rgba(200,16,46,.1);border:1px solid rgba(200,16,46,.25);color:var(--red);',
                    'committee'   => 'background:rgba(217,119,6,.1);border:1px solid rgba(217,119,6,.25);color:#d97706;',
                    default       => 'background:rgba(26,107,60,.08);border:1px solid rgba(26,107,60,.2);color:var(--green);',
                };
                $roleChipLabel = match($currentRole) {
                    'super-admin' => '★ Super Admin',
                    'admin'       => '⚡ Admin',
                    'committee'   => '📊 Committee',
                    default       => '● Member',
                };
                $committeeOverdue = $isCommittee
                    ? \App\Models\CommitteeAction::where('due_date', '<', now())->whereNotIn('status', ['closed','cancelled'])->count()
                    : 0;
            @endphp

            @if (session('original_admin_id'))
                <span class="nav-impersonate-pill"><span class="pip"></span>Impersonating</span>
            @endif

            <div class="bell-wrap" id="bellWrap">
                <button class="bell-btn" id="bellBtn" onclick="toggleNotif(event)" aria-label="Notifications" aria-expanded="false">
                    🔔<span class="bell-badge" id="bellBadge"></span>
                </button>
                <div class="notif-panel" id="notifPanel" role="dialog" aria-label="Notifications">
                    <div class="notif-panel-head">
                        <span class="notif-panel-title">Notifications</span>
                        <button class="notif-mark-all" onclick="markAllRead()">Mark all read</button>
                    </div>
                    <div class="notif-list" id="notifList"><div class="notif-empty"><div class="notif-empty-icon">🔔</div>Loading…</div></div>
                    <div class="notif-footer"><a href="{{ route('members') }}">View all in members area →</a></div>
                </div>
            </div>

            <div class="avatar-wrap" id="avatarWrap">
                <button class="avatar-btn" id="avatarBtn" onclick="toggleAvatar(event)" aria-haspopup="true" aria-expanded="false">
                    @if(auth()->user()->avatar)
                        <img src="{{ Storage::url(auth()->user()->avatar) }}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid rgba(0,51,102,.15);flex-shrink:0;" alt="">
                    @else
                        <div class="avatar-circle">{{ $initials }}</div>
                    @endif
                    <div class="avatar-btn-text">
                        <span class="avatar-name">{{ $user->name }}</span>
                        @if ($user->callsign)<span class="avatar-callsign">{{ $user->callsign }}</span>@endif
                    </div>
                    <span class="avatar-chevron">▼</span>
                </button>
                <div class="avatar-dropdown" id="avatarDropdown" role="menu">
                    <div class="avatar-dd-user">
                        @if(auth()->user()->avatar)
                            <img src="{{ Storage::url(auth()->user()->avatar) }}" style="width:42px;height:42px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,.25);flex-shrink:0;" alt="">
                        @else
                            <div class="avatar-dd-circle">{{ $initials }}</div>
                        @endif
                        <div class="avatar-dd-user-info">
                            <div class="avatar-dd-name">{{ $user->name }}</div>
                            @if ($user->callsign)<div class="avatar-dd-callsign">{{ $user->callsign }}</div>@endif
                            <div class="avatar-dd-email">{{ $user->email }}</div>
                        </div>
                        @if ($user->callsign)<div class="avatar-dd-callsign-badge">{{ $user->callsign }}</div>@endif
                    </div>
                    <div class="avatar-dd-status">
                        <span class="avatar-dd-status-dot"></span>
                        <span class="avatar-dd-status-text">Online</span>
                        @if ($isImpersonating)
                            <span class="avatar-dd-role-chip" style="background:rgba(234,88,12,.15);border:1px solid rgba(234,88,12,.35);color:#ea580c;">👤 Impersonating</span>
                        @else
                            <span class="avatar-dd-role-chip" style="{{ $roleChipStyle }}">{{ $roleChipLabel }}</span>
                        @endif
                    </div>
                    <div class="avatar-dd-menu">
                        <a href="{{ route('members') }}" class="avatar-dd-item" role="menuitem"><span class="avatar-dd-item-icon">🏠</span><span>Members Area</span><span class="avatar-dd-item-arrow">→</span></a>
                        <a href="{{ route('profile.edit') }}" class="avatar-dd-item" role="menuitem"><span class="avatar-dd-item-icon">👤</span><span>My Profile</span><span class="avatar-dd-item-arrow">→</span></a>
                        <a href="{{ route('members.activity') }}" class="avatar-dd-item" role="menuitem"><span class="avatar-dd-item-icon">📅</span><span>Activity Log</span><span class="avatar-dd-item-arrow">→</span></a>
                        <a href="{{ route('password.change') }}" class="avatar-dd-item" role="menuitem"><span class="avatar-dd-item-icon">🔑</span><span>Change Password</span><span class="avatar-dd-item-arrow">→</span></a>
                        @if ($isCommittee)
                        <div class="avatar-dd-divider"><span class="avatar-dd-divider-label">Committee</span></div>
                        <a href="{{ route('committee.dashboard') }}" class="avatar-dd-item avatar-dd-item-elevated" role="menuitem">
                            <span class="avatar-dd-item-icon">📊</span><span>Committee</span>
                            @if($committeeOverdue > 0)<span style="margin-left:auto;background:#C8102E;color:#fff;font-size:10px;font-weight:700;padding:1px 6px;border-radius:999px;line-height:1.6;">{{ $committeeOverdue }}</span>@else<span class="avatar-dd-item-arrow">→</span>@endif
                        </a>
                        @endif
                        @if ($isAdmin)
                        <div class="avatar-dd-divider"><span class="avatar-dd-divider-label">Administration</span></div>
                        <a href="{{ route('admin.dashboard') }}" class="avatar-dd-item avatar-dd-item-elevated" role="menuitem"><span class="avatar-dd-item-icon">⚙️</span><span>Admin Panel</span><span class="avatar-dd-item-arrow">→</span></a>
                        <a href="{{ route('admin.users.roles') }}" class="avatar-dd-item avatar-dd-item-elevated" role="menuitem"><span class="avatar-dd-item-icon">🎭</span><span>Role Management</span><span class="avatar-dd-item-arrow">→</span></a>
                        @endif
                        @if ($isSuperAdminUser)
                        <a href="{{ route('admin.super.index') }}" class="avatar-dd-item" role="menuitem" style="color:#7c3aed;"><span class="avatar-dd-item-icon">★</span><span style="font-weight:bold;">Super Admin Panel</span><span class="avatar-dd-item-arrow">→</span></a>
                        @endif
                        <div class="avatar-dd-divider"></div>
                        @if (session('original_admin_id'))
                        <form method="POST" action="{{ route('admin.impersonate.exit') }}" style="display:contents;">@csrf<button type="submit" class="avatar-dd-item danger" role="menuitem"><span class="avatar-dd-item-icon">↩</span><span>Exit Impersonation</span></button></form>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" style="display:contents;">@csrf<button type="submit" class="avatar-dd-item danger" role="menuitem"><span class="avatar-dd-item-icon">⏻</span><span>Sign Out</span></button></form>
                    </div>
                    <div class="avatar-dd-footer">{{ \App\Helpers\RaynetSetting::groupName() }} · {{ ucfirst($currentRole) }}</div>
                </div>
            </div>
            @endauth
            @guest
                <a href="{{ route('login') }}"    class="btn-pill btn-member">Login</a>
                <a href="{{ route('register') }}" class="btn-pill btn-register">Register</a>
            @endguest
        </div>

        <button class="hamburger" id="hamburger" aria-label="Toggle menu" aria-expanded="false">
            <span></span><span></span><span></span>
            <span class="hamburger-badge" id="hamburgerBadge" style="display:none;"></span>
        </button>
    </div>

    <div class="mobile-menu" id="mobileMenu" style="{{ $isAdminPage ? 'display:none!important' : '' }}">
        @auth
        <div class="mobile-menu-user">
            @if(auth()->user()->avatar)
                <img src="{{ Storage::url(auth()->user()->avatar) }}" style="width:42px;height:42px;border-radius:50%;object-fit:cover;border:2px solid rgba(0,51,102,.15);flex-shrink:0;" alt="">
            @else
                <div class="mobile-avatar-circle">{{ $initials }}</div>
            @endif
            <div>
                <div class="mobile-user-name">{{ $user->name }}</div>
                <div class="mobile-user-role" style="{{ $roleChipStyle }}">{{ $roleChipLabel }}</div>
            </div>
        </div>
        @endauth
        <div class="mobile-nav-section">
            <div class="mobile-nav-label">Navigation</div>
            <a href="{{ route('home') }}"            class="{{ request()->routeIs('home')            ? 'active' : '' }}"><span class="mobile-menu-icon">🏠</span> Home</a>
            <a href="{{ route('about') }}"           class="{{ request()->routeIs('about')           ? 'active' : '' }}"><span class="mobile-menu-icon">ℹ️</span> About</a>
            <a href="{{ route('event-support') }}"   class="{{ request()->routeIs('event-support')   ? 'active' : '' }}"><span class="mobile-menu-icon">📡</span> Event Support</a>
            <a href="{{ route('request-support') }}" class="{{ request()->routeIs('request-support') ? 'active' : '' }}"><span class="mobile-menu-icon">🆘</span> Request Support</a>
            <a href="{{ route('data-dashboard') }}"  class="{{ request()->routeIs('data-dashboard')  ? 'active' : '' }}"><span class="mobile-menu-icon">📊</span> Data Dashboard</a>
            <a href="{{ route('training') }}"        class="{{ request()->routeIs('training')        ? 'active' : '' }}"><span class="mobile-menu-icon">🎓</span> Training</a>
        </div>
        @auth
        <div class="mobile-notif-section">
            <button class="mobile-notif-toggle" onclick="toggleMobileNotif()">
                <span class="mobile-notif-toggle-left"><span class="mobile-menu-icon">🔔</span>Notifications</span>
                <span class="mobile-notif-badge" id="mobileNotifBadge" style="display:none;">0</span>
            </button>
            <div class="mobile-notif-body" id="mobileNotifBody" style="display:none;">
                <div style="display:flex;align-items:center;justify-content:space-between;padding:.5rem .85rem;background:var(--grey);border-bottom:1px solid var(--border);">
                    <span style="font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;color:var(--text-muted);">Notifications</span>
                    <button onclick="markAllRead()" style="font-size:10px;font-weight:bold;color:var(--navy);background:none;border:none;cursor:pointer;padding:0;font-family:var(--font);text-transform:uppercase;letter-spacing:.05em;">Mark all read</button>
                </div>
                <div class="notif-list" id="mobileNotifList"><div class="notif-empty"><div class="notif-empty-icon">🔔</div>Loading…</div></div>
            </div>
        </div>
        <div class="mobile-nav-section">
            <div class="mobile-nav-label">My Account</div>
            <a href="{{ route('members') }}"          class="{{ request()->routeIs('members')          ? 'active' : '' }}"><span class="mobile-menu-icon">🏠</span> Members Area</a>
            <a href="{{ route('profile.edit') }}"     class="{{ request()->routeIs('profile.edit')     ? 'active' : '' }}"><span class="mobile-menu-icon">👤</span> My Profile</a>
            <a href="{{ route('members.activity') }}" class="{{ request()->routeIs('members.activity') ? 'active' : '' }}"><span class="mobile-menu-icon">📅</span> Activity Log</a>
            <a href="{{ route('ops-map') }}"          class="{{ request()->routeIs('ops-map')          ? 'active' : '' }}"><span class="mobile-menu-icon">🗺️</span> Ops Map</a>
        </div>
        @if ($isCommittee)
        <div class="mobile-nav-section">
            <div class="mobile-nav-label">Committee</div>
            <a href="{{ route('committee.dashboard') }}" class="{{ request()->routeIs('committee.*') ? 'active' : '' }}" style="font-weight:600;">
                <span class="mobile-menu-icon">📊</span>Committee
                @if($committeeOverdue > 0)<span style="margin-left:auto;background:#C8102E;color:#fff;font-size:10px;font-weight:700;padding:1px 6px;border-radius:999px;">{{ $committeeOverdue }}</span>@endif
            </a>
        </div>
        @endif
        @if ($isAdmin)
        <div class="mobile-nav-section">
            <div class="mobile-nav-label">Administration</div>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" style="font-weight:600;"><span class="mobile-menu-icon">⚙️</span>Admin Panel</a>
        </div>
        @endif
        @if (session('original_admin_id'))
            <form action="{{ route('admin.impersonate.exit') }}" method="POST" style="display:contents;">@csrf<button type="submit" class="mobile-exit-impersonate">👤 ✕ Exit impersonation</button></form>
        @else
            <form action="{{ route('logout') }}" method="POST" style="display:contents;">@csrf<button type="submit" class="mobile-dd-btn danger"><span class="mobile-menu-icon">⏻</span>Sign Out</button></form>
        @endif
        @endauth
        @guest
        <div style="display:flex;flex-direction:column;gap:.8rem;margin-top:.5rem;">
            <a href="{{ route('login') }}"    class="btn-pill btn-member"   style="justify-content:center;">Login</a>
            <a href="{{ route('register') }}" class="btn-pill btn-register" style="justify-content:center;">Register</a>
        </div>
        @endguest
    </div>
</nav>

@if(!$isAdminPage && $alertStatus && ($meta = $alertStatus->meta()) && in_array($alertStatus->level, [1,2,3]))
    @php $colour = $meta['colour'] ?? '#C8102E'; @endphp
    <div class="alert-banner" style="background:linear-gradient(to right,{{ $colour }},var(--navy));">
        <div class="alert-inner">
            <div class="alert-badge">{{ $meta['title'] ?? 'Alert Level ' . $alertStatus->level }}</div>
            <div>
                @if($alertStatus->headline)<strong>{{ $alertStatus->headline }}</strong> — @endif
                {{ $alertStatus->message ?: $meta['description'] }}
            </div>
        </div>
    </div>
@endif

@if($isAdminPage)
<div class="sb-overlay" id="sbOverlay" onclick="closeSidebar()"></div>
<aside class="rn-sidebar" id="rnSidebar">
    <div class="sb-brand">
        <div class="sb-logo"><span>RAY<br>NET</span></div>
        <div>
            <div class="sb-brand-site">{{ \App\Helpers\RaynetSetting::groupName() }}</div>
            <div class="sb-brand-sub">Admin Panel</div>
        </div>
    </div>
    <nav class="sb-nav">
        <a href="{{ route('admin.dashboard') }}" class="sb-item {{ $currentRoute === 'admin.dashboard' ? 'active' : '' }}"><span class="sb-icon">⊞</span> Dashboard</a>
        <a href="{{ route('admin.pages.index') }}" class="sb-item {{ str_starts_with($currentRoute,'admin.pages') ? 'active' : '' }}"><span class="sb-icon">📄</span> Pages</a>
        <div class="sb-divider"></div>
        <div class="sb-section-label">People</div>
        <div id="grp-members">
            <div class="sb-group-toggle" onclick="sbToggle('grp-members')">
                <span class="sb-icon">👥</span> Members
                @if($adminPendingCount > 0)<span class="sb-badge">{{ $adminPendingCount }}</span>@endif
                <span class="sb-chevron" id="grp-members-ch">▼</span>
            </div>
            <div class="sb-subnav" id="grp-members-sub">
                <a href="{{ route('admin.users.index') }}" class="sb-subitem {{ $currentRoute === 'admin.users.index' ? 'active' : '' }}">All Members @if($adminPendingCount > 0)<span class="sb-badge">{{ $adminPendingCount }}</span>@endif</a>
                <a href="{{ route('admin.roles') }}" class="sb-subitem {{ $currentRoute === 'admin.roles' ? 'active' : '' }}">Roles</a>
                <a href="{{ route('admin.availability.index') }}" class="sb-subitem {{ $currentRoute === 'admin.availability.index' ? 'active' : '' }}">Availability</a>
                <a href="{{ route('admin.online') }}" class="sb-subitem {{ $currentRoute === 'admin.online' ? 'active' : '' }}">Who's Online</a>
            </div>
        </div>
        <div class="sb-divider"></div>
        <div class="sb-section-label">Events</div>
        <div id="grp-events">
            <div class="sb-group-toggle" onclick="sbToggle('grp-events')">
                <span class="sb-icon">📅</span> Events
                <span class="sb-chevron" id="grp-events-ch">▼</span>
            </div>
            <div class="sb-subnav" id="grp-events-sub">
                <a href="{{ route('admin.events') }}" class="sb-subitem {{ $currentRoute === 'admin.events' ? 'active' : '' }}">All Events</a>
                <a href="{{ route('admin.event-types') }}" class="sb-subitem {{ $currentRoute === 'admin.event-types' ? 'active' : '' }}">Event Types</a>
                <a href="{{ route('calendar') }}" class="sb-subitem" target="_blank">Public Calendar ↗</a>
            </div>
        </div>
        <div class="sb-divider"></div>
        <div class="sb-section-label">Operations</div>
        <a href="{{ route('admin.dashboard') }}" class="sb-item {{ $currentRoute === 'admin.dashboard' ? 'active' : '' }}"><span class="sb-icon">⚠</span> Alert Status</a>
        <a href="{{ route('admin.notifications.index') }}" class="sb-item {{ str_starts_with($currentRoute,'admin.notifications') ? 'active' : '' }}"><span class="sb-icon">🔔</span> Notifications</a>
        @if(\Illuminate\Support\Facades\Route::has('admin.netlog.index'))
        <a href="{{ route('admin.netlog.index') }}" class="sb-item {{ str_starts_with($currentRoute,'admin.netlog') ? 'active' : '' }}"><span class="sb-icon">📻</span> Net Log</a>
        @endif
        @if(\Illuminate\Support\Facades\Route::has('admin.announcements.index'))
        <a href="{{ route('admin.announcements.index') }}" class="sb-item {{ str_starts_with($currentRoute,'admin.announcements') ? 'active' : '' }}"><span class="sb-icon">📢</span> Announcements</a>
        @endif
        <div class="sb-divider"></div>
        <div class="sb-section-label">Training</div>
        <div id="grp-lms">
            <div class="sb-group-toggle" onclick="sbToggle('grp-lms')">
                <span class="sb-icon">🎓</span> Training
                <span class="sb-chevron" id="grp-lms-ch">▼</span>
            </div>
            <div class="sb-subnav" id="grp-lms-sub">
                <a href="{{ route('admin.lms.index') }}" class="sb-subitem {{ $currentRoute === 'admin.lms.index' ? 'active' : '' }}">Course Builder</a>
                <a href="{{ route('admin.lms.scorm-builder') }}" class="sb-subitem">SCORM Builder</a>
                <a href="{{ route('lms.index') }}" class="sb-subitem" target="_blank">Training Portal ↗</a>
            </div>
        </div>
        <div class="sb-divider"></div>
        <div class="sb-section-label">Analytics</div>
        <a href="{{ route('admin.activity-logs.index') }}" class="sb-item {{ str_starts_with($currentRoute,'admin.activity') ? 'active' : '' }}"><span class="sb-icon">📊</span> Activity Logs</a>
        <a href="{{ route('data-dashboard') }}" class="sb-item" target="_blank"><span class="sb-icon">📡</span> Data Dashboard ↗</a>
        <div class="sb-divider"></div>
        <div class="sb-section-label">System</div>
        <div id="grp-system">
            <div class="sb-group-toggle" onclick="sbToggle('grp-system')">
                <span class="sb-icon">⚙️</span> System
                <span class="sb-chevron" id="grp-system-ch">▼</span>
            </div>
            <div class="sb-subnav" id="grp-system-sub">
                <a href="{{ route('admin.settings') }}" class="sb-subitem {{ $currentRoute === 'admin.settings' ? 'active' : '' }}">Site Settings</a>
                <a href="{{ route('admin.modules.index') }}" class="sb-subitem {{ str_starts_with($currentRoute,'admin.modules') ? 'active' : '' }}">Module Manager</a>
                <a href="{{ route('admin.settings') }}" class="sb-subitem {{ $currentRoute === 'admin.settings' ? 'active' : '' }}">Site Settings</a>
                @if($isSuperAdmin)
                <a href="{{ route('admin.super.index') }}" class="sb-subitem {{ $currentRoute === 'admin.super.index' ? 'active' : '' }}">Super Admin</a>
                <a href="{{ route('admin.super.permissions.index') }}" class="sb-subitem {{ $currentRoute === 'admin.super.permissions.index' ? 'active' : '' }}">Permissions</a>
                <a href="{{ route('admin.oauth.clients') }}" class="sb-subitem {{ str_starts_with($currentRoute,'admin.oauth') ? 'active' : '' }}">SSO / OAuth</a>
                @endif
                <a href="{{ route('admin.aprs.index') }}" class="sb-subitem {{ $currentRoute === 'admin.aprs.index' ? 'active' : '' }}">APRS Locations</a>
            </div>
        </div>
    </nav>
    <div class="sb-footer">
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit" class="sb-logout">⏻ Log Out of Admin</button>
        </form>
    </div>
</aside>
@endif

@if($isAdminPage)
<div class="rn-admin-main" id="rn-admin-main">
    @if(session('success'))
    <div style="display:flex;align-items:center;gap:.55rem;padding:.65rem 1rem;margin-bottom:1rem;font-size:12.5px;background:#eef7f2;border:1px solid #b8ddc9;border-left:3px solid #1a6b3c;color:#1a6b3c;">✓ {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div style="display:flex;align-items:center;gap:.55rem;padding:.65rem 1rem;margin-bottom:1rem;font-size:12.5px;background:#fdf0f2;border:1px solid rgba(200,16,46,.25);border-left:3px solid #C8102E;color:#C8102E;">⚠ {{ session('error') }}</div>
    @endif
    @if(session('status'))
    <div style="display:flex;align-items:center;gap:.55rem;padding:.65rem 1rem;margin-bottom:1rem;font-size:12.5px;background:#eef7f2;border:1px solid #b8ddc9;border-left:3px solid #1a6b3c;color:#1a6b3c;">✓ {{ session('status') }}</div>
    @endif
    @yield('content')
</div>
@else
<main class="content-wrap">
    @yield('content')
</main>
<footer class="footer">
    <div class="footer-inner">
        <span>© {{ date('Y') }} {{ \App\Helpers\RaynetSetting::groupName() }} (Group 10/ME/179). All rights reserved.</span>
        <span>Affiliated to RAYNET-UK · Volunteer emergency communications for {{ \App\Helpers\RaynetSetting::groupRegion() }}.</span>
    </div>
    <div class="credits">
        Website designed &amp; built by
        <a href="https://www.qrz.com/db/G4BDS" target="_blank">G4BDS</a>
        &amp; <a href="https://www.qrz.com/db/M7NDN" target="_blank">M7NDN</a>
        &nbsp;·&nbsp;
        <a href="{{ route('privacy') }}">Privacy Notice</a>
        &nbsp;·&nbsp;
        <a href="{{ route('cookies') }}">Cookie Policy</a>
        &nbsp;·&nbsp;
        <button onclick="openCookieSettings()" style="background:none;border:none;cursor:pointer;color:var(--red);font-size:.85rem;font-family:inherit;padding:0;text-decoration:underline;">Manage Cookies</button>
    </div>
</footer>
@endif

</div>

<script>
function adjustNavTop() {
    let offset = 0;
    ['impersonateBar','adminMessageBar','broadcastBar'].forEach(id => {
        const el = document.getElementById(id);
        if (el) offset += el.offsetHeight;
    });
    const nav = document.getElementById('mainNavbar');
    if (nav) nav.style.top = offset + 'px';
    const sb = document.getElementById('rnSidebar');
    if (sb) sb.style.top = (nav ? nav.offsetHeight + offset : 60) + 'px';
}
adjustNavTop();
window.addEventListener('resize', adjustNavTop);

const hamburger  = document.getElementById('hamburger');
const mobileMenu = document.getElementById('mobileMenu');
if (hamburger && mobileMenu) {
    hamburger.addEventListener('click', () => {
        const isOpen = mobileMenu.classList.toggle('open');
        hamburger.classList.toggle('open', isOpen);
        hamburger.setAttribute('aria-expanded', isOpen);
        if (isOpen) loadNotifications();
    });
    mobileMenu.querySelectorAll('a').forEach(el => {
        el.addEventListener('click', () => {
            mobileMenu.classList.remove('open');
            hamburger.classList.remove('open');
            hamburger.setAttribute('aria-expanded', 'false');
        });
    });
}

document.addEventListener('click', e => {
    const bellWrap   = document.getElementById('bellWrap');
    const avatarWrap = document.getElementById('avatarWrap');
    if (bellWrap   && !bellWrap.contains(e.target))   closeNotif();
    if (avatarWrap && !avatarWrap.contains(e.target)) closeAvatar();
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeNotif(); closeAvatar(); }
});

function toggleAvatar(e) {
    e.stopPropagation();
    const dd  = document.getElementById('avatarDropdown');
    const btn = document.getElementById('avatarBtn');
    if (!dd) return;
    const open = dd.classList.toggle('open');
    btn.setAttribute('aria-expanded', open);
    if (open) closeNotif();
}
function closeAvatar() {
    const dd  = document.getElementById('avatarDropdown');
    const btn = document.getElementById('avatarBtn');
    if (dd)  dd.classList.remove('open');
    if (btn) btn.setAttribute('aria-expanded', 'false');
}

let notificationsLoaded = false;
let notifications = [];
function toggleNotif(e) {
    e.stopPropagation();
    const panel = document.getElementById('notifPanel');
    const btn   = document.getElementById('bellBtn');
    if (!panel) return;
    const open = panel.classList.toggle('open');
    btn.setAttribute('aria-expanded', open);
    if (open) { closeAvatar(); loadNotifications(); }
}
function closeNotif() {
    const panel = document.getElementById('notifPanel');
    const btn   = document.getElementById('bellBtn');
    if (panel) panel.classList.remove('open');
    if (btn)   btn.setAttribute('aria-expanded', 'false');
}
function toggleMobileNotif() {
    const body = document.getElementById('mobileNotifBody');
    if (!body) return;
    body.style.display = body.style.display === 'block' ? 'none' : 'block';
    loadNotifications();
}
async function loadNotifications() {
    if (notificationsLoaded) return;
    notificationsLoaded = true;
    try {
        const resp = await fetch('/members/notifications/recent', { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
        if (!resp.ok) throw new Error('failed');
        const data = await resp.json();
        notifications = data.notifications || [];
    } catch { notifications = []; }
    renderNotifications();
}
function renderNotifications() {
    const unreadCount = notifications.filter(n => !n.read_at).length;
    const badge = document.getElementById('bellBadge');
    if (badge) { badge.classList.toggle('visible', unreadCount > 0); badge.textContent = unreadCount > 9 ? '9+' : String(unreadCount); }
    const hamburgerBadge = document.getElementById('hamburgerBadge');
    if (hamburgerBadge) { hamburgerBadge.style.display = unreadCount > 0 ? 'flex' : 'none'; hamburgerBadge.textContent = unreadCount > 9 ? '9+' : String(unreadCount); }
    const mobileBadge = document.getElementById('mobileNotifBadge');
    if (mobileBadge) { mobileBadge.style.display = unreadCount > 0 ? 'inline-block' : 'none'; mobileBadge.textContent = unreadCount > 9 ? '9+' : String(unreadCount); }
    ['notifList','mobileNotifList'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        if (notifications.length === 0) { el.innerHTML = '<div class="notif-empty"><div class="notif-empty-icon">🔔</div>You\'re all caught up!</div>'; return; }
        el.innerHTML = notifications.map(n => '<div class="notif-item ' + (n.read_at ? '' : 'unread') + '"><div class="notif-dot ' + (n.read_at ? 'read' : '') + '"></div><div class="notif-text"><strong>' + escHtml(n.title||'Notification') + '</strong><div class="notif-body">' + escHtml(n.body||'') + '</div><div class="notif-time">' + escHtml(n.ago||'') + '</div></div></div>').join('');
    });
}
async function markAllRead() {
    try {
        await fetch('/members/notifications/mark-all-read', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content||'', 'Accept': 'application/json' } });
        notifications = notifications.map(n => ({ ...n, read_at: true }));
        renderNotifications();
    } catch {}
}
function escHtml(str) { return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
@auth
loadNotifications();
@endauth

function sbToggle(id) {
    var sub = document.getElementById(id + '-sub');
    var ch  = document.getElementById(id + '-ch');
    var tog = sub ? sub.previousElementSibling : null;
    if (!sub) return;
    var o = sub.classList.contains('open');
    sub.classList.toggle('open', !o);
    if (tog) tog.classList.toggle('open', !o);
    if (ch)  ch.classList.toggle('open', !o);
}
function toggleSidebar() {
    var s = document.getElementById('rnSidebar');
    var o = document.getElementById('sbOverlay');
    if (s) s.classList.toggle('open');
    if (o) o.classList.toggle('open');
}
function closeSidebar() {
    var s = document.getElementById('rnSidebar');
    var o = document.getElementById('sbOverlay');
    if (s) s.classList.remove('open');
    if (o) o.classList.remove('open');
}
(function() {
    document.querySelectorAll('.sb-subitem.active').forEach(function(el) {
        var sub = el.closest('.sb-subnav');
        if (!sub) return;
        sub.classList.add('open');
        var tog = sub.previousElementSibling;
        if (tog) tog.classList.add('open');
        var id = sub.id.replace('-sub','');
        var ch = document.getElementById(id + '-ch');
        if (ch) ch.classList.add('open');
    });
})();
</script>
@include('partials.cookie-banner')
@stack('styles')
@stack('scripts')
</body>
</html>