@extends('layouts.app')
@section('title', 'Create New Page')
@section('content')

<style>
.pc { max-width:600px; margin:0 auto; padding:2rem 1rem; }
.pc-card { background:#fff; border:1px solid #dde2e8; border-top:3px solid #003366; }
.pc-head { padding:.75rem 1.2rem; background:#f4f5f7; border-bottom:1px solid #dde2e8; font-size:12px; font-weight:bold; text-transform:uppercase; letter-spacing:.07em; color:#003366; }
.pc-body { padding:1.5rem 1.2rem; display:flex; flex-direction:column; gap:1.1rem; }
.pc-field { display:flex; flex-direction:column; gap:.35rem; }
.pc-label { font-size:11px; font-weight:bold; text-transform:uppercase; letter-spacing:.1em; color:#6b7f96; }
.pc-input { border:1px solid #dde2e8; padding:.5rem .8rem; font-size:13px; font-family:inherit; color:#111827; outline:none; transition:border-color .15s; width:100%; box-sizing:border-box; }
.pc-input:focus { border-color:#003366; box-shadow:0 0 0 3px rgba(0,51,102,.07); }
.pc-hint { font-size:11px; color:#9aa3ae; }
.pc-err  { font-size:11px; color:#C8102E; font-weight:bold; }
.pc-foot { padding:.75rem 1.2rem; background:#f4f5f7; border-top:1px solid #dde2e8; display:flex; align-items:center; justify-content:space-between; gap:.75rem; }
.pc-btn { display:inline-flex; align-items:center; gap:.35rem; padding:.45rem 1.1rem; border:1px solid; font-family:inherit; font-size:12px; font-weight:bold; cursor:pointer; transition:all .12s; text-transform:uppercase; letter-spacing:.05em; text-decoration:none; }
.pc-btn-primary { background:#003366; border-color:#003366; color:#fff; }
.pc-btn-primary:hover { background:#002244; }
.pc-btn-ghost { background:transparent; border-color:#dde2e8; color:#6b7f96; }
.pc-btn-ghost:hover { border-color:#003366; color:#003366; }
.pc-divider { height:1px; background:#dde2e8; margin:.25rem 0; }
.pc-toggle-row { display:flex; align-items:flex-start; gap:.75rem; padding:.75rem; background:#f4f5f7; border:1px solid #dde2e8; cursor:pointer; user-select:none; }
.pc-toggle-row:hover { background:#e8eef5; border-color:#003366; }
.pc-toggle-row input[type=checkbox] { width:16px; height:16px; flex-shrink:0; margin-top:2px; accent-color:#003366; cursor:pointer; }
.pc-toggle-label { font-size:13px; font-weight:bold; color:#003366; }
.pc-toggle-sub   { font-size:11px; color:#6b7f96; margin-top:.15rem; }
.pc-url-row { display:flex; align-items:center; border:1px solid #dde2e8; background:#fff; overflow:hidden; }
.pc-url-prefix { padding:.45rem .65rem; background:#f4f5f7; border-right:1px solid #dde2e8; font-size:12px; color:#6b7f96; white-space:nowrap; }
.pc-url-input { flex:1; border:none; padding:.45rem .65rem; font-size:13px; font-family:ui-monospace,monospace; outline:none; }
</style>

<div class="pc">
    <div style="margin-bottom:1rem">
        <a href="{{ route('admin.pages.index') }}" style="font-size:12px;color:#6b7f96;text-decoration:none">← Back to Pages</a>
    </div>

    <div class="pc-card">
        <div class="pc-head">📄 Create New Page</div>
        <form method="POST" action="{{ route('admin.pages.store') }}">
            @csrf
            <div class="pc-body">

                {{-- Title --}}
                <div class="pc-field">
                    <label class="pc-label" for="title">Page Title</label>
                    <input type="text" id="title" name="title" class="pc-input"
                           value="{{ old('title') }}"
                           placeholder="e.g. Our Equipment"
                           oninput="autoFill(this.value)"
                           required maxlength="100" autofocus>
                    @error('title') <div class="pc-err">{{ $message }}</div> @enderror
                    <div class="pc-hint">Shown in the browser tab and as the page heading.</div>
                </div>

                {{-- Slug --}}
                <div class="pc-field">
                    <label class="pc-label" for="slug">File Slug</label>
                    <div style="display:flex;align-items:center;border:1px solid #dde2e8;background:#fff;overflow:hidden">
                        <span class="pc-url-prefix">resources/views/pages/</span>
                        <input type="text" id="slug" name="slug" style="flex:1;border:none;padding:.45rem .65rem;font-size:13px;font-family:ui-monospace,monospace;outline:none"
                               value="{{ old('slug') }}"
                               placeholder="our-equipment"
                               required maxlength="50"
                               pattern="[a-z0-9\-]+"
                               oninput="updatePreviews()"
                               title="Lowercase letters, numbers and hyphens only">
                        <span class="pc-url-prefix" style="border-left:1px solid #dde2e8;border-right:none">.blade.php</span>
                    </div>
                    @error('slug') <div class="pc-err">{{ $message }}</div> @enderror
                </div>

                <div class="pc-divider"></div>

                {{-- Publish toggle --}}
                <label class="pc-toggle-row" for="create_route">
                    <input type="checkbox" id="create_route" name="create_route" value="1"
                           {{ old('create_route', '1') ? 'checked' : '' }}
                           onchange="toggleUrlField(this.checked)">
                    <div>
                        <div class="pc-toggle-label">Publish immediately with a public URL</div>
                        <div class="pc-toggle-sub">Adds a route to routes/web.php so the page is accessible to visitors. Uncheck to create a draft you can publish later.</div>
                    </div>
                </label>

                {{-- Public URL --}}
                <div class="pc-field" id="urlField">
                    <label class="pc-label" for="url">Public URL</label>
                    <div class="pc-url-row">
                        <span class="pc-url-prefix">{{ request()->getSchemeAndHttpHost() }}</span>
                        <input type="text" id="url" name="url" class="pc-url-input"
                               value="{{ old('url') }}"
                               placeholder="/our-equipment">
                    </div>
                    <div class="pc-hint">The URL visitors will use to access this page.</div>
                </div>

            </div>
            <div class="pc-foot">
                <a href="{{ route('admin.pages.index') }}" class="pc-btn pc-btn-ghost">Cancel</a>
                <button type="submit" class="pc-btn pc-btn-primary">Create Page →</button>
            </div>
        </form>
    </div>

    <div style="margin-top:1rem;padding:.85rem 1rem;background:#f4f5f7;border:1px solid #dde2e8;font-size:12px;color:#6b7f96;line-height:1.7">
        <strong style="color:#003366">After creating the page:</strong><br>
        You'll be taken straight to the editor. If you published with a URL, it will be live immediately.
        Use the <strong>Builder</strong> button for visual Divi-style editing, or <strong>Source</strong> for full code access.
    </div>
</div>

<script>
function autoFill(title) {
    const slug = title.toLowerCase()
        .replace(/[^a-z0-9\s\-]/g, '').trim().replace(/\s+/g, '-').replace(/-+/g, '-');
    document.getElementById('slug').value = slug;
    document.getElementById('url').value = slug ? '/' + slug : '';
}
function updatePreviews() {
    const slug = document.getElementById('slug').value;
    const urlEl = document.getElementById('url');
    if (!urlEl.dataset.edited) {
        urlEl.value = slug ? '/' + slug : '';
    }
}
document.getElementById('url').addEventListener('input', function() {
    this.dataset.edited = '1';
});
function toggleUrlField(checked) {
    document.getElementById('urlField').style.display = checked ? '' : 'none';
}
toggleUrlField(document.getElementById('create_route').checked);
</script>
@endsection