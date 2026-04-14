var ScormAPI=(function(){var _api=null,_init=false,_data={};
function _find(w){var t=0;while(!w.API&&w.parent&&w.parent!==w&&t<10){w=w.parent;t++;}return w.API||null;}
function _get(){if(_api)return _api;_api=_find(window);if(!_api&&window.opener)_api=_find(window.opener);return _api;}
function init(){var a=_get();if(!a){_init=true;return true;}var r=a.LMSInitialize('');_init=(r==='true'||r===true);return _init;}
function finish(s){var a=_get();if(!a||!_init)return;if(s)setValue('cmi.core.lesson_status',s);a.LMSCommit('');a.LMSFinish('');_init=false;}
function getValue(k){var a=_get();if(!a)return _data[k]||'';return a.LMSGetValue(k)||'';}
function setValue(k,v){_data[k]=v;var a=_get();if(!a)return;a.LMSSetValue(k,v);a.LMSCommit('');}
function setStatus(s){setValue('cmi.core.lesson_status',s);}
function setScore(r,mn,mx){setValue('cmi.core.score.raw',r);setValue('cmi.core.score.min',mn||0);setValue('cmi.core.score.max',mx||100);}
function complete(s){if(s!==undefined)setScore(s,0,100);finish(s>=80?'passed':'completed');}
window.addEventListener('load',function(){init();});
window.addEventListener('beforeunload',function(){if(getValue('cmi.core.lesson_status')==='not attempted')finish('incomplete');});
return{init,finish,getValue,setValue,setStatus,setScore,complete};})();