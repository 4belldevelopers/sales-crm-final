@php
    use App\Helper\GeneralMethod;
    // ---- inputs (caller sets these; sensible defaults) ----
    $rcvResume      = $rcvResume      ?? '';
    $rcvSkills      = $rcvSkills      ?? [];
    $rcvCompanyLogo = $rcvCompanyLogo ?? '';
    $rcvCompanyName = $rcvCompanyName ?? '';
    $rcvBExp        = $rcvBExp        ?? '--';
    $rcvBCctc       = $rcvBCctc       ?? '--';
    $rcvBEctc       = $rcvBEctc       ?? '--';
    $rcvBNotice     = $rcvBNotice     ?? '--';
    $rcvBLoc        = $rcvBLoc        ?? '--';
    $rcvBPresented  = $rcvBPresented  ?? '--';
    $rcvDownloadUrl = $rcvDownloadUrl ?? '';
    $rcvMaskContact = $rcvMaskContact ?? true;  // false = same-company candidate -> reveal contact/social

    $__p = parse_url((string) $rcvResume, PHP_URL_PATH) ?: (string) $rcvResume;
    $rcvExt = strtolower(pathinfo($__p, PATHINFO_EXTENSION));
    $rcvFileName = basename($__p);
    // single same-origin endpoint: PDFs pass through, doc/docx are converted (LibreOffice, cached)
    $rcvConvertUrl = $rcvResume
        ? route('service_company.job-applications.resume-convert', ['token' => GeneralMethod::value_encrypt($rcvResume)])
        : '';
    if ($rcvDownloadUrl === '') $rcvDownloadUrl = $rcvConvertUrl;
@endphp

