{{-- resources/views/admin/activity-logs/_form-styles.blade.php --}}
<style>
    :root {
        --al-bg:        #0d0f14;
        --al-surface:   #13161e;
        --al-border:    #1f2433;
        --al-accent:    #5b8af0;
        --al-accent-lo: rgba(91,138,240,.12);
        --al-danger:    #e05c5c;
        --al-text:      #e2e6f0;
        --al-muted:     #616880;
    }

    .al-page {
        background: var(--al-bg);
        color: var(--al-text);
        font-family: 'JetBrains Mono', monospace;
        font-size: 13px;
        min-height: calc(100vh - 70px);
        margin: -20px -20px -20px;
        padding: 40px 32px 60px;
    }

    .al-form-wrap { max-width: 760px; margin: 0 auto; }

    .al-form-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 28px;
        padding-bottom: 24px;
        border-bottom: 1px solid var(--al-border);
    }
    .al-back-link {
        color: var(--al-muted);
        font-size: 12px;
        text-decoration: none;
        display: block;
        margin-bottom: 8px;
        transition: color .15s;
    }
    .al-back-link:hover { color: var(--al-text); }
    .al-form-header h1 {
        font-family: 'Syne', sans-serif;
        font-size: 24px;
        font-weight: 800;
        color: #fff;
        margin: 0;
    }

    .al-form-card {
        background: var(--al-surface);
        border: 1px solid var(--al-border);
        border-radius: 12px;
        padding: 32px;
    }

    .al-form-errors {
        background: rgba(224,92,92,.1);
        border: 1px solid rgba(224,92,92,.3);
        color: #e07070;
        border-radius: 8px;
        padding: 14px 18px;
        margin-bottom: 24px;
        font-size: 12px;
    }
    .al-form-errors ul { margin-top: 8px; padding-left: 18px; }
    .al-form-errors li { margin-top: 4px; }

    .al-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }
    @media (max-width: 560px) { .al-form-grid { grid-template-columns: 1fr; } }

    .al-form-group { display: flex; flex-direction: column; gap: 6px; }
    .al-form-group label {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 1.3px;
        color: var(--al-muted);
        font-weight: 500;
    }
    .al-required { color: var(--al-accent); }

    .al-form-group input,
    .al-form-group select {
        background: var(--al-bg);
        border: 1px solid var(--al-border);
        border-radius: 7px;
        color: var(--al-text);
        padding: 11px 14px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 13px;
        transition: border-color .15s, box-shadow .15s;
        width: 100%;
    }
    .al-form-group input:focus,
    .al-form-group select:focus {
        outline: none;
        border-color: var(--al-accent);
        box-shadow: 0 0 0 3px rgba(91,138,240,.15);
    }
    .al-form-group input.is-error,
    .al-form-group select.is-error { border-color: var(--al-danger); }

    .al-field-error { color: var(--al-danger); font-size: 11px; }
    .al-field-hint { color: var(--al-muted); font-size: 11px; }

    .al-acad-preview {
        background: var(--al-accent-lo);
        border: 1px solid rgba(91,138,240,.2);
        border-radius: 6px;
        padding: 10px 14px;
        font-size: 12px;
        color: var(--al-muted);
        margin-bottom: 24px;
    }
    .al-acad-preview strong { color: var(--al-accent); }

    .al-form-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-top: 24px;
        border-top: 1px solid var(--al-border);
        flex-wrap: wrap;
    }

    .al-btn-submit {
        background: var(--al-accent);
        color: #fff;
        border: none;
        border-radius: 7px;
        padding: 11px 24px;
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        transition: opacity .15s, transform .1s;
    }
    .al-btn-submit:hover { opacity: .88; transform: translateY(-1px); }

    .al-btn-cancel {
        background: transparent;
        border: 1px solid var(--al-border);
        color: var(--al-muted);
        border-radius: 7px;
        padding: 11px 18px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 12px;
        text-decoration: none;
        transition: border-color .15s, color .15s;
        display: inline-block;
    }
    .al-btn-cancel:hover { border-color: var(--al-text); color: var(--al-text); }

    .al-btn-delete {
        background: transparent;
        border: 1px solid rgba(224,92,92,.3);
        color: var(--al-danger);
        border-radius: 7px;
        padding: 11px 18px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 12px;
        cursor: pointer;
        transition: background .15s;
    }
    .al-btn-delete:hover { background: rgba(224,92,92,.1); }
</style>