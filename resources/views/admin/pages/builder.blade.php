<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Builder: {{ ucwords(str_replace('-',' ',$slug)) }} — RAYNET Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/dracula.min.css">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%;overflow:hidden;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;font-size:13px;color:#111827}
:root{
  --navy:#003366;--red:#C8102E;--grey:#f4f5f7;--grey-mid:#dde2e8;
  --muted:#6b7f96;--green:#1a6b3c;--green-bg:#eef7f2;
  --sidebar:#1a2332;--sidebar-hover:#243144;--sidebar-active:#2d3d57;
}

/* ── Layout ── */
.pb{display:flex;flex-direction:column;height:100vh;overflow:hidden;background:#f0f1f4}
.pb-topbar{background:#fff;border-bottom:2px solid var(--navy);padding:.5rem 1rem;display:flex;align-items:center;gap:.65rem;flex-shrink:0;z-index:100;box-shadow:0 2px 8px rgba(0,51,102,.09)}
.pb-body{flex:1;display:flex;overflow:hidden}

/* ── Top bar ── */
.pb-logo{width:30px;height:30px;background:var(--red);display:flex;align-items:center;justify-content:center;font-size:7px;font-weight:bold;color:#fff;text-align:center;line-height:1.2;text-transform:uppercase;flex-shrink:0}
.pb-page-name{font-weight:bold;color:var(--navy);font-size:.95rem}
.pb-page-slug{font-size:.72rem;color:var(--muted);font-family:ui-monospace,monospace;margin-top:.1rem}
.pb-topbar-right{display:flex;align-items:center;gap:.45rem;margin-left:auto}
.pb-btn{display:inline-flex;align-items:center;gap:.3rem;padding:.38rem .85rem;border:1px solid;font-family:inherit;font-size:11px;font-weight:bold;cursor:pointer;transition:all .12s;text-transform:uppercase;letter-spacing:.05em;text-decoration:none;white-space:nowrap}
.pb-btn-primary{background:var(--navy);border-color:var(--navy);color:#fff}
.pb-btn-primary:hover{background:#002244}
.pb-btn-ghost{background:#fff;border-color:var(--grey-mid);color:var(--muted)}
.pb-btn-ghost:hover{border-color:var(--navy);color:var(--navy)}
.pb-btn-green{background:var(--green-bg);border-color:#b8ddc9;color:var(--green)}
.pb-btn-green:hover{background:#d6ede3}
.pb-btn-red{background:#fdf0f2;border-color:rgba(200,16,46,.3);color:var(--red)}
.pb-btn-red:hover{background:#fce4e8}
.pb-btn-sm{padding:.25rem .6rem;font-size:10px}

/* ── Left sidebar — block library ── */
.pb-library{width:200px;background:var(--sidebar);flex-shrink:0;display:flex;flex-direction:column;overflow:hidden}
.pb-lib-head{padding:.75rem .85rem;font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.12em;color:rgba(255,255,255,.35);border-bottom:1px solid rgba(255,255,255,.06);flex-shrink:0}
.pb-lib-scroll{flex:1;overflow-y:auto;padding:.5rem;scrollbar-width:thin;scrollbar-color:rgba(255,255,255,.1) transparent}
.pb-lib-block{display:flex;align-items:center;gap:.55rem;padding:.55rem .65rem;border:1px solid rgba(255,255,255,.08);margin-bottom:.35rem;cursor:pointer;transition:all .12s;user-select:none;color:rgba(255,255,255,.65);font-size:12px;font-weight:500;background:rgba(255,255,255,.03);border-radius:4px}
.pb-lib-block:hover{background:var(--sidebar-hover);border-color:rgba(255,255,255,.18);color:#fff}
.pb-lib-block__icon{width:24px;height:24px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.08);border-radius:3px;font-size:12px;flex-shrink:0}
.pb-lib-section{font-size:9px;font-weight:bold;text-transform:uppercase;letter-spacing:.15em;color:rgba(255,255,255,.22);padding:.6rem .65rem .2rem;margin-top:.25rem}

/* ── Canvas ── */
.pb-canvas-wrap{flex:1;overflow-y:auto;padding:1.5rem;display:flex;flex-direction:column;align-items:center;gap:0;background:linear-gradient(135deg,#e8edf2 0%,#f0f3f7 100%)}
.pb-canvas{width:100%;max-width:880px;min-height:100%}
.pb-canvas-inner{background:#fff;box-shadow:0 4px 24px rgba(0,51,102,.12),0 1px 4px rgba(0,51,102,.08);min-height:400px;position:relative}

/* ── Block row ── */
.pb-block-row{position:relative;border:2px solid transparent;transition:border-color .15s}
.pb-block-row:hover{border-color:#2563eb}
.pb-block-row.pb-block-selected{border-color:#2563eb;border-style:solid}

/* Block controls overlay */
.pb-block-controls{position:absolute;top:-1px;right:-1px;display:none;z-index:10;flex-direction:row;gap:0;background:#2563eb}
.pb-block-row:hover .pb-block-controls,.pb-block-row.pb-block-selected .pb-block-controls{display:flex}
.pb-block-label{position:absolute;top:-1px;left:-1px;background:#2563eb;color:#fff;font-size:9px;font-weight:bold;padding:2px 6px;letter-spacing:.05em;text-transform:uppercase;display:none}
.pb-block-row:hover .pb-block-label,.pb-block-row.pb-block-selected .pb-block-label{display:block}
.pb-ctrl{width:28px;height:26px;background:none;border:none;color:#fff;cursor:pointer;font-size:13px;display:flex;align-items:center;justify-content:center;transition:background .1s}
.pb-ctrl:hover{background:rgba(0,0,0,.2)}
.pb-ctrl--drag{cursor:grab;font-size:11px}
.pb-ctrl--drag:active{cursor:grabbing}

/* Block preview */
.pb-block-preview{pointer-events:none;overflow:hidden;padding:0}


/* Add block zone */
.pb-add-zone{text-align:center;padding:.5rem;opacity:0;transition:opacity .2s}
.pb-block-row:last-child + .pb-add-zone,.pb-add-zone.pb-add-zone--visible,.pb-canvas-inner:hover .pb-add-zone{opacity:1}
.pb-add-btn{display:inline-flex;align-items:center;gap:.35rem;padding:.35rem .9rem;border:2px dashed #d1d5db;background:#fff;color:#9ca3af;font-size:12px;font-weight:bold;cursor:pointer;transition:all .15s;font-family:inherit}
.pb-add-btn:hover{border-color:#2563eb;color:#2563eb;background:#eff6ff}
.pb-add-between{height:8px;display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity .2s;position:relative}
.pb-add-between:hover{opacity:1}
.pb-add-between-btn{position:absolute;display:inline-flex;align-items:center;gap:.25rem;padding:.2rem .6rem;background:#2563eb;color:#fff;font-size:10px;font-weight:bold;cursor:pointer;border:none;font-family:inherit}

/* Empty state */
.pb-empty{padding:4rem 2rem;text-align:center;color:#9ca3af}
.pb-empty-icon{font-size:2.5rem;opacity:.3;margin-bottom:1rem}

/* ── Right edit panel ── */
.pb-edit-panel{width:320px;background:#fff;border-left:1px solid var(--grey-mid);display:flex;flex-direction:column;flex-shrink:0;overflow:hidden;transition:width .2s}
.pb-edit-panel--closed{width:0;border:none}
.pb-edit-head{padding:.65rem 1rem;background:var(--grey);border-bottom:1px solid var(--grey-mid);display:flex;align-items:center;justify-content:space-between;flex-shrink:0}
.pb-edit-head-title{font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:.08em;color:var(--navy);display:flex;align-items:center;gap:.4rem}
.pb-edit-close{background:none;border:none;cursor:pointer;color:var(--muted);font-size:18px;line-height:1;padding:0}
.pb-edit-close:hover{color:var(--navy)}
.pb-edit-body{flex:1;overflow-y:auto;padding:1rem}
.pb-edit-foot{padding:.65rem 1rem;border-top:1px solid var(--grey-mid);background:var(--grey);display:flex;gap:.5rem}

/* Edit fields */
.pb-field{margin-bottom:.9rem}
.pb-field:last-child{margin-bottom:0}
.pb-field-label{font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.09em;color:var(--muted);margin-bottom:.3rem;display:block}
.pb-field-hint{font-size:10px;color:#9ca3af;margin-top:.2rem}
.pb-input{width:100%;border:1px solid var(--grey-mid);padding:.42rem .7rem;font-size:13px;font-family:inherit;color:#111827;outline:none;transition:border-color .15s}
.pb-input:focus{border-color:var(--navy);box-shadow:0 0 0 3px rgba(0,51,102,.07)}
.pb-select{width:100%;border:1px solid var(--grey-mid);padding:.42rem .7rem;font-size:13px;font-family:inherit;color:#111827;outline:none;background:#fff}
.pb-color-row{display:flex;gap:.5rem;align-items:center}
.pb-color-input{width:36px;height:30px;border:1px solid var(--grey-mid);padding:2px;cursor:pointer;background:#fff}
.pb-color-text{flex:1}
.pb-tab-row{display:flex;border:1px solid var(--grey-mid);margin-bottom:.75rem;overflow:hidden}
.pb-tab-item{flex:1;padding:.35rem;font-size:11px;font-weight:bold;text-transform:uppercase;text-align:center;cursor:pointer;background:#fff;border:none;font-family:inherit;color:var(--muted);transition:all .12s}
.pb-tab-item.active{background:var(--navy);color:#fff}
.pb-col-editors{display:flex;flex-direction:column;gap:.75rem}
.pb-col-editor-label{font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:.25rem}

/* Quill in panel */
.pb-quill-wrap .ql-container{border:1px solid var(--grey-mid);border-top:none;font-size:13px;max-height:220px;overflow-y:auto}
.pb-quill-wrap .ql-toolbar{border:1px solid var(--grey-mid);background:var(--grey)}

/* CodeMirror in panel */
.pb-cm-wrap .CodeMirror{height:220px;font-size:12px;border:1px solid var(--grey-mid)}

/* Notice */
.pb-notice{padding:.55rem 1rem;font-size:12px;font-weight:bold;display:flex;align-items:center;gap:.5rem;flex-shrink:0}
.pb-notice--ok{background:var(--green-bg);border-bottom:1px solid #b8ddc9;color:var(--green)}

/* Sortable ghost */
.sortable-ghost{opacity:.3;background:#eff6ff;border:2px dashed #2563eb}
.sortable-chosen{box-shadow:0 8px 24px rgba(0,0,0,.2)}

/* Responsive */
@media(max-width:1100px){.pb-library{width:160px}}
@media(max-width:900px){.pb-edit-panel{position:fixed;right:0;top:0;bottom:0;z-index:500;box-shadow:-4px 0 24px rgba(0,0,0,.2)}}
</style>
<!-- Page CSS passed to JS for srcdoc iframes -->
<div id="pb-page-css-data" style="display:none">{{ base64_encode($pageStyles) }}</div>
<div id="pb-site-vars" style="display:none">{{ base64_encode('
:root{
  --navy:#003366;--navy-mid:#004080;--navy-faint:#e8eef5;
  --red:#C8102E;--red-faint:#fdf0f2;
  --white:#FFFFFF;--light:#F2F2F2;--grey:#F2F2F2;
  --grey-mid:#dde2e8;--grey-dark:#9aa3ae;
  --text:#003366;--text-light:#1A1A1A;--text-mid:#2d4a6b;--text-muted:#6b7f96;
  --muted:#4A4A1A;--border:#D0D0D0;
  --green:#1a6b3c;--green-bg:#eef7f2;
  --shadow-sm:0 2px 8px rgba(0,51,102,0.06);
  --font:Arial,"Helvetica Neue",Helvetica,sans-serif;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:var(--font);color:var(--text);line-height:1.55}
') }}</div>
</head>
<body>
<div class="pb">

{{-- ── TOP BAR ── --}}
<div class="pb-topbar">
    <div class="pb-logo"><span>RAY<br>NET</span></div>
    <div>
        <div class="pb-page-name">{{ ucwords(str_replace('-',' ',$slug)) }}</div>
        <div class="pb-page-slug">{{ $slug }}.blade.php</div>
    </div>
    <div class="pb-topbar-right">
        <a href="{{ route('admin.pages.index') }}" class="pb-btn pb-btn-ghost">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            All Pages
        </a>
        <a href="{{ route('admin.pages.edit', $slug) }}" class="pb-btn pb-btn-ghost">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
            Source Editor
        </a>
        <button class="pb-btn pb-btn-ghost" id="pageStylesBtn" onclick="toggleStylesPanel()">🎨 Page CSS</button>
        <button class="pb-btn pb-btn-primary" onclick="savePage()" id="saveBtn">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Save  <kbd style="font-size:9px;opacity:.6;font-weight:normal;border:1px solid rgba(255,255,255,.3);padding:0 3px">Ctrl+S</kbd>
        </button>
    </div>
</div>

@if(session('success'))
<div class="pb-notice pb-notice--ok">✓ {{ session('success') }}</div>
@endif

{{-- ── BODY ── --}}
<div class="pb-body">

    {{-- ── BLOCK LIBRARY ── --}}
    <div class="pb-library">
        <div class="pb-lib-head">Add Blocks</div>
        <div class="pb-lib-scroll">
            <div class="pb-lib-section">Content</div>
            <div class="pb-lib-block" onclick="addBlock('heading')"><div class="pb-lib-block__icon">H</div> Heading</div>
            <div class="pb-lib-block" onclick="addBlock('text')"><div class="pb-lib-block__icon">¶</div> Text</div>
            <div class="pb-lib-block" onclick="addBlock('image')"><div class="pb-lib-block__icon">🖼</div> Image</div>
            <div class="pb-lib-block" onclick="addBlock('html')"><div class="pb-lib-block__icon">&lt;/&gt;</div> HTML</div>

            <div class="pb-lib-section">Layout</div>
            <div class="pb-lib-block" onclick="addBlock('hero')"><div class="pb-lib-block__icon">⋯</div> Hero Banner</div>
            <div class="pb-lib-block" onclick="addBlock('two-col')"><div class="pb-lib-block__icon">▥</div> 2 Columns</div>
            <div class="pb-lib-block" onclick="addBlock('three-col')"><div class="pb-lib-block__icon">▦</div> 3 Columns</div>
            <div class="pb-lib-block" onclick="addBlock('divider')"><div class="pb-lib-block__icon">—</div> Divider</div>

            <div class="pb-lib-section">Callouts</div>
            <div class="pb-lib-block" onclick="addBlock('cta')"><div class="pb-lib-block__icon">⚡</div> Call to Action</div>
            <div class="pb-lib-block" onclick="addBlock('alert')"><div class="pb-lib-block__icon">ℹ</div> Alert Box</div>
        </div>
    </div>

    {{-- ── CANVAS ── --}}
    <div class="pb-canvas-wrap" id="canvasWrap">
        <div class="pb-canvas">
            <div class="pb-canvas-inner" id="canvas">
                <div id="blockList">
                    {{-- Blocks rendered by JS --}}
                </div>
                <div class="pb-add-zone pb-add-zone--visible">
                    <button class="pb-add-btn" onclick="addBlock('text')">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Add a block
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── EDIT PANEL ── --}}
    <div class="pb-edit-panel pb-edit-panel--closed" id="editPanel">
        <div class="pb-edit-head">
            <span class="pb-edit-head-title" id="editPanelTitle">✏️ Edit Block</span>
            <button class="pb-edit-close" onclick="closeEditPanel()">×</button>
        </div>
        <div class="pb-edit-body" id="editPanelBody">
            {{-- Fields injected by JS --}}
        </div>
        <div class="pb-edit-foot">
            <button class="pb-btn pb-btn-primary" onclick="applyEdit()" id="applyBtn">✓ Apply</button>
            <button class="pb-btn pb-btn-ghost" onclick="closeEditPanel()">Cancel</button>
        </div>
    </div>

</div>

{{-- ── PAGE CSS PANEL ── --}}
<div id="stylesPanelOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:400" onclick="toggleStylesPanel()"></div>
<div id="stylesPanel" style="display:none;position:fixed;top:0;right:0;bottom:0;width:420px;background:#fff;z-index:401;display:none;flex-direction:column;box-shadow:-4px 0 24px rgba(0,0,0,.2)">
    <div style="padding:.65rem 1rem;background:#1a2332;border-bottom:1px solid rgba(255,255,255,.1);display:flex;align-items:center;justify-content:space-between">
        <span style="font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:.08em;color:#fff">🎨 Page CSS</span>
        <button onclick="toggleStylesPanel()" style="background:none;border:none;color:rgba(255,255,255,.6);cursor:pointer;font-size:18px;line-height:1">×</button>
    </div>
    <div style="padding:.65rem 1rem;background:#fffbeb;border-bottom:1px solid #fde68a;font-size:11px;color:#92400e">
        These styles apply to this page only. They'll be saved inside a &lt;style&gt; tag in the blade file.
    </div>
    <div style="flex:1;overflow:hidden">
        <textarea id="pageCssEditor" style="width:100%;height:100%;border:none;outline:none;font-family:ui-monospace,monospace;font-size:12px;padding:1rem;background:#1e1e2e;color:#cdd6f4;resize:none">{{ $pageStyles }}</textarea>
    </div>
    <div style="padding:.65rem 1rem;background:#f4f5f7;border-top:1px solid #dde2e8;display:flex;gap:.5rem">
        <button class="pb-btn pb-btn-primary" onclick="toggleStylesPanel()">✓ Done</button>
    </div>
</div>

{{-- Hidden save form --}}
<form id="saveForm" method="POST" action="{{ route('admin.pages.blocks.save', $slug) }}" style="display:none">
    @csrf
    <input type="hidden" name="blocks" id="saveBlocks">
    <input type="hidden" name="page_styles" id="saveStyles">
</form>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/htmlmixed/htmlmixed.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/css/css.min.js"></script>

<script>
// ── Block data ────────────────────────────────────────────────────────────
let blocks = {!! $blocksJson !!};
let editingId   = null;
let editQuill   = null;
let editCm      = null;
let colQuills   = {};
let dirty       = false;

// ── Block type definitions ────────────────────────────────────────────────
const BLOCK_TYPES = {
    heading:   { label: 'Heading',        icon: 'H' },
    text:      { label: 'Text',           icon: '¶' },
    hero:      { label: 'Hero Banner',    icon: '⋯' },
    'two-col': { label: '2 Columns',      icon: '▥' },
    'three-col':{ label:'3 Columns',      icon: '▦' },
    alert:     { label: 'Alert Box',      icon: 'ℹ' },
    cta:       { label: 'Call to Action', icon: '⚡' },
    divider:   { label: 'Divider',        icon: '—' },
    image:     { label: 'Image',          icon: '🖼' },
    html:      { label: 'HTML',           icon: '</>' },
};

// ── Preview renderers ─────────────────────────────────────────────────────
function b64Dec(b64) {
    try { return decodeURIComponent(Array.prototype.map.call(atob(b64), c => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)).join('')); } catch(e) { return ''; }
}

const _siteVars   = b64Dec(document.getElementById('pb-site-vars')?.textContent?.trim() || '');
const _pageCssB64 = document.getElementById('pb-page-css-data')?.textContent?.trim() || '';

function buildSrcdoc(innerHtml) {
    const pageCss = b64Dec(_pageCssB64);
    return `<!DOCTYPE html><html><head><style>${_siteVars}${pageCss}
    body{padding:0;margin:0}img{max-width:100%}
    </style></head><body>${innerHtml}</body></html>`;
}

function makeIframe(innerHtml, minH) {
    const srcdoc = buildSrcdoc(innerHtml).replace(/"/g, '&quot;');
    const h = minH || 60;
    return `<iframe srcdoc="${srcdoc}" style="width:100%;border:none;display:block;min-height:${h}px" scrolling="no" onload="try{this.style.height=Math.max(${h},this.contentDocument.documentElement.scrollHeight)+'px'}catch(e){}"></iframe>`;
}

function previewHtml(block) {
    const d = block.data || {};
    switch (block.type) {
        case 'heading':
            const level = d.level || 'h2';
            return makeIframe(`<${level} style="text-align:${d.align||'left'};color:${d.color||'#003366'};padding:.75rem 1.25rem;margin:0">${escHtml(d.text||'Heading text')}</${level}>`, 60);

        case 'text':
            return makeIframe(`<div style="padding:.75rem 1.25rem;line-height:1.6">${d.content||'<p>Text content here.</p>'}</div>`, 60);

        case 'hero':
            const bg = d.bg||'#003366', col = d.color||'#fff', align = d.align||'center';
            return makeIframe(`<div style="background:${bg};color:${col};padding:${d.padding||'2.5rem 1.5rem'};text-align:${align}">
                <h1 style="font-size:1.75rem;font-weight:bold;margin-bottom:.5rem;color:${col}">${escHtml(d.title||'Hero Title')}</h1>
                ${d.subtitle?`<p style="font-size:1rem;opacity:.85;margin-bottom:1rem">${escHtml(d.subtitle)}</p>`:''}
                ${d.button_text?`<span style="display:inline-block;padding:.6rem 1.75rem;background:#C8102E;color:#fff;font-weight:bold">${escHtml(d.button_text)}</span>`:''}
            </div>`, 120);

        case 'two-col':
            return makeIframe(`<div style="display:grid;grid-template-columns:1fr 1fr;gap:${d.gap||'2rem'};padding:1rem 1.25rem">
                <div style="border-left:3px solid #e5e7eb;padding-left:.75rem;min-height:40px">${d.left||'<p>Left column.</p>'}</div>
                <div style="border-left:3px solid #e5e7eb;padding-left:.75rem;min-height:40px">${d.right||'<p>Right column.</p>'}</div>
            </div>`, 80);

        case 'three-col':
            return makeIframe(`<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.5rem;padding:1rem 1.25rem">
                <div style="border-left:3px solid #e5e7eb;padding-left:.75rem;min-height:40px">${d.col1||'<p>Col 1.</p>'}</div>
                <div style="border-left:3px solid #e5e7eb;padding-left:.75rem;min-height:40px">${d.col2||'<p>Col 2.</p>'}</div>
                <div style="border-left:3px solid #e5e7eb;padding-left:.75rem;min-height:40px">${d.col3||'<p>Col 3.</p>'}</div>
            </div>`, 80);

        case 'alert':
            const alertColors = {
                info:    {bg:'#eff6ff',border:'#2563eb',color:'#1e40af'},
                success: {bg:'#eef7f2',border:'#1a6b3c',color:'#1a6b3c'},
                warning: {bg:'#fffbeb',border:'#f59e0b',color:'#92400e'},
                danger:  {bg:'#fdf0f2',border:'#C8102E',color:'#C8102E'},
            };
            const ac = alertColors[d.style||'info']||alertColors.info;
            return makeIframe(`<div style="background:${ac.bg};border-left:4px solid ${ac.border};color:${ac.color};padding:1rem 1.25rem;margin:.25rem">${d.message||'Alert message here.'}</div>`, 60);

        case 'cta':
            return makeIframe(`<div style="background:${d.bg||'#f4f5f7'};padding:2.5rem;text-align:center">
                <h2 style="font-size:1.5rem;font-weight:bold;color:#003366;margin-bottom:.5rem">${escHtml(d.title||'Ready to get involved?')}</h2>
                ${d.subtitle?`<p style="color:#6b7f96;margin-bottom:1rem">${escHtml(d.subtitle)}</p>`:''}
                <span style="display:inline-block;padding:.65rem 1.75rem;background:#003366;color:#fff;font-weight:bold">${escHtml(d.button_text||'Get Started')}</span>
            </div>`, 120);

        case 'divider':
            return makeIframe(`<div style="padding:.25rem 1.25rem"><hr style="border:none;border-top:1px solid ${d.color||'#dde2e8'};margin:1rem 0"></div>`, 30);

        case 'image':
            return makeIframe(`<div style="text-align:${d.align||'center'};padding:1rem 1.25rem">
                ${d.src?`<img src="${escHtml(d.src)}" alt="${escHtml(d.alt||'')}" style="max-width:100%;max-height:200px;object-fit:contain">`:'<div style="background:#f3f4f6;height:120px;display:flex;align-items:center;justify-content:center;color:#9ca3af;font-size:14px">🖼 Image URL not set</div>'}
                ${d.caption?`<p style="font-size:.82rem;color:#6b7f96;margin-top:.35rem">${escHtml(d.caption)}</p>`:''}
            </div>`, 100);

        case 'html':
            return makeIframe((d.content||''), 60);

        default:
            return makeIframe(`<div style="padding:1rem;color:#9ca3af">[${block.type}]</div>`, 40);
    }
}

// ── Render all blocks ─────────────────────────────────────────────────────
function renderBlocks() {
    const list = document.getElementById('blockList');
    if (!blocks.length) {
        list.innerHTML = '<div class="pb-empty"><div class="pb-empty-icon">📄</div><p>No blocks yet. Add one from the sidebar or click the button below.</p></div>';
        return;
    }
    list.innerHTML = blocks.map((b, i) => {
        const type = BLOCK_TYPES[b.type] || { label: b.type, icon: '?' };
        return `
        <div class="pb-add-between" data-index="${i}">
            <button class="pb-add-between-btn" onclick="addBlockAt(${i})">+ Insert block here</button>
        </div>
        <div class="pb-block-row" id="row-${b.id}" data-id="${b.id}">
            <div class="pb-block-label">${type.icon} ${type.label}</div>
            <div class="pb-block-controls">
                <button class="pb-ctrl pb-ctrl--drag" title="Drag to reorder">≡</button>
                <button class="pb-ctrl" title="Edit block" onclick="editBlock('${b.id}')">✏</button>
                <button class="pb-ctrl" title="Clone block" onclick="cloneBlock('${b.id}')">⧉</button>
                <button class="pb-ctrl" title="Move up" onclick="moveBlock('${b.id}',-1)">↑</button>
                <button class="pb-ctrl" title="Move down" onclick="moveBlock('${b.id}',1)">↓</button>
                <button class="pb-ctrl" title="Delete block" onclick="deleteBlock('${b.id}')" style="background:rgba(200,16,46,.2)" onmouseover="this.style.background='rgba(200,16,46,.5)'" onmouseout="this.style.background='rgba(200,16,46,.2)'">🗑</button>
            </div>
            <div class="pb-block-preview">${previewHtml(b)}</div>
        </div>`;
    }).join('') + `
    <div class="pb-add-between" data-index="${blocks.length}">
        <button class="pb-add-between-btn" onclick="addBlockAt(${blocks.length})">+ Insert block here</button>
    </div>`;
}

// ── Sortable drag and drop ────────────────────────────────────────────────
let sortable;
function initSortable() {
    sortable = Sortable.create(document.getElementById('blockList'), {
        animation:       150,
        handle:          '.pb-ctrl--drag',
        ghostClass:      'sortable-ghost',
        chosenClass:     'sortable-chosen',
        filter:          '.pb-add-between',
        preventOnFilter: true,
        onEnd(evt) {
            const rows = [...document.querySelectorAll('.pb-block-row')];
            const newOrder = rows.map(r => r.dataset.id);
            blocks = newOrder.map(id => blocks.find(b => b.id === id)).filter(Boolean);
            dirty = true;
            renderBlocks();
            initSortable();
        }
    });
}

// ── Add block ─────────────────────────────────────────────────────────────
let insertAtIndex = null;
function addBlock(type) {
    const b = makeDefaultBlock(type);
    if (insertAtIndex !== null) {
        blocks.splice(insertAtIndex, 0, b);
        insertAtIndex = null;
    } else {
        blocks.push(b);
    }
    dirty = true;
    renderBlocks();
    initSortable();
    editBlock(b.id);
}

function addBlockAt(index) {
    insertAtIndex = index;
    // Highlight which type to add — just add a text block by default and then edit
    const b = makeDefaultBlock('text');
    blocks.splice(index, 0, b);
    insertAtIndex = null;
    dirty = true;
    renderBlocks();
    initSortable();
    editBlock(b.id);
}

function makeDefaultBlock(type) {
    const id = 'block-' + Date.now();
    const defaults = {
        heading:    { level:'h2', text:'New Heading', align:'left', color:'#003366' },
        text:       { content:'<p>Add your text here.</p>' },
        hero:       { title:'Hero Title', subtitle:'Your subtitle goes here.', button_text:'', button_url:'#', bg:'#003366', color:'#ffffff', align:'center', padding:'3rem 1.5rem' },
        'two-col':  { left:'<p>Left column content.</p>', right:'<p>Right column content.</p>', gap:'2rem' },
        'three-col':{ col1:'<p>Column one.</p>', col2:'<p>Column two.</p>', col3:'<p>Column three.</p>' },
        alert:      { message:'<strong>Note:</strong> Alert message here.', style:'info' },
        cta:        { title:'Ready to get involved?', subtitle:'Join {{ \App\Helpers\RaynetSetting::groupName() }} today.', button_text:'Get Started', button_url:'/register', bg:'#f4f5f7' },
        divider:    { style:'solid', color:'#dde2e8' },
        image:      { src:'', alt:'', caption:'', align:'center' },
        html:       { content:'<div>\n  <!-- Your HTML here -->\n</div>' },
    };
    return { id, type, data: defaults[type] || {} };
}

// ── Edit block ────────────────────────────────────────────────────────────
function editBlock(id) {
    const block = blocks.find(b => b.id === id);
    if (!block) return;

    document.querySelectorAll('.pb-block-row').forEach(r => r.classList.remove('pb-block-selected'));
    document.getElementById('row-' + id)?.classList.add('pb-block-selected');

    editingId = id;
    const type = BLOCK_TYPES[block.type] || { label: block.type, icon: '?' };
    document.getElementById('editPanelTitle').textContent = `${type.icon} ${type.label}`;

    // Build editor UI
    document.getElementById('editPanelBody').innerHTML = buildEditorHtml(block);

    // Init Quill/CodeMirror after DOM update
    editQuill = null; editCm = null; colQuills = {};
    setTimeout(() => initEditorWidgets(block), 50);

    // Show panel
    const panel = document.getElementById('editPanel');
    panel.classList.remove('pb-edit-panel--closed');
}

function buildEditorHtml(block) {
    const d = block.data || {};
    switch (block.type) {
        case 'heading':
            return `
            <div class="pb-field"><label class="pb-field-label">Heading Text</label>
                <input type="text" class="pb-input" id="ef-text" value="${escAttr(d.text||'')}"></div>
            <div class="pb-field"><label class="pb-field-label">Level</label>
                <select class="pb-select" id="ef-level">
                    ${['h1','h2','h3','h4'].map(l=>`<option value="${l}" ${d.level===l?'selected':''}>${l.toUpperCase()}</option>`).join('')}
                </select></div>
            <div class="pb-field"><label class="pb-field-label">Alignment</label>
                <select class="pb-select" id="ef-align">
                    <option value="left" ${d.align==='left'?'selected':''}>Left</option>
                    <option value="center" ${d.align==='center'?'selected':''}>Centre</option>
                    <option value="right" ${d.align==='right'?'selected':''}>Right</option>
                </select></div>
            <div class="pb-field"><label class="pb-field-label">Colour</label>
                <div class="pb-color-row">
                    <input type="color" class="pb-color-input" id="ef-color" value="${d.color||'#003366'}">
                    <input type="text" class="pb-input pb-color-text" id="ef-color-text" value="${d.color||'#003366'}">
                </div></div>`;

        case 'text':
            return `<div class="pb-field"><label class="pb-field-label">Content</label>
                <div class="pb-quill-wrap"><div id="ef-quill">${d.content||''}</div></div></div>`;

        case 'hero':
            return `
            <div class="pb-field"><label class="pb-field-label">Title</label><input type="text" class="pb-input" id="ef-title" value="${escAttr(d.title||'')}"></div>
            <div class="pb-field"><label class="pb-field-label">Subtitle</label><input type="text" class="pb-input" id="ef-subtitle" value="${escAttr(d.subtitle||'')}"></div>
            <div class="pb-field"><label class="pb-field-label">Button Text</label><input type="text" class="pb-input" id="ef-button_text" value="${escAttr(d.button_text||'')}"></div>
            <div class="pb-field"><label class="pb-field-label">Button URL</label><input type="text" class="pb-input" id="ef-button_url" value="${escAttr(d.button_url||'')}"></div>
            <div class="pb-field"><label class="pb-field-label">Background Colour</label>
                <div class="pb-color-row"><input type="color" class="pb-color-input" id="ef-bg" value="${d.bg||'#003366'}"><input type="text" class="pb-input pb-color-text" id="ef-bg-text" value="${d.bg||'#003366'}"></div></div>
            <div class="pb-field"><label class="pb-field-label">Text Colour</label>
                <div class="pb-color-row"><input type="color" class="pb-color-input" id="ef-color" value="${d.color||'#ffffff'}"><input type="text" class="pb-input pb-color-text" id="ef-color-text" value="${d.color||'#ffffff'}"></div></div>
            <div class="pb-field"><label class="pb-field-label">Alignment</label>
                <select class="pb-select" id="ef-align">
                    <option value="left" ${d.align==='left'?'selected':''}>Left</option>
                    <option value="center" ${d.align==='center'?'selected':''}>Centre</option>
                    <option value="right" ${d.align==='right'?'selected':''}>Right</option>
                </select></div>`;

        case 'two-col':
            return `
            <div class="pb-col-editors">
                <div><div class="pb-col-editor-label">Left Column</div>
                    <div class="pb-quill-wrap"><div id="ef-quill-left">${d.left||''}</div></div></div>
                <div><div class="pb-col-editor-label">Right Column</div>
                    <div class="pb-quill-wrap"><div id="ef-quill-right">${d.right||''}</div></div></div>
            </div>
            <div class="pb-field" style="margin-top:.75rem"><label class="pb-field-label">Column Gap</label>
                <input type="text" class="pb-input" id="ef-gap" value="${escAttr(d.gap||'2rem')}"></div>`;

        case 'three-col':
            return `
            <div class="pb-col-editors">
                <div><div class="pb-col-editor-label">Column 1</div><div class="pb-quill-wrap"><div id="ef-quill-col1">${d.col1||''}</div></div></div>
                <div><div class="pb-col-editor-label">Column 2</div><div class="pb-quill-wrap"><div id="ef-quill-col2">${d.col2||''}</div></div></div>
                <div><div class="pb-col-editor-label">Column 3</div><div class="pb-quill-wrap"><div id="ef-quill-col3">${d.col3||''}</div></div></div>
            </div>`;

        case 'alert':
            return `
            <div class="pb-field"><label class="pb-field-label">Style</label>
                <select class="pb-select" id="ef-style">
                    ${['info','success','warning','danger'].map(s=>`<option value="${s}" ${d.style===s?'selected':''}>${s.charAt(0).toUpperCase()+s.slice(1)}</option>`).join('')}
                </select></div>
            <div class="pb-field"><label class="pb-field-label">Message</label>
                <div class="pb-quill-wrap"><div id="ef-quill">${d.message||''}</div></div></div>`;

        case 'cta':
            return `
            <div class="pb-field"><label class="pb-field-label">Title</label><input type="text" class="pb-input" id="ef-title" value="${escAttr(d.title||'')}"></div>
            <div class="pb-field"><label class="pb-field-label">Subtitle</label><input type="text" class="pb-input" id="ef-subtitle" value="${escAttr(d.subtitle||'')}"></div>
            <div class="pb-field"><label class="pb-field-label">Button Text</label><input type="text" class="pb-input" id="ef-button_text" value="${escAttr(d.button_text||'')}"></div>
            <div class="pb-field"><label class="pb-field-label">Button URL</label><input type="text" class="pb-input" id="ef-button_url" value="${escAttr(d.button_url||'')}"></div>
            <div class="pb-field"><label class="pb-field-label">Background Colour</label>
                <div class="pb-color-row"><input type="color" class="pb-color-input" id="ef-bg" value="${d.bg||'#f4f5f7'}"><input type="text" class="pb-input pb-color-text" id="ef-bg-text" value="${d.bg||'#f4f5f7'}"></div></div>`;

        case 'divider':
            return `
            <div class="pb-field"><label class="pb-field-label">Line Colour</label>
                <div class="pb-color-row"><input type="color" class="pb-color-input" id="ef-color" value="${d.color||'#dde2e8'}"><input type="text" class="pb-input pb-color-text" id="ef-color-text" value="${d.color||'#dde2e8'}"></div></div>`;

        case 'image':
            return `
            <div class="pb-field"><label class="pb-field-label">Image URL</label><input type="text" class="pb-input" id="ef-src" value="${escAttr(d.src||'')}" placeholder="/images/example.jpg"></div>
            <div class="pb-field"><label class="pb-field-label">Alt Text</label><input type="text" class="pb-input" id="ef-alt" value="${escAttr(d.alt||'')}"></div>
            <div class="pb-field"><label class="pb-field-label">Caption</label><input type="text" class="pb-input" id="ef-caption" value="${escAttr(d.caption||'')}"></div>
            <div class="pb-field"><label class="pb-field-label">Alignment</label>
                <select class="pb-select" id="ef-align">
                    <option value="left" ${d.align==='left'?'selected':''}>Left</option>
                    <option value="center" ${d.align==='center'?'selected':''}>Centre</option>
                    <option value="right" ${d.align==='right'?'selected':''}>Right</option>
                </select></div>`;

        case 'html':
            return `<div class="pb-field"><label class="pb-field-label">HTML Code</label>
                <div class="pb-cm-wrap"><textarea id="ef-cm">${escHtml(d.content||'')}</textarea></div>
                <div class="pb-field-hint">Write any HTML here — it'll be placed directly in the page.</div></div>`;

        default:
            return `<div class="pb-field"><label class="pb-field-label">Raw HTML</label>
                <div class="pb-cm-wrap"><textarea id="ef-cm">${escHtml(JSON.stringify(block.data||{}, null, 2))}</textarea></div></div>`;
    }
}

function initEditorWidgets(block) {
    const d = block.data || {};
    const quillOpts = { theme:'snow', modules:{ toolbar:[[{header:[1,2,3,false]}],['bold','italic','underline'],['link'],['clean']] } };

    // Single Quill
    if (document.getElementById('ef-quill')) {
        editQuill = new Quill('#ef-quill', quillOpts);
    }

    // Multi-column Quills
    ['left','right','col1','col2','col3'].forEach(key => {
        const el = document.getElementById('ef-quill-' + key);
        if (el) colQuills[key] = new Quill('#ef-quill-' + key, { theme:'snow', modules:{ toolbar:[['bold','italic','link'],['clean']] } });
    });

    // CodeMirror
    const cmEl = document.getElementById('ef-cm');
    if (cmEl) {
        editCm = CodeMirror.fromTextArea(cmEl, { mode:'htmlmixed', theme:'dracula', lineNumbers:true, lineWrapping:true, tabSize:2 });
        editCm.setSize('100%', 220);
    }

    // Colour sync
    document.querySelectorAll('.pb-color-input').forEach(input => {
        const textId = input.id + '-text';
        const textEl = document.getElementById(textId);
        if (textEl) {
            input.addEventListener('input', () => textEl.value = input.value);
            textEl.addEventListener('input', () => { if(/^#[0-9a-f]{6}$/i.test(textEl.value)) input.value = textEl.value; });
        }
    });
}

// ── Apply edit ────────────────────────────────────────────────────────────
function applyEdit() {
    if (!editingId) return;
    const block = blocks.find(b => b.id === editingId);
    if (!block) return;

    const d = block.data || {};
    const g = (id) => document.getElementById(id)?.value?.trim() || '';

    switch (block.type) {
        case 'heading':
            block.data = { level: g('ef-level'), text: g('ef-text'), align: g('ef-align'), color: g('ef-color-text')||g('ef-color') }; break;
        case 'text':
            block.data = { content: editQuill ? editQuill.root.innerHTML : d.content }; break;
        case 'hero':
            block.data = { title: g('ef-title'), subtitle: g('ef-subtitle'), button_text: g('ef-button_text'), button_url: g('ef-button_url'), bg: g('ef-bg-text')||g('ef-bg'), color: g('ef-color-text')||g('ef-color'), align: g('ef-align'), padding: d.padding||'3rem 1.5rem' }; break;
        case 'two-col':
            block.data = { left: colQuills.left?.root.innerHTML||d.left, right: colQuills.right?.root.innerHTML||d.right, gap: g('ef-gap')||'2rem' }; break;
        case 'three-col':
            block.data = { col1: colQuills.col1?.root.innerHTML||d.col1, col2: colQuills.col2?.root.innerHTML||d.col2, col3: colQuills.col3?.root.innerHTML||d.col3 }; break;
        case 'alert':
            block.data = { message: editQuill ? editQuill.root.innerHTML : d.message, style: g('ef-style') }; break;
        case 'cta':
            block.data = { title: g('ef-title'), subtitle: g('ef-subtitle'), button_text: g('ef-button_text'), button_url: g('ef-button_url'), bg: g('ef-bg-text')||g('ef-bg') }; break;
        case 'divider':
            block.data = { color: g('ef-color-text')||g('ef-color') }; break;
        case 'image':
            block.data = { src: g('ef-src'), alt: g('ef-alt'), caption: g('ef-caption'), align: g('ef-align') }; break;
        case 'html': default:
            if (editCm) editCm.save();
            block.data = { content: document.getElementById('ef-cm')?.value || d.content }; break;
    }

    dirty = true;
    renderBlocks();
    initSortable();
    closeEditPanel();
}

function closeEditPanel() {
    editingId = null;
    editQuill = null; editCm = null; colQuills = {};
    document.getElementById('editPanel').classList.add('pb-edit-panel--closed');
    document.getElementById('editPanelBody').innerHTML = '';
    document.querySelectorAll('.pb-block-row').forEach(r => r.classList.remove('pb-block-selected'));
}

// ── Block actions ─────────────────────────────────────────────────────────
function deleteBlock(id) {
    if (!confirm('Delete this block?')) return;
    blocks = blocks.filter(b => b.id !== id);
    if (editingId === id) closeEditPanel();
    dirty = true;
    renderBlocks(); initSortable();
}

function cloneBlock(id) {
    const idx = blocks.findIndex(b => b.id === id);
    if (idx === -1) return;
    const clone = JSON.parse(JSON.stringify(blocks[idx]));
    clone.id = 'block-' + Date.now();
    blocks.splice(idx + 1, 0, clone);
    dirty = true;
    renderBlocks(); initSortable();
}

function moveBlock(id, dir) {
    const idx = blocks.findIndex(b => b.id === id);
    if (idx === -1) return;
    const newIdx = idx + dir;
    if (newIdx < 0 || newIdx >= blocks.length) return;
    [blocks[idx], blocks[newIdx]] = [blocks[newIdx], blocks[idx]];
    dirty = true;
    renderBlocks(); initSortable();
}

// ── Page CSS panel ─────────────────────────────────────────────────────────
function toggleStylesPanel() {
    const panel = document.getElementById('stylesPanel');
    const overlay = document.getElementById('stylesPanelOverlay');
    const isOpen = panel.style.display === 'flex';
    panel.style.display = isOpen ? 'none' : 'flex';
    overlay.style.display = isOpen ? 'none' : 'block';
}

// ── Save ──────────────────────────────────────────────────────────────────
function savePage() {
    const btn = document.getElementById('saveBtn');
    btn.textContent = 'Saving…';
    btn.disabled = true;
    document.getElementById('saveBlocks').value = JSON.stringify(blocks);
    document.getElementById('saveStyles').value = document.getElementById('pageCssEditor').value;
    document.getElementById('saveForm').submit();
    dirty = false;
}

document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && e.key === 's') { e.preventDefault(); savePage(); }
    if (e.key === 'Escape') closeEditPanel();
});

window.addEventListener('beforeunload', e => { if (dirty) { e.preventDefault(); e.returnValue=''; } });
document.getElementById('saveForm').addEventListener('submit', () => dirty = false);

// ── Helpers ───────────────────────────────────────────────────────────────
function escHtml(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function escAttr(s) { return String(s).replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }

// ── Init ──────────────────────────────────────────────────────────────────
renderBlocks();
initSortable();
</script>
</body>
</html>