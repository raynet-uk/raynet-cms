/**
 * SCORM 1.2 API Wrapper
 * Handles communication with the LMS via window.API
 */

var ScormAPI = (function () {

    var _api = null;
    var _initialized = false;
    var _data = {};

    // ── Find the LMS API ─────────────────────────────────────────
    function _findAPI(win) {
        var tries = 0;
        while (!win.API && win.parent && win.parent !== win && tries < 10) {
            win = win.parent;
            tries++;
        }
        return win.API || null;
    }

    function _getAPI() {
        if (_api) return _api;
        _api = _findAPI(window);
        if (!_api && window.opener) _api = _findAPI(window.opener);
        return _api;
    }

    // ── Init / Finish ────────────────────────────────────────────
    function init() {
        var api = _getAPI();
        if (!api) {
            console.warn('SCORM: No LMS API found — running in standalone mode.');
            _initialized = true;
            return true;
        }
        var result = api.LMSInitialize('');
        _initialized = (result === 'true' || result === true);
        if (_initialized) {
            // Restore any saved data
            _data['cmi.core.lesson_status'] = getValue('cmi.core.lesson_status') || 'not attempted';
            _data['cmi.core.score.raw']     = getValue('cmi.core.score.raw') || '';
        }
        return _initialized;
    }

    function finish(status) {
        var api = _getAPI();
        if (!api || !_initialized) return;
        if (status) setValue('cmi.core.lesson_status', status);
        api.LMSCommit('');
        api.LMSFinish('');
        _initialized = false;
    }

    // ── Get / Set ────────────────────────────────────────────────
    function getValue(key) {
        var api = _getAPI();
        if (!api) return _data[key] || '';
        return api.LMSGetValue(key) || '';
    }

    function setValue(key, value) {
        _data[key] = value;
        var api = _getAPI();
        if (!api) return;
        api.LMSSetValue(key, value);
        api.LMSCommit('');
    }

    // ── Convenience helpers ───────────────────────────────────────
    function setStatus(status) {
        // status: 'passed' | 'failed' | 'completed' | 'incomplete' | 'not attempted' | 'browsed'
        setValue('cmi.core.lesson_status', status);
    }

    function setScore(raw, min, max) {
        setValue('cmi.core.score.raw',  raw);
        setValue('cmi.core.score.min',  min || 0);
        setValue('cmi.core.score.max',  max || 100);
    }

    function complete(score) {
        if (score !== undefined) setScore(score, 0, 100);
        var status = (score !== undefined && score >= 80) ? 'passed' : 'completed';
        finish(status);
    }

    return { init, finish, getValue, setValue, setStatus, setScore, complete };

})();

// Auto-init on load
window.addEventListener('load', function () { ScormAPI.init(); });
// Auto-finish on unload
window.addEventListener('beforeunload', function () {
    if (ScormAPI.getValue('cmi.core.lesson_status') === 'not attempted') {
        ScormAPI.finish('incomplete');
    }
});