<div class="tab-pane fade" id="resume_cv" role="tabpanel">
  <style>
    #resume_cv, #resume_cv * { font-family:'Poppins',-apple-system,BlinkMacSystemFont,sans-serif; }
    #resume_cv { font-size:13px; }
    #resume_cv .rcv-wrap { display:flex; flex-direction:column; gap:16px; }
    #resume_cv .rcv-main { order:2; }
    #resume_cv .rcv-side { order:1; background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:16px; }
    #resume_cv .rcv-side-head { display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; }
    #resume_cv .rcv-side-head .rcv-head-txt h3 { font-size:14px; font-weight:700; color:#0f172a; margin:0; }
    #resume_cv .rcv-sub { font-size:11px; color:#94a3b8; }
    #resume_cv .rcv-summary { display:flex; gap:8px; margin:0; }
    #resume_cv .rcv-sumitem { min-width:78px; background:#f8fafc; border:1px solid #eef2f7; border-radius:8px; padding:8px 10px; text-align:center; }
    #resume_cv .rcv-sumnum { display:block; font-size:14px; font-weight:700; color:#0f172a; line-height:1; }
    #resume_cv .rcv-sumlbl { font-size:10px; color:#94a3b8; text-transform:uppercase; letter-spacing:.3px; }
    #resume_cv .rcv-kw-add { display:flex; gap:8px; margin:12px 0 4px; }
    #resume_cv .rcv-kw-add input { flex:1; min-width:0; padding:8px 10px; border:1px solid #e2e8f0; border-radius:8px; font-size:12px; outline:none; }
    #resume_cv .rcv-kw-add input:focus { border-color:#6366f1; }
    #resume_cv .rcv-add-btn { padding:0 14px; border:0; border-radius:8px; background:#6366f1; color:#fff; font-size:12px; font-weight:600; cursor:pointer; white-space:nowrap; }
    #resume_cv .rcv-add-btn:hover { background:#4f46e5; }
    #resume_cv .rcv-chip-x { cursor:pointer; margin-left:1px; color:#94a3b8; font-weight:700; }
    #resume_cv .rcv-chip-x:hover { color:#ef4444; }
    #resume_cv .rcv-kw-cols { display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:16px; margin-top:14px; }
    #resume_cv .rcv-kw-title { font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.4px; margin-bottom:8px; }
    #resume_cv .rcv-chips { display:flex; flex-wrap:wrap; gap:6px; }
    #resume_cv .rcv-chip { display:inline-flex; align-items:center; gap:5px; padding:4px 9px; border-radius:20px; font-size:11px; font-weight:600; cursor:pointer; border:1px solid #e2e8f0; background:#f8fafc; color:#475569; user-select:none; transition:all .12s; }
    #resume_cv .rcv-chip .rcv-cnt { font-size:10px; background:#e2e8f0; color:#475569; border-radius:10px; padding:0 5px; min-width:15px; text-align:center; }
    #resume_cv .rcv-chip.found { border-color:#bbf7d0; background:#f0fdf4; color:#15803d; }
    #resume_cv .rcv-chip.found .rcv-cnt { background:#bbf7d0; color:#15803d; }
    #resume_cv .rcv-chip.missing { opacity:.6; }
    #resume_cv .rcv-chip.active { background:#fef3c7; border-color:#fcd34d; color:#92400e; box-shadow:0 0 0 2px rgba(251,191,36,.25); }
    #resume_cv .rcv-chip.active .rcv-cnt { background:#fcd34d; color:#92400e; }
    #resume_cv .rcv-main { background:#fff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; display:flex; flex-direction:column; min-height:520px; }
    #resume_cv .rcv-toolbar { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:10px 14px; border-bottom:1px solid #eef2f7; background:#fafbff; flex-wrap:wrap; }
    #resume_cv .rcv-file { display:flex; align-items:center; gap:8px; font-size:12px; font-weight:600; color:#334155; min-width:0; }
    #resume_cv .rcv-file svg { width:16px; height:16px; color:#6366f1; flex-shrink:0; }
    #resume_cv .rcv-file span { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:230px; }
    #resume_cv .rcv-tools { display:flex; align-items:center; gap:6px; }
    #resume_cv .rcv-tools button, #resume_cv .rcv-tools a { display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border:1px solid #e2e8f0; background:#fff; border-radius:7px; color:#475569; cursor:pointer; text-decoration:none; }
    #resume_cv .rcv-tools button:hover, #resume_cv .rcv-tools a:hover { background:#f1f5f9; color:#6366f1; }
    #resume_cv .rcv-tools svg { width:15px; height:15px; }
    #resume_cv .rcv-hitnav { display:flex; align-items:center; gap:4px; font-size:11px; font-weight:600; color:#64748b; padding:0 4px; }
    #resume_cv .rcv-hl-dl { display:inline-flex; align-items:center; gap:5px; width:auto !important; padding:0 10px !important; height:30px; border:1px solid #c7d2fe !important; background:#eef2ff !important; color:#4f46e5 !important; border-radius:7px; font-size:11px; font-weight:600; cursor:pointer; white-space:nowrap; }
    #resume_cv .rcv-hl-dl:hover { background:#6366f1 !important; color:#fff !important; border-color:#6366f1 !important; }
    #resume_cv .rcv-hl-dl[disabled] { opacity:.6; cursor:default; }
    #resume_cv .rcv-dl { position:relative; }
    #resume_cv .rcv-dl-menu { position:absolute; right:0; top:calc(100% + 5px); background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,.12); min-width:248px; z-index:60; display:none; overflow:hidden; }
    #resume_cv .rcv-dl.open .rcv-dl-menu { display:block; }
    #resume_cv .rcv-dl-menu .rcv-dl-item { display:block; width:100%; height:auto; justify-content:flex-start; text-align:left; padding:9px 13px; font-size:12px; font-weight:500; color:#334155; background:#fff; border:0; border-radius:0; cursor:pointer; text-decoration:none; white-space:nowrap; }
    #resume_cv .rcv-dl-menu .rcv-dl-item:hover { background:#eef2ff; color:#4f46e5; }
    #resume_cv .rcv-dl-menu .rcv-dl-item + .rcv-dl-item { border-top:1px solid #f1f5f9; }
    #resume_cv .rcv-viewport { flex:1; overflow:auto; background:#525659; padding:18px; display:flex; flex-direction:column; align-items:center; gap:14px; }
    #resume_cv .rcv-page { position:relative; box-shadow:0 4px 16px rgba(0,0,0,.35); background:#fff; }
    #resume_cv .rcv-page canvas { display:block; }
    #resume_cv .rcv-textlayer { position:absolute; left:0; top:0; right:0; bottom:0; overflow:hidden; line-height:1; opacity:1; }
    #resume_cv .rcv-textlayer span { position:absolute; white-space:pre; color:transparent; transform-origin:0% 0%; }
    #resume_cv .rcv-textlayer mark.rcv-hit { background:rgba(251,191,36,.45); color:transparent; border-radius:2px; }
    #resume_cv .rcv-textlayer mark.rcv-hit-active { background:rgba(249,115,22,.6); box-shadow:0 0 0 2px rgba(249,115,22,.5); }
    #resume_cv .rcv-textlayer mark.rcv-redact { background:#111827; color:transparent !important; border-radius:2px; user-select:none !important; -webkit-user-select:none !important; -moz-user-select:none !important; pointer-events:none; }
    #resume_cv .rcv-mask { display:flex; align-items:center; gap:6px; font-size:11px; color:#64748b; cursor:pointer; user-select:none; }
    #resume_cv .rcv-switch { position:relative; width:32px; height:17px; background:#cbd5e1; border-radius:20px; transition:.15s; display:inline-block; flex-shrink:0; }
    #resume_cv .rcv-switch.on { background:#6366f1; }
    #resume_cv .rcv-switch::after { content:''; position:absolute; top:2px; left:2px; width:13px; height:13px; background:#fff; border-radius:50%; transition:.15s; }
    #resume_cv .rcv-switch.on::after { left:17px; }
    #resume_cv .rcv-loading { color:#e2e8f0; font-size:13px; padding:40px; }
    #resume_cv .rcv-iframe { width:100%; height:70vh; border:0; background:#fff; }
    #resume_cv .rcv-note { background:#fff7ed; border:1px solid #fed7aa; color:#9a3412; font-size:12px; padding:10px 14px; border-radius:8px; margin:12px; }
  </style>

  <div class="rcv-wrap"
       data-convert="{{ $rcvConvertUrl }}"
       data-public="{{ $rcvResume }}"
       data-ext="{{ $rcvExt }}"
       data-mask="{{ $rcvMaskContact ? '1' : '0' }}"
       data-logo="{{ $rcvCompanyLogo }}"
       data-company="{{ $rcvCompanyName }}"
       data-b-exp="{{ $rcvBExp }}"
       data-b-cctc="{{ $rcvBCctc }}"
       data-b-ectc="{{ $rcvBEctc }}"
       data-b-notice="{{ $rcvBNotice }}"
       data-b-loc="{{ $rcvBLoc }}"
       data-b-presented="{{ $rcvBPresented }}">
    <!-- Keyword panel -->
    <aside class="rcv-side">
      <div class="rcv-side-head">
        <div class="rcv-head-txt">
          <h3>Keyword Highlights</h3>
          <span class="rcv-sub">Candidate skills matched against this résumé</span>
        </div>
        <div class="rcv-summary">
          <div class="rcv-sumitem"><span class="rcv-sumnum" id="rcvMatched">0</span><span class="rcv-sumlbl">Matched</span></div>
          <div class="rcv-sumitem"><span class="rcv-sumnum" id="rcvMissing">0</span><span class="rcv-sumlbl">Missing</span></div>
          <div class="rcv-sumitem"><span class="rcv-sumnum" id="rcvHits">0</span><span class="rcv-sumlbl">Highlights</span></div>
        </div>
      </div>
      <div class="rcv-kw-add">
        <input type="text" id="rcvKwInput" placeholder="Add a keyword to check &amp; highlight…" autocomplete="off">
        <button type="button" class="rcv-add-btn" id="rcvKwAddBtn">Add &amp; Check</button>
      </div>
      <div class="rcv-kw-cols">
        <div class="rcv-kw-group">
          <div class="rcv-kw-title">Key Skills</div>
          <div class="rcv-chips" id="rcvJobChips"></div>
        </div>
        <div class="rcv-kw-group" id="rcvCustomGroup" style="display:none;">
          <div class="rcv-kw-title">Added Keywords</div>
          <div class="rcv-chips" id="rcvCustomChips"></div>
        </div>
      </div>
    </aside>

    <!-- Viewer -->
    <section class="rcv-main">
      <div class="rcv-toolbar">
        <div class="rcv-file">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          <span title="{{ $rcvFileName }}">{{ $rcvFileName }}</span>
        </div>
        <div class="rcv-tools">
          <div class="rcv-hitnav">
            <button type="button" id="rcvPrevHit" title="Previous match"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18 15 12 9 6 15"/></svg></button>
            <span id="rcvHitNav">0/0</span>
            <button type="button" id="rcvNextHit" title="Next match"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg></button>
          </div>
          <a id="rcvOpenNew" href="{{ $rcvResume }}" target="_blank" title="Open in new tab"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg></a>
          <div class="rcv-dl" id="rcvDl">
            <button type="button" class="rcv-hl-dl" id="rcvDlToggle">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
              <span>Download</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div class="rcv-dl-menu" id="rcvDlMenu">
              <a class="rcv-dl-item" href="{{ $rcvDownloadUrl }}" target="_blank">Download Resume</a>
              <button type="button" class="rcv-dl-item" data-dl="logo">Resume with Company Logo</button>
              <button type="button" class="rcv-dl-item" data-dl="hl">Resume with Highlighted Keywords</button>
              <button type="button" class="rcv-dl-item" data-dl="both">Highlighted Keywords + Company Logo</button>
            </div>
          </div>
        </div>
      </div>
      <div class="rcv-viewport" id="rcvViewport">
        <div class="rcv-loading" id="rcvLoading">Loading résumé…</div>
        <div id="rcvPages" style="display:flex;flex-direction:column;align-items:center;gap:14px;width:100%;"></div>
        <iframe class="rcv-iframe" id="rcvIframe" style="display:none;"></iframe>
      </div>
    </section>
  </div>

  <script>
  (function(){
    var inited=false, resumeText='', keywords={}, hits=[], curHit=-1, hlOn=true, hideContact=true, pdfDoc=null;
    var EMAIL_RE=/[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}/g;
    var PHONE_RE=/\+?\d[\d\s().\-]{7,}\d/g;
    var URL_RE=/(?:https?:\/\/|www\.)[^\s<>"']+/gi;
    var SOCIAL_RE=/\b(?:linkedin|github|gitlab|twitter|facebook|fb|instagram|insta|behance|dribbble|medium|stackoverflow|youtube|t)\.(?:com|me|io|in|co)[^\s<>"']*/gi;
    function redactHtml(html){
      var wrap=function(m){ return '<mark class="rcv-redact">'+m+'</mark>'; };
      return html.replace(EMAIL_RE,wrap).replace(URL_RE,wrap).replace(SOCIAL_RE,wrap).replace(PHONE_RE,wrap);
    }
    var jobSkills = @json(array_values($rcvSkills));
    function esc(s){ return String(s==null?'':s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
    function escRe(s){ return String(s).replace(/[.*+?^${}()|[\]\\]/g,'\\$&'); }
    function xmlEsc(s){ return String(s==null?'':s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
    function clip(s,n){ s=String(s==null?'':s); return s.length>n ? s.slice(0,n-1)+'…' : s; }
    function $id(id){ return document.getElementById(id); }
    function activeList(){ return Object.keys(keywords).filter(function(k){ return keywords[k].active; }); }
    function buildRegex(){ var a=activeList(); if(!a.length) return null; return new RegExp('('+a.map(escRe).sort(function(x,y){return y.length-x.length;}).join('|')+')','gi'); }
    function countOccurrences(){ Object.keys(keywords).forEach(function(k){ var m=resumeText.match(new RegExp(escRe(k),'gi')); keywords[k].count=m?m.length:0; }); }
    function chipHtml(name){ var k=keywords[name]; var cls='rcv-chip'+(k.count>0?' found':' missing')+(k.active?' active':''); var rm=(k.type==='custom')?'<span class="rcv-chip-x" data-rm="'+esc(name)+'" title="Remove">&times;</span>':''; return '<span class="'+cls+'" data-kw="'+esc(name)+'">'+esc(name)+'<span class="rcv-cnt">'+k.count+'</span>'+rm+'</span>'; }
    function renderChips(){
      var jc='', xc='';
      Object.keys(keywords).forEach(function(n){ if(keywords[n].type==='custom') xc+=chipHtml(n); else jc+=chipHtml(n); });
      $id('rcvJobChips').innerHTML = jc || '<span class="rcv-sub">No skills</span>';
      var cg=$id('rcvCustomGroup');
      if(cg){ if(xc){ cg.style.display='block'; $id('rcvCustomChips').innerHTML=xc; } else { cg.style.display='none'; $id('rcvCustomChips').innerHTML=''; } }
      var matched=0, missing=0; Object.keys(keywords).forEach(function(k){ if(keywords[k].count>0) matched++; else missing++; });
      $id('rcvMatched').textContent=matched; $id('rcvMissing').textContent=missing;
    }
    function addKeyword(name){
      name=(name||'').trim(); if(!name) return;
      var k=Object.keys(keywords).filter(function(x){ return x.toLowerCase()===name.toLowerCase(); })[0];
      if(k){ keywords[k].active=true; }
      else { var m=resumeText.match(new RegExp(escRe(name),'gi')); keywords[name]={type:'custom',active:true,count:m?m.length:0}; }
      renderChips(); applyHighlights();
    }
    function applyHighlights(){
      var re = hlOn ? buildRegex() : null;
      document.querySelectorAll('#rcvPages .rcv-textlayer span').forEach(function(sp){
        if(sp.dataset.orig===undefined) sp.dataset.orig=sp.textContent;
        var t=sp.dataset.orig;
        if(!re && !hideContact){ sp.textContent=t; return; }
        var html=esc(t);
        if(re){ re.lastIndex=0; html=html.replace(re,'<mark class="rcv-hit">$1</mark>'); }
        if(hideContact){ html=redactHtml(html); }
        sp.innerHTML=html;
      });
      hits = Array.prototype.slice.call(document.querySelectorAll('#rcvPages mark.rcv-hit'));
      curHit=-1; $id('rcvHits').textContent=hits.length; $id('rcvHitNav').textContent=hits.length?('0/'+hits.length):'0/0';
    }
    function gotoHit(d){ if(!hits.length) return; if(curHit>=0&&hits[curHit]) hits[curHit].classList.remove('rcv-hit-active'); curHit=(curHit+d+hits.length)%hits.length; var el=hits[curHit]; el.classList.add('rcv-hit-active'); el.scrollIntoView({behavior:'smooth',block:'center'}); $id('rcvHitNav').textContent=(curHit+1)+'/'+hits.length; }
    function withTimeout(p,ms){ return Promise.race([p, new Promise(function(_,r){ setTimeout(function(){ r(new Error('timeout')); }, ms); })]); }
    function loadScript(src){ return new Promise(function(res,rej){ var s=document.createElement('script'); s.src=src; s.onload=res; s.onerror=rej; document.head.appendChild(s); }); }

    function showIframe(msg){
      var wrap=document.querySelector('#resume_cv .rcv-wrap'); var pub=wrap.getAttribute('data-public')||'';
      $id('rcvLoading').style.display='none'; $id('rcvPages').style.display='none';
      var ifr=$id('rcvIframe');
      if(pub){ ifr.src='https://docs.google.com/gview?url='+encodeURIComponent(pub)+'&embedded=true'; ifr.style.display='block'; }
      else { $id('rcvLoading').style.display='block'; $id('rcvLoading').textContent='Résumé is not available for preview.'; }
      if(!document.getElementById('rcvNote')){ var n=document.createElement('div'); n.id='rcvNote'; n.className='rcv-note'; n.textContent=msg||'This file is shown in preview mode.'; $id('rcvViewport').parentNode.insertBefore(n,$id('rcvViewport')); }
      countOccurrences(); renderChips();
    }

    async function renderPdf(url){
      if(typeof pdfjsLib==='undefined'){ showIframe('PDF viewer library not available.'); return; }
      try { var ver=pdfjsLib.version||'2.11.338'; pdfjsLib.GlobalWorkerOptions.workerSrc='https://cdnjs.cloudflare.com/ajax/libs/pdf.js/'+ver+'/pdf.worker.min.js'; } catch(e){}
      var pagesEl=$id('rcvPages'); pagesEl.innerHTML=''; hits=[]; var bytes;
      try {
        var resp=await withTimeout(fetch(url,{credentials:'include'}),35000);
        if(!resp.ok) throw new Error('HTTP '+resp.status);
        bytes=new Uint8Array(await resp.arrayBuffer());
        if(String.fromCharCode.apply(null,bytes.subarray(0,5)).indexOf('%PDF')===-1) throw new Error('not-pdf');
      } catch(err){ showIframe('Showing résumé in preview mode.'); return; }
      var doc;
      try { doc=await withTimeout(pdfjsLib.getDocument({data:bytes}).promise,25000); }
      catch(err){ showIframe('Showing résumé in preview mode.'); return; }
      pdfDoc = doc;
      $id('rcvPages').style.display='flex'; $id('rcvLoading').style.display='none';
      var containerW=Math.min(pagesEl.clientWidth||760,820); resumeText='';
      for(var p=1;p<=doc.numPages;p++){
        var page=await doc.getPage(p);
        var base=page.getViewport({scale:1}); var scale=(containerW-2)/base.width; var viewport=page.getViewport({scale:scale});
        var wrap=document.createElement('div'); wrap.className='rcv-page'; wrap.style.width=Math.floor(viewport.width)+'px'; wrap.style.height=Math.floor(viewport.height)+'px';
        var canvas=document.createElement('canvas'); var outScale=window.devicePixelRatio||1;
        canvas.width=Math.floor(viewport.width*outScale); canvas.height=Math.floor(viewport.height*outScale);
        canvas.style.width=Math.floor(viewport.width)+'px'; canvas.style.height=Math.floor(viewport.height)+'px'; wrap.appendChild(canvas);
        var tl=document.createElement('div'); tl.className='rcv-textlayer'; tl.style.width=Math.floor(viewport.width)+'px'; tl.style.height=Math.floor(viewport.height)+'px'; wrap.appendChild(tl);
        pagesEl.appendChild(wrap);
        var ctx=canvas.getContext('2d'); var tr=outScale!==1?[outScale,0,0,outScale,0,0]:null;
        await page.render({canvasContext:ctx,viewport:viewport,transform:tr}).promise;
        var tc=await page.getTextContent(); resumeText+=tc.items.map(function(i){return i.str;}).join(' ')+'\n';
        try { var t=pdfjsLib.renderTextLayer({textContent:tc,container:tl,viewport:viewport,textDivs:[]}); if(t&&t.promise) await t.promise; } catch(e){}
      }
      countOccurrences();
      Object.keys(keywords).forEach(function(k){ keywords[k].active=(keywords[k].count>0); });
      renderChips(); applyHighlights();
    }

    // ---- banner + downloads ----
    function loadLogo(){
      if(window.__rcvLogoTried) return Promise.resolve(window.__rcvLogo||null);
      window.__rcvLogoTried=true;
      var wrap=document.querySelector('#resume_cv .rcv-wrap'); var src=(wrap&&wrap.getAttribute('data-logo'))||'';
      if(!src) return Promise.resolve(null);
      return fetch(src,{credentials:'include'}).then(function(r){ if(!r.ok) throw 0; return r.blob(); })
        .then(function(b){ return new Promise(function(res){ var fr=new FileReader(); fr.onload=function(){ var im=new Image(); im.onload=function(){ im.__dataUri=fr.result; window.__rcvLogo=im; res(im); }; im.onerror=function(){ res(null); }; im.src=fr.result; }; fr.onerror=function(){ res(null); }; fr.readAsDataURL(b); }); })
        .catch(function(){ return null; });
    }
    function setDlBusy(b){ var t=$id('rcvDlToggle'); if(!t) return; t.disabled=b; var sp=t.querySelector('span'); if(sp) sp.textContent=b?'Preparing…':'Download'; }
    function svgToImage(svg){ return new Promise(function(res,rej){ var blob=new Blob([svg],{type:'image/svg+xml;charset=utf-8'}); var url=URL.createObjectURL(blob); var img=new Image(); img.onload=function(){ URL.revokeObjectURL(url); res(img); }; img.onerror=function(e){ URL.revokeObjectURL(url); rej(e); }; img.src=url; }); }
    function buildBannerSvg(wrap, logoUri){
      var g=function(a){ var v=wrap.getAttribute(a); return (v==null||v==='')?'--':v; };
      var company=wrap.getAttribute('data-company')||'';
      var cols=[ {ic:'bag',l:'EXPERIENCE',v:clip(g('data-b-exp'),14)},{ic:'rupee',l:'CURRENT CTC',v:clip(g('data-b-cctc'),14)},{ic:'rupee',l:'EXPECTED CTC',v:clip(g('data-b-ectc'),14)},{ic:'clock',l:'NOTICE PERIOD',v:clip(g('data-b-notice'),16)},{ic:'pin',l:'CURRENT LOCATION',v:clip(g('data-b-loc'),16)},{ic:'user',l:'PRESENTED BY',v:clip(g('data-b-presented'),16)} ];
      var W=1500,H=110,navy='#0b1f3a',blue='#2563eb',gray='#64748b';
      function icon(type,cx,cy){ var t='transform="translate('+(cx-9)+','+(cy-9)+') scale(0.78)"'; var st='fill="none" stroke="'+blue+'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';
        if(type==='bag') return '<g '+t+'><rect x="2" y="7" width="20" height="14" rx="2" '+st+'/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" '+st+'/></g>';
        if(type==='clock') return '<g '+t+'><circle cx="12" cy="12" r="9" '+st+'/><polyline points="12 7 12 12 16 14" '+st+'/></g>';
        if(type==='pin') return '<g '+t+'><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z" '+st+'/><circle cx="12" cy="10" r="3" '+st+'/></g>';
        if(type==='user') return '<g '+t+'><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" '+st+'/><circle cx="12" cy="7" r="4" '+st+'/></g>';
        if(type==='rupee') return '<g '+t+'><circle cx="12" cy="12" r="10" fill="none" stroke="'+blue+'" stroke-width="1.6"/><text x="12" y="16.5" text-anchor="middle" font-family="Poppins,Arial,sans-serif" font-size="13" font-weight="700" fill="'+blue+'">₹</text></g>'; return ''; }
      var s='<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 '+W+' '+H+'">';
      s+='<rect width="'+W+'" height="'+H+'" fill="#ffffff"/>';
      s+='<rect x="6" y="7" width="'+(W-12)+'" height="96" rx="12" fill="#ffffff" stroke="'+navy+'" stroke-width="2"/>';
      s+='<path d="M456 7 L494 7 L462 103 L424 103 Z" fill="'+navy+'"/><path d="M482 7 L493 7 L461 103 L450 103 Z" fill="'+blue+'"/>';
      if(logoUri){ s+='<image xlink:href="'+logoUri+'" href="'+logoUri+'" x="22" y="20" width="92" height="44" preserveAspectRatio="xMinYMid meet"/>'; if(company) s+='<text x="126" y="48" font-family="Poppins,Arial,sans-serif" font-size="18" font-weight="800" fill="'+navy+'">'+xmlEsc(clip(company,16))+'</text>'; }
      else { s+='<text x="28" y="52" font-family="Poppins,Arial,sans-serif" font-size="22" font-weight="800" fill="'+navy+'">'+xmlEsc(clip(company||'Company',18))+'</text>'; }
      s+='<rect x="22" y="70" width="312" height="26" rx="13" fill="#eaf1ff"/>';
      s+='<circle cx="40" cy="83" r="8" fill="'+blue+'"/><path d="M36 83 l3 3 6 -7" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
      s+='<text x="54" y="87" font-family="Poppins,Arial,sans-serif" font-size="11" font-weight="700" letter-spacing="0.3" fill="'+navy+'">PRESCREENED VERIFIED CANDIDATE</text>';
      var x0=515,x1=W-24,cw=(x1-x0)/cols.length;
      cols.forEach(function(c,i){ var cx=x0+cw*i+cw/2; if(i>0){ var dx=x0+cw*i; s+='<line x1="'+dx+'" y1="18" x2="'+dx+'" y2="92" stroke="#e2e8f0" stroke-width="1.4"/>'; }
        s+=icon(c.ic,cx,34);
        s+='<text x="'+cx+'" y="62" text-anchor="middle" font-family="Poppins,Arial,sans-serif" font-size="10.5" font-weight="600" letter-spacing="0.3" fill="'+gray+'">'+xmlEsc(c.l)+'</text>';
        s+='<text x="'+cx+'" y="84" text-anchor="middle" font-family="Poppins,Arial,sans-serif" font-size="15" font-weight="700" fill="'+navy+'">'+xmlEsc(c.v)+'</text>'; });
      s+='</svg>'; return s;
    }
    async function buildPdf(opts){
      var pages=document.querySelectorAll('#rcvPages .rcv-page');
      if(!pages.length){ alert('This option is available once the résumé has loaded.'); return; }
      setDlBusy(true);
      try {
        if(!(window.jspdf&&window.jspdf.jsPDF)) await loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js');
        var JsPDF=window.jspdf.jsPDF; var wrap=document.querySelector('#resume_cv .rcv-wrap');
        var bannerImg=null, logoFailed=false;
        if(opts.logo){ var logo=await loadLogo(); var logoUri=(logo&&logo.__dataUri)?logo.__dataUri:''; if(!logoUri) logoFailed=true; try { bannerImg=await svgToImage(buildBannerSvg(wrap,logoUri)); } catch(e){ bannerImg=null; } }
        var QUALITY=2.5, MAXW=2200, pdf=null;
        for(var idx=0; idx<pages.length; idx++){
          var pw0=pages[idx];
          // re-render the page from the PDF at high resolution (crisp export)
          var cssW=pw0.clientWidth||760, cssH=pw0.clientHeight||1000, w, h, hi;
          if(pdfDoc){
            var page=await pdfDoc.getPage(idx+1);
            var base=page.getViewport({scale:1});
            var renderW=Math.min(cssW*QUALITY, MAXW);
            var vp=page.getViewport({scale:renderW/base.width});
            hi=document.createElement('canvas'); hi.width=Math.floor(vp.width); hi.height=Math.floor(vp.height);
            await page.render({canvasContext:hi.getContext('2d'), viewport:vp}).promise;
          } else { hi=pw0.querySelector('canvas'); if(!hi) continue; }
          w=hi.width; h=hi.height;
          var headerH=(bannerImg&&idx===0)?Math.round(w*0.073):0;
          var tmp=document.createElement('canvas'); tmp.width=w; tmp.height=h+headerH; var ctx=tmp.getContext('2d');
          ctx.fillStyle='#ffffff'; ctx.fillRect(0,0,w,tmp.height);
          if(headerH) ctx.drawImage(bannerImg,0,0,w,headerH);
          ctx.drawImage(hi,0,headerH,w,h);
          // overlay highlight/redaction by fraction of the on-screen page box
          var pr=pw0.getBoundingClientRect();
          if(opts.highlights){ ctx.fillStyle='rgba(255,210,0,0.4)';
            pw0.querySelectorAll('mark.rcv-hit').forEach(function(mk){ var r=mk.getBoundingClientRect(); ctx.fillRect((r.left-pr.left)/pr.width*w, headerH+(r.top-pr.top)/pr.height*h, r.width/pr.width*w, Math.max(r.height/pr.height*h,6)); }); }
          if(hideContact){ ctx.fillStyle='#111827';
            pw0.querySelectorAll('mark.rcv-redact').forEach(function(mk){ var r=mk.getBoundingClientRect(); ctx.fillRect((r.left-pr.left)/pr.width*w, headerH+(r.top-pr.top)/pr.height*h, r.width/pr.width*w, Math.max(r.height/pr.height*h,6)); }); }
          var img=tmp.toDataURL('image/jpeg',0.96);
          // page size = CSS dims -> the hi-res image lands at ~QUALITY*96 DPI
          var headerCssH=headerH*(cssW/w);
          var ppw=Math.round(cssW), pph=Math.round(cssH+headerCssH); var or=ppw>pph?'l':'p';
          if(!pdf){ pdf=new JsPDF({orientation:or,unit:'px',format:[ppw,pph]}); } else { pdf.addPage([ppw,pph],or); }
          pdf.addImage(img,'JPEG',0,0,ppw,pph,undefined,'FAST');
        }
        if(pdf){ var fn=(($id('rcvDlMenu')&&document.querySelector('#resume_cv .rcv-file span'))||{textContent:'resume'}).textContent.replace(/\.[^.]+$/,''); var sfx=(opts.highlights&&opts.logo)?'_highlighted_logo':opts.highlights?'_highlighted':opts.logo?'_logo':''; pdf.save(fn+sfx+'.pdf'); }
        if(opts.logo&&!bannerImg){ alert('Could not add the company banner; downloaded without it.'); }
        else if(opts.logo&&logoFailed){ alert('Banner added without the company logo (image security).'); }
      } catch(err){ alert('Could not generate the PDF.'); } finally { setDlBusy(false); }
    }

    function wireControls(){
      $id('rcvPrevHit').addEventListener('click', function(){ gotoHit(-1); });
      $id('rcvNextHit').addEventListener('click', function(){ gotoHit(1); });
      var dl=$id('rcvDl'), tg=$id('rcvDlToggle'), mn=$id('rcvDlMenu');
      if(dl&&tg&&mn){
        tg.addEventListener('click', function(e){ e.stopPropagation(); dl.classList.toggle('open'); });
        document.addEventListener('click', function(e){ if(!dl.contains(e.target)) dl.classList.remove('open'); });
        mn.addEventListener('click', function(e){ var it=e.target.closest('.rcv-dl-item'); if(!it) return; dl.classList.remove('open'); var t=it.getAttribute('data-dl');
          if(t==='logo') buildPdf({highlights:false,logo:true}); else if(t==='hl') buildPdf({highlights:true,logo:false}); else if(t==='both') buildPdf({highlights:true,logo:true}); });
      }
      var kwI=$id('rcvKwInput');
      if(kwI){ kwI.addEventListener('keydown', function(e){ if(e.key==='Enter'||e.key===','){ e.preventDefault(); addKeyword(this.value); this.value=''; } }); }
      var kwB=$id('rcvKwAddBtn');
      if(kwB){ kwB.addEventListener('click', function(){ var i=$id('rcvKwInput'); addKeyword(i.value); i.value=''; i.focus(); }); }
      document.addEventListener('click', function(e){ var x=e.target.closest&&e.target.closest('#resume_cv .rcv-chip-x'); if(x){ e.preventDefault(); var nm=x.getAttribute('data-rm'); if(keywords[nm]){ delete keywords[nm]; renderChips(); applyHighlights(); } } });
      // chip click toggles highlight on/off
      document.addEventListener('click', function(e){ if(e.target.closest&&e.target.closest('#resume_cv .rcv-chip-x')) return; var chip=e.target.closest&&e.target.closest('#resume_cv .rcv-chip[data-kw]'); if(chip){ var nm=chip.getAttribute('data-kw'); if(keywords[nm]){ keywords[nm].active=!keywords[nm].active; renderChips(); applyHighlights(); } } });
    }
    function seedKeywords(){ jobSkills.forEach(function(s){ s=String(s||'').trim(); if(s) keywords[s]={type:'skill',active:false,count:0}; }); }
    function init(){
      if(inited) return;
      var pane=document.getElementById('resume_cv');
      if(!pane || pane.getAttribute('data-rcv-inited')==='1') return;  // another (stale) instance already owns this DOM
      pane.setAttribute('data-rcv-inited','1');
      inited=true;
      var wrap=pane.querySelector('.rcv-wrap');
      hideContact = wrap.getAttribute('data-mask') !== '0';
      var convert=wrap.getAttribute('data-convert')||'';
      seedKeywords(); renderChips(); wireControls();
      if(convert){ $id('rcvLoading').textContent='Loading résumé…'; renderPdf(convert); }
      else { showIframe(); }
    }
    window.rcvInitResumeViewer = init;
    var MY_TOKEN = (window.__rcvSeq = (window.__rcvSeq || 0) + 1);
    document.addEventListener('click', function(e){ if(MY_TOKEN !== window.__rcvSeq) return; var a=e.target.closest&&e.target.closest('a[href="#resume_cv"],[data-rcv-tab]'); if(a){ setTimeout(init,120); } });
    if(document.querySelector('#resume_cv.active, #resume_cv.show.active')) setTimeout(init,250);
  })();
  </script>
</div>
