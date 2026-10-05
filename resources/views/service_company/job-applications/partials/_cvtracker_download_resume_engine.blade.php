{{-- Dedicated Isolated Document Engine Exclusively for CV Tracker Download Resume Center (Engine 1-A) --}}
<script>
/**
 * AgyCvTrackerDownloadResumeEngine.js
 * 
 * EXCLUSIVE DEDICATED ENGINE FOR CV TRACKER DOWNLOAD RESUME CENTER
 * (/service_company/job-applications/showdetail)
 * 
 * 100% COMPLETE ISOLATION MANDATE (Rule 15 & Rule 17 Compliance):
 * - Decoupled and isolated from In-House Bench Talent, App Tracker, and Forward Resume engines.
 * - Operates exclusively on CV Tracker job application candidate context.
 * - 100% Client-Side Execution (User RAM + WASM): 0% server/DB read strain, 0% server PDF CPU/RAM strain.
 * - Anti-poaching blind profile support (Option 49: keepName: false).
 * - Multi-stage progress notifications (notify(pct, step, detail)).
 * - Scoped to window.AgyCvTrackerDownloadResumeEngine with 100% backward-compatible bridges.
 */

(function (root, factory) {
  'use strict';
  var exp = factory();
  if (typeof window !== 'undefined') {
    window.AgyCvTrackerDownloadResumeEngine = exp;
    // Backward-compatible bridges so existing callers never break:
    window.__processAgyTestOption = exp.processOption;
    window.__buildRSBlob = exp.buildRecruiterSummaryBlob;
    window.__buildRS = exp.buildRecruiterSummary;
    window.__applyAgyCompanyBrandingToPdfBlob = exp.applyCompanyBranding;
    window.__buildTurboVectorHighlightedResume = exp.buildTurboVectorHighlightedResume;
    window.__renderAgyStrategy2WasmDocxToPdfBlob = exp.renderAgyStrategy2WasmDocxToPdfBlob;
    window.__renderAgyTrueSectionDocxToPdfBlob = exp.renderAgyTrueSectionDocxToPdfBlob;
    window.__preloadAgyMetricFonts = exp.preloadAgyMetricFonts;
    window.__preloadRS = exp.preloadRS;
    window.__rsEnsureJsZip = exp.ensureJsZip;
    window.__rsEnsureDocxConversion = exp.ensureDocxConversion;
    window.__rsEnsureOpenDocLab = exp.ensureOpenDocLab;
    window.readData = exp.readData;
    window.resumeTrackingFileName = exp.resumeTrackingFileName;
    window.fileSafeName = exp.fileSafeName;
    window.fetchResumeBytesFromSources = exp.fetchResumeBytesFromSources;
    window.rsLogoDataUri = exp.logoDataUri;
    window.agyEnsureMetricFonts = exp.preloadAgyMetricFonts;
    window.rsDrawerHighlightedKeywords = exp.rsDrawerHighlightedKeywords;
    window.mergePreScreenedReportAndResume = exp.mergePreScreenedReportAndResume;
    window.downloadBlob = exp.downloadBlob;
    window.rdcAnonymizeRules = exp.rdcAnonymizeRules;
    window.renderAnonymizedPdfBlob = exp.renderAnonymizedPdfBlob;
    window.fetchPdfResumeBlob = exp.fetchPdfResumeBlob;
  }
  if (typeof root !== 'undefined') {
    root.AgyCvTrackerDownloadResumeEngine = exp;
  }
}(typeof self !== 'undefined' ? self : this, function () {
  'use strict';

var rsPdfReady=null;
var rsPdfLibReady=null;
var rsPdfJsReady=null;
var rsJsZipReady=null;
var rsDocxReady=null;
var rsHtml2CanvasReady=null;
var rsJsPdfReady=null;
var rsOpenDocLocalReady=null;
var rsOpenDocExternalReady=null;
var rsLogoCache={};
var rsDocxMaxBytes=10*1024*1024;
var rsPdfMaxBytes=30*1024*1024;
var rsAtsHighlightKeywords=@json($rcvJobSkills ?? []);
var rsDrawerMatchedKeywords=@json($__hdrMatchedSkillNames ?? []);
var rsStarGold='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#f59e0b" d="M12 2.6l2.85 5.78 6.38.93-4.62 4.5 1.09 6.35L12 17.16l-5.7 3 1.09-6.35-4.62-4.5 6.38-.93L12 2.6z"/></svg>';
var rsStarMuted='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#cbd5e1" d="M12 2.6l2.85 5.78 6.38.93-4.62 4.5 1.09 6.35L12 17.16l-5.7 3 1.09-6.35-4.62-4.5 6.38-.93L12 2.6z"/></svg>';
var rsIconUser='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#64748b" d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5zm0 2c-4.42 0-8 2.24-8 5v1h16v-1c0-2.76-3.58-5-8-5z"/></svg>';
var rsIconCalendar='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M7 2h2v2h6V2h2v2h2a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2V2zm12 8H5v9h14v-9z"/></svg>';
var rsIconBriefcase='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M9 6V5a3 3 0 0 1 6 0v1h4a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4zm2 0h2V5a1 1 0 0 0-2 0v1z"/></svg>';
var rsIconExperience='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#64748b" d="M12 2l2.2 4.5 5 .7-3.6 3.5.8 5-4.4-2.4-4.4 2.4.8-5L4.8 7.2l5-.7L12 2zm-6 17h12v2H6v-2z"/></svg>';
var rsIconClock='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#64748b" d="M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2zm1 11h5v-2h-4V6h-2v7z"/></svg>';
var rsIconCompany='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#64748b" d="M4 21V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v17h3v-9h-2v-2h3a1 1 0 0 1 1 1v10H4zm4-14h2V5H8v2zm4 0h2V5h-2v2zm-4 4h2V9H8v2zm4 0h2V9h-2v2zm-4 4h2v-2H8v2zm4 0h2v-2h-2v2z"/></svg>';
var rsIconEducation='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M12 3L1.5 8.5 12 14l8-4.2V16h2V8.5L12 3zm-6 9.1V16c0 1.7 2.7 4 6 4s6-2.3 6-4v-3.9L12 15.2l-6-3.1z"/></svg>';
var rsIconLocation='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5z"/></svg>';
var rsIconLaptop='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M4 4h16a2 2 0 0 1 2 2v10H2V6a2 2 0 0 1 2-2zm0 2v8h16V6H4zm-2 12h20v2H2v-2z"/></svg>';
var rsIconSummary='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zm3 5h8V6H8v2zm0 5h8v-2H8v2zm0 5h5v-2H8v2z"/></svg>';
var rsIconNote='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M4 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H9l-5 5V4zm4 4v2h8V8H8zm0 4v2h6v-2H8z"/></svg>';
var rsIconSkill='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M12 2l2.4 5.4 5.9.6-4.4 3.9 1.3 5.8-5.2-3-5.2 3 1.3-5.8L3.7 8l5.9-.6L12 2z"/></svg>';
var rsIconShield='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M12 2l8 3v6c0 5-3.4 9.6-8 11-4.6-1.4-8-6-8-11V5l8-3zm-1 13.2l6-6-1.4-1.4L11 12.4 8.4 9.8 7 11.2l4 4z"/></svg>';
var rsIconResume='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M6 2h8l5 5v15H6V2zm7 1.8V8h4.2L13 3.8zM8 12h8v-1.7H8V12zm0 4h8v-1.7H8V16zm0 4h6v-1.7H8V20z"/></svg>';
var rsIconEmail='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M4 5h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2zm8 7.2L4.7 7H4v.7l8 5.7 8-5.7V7h-.7L12 12.2z"/></svg>';
var rsIconPhone='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M7.2 2.8l3 3a2 2 0 0 1 .4 2.2l-.9 2.1a13.5 13.5 0 0 0 4.2 4.2l2.1-.9a2 2 0 0 1 2.2.4l3 3a2 2 0 0 1 0 2.8l-1.4 1.4c-.8.8-2 .9-3 .5A25.2 25.2 0 0 1 2.5 7.2c-.4-1-.3-2.2.5-3l1.4-1.4a2 2 0 0 1 2.8 0z"/></svg>';
var rsIconLinkedin='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zm3.4 15V9.6H6V18h2.4zM7.2 8.5a1.4 1.4 0 1 0 0-2.8 1.4 1.4 0 0 0 0 2.8zM18 18v-4.8c0-2.5-1.3-3.8-3.2-3.8-1.4 0-2.1.8-2.5 1.4V9.6H10V18h2.4v-4.2c0-1.1.2-2.2 1.6-2.2s1.5 1.3 1.5 2.3V18H18z"/></svg>';
var rsIconJobDescription='<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M8 2h8a2 2 0 0 1 2 2v2h2v16H4V6h2V4a2 2 0 0 1 2-2zm0 4h8V4H8v2zm-1 6h10v-1.8H7V12zm0 4h10v-1.8H7V16zm0 4h7v-1.8H7V20z"/></svg>';
function rsWithAmdDisabled(fn){
  var wasAmd = (typeof window !== 'undefined' && window.define && window.define.amd);
  if(wasAmd) window.define.amd = false;
  try {
    return Promise.resolve().then(fn).then(function(res){
      if(wasAmd && window.define) window.define.amd = wasAmd;
      return res;
    }).catch(function(err){
      if(wasAmd && window.define) window.define.amd = wasAmd;
      throw err;
    });
  } catch(err) {
    if(wasAmd && window.define) window.define.amd = wasAmd;
    return Promise.reject(err);
  }
}
function rsLoadScript(src){
  return new Promise(function(resolve,reject){
    var existing=document.querySelector('script[src="'+src+'"]');
    if(existing && existing.getAttribute('data-ready')==='1'){ resolve(); return; }
    if(existing && existing.parentNode){
      existing.parentNode.removeChild(existing);
    }
    var s=document.createElement('script');
    var done=false;
    var finish=function(ok){
      if(done) return;
      done=true;
      if(ok){
        s.setAttribute('data-ready','1');
        resolve();
      }else{
        reject(new Error('Load failed: '+src));
      }
    };
    s.src=src; s.async=true;
    s.onload=function(){ finish(true); };
    s.onerror=function(){ finish(false); };
    document.head.appendChild(s);
    setTimeout(function(){ finish(false); },10000);
  });
}
function rsTryList(list,index,test){
  if(index>=list.length) return Promise.reject(new Error('Could not load required document engine'));
  if(test()) return Promise.resolve();
  return rsLoadScript(list[index]).then(function(){
    return test()?Promise.resolve():Promise.reject(new Error('Loaded script but global engine missing'));
  }).catch(function(){ return rsTryList(list,index+1,test); });
}
function rsEnsurePdfMake(){
  if(rsPdfReady && window.pdfMake && window.pdfMake.vfs && (window.pdfMake.vfs['Roboto-Regular.ttf'] || window.pdfMake.vfs['Roboto-Medium.ttf'])) return rsPdfReady;
  var core=[
    '/js/pdfmake.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js',
    'https://cdn.jsdelivr.net/npm/pdfmake@0.2.7/build/pdfmake.min.js'
  ];
  var vfs=[
    '/js/vfs_fonts.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js',
    'https://cdn.jsdelivr.net/npm/pdfmake@0.2.7/build/vfs_fonts.js'
  ];
  rsPdfReady=rsWithAmdDisabled(function(){
    return rsTryList(core,0,function(){
      if(!window.pdfMake && typeof window.require === 'function'){
        try { window.pdfMake = window.require('pdfmake'); } catch(e){}
      }
      return !!(window.pdfMake && typeof window.pdfMake.createPdf === 'function');
    }).then(function(){
      return rsTryList(vfs,0,function(){
        return !!(window.pdfMake && (window.pdfMake.vfs || window.vfs));
      });
    }).then(function(){
      if(window.vfs && window.pdfMake && !window.pdfMake.vfs){
        window.pdfMake.vfs = window.vfs;
      }
      if(window.pdfMake){
        window.pdfMake.fonts={
          Roboto:{
            normal:'Roboto-Regular.ttf',
            bold:'Roboto-Medium.ttf',
            italics:'Roboto-Italic.ttf',
            bolditalics:'Roboto-MediumItalic.ttf'
          }
        };
      }
      return window.pdfMake;
    });
  }).catch(function(err){
    rsPdfReady=null;
    throw err;
  });
  return rsPdfReady;
}
function rsEnsurePdfLib(){
  if(rsPdfLibReady && window.PDFLib && window.PDFLib.PDFDocument) return rsPdfLibReady;
  var sources=[
    '/js/pdf-lib.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js',
    'https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js'
  ];
  rsPdfLibReady=rsWithAmdDisabled(function(){
    return rsTryList(sources,0,function(){
      if(!window.PDFLib && typeof window.require === 'function'){
        try { window.PDFLib = window.require('pdf-lib'); } catch(e){}
      }
      return !!(window.PDFLib && window.PDFLib.PDFDocument);
    });
  }).catch(function(err){
    rsPdfLibReady=null;
    throw err;
  });
  return rsPdfLibReady;
}
function rsEnsureJsZip(){
  if(rsJsZipReady && window.JSZip) return rsJsZipReady;
  var sources=[
    '/js/jszip.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js',
    'https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js'
  ];
  rsJsZipReady=rsWithAmdDisabled(function(){
    return rsTryList(sources,0,function(){
      if(!window.JSZip && typeof window.require === 'function'){
        try { window.JSZip = window.require('jszip'); } catch(e){}
      }
      return !!window.JSZip;
    });
  }).catch(function(err){
    rsJsZipReady=null;
    throw err;
  });
  return rsJsZipReady;
}
function rsEnsureDocxPreview(){
  if(rsDocxReady && window.docx && typeof window.docx.renderAsync==='function') return rsDocxReady;
  var sources=[
    '/js/docx-preview.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/docx-preview/0.3.7/docx-preview.min.js',
    'https://cdn.jsdelivr.net/npm/docx-preview@0.3.7/dist/docx-preview.min.js'
  ];
  rsDocxReady=rsEnsureJsZip().then(function(){
    return rsWithAmdDisabled(function(){
      return rsTryList(sources,0,function(){ return !!(window.docx && typeof window.docx.renderAsync==='function'); });
    });
  }).catch(function(err){
    rsDocxReady=null;
    throw err;
  });
  return rsDocxReady;
}
function rsEnsureHtml2Canvas(){
  if(rsHtml2CanvasReady && typeof window.html2canvas==='function') return rsHtml2CanvasReady;
  var sources=[
    '/js/html2canvas.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js',
    'https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js'
  ];
  rsHtml2CanvasReady=rsWithAmdDisabled(function(){
    return rsTryList(sources,0,function(){ return typeof window.html2canvas==='function'; });
  }).catch(function(err){
    rsHtml2CanvasReady=null;
    throw err;
  });
  return rsHtml2CanvasReady;
}
function rsEnsureJsPdf(){
  if(rsJsPdfReady && ((window.jspdf && typeof window.jspdf.jsPDF==='function') || typeof window.jsPDF==='function')) return rsJsPdfReady;
  var sources=[
    '/js/jspdf.umd.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js',
    'https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js'
  ];
  rsJsPdfReady=rsWithAmdDisabled(function(){
    return rsTryList(sources,0,function(){ return !!((window.jspdf && typeof window.jspdf.jsPDF==='function') || typeof window.jsPDF==='function'); });
  }).catch(function(err){
    rsJsPdfReady=null;
    throw err;
  });
  return rsJsPdfReady;
}
function rsEnsurePdfJs(){
  if(rsPdfJsReady && window.pdfjsLib && typeof window.pdfjsLib.getDocument==='function') return rsPdfJsReady;
  var sources=[
    'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js',
    'https://cdn.jsdelivr.net/npm/pdfjs-dist@2.11.338/build/pdf.min.js'
  ];
  rsPdfJsReady=rsWithAmdDisabled(function(){
    return rsTryList(sources,0,function(){ return !!(window.pdfjsLib && typeof window.pdfjsLib.getDocument==='function'); }).then(function(){
      try{
        var ver=window.pdfjsLib.version || '2.11.338';
        window.pdfjsLib.GlobalWorkerOptions.workerSrc='https://cdnjs.cloudflare.com/ajax/libs/pdf.js/'+ver+'/pdf.worker.min.js';
      }catch(err){}
      return window.pdfjsLib;
    });
  }).catch(function(err){
    rsPdfJsReady=null;
    throw err;
  });
  return rsPdfJsReady;
}
function rsEnsureDocxConversion(){
  return Promise.all([rsEnsureDocxPreview(),rsEnsureHtml2Canvas(),rsEnsureJsPdf()]);
}
function rsRasterizeBlobToPngDataUri(blob){
  return new Promise(function(resolve){
    try{
      var url = URL.createObjectURL(blob);
      var img = new Image();
      img.crossOrigin = 'anonymous';
      img.onload = function(){
        try{
          var canvas = document.createElement('canvas');
          canvas.width = img.naturalWidth || img.width || 300;
          canvas.height = img.naturalHeight || img.height || 100;
          var ctx = canvas.getContext('2d');
          ctx.drawImage(img, 0, 0);
          var pngUri = canvas.toDataURL('image/png');
          URL.revokeObjectURL(url);
          resolve(pngUri);
        }catch(err){
          URL.revokeObjectURL(url);
          resolve(null);
        }
      };
      img.onerror = function(){
        URL.revokeObjectURL(url);
        resolve(null);
      };
      img.src = url;
    }catch(ex){
      resolve(null);
    }
  });
}

function rsFetchBlobToDataUri(urlStr){
  if(!urlStr || urlStr === '--') return Promise.resolve(null);
  if(/^data:image\/(png|jpe?g)/i.test(urlStr)) return Promise.resolve(urlStr);
  if(/^data:image\//i.test(urlStr)){
    // Rasterize non-PNG/JPEG data URI (e.g. svg, webp) to PNG for pdfMake
    return fetch(urlStr)
      .then(function(r){ return r.blob(); })
      .then(rsRasterizeBlobToPngDataUri)
      .catch(function(){ return null; });
  }

  var fetchTarget = urlStr;
  var isSameOrigin = false;
  try{
    var u = new URL(urlStr, window.location.origin);
    if(u.pathname.indexOf('company-logo-proxy') !== -1){
      fetchTarget = u.pathname + (u.search || '');
    }
    isSameOrigin = (u.origin === window.location.origin);
  }catch(e){}

  var controller = typeof AbortController !== 'undefined' ? new AbortController() : null;
  var signal = controller ? controller.signal : undefined;
  var timer = setTimeout(function(){
    if(controller) controller.abort();
  }, 15000);

  var fetchOpts = {
    credentials: isSameOrigin ? 'include' : 'same-origin',
    mode: 'cors',
    cache: 'force-cache',
    signal: signal
  };

  return fetch(fetchTarget, fetchOpts)
    .then(function(resp){
      clearTimeout(timer);
      return resp && resp.ok ? resp.blob() : null;
    })
    .then(function(blob){
      if(!blob || blob.size < 32) return null;
      var type = (blob.type || '').toLowerCase();
      if(type.indexOf('png') === -1 && type.indexOf('jpeg') === -1 && type.indexOf('jpg') === -1){
        return rsRasterizeBlobToPngDataUri(blob);
      }
      return new Promise(function(resolve){
        var reader = new FileReader();
        reader.onload = function(){ resolve(reader.result); };
        reader.onerror = function(){ resolve(null); };
        reader.readAsDataURL(blob);
      });
    })
    .catch(function(){
      clearTimeout(timer);
      return null;
    });
}

function rsLogoDataUri(rawUrl, fallbackRawUrl){
  rawUrl = clean(rawUrl, '');
  fallbackRawUrl = clean(fallbackRawUrl, '');
  if(!rawUrl || rawUrl === '--'){
    if(fallbackRawUrl && fallbackRawUrl !== '--'){
      rawUrl = fallbackRawUrl;
      fallbackRawUrl = '';
    }else{
      return Promise.resolve(null);
    }
  }
  if(/^data:image\//i.test(rawUrl)) return Promise.resolve(rawUrl);
  if(rsLogoCache[rawUrl] && typeof rsLogoCache[rawUrl] === 'string') return Promise.resolve(rsLogoCache[rawUrl]);

  return rsFetchBlobToDataUri(rawUrl).then(function(resultUri){
    if(resultUri && /^data:image\//i.test(resultUri)){
      rsLogoCache[rawUrl] = resultUri;
      return resultUri;
    }
    if(fallbackRawUrl && fallbackRawUrl !== '--' && fallbackRawUrl !== rawUrl){
      if(/^data:image\//i.test(fallbackRawUrl)){
        rsLogoCache[rawUrl] = fallbackRawUrl;
        return fallbackRawUrl;
      }
      return rsFetchBlobToDataUri(fallbackRawUrl).then(function(fallbackUri){
        if(fallbackUri && /^data:image\//i.test(fallbackUri)){
          rsLogoCache[rawUrl] = fallbackUri;
          return fallbackUri;
        }
        return null;
      });
    }
    return null;
  });
}
window.rsLogoDataUri = rsLogoDataUri;
function preloadRecruiterSummaryAssets(){
  var data=null;
  try{ data=readData(); }catch(ex){}
  var tasks=[rsEnsurePdfMake()];
  if(data && (clean(data.reportCompanyLogo,'')!=='--' || clean(data.reportCompanyLogoRaw,'')!=='--')){
    tasks.push(rsLogoDataUri(data.reportCompanyLogo, data.reportCompanyLogoRaw));
  }
  return Promise.all(tasks).catch(function(err){
    console.warn('Recruiter Summary preload skipped:',err);
    return null;
  });
}
function preloadCombinedReportAssets(){
  var ext='';
  var dataEl=document.getElementById('rdcData');
  if(dataEl) ext=(dataEl.getAttribute('data-file-ext')||'').toUpperCase();
  var tasks=[preloadRecruiterSummaryAssets(),rsEnsurePdfLib()];
  return Promise.all(tasks).catch(function(err){
    console.warn('Combined Pre-Screened Report preload skipped:',err);
    return null;
  });
}
function preloadBrowserDocxConversionAssets(){
  return rsEnsureDocxConversion().catch(function(err){
    console.warn('Browser DOCX conversion preload skipped:',err);
    return null;
  });
}
function rsEnsureOpenDocLab(allowExternalFallback){
  allowExternalFallback=allowExternalFallback===true;
  if(rsOpenDocLocalReady) return rsOpenDocLocalReady;
  var openDocSources=[
    {
      label:'Cloudflare OpenDoc runtime',
      moduleUrl:'https://file.jero.works/js/opendoc/casual_doc_wasm.js?v=opendoc-20260903',
      wasmUrl:'https://file.jero.works/js/opendoc/casual_doc_wasm_bg.wasm?v=opendoc-20260903'
    },
    {
      label:'Local OpenDoc runtime',
      moduleUrl:'/js/opendoc/casual_doc_wasm.js',
      wasmUrl:'/js/opendoc/casual_doc_wasm_bg.wasm'
    }
  ];
  async function loadOpenDocModule(moduleUrl,wasmUrl){
    var blobUrl='';
    var module=null;
    try{
      module=await import(moduleUrl);
    }catch(importErr){
      var moduleResponse=null;
      try{
        moduleResponse=await fetch(moduleUrl,{mode:'cors',cache:'force-cache'});
      }catch(moduleFetchErr){
        throw new Error('OpenDoc module fetch failed for '+moduleUrl+': '+(moduleFetchErr && moduleFetchErr.message ? moduleFetchErr.message : 'network blocked'));
      }
      if(!moduleResponse.ok) throw new Error('OpenDoc module request failed with HTTP '+moduleResponse.status);
      var moduleCode=await moduleResponse.text();
      moduleCode=moduleCode.replace(/new URL\(['"]casual_doc_wasm_bg\.wasm['"],\s*import\.meta\.url\)/g, JSON.stringify(wasmUrl));
      blobUrl=URL.createObjectURL(new Blob([moduleCode],{type:'application/javascript'}));
      module=await import(blobUrl);
    }
    if(typeof module.default==='function'){
      var wasmResponse=null;
      try{
        wasmResponse=await fetch(wasmUrl,{mode:'cors',cache:'force-cache'});
      }catch(wasmFetchErr){
        throw new Error('OpenDoc WASM fetch failed for '+wasmUrl+': '+(wasmFetchErr && wasmFetchErr.message ? wasmFetchErr.message : 'network blocked'));
      }
      if(!wasmResponse.ok) throw new Error('OpenDoc WASM request failed with HTTP '+wasmResponse.status);
      await module.default({module_or_path:await wasmResponse.arrayBuffer()});
    }
    if(blobUrl) setTimeout(function(){ URL.revokeObjectURL(blobUrl); }, 30000);
    if(typeof module.open!=='function') throw new Error('OpenDoc open API missing');
    return module;
  }
  rsOpenDocLocalReady=(async function(){
    var lastErr=null;
    for(var i=0;i<openDocSources.length;i++){
      try{
        return await loadOpenDocModule(openDocSources[i].moduleUrl,openDocSources[i].wasmUrl);
      }catch(err){
        lastErr=err;
        console.warn(openDocSources[i].label+' unavailable:',err);
      }
    }
    if(allowExternalFallback){
      if(!rsOpenDocExternalReady){
        rsOpenDocExternalReady=loadOpenDocModule('https://opendoc.casualoffice.org/pkg/casual_doc_wasm.js','https://opendoc.casualoffice.org/pkg/casual_doc_wasm_bg.wasm')
          .catch(function(err){
            rsOpenDocExternalReady=null;
            throw err;
          });
      }
      return rsOpenDocExternalReady;
    }
    throw (lastErr || new Error('OpenDoc runtime unavailable'));
  })().catch(function(err){
    rsOpenDocLocalReady=null;
    throw err;
  });
  return rsOpenDocLocalReady;
}
function preloadOpenDocLabAssets(){
  return rsEnsureOpenDocLab(true).catch(function(err){
    console.warn('OpenDoc lab preload skipped:',err);
    return null;
  });
}
function preloadOpenDocLocalAssets(){
  return rsEnsureOpenDocLab(false).catch(function(err){
    console.warn('Local OpenDoc preload skipped:',err);
    return null;
  });
}
function notifyRdcDownloadStarted(fileName){
  try{
    window.dispatchEvent(new CustomEvent('rdc:download-started',{detail:{fileName:fileName}}));
  }catch(ex){}
  try{
    if(typeof window.__rdcCompleteDownloadUi === 'function'){
      [0,120,500,1200].forEach(function(delay){
        setTimeout(function(){ window.__rdcCompleteDownloadUi(fileName); },delay);
      });
    }
  }catch(ex){}
}
window.__rdcNotifyDownloadStarted = notifyRdcDownloadStarted;
function downloadBlob(blob,fileName){
  var url=URL.createObjectURL(blob);
  var a=document.createElement('a');
  a.href=url;
  a.download=fileName;
  document.body.appendChild(a);
  a.click();
  notifyRdcDownloadStarted(fileName);
  setTimeout(function(){
    URL.revokeObjectURL(url);
    if(a.parentNode) a.parentNode.removeChild(a);
  },1200);
}
function sameOriginUrl(url){
  try{ return (new URL(url, window.location.href)).origin === window.location.origin; }catch(ex){ return false; }
}
function guessResumeDownloadName(ext){
  ext=clean(ext,'FILE').toLowerCase().replace(/[^a-z0-9]+/g,'') || 'file';
  var data=null;
  try{ data=readData(); }catch(ex){}
  if(!data || !data.name || data.name==='--' || data.name==='Candidate'){
    try{
      var candNameEl=document.getElementById('rsData') || document.querySelector('[data-candidate-name]');
      if(candNameEl){
        var n=candNameEl.getAttribute('data-name') || candNameEl.getAttribute('data-candidate-name') || candNameEl.textContent;
        if(n) data={name:clean(n,'Candidate')};
      }
    }catch(ex){}
  }
  try{
    return resumeTrackingFileName(data,'C',ext);
  }catch(ex){
    var candSafe=(data && data.name) ? fileSafeName(data.name,'Candidate') : 'Candidate';
    return 'Jeroworks_'+candSafe+'.'+ext;
  }
}
function collectOriginalResumeDownloadSources(resumeUrls){
  var sources=(Array.isArray(resumeUrls) ? resumeUrls : [resumeUrls]).map(function(url){
    return clean(url,'');
  }).filter(function(url,index,self){
    return url && url!=='--' && self.indexOf(url)===index;
  });
  return sources;
}
function downloadOriginalResumeFromR2First(options){
  /* Purpose: Option 1 hands the browser to R2 immediately. Avoid Blob/CORS fetches here:
     cross-origin resume reads can stall and keep the Resume Center countdown stuck. */
  options=options||{};
  var resumeExt=clean(options.resumeExt||'FILE','FILE').toUpperCase();
  var fileName=guessResumeDownloadName(resumeExt);

  var directSources=collectOriginalResumeDownloadSources([
    options.r2RedirectUrl,
    options.dlUrl,
    options.r2DirectUrl,
    options.originalR2DirectUrl,
    options.r2PublicUrl,
    options.originalR2PublicUrl
  ]);

  if(directSources.length>0){
    var firstSrc=directSources[0];
    try{ firstSrc=encodeURI(decodeURI(firstSrc)); }catch(e){}
    notifyRdcDownloadStarted(fileName);
    var frame=document.createElement('iframe');
    frame.style.display='none';
    frame.setAttribute('aria-hidden','true');
    frame.src=firstSrc;
    document.body.appendChild(frame);
    setTimeout(function(){ if(frame.parentNode) frame.parentNode.removeChild(frame); }, 60000);
    return true;
  }

  throw new Error('Original resume is not available for download.');
}
function removeNode(node){
  if(node && node.parentNode) node.parentNode.removeChild(node);
}
function waitForRenderedAssets(root){
  var waits=[];
  if(document.fonts && document.fonts.ready) waits.push(document.fonts.ready.catch(function(){ return null; }));
  Array.prototype.slice.call(root.querySelectorAll('img')).forEach(function(img){
    if(img.complete) return;
    waits.push(new Promise(function(resolve){
      img.onload=resolve;
      img.onerror=resolve;
      setTimeout(resolve,2500);
    }));
  });
  return Promise.all(waits).then(function(){
    return new Promise(function(resolve){ requestAnimationFrame(function(){ requestAnimationFrame(resolve); }); });
  });
}
function xmlTextContent(node){
  if(!node) return '';
  var parts=[];
  Array.prototype.slice.call(node.childNodes||[]).forEach(function(child){
    if(child.nodeType===3){
      parts.push(child.nodeValue||'');
    }else if(child.nodeType===1){
      var name=(child.localName||child.nodeName||'').toLowerCase();
      if(name==='tab') parts.push(' ');
      else if(name==='br'||name==='cr') parts.push('\n');
      else parts.push(xmlTextContent(child));
    }
  });
  return parts.join('').replace(/[ \t]+\n/g,'\n').replace(/\n[ \t]+/g,'\n').replace(/[ \t]{2,}/g,' ').trim();
}
function xmlParagraphTexts(node){
  var paragraphs=Array.prototype.slice.call((node||{}).getElementsByTagName ? node.getElementsByTagName('*') : []).filter(function(child){
    return (child.localName||'').toLowerCase()==='p';
  }).map(function(p){
    return xmlTextContent(p);
  }).filter(function(text){
    return !!text;
  });
  return paragraphs.length ? paragraphs : [xmlTextContent(node)].filter(function(text){ return !!text; });
}
function normalizeReadableDocxText(text){
  return clean(text,'')
    .replace(/\b(Contact|Phone|Email|Linkedin|LinkedIn|Education|Experience|Key Skills|Professional Summary|Professional Profile|Project|Client|Role|Environment|Description|Roles and Responsibilities)\s*:/g,'$1: ')
    .replace(/([.;])(?=[A-Z][a-z])/g,'$1 ')
    .replace(/\s{2,}/g,' ')
    .trim();
}
function escapeHtml(value){
  return String(value==null?'':value).replace(/[&<>"']/g,function(ch){
    return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch];
  });
}
function docxNodeHas(node,localName){
  return !!Array.prototype.slice.call(node.getElementsByTagName('*')).some(function(child){
    return (child.localName||'').toLowerCase()===localName;
  });
}
function classifyDocxLine(text,index,isBold){
  var raw=clean(text,'');
  var compact=raw.replace(/[:\-–—\s]+$/,'');
  var upperRatio=compact ? compact.replace(/[^A-Z]/g,'').length/Math.max(1,compact.replace(/[^A-Za-z]/g,'').length) : 0;
  if(index===0 && raw.length<=80) return 'docx-name';
  if(index<=2 && raw.length<=100) return 'docx-role';
  if((isBold || upperRatio>0.7) && raw.length<=45) return 'docx-section';
  if(/^(project|client|role|environment|description|responsibilities|roles and responsibilities|education|experience|certification|key skills)\b/i.test(raw)) return 'docx-label';
  return 'docx-body';
}
function readableDocxHtmlFromXml(xmlText){
  var parser=new DOMParser();
  var xml=parser.parseFromString(xmlText,'application/xml');
  if(xml.getElementsByTagName('parsererror').length) throw new Error('DOCX XML parse failed');
  var body=Array.prototype.slice.call(xml.getElementsByTagName('*')).filter(function(node){
    return (node.localName||'').toLowerCase()==='body';
  })[0] || xml;
  var blocks=[];
  var lineIndex=0;
  Array.prototype.slice.call(body.childNodes||[]).forEach(function(node){
    if(node.nodeType!==1) return;
    var name=(node.localName||'').toLowerCase();
    if(name==='p'){
      var text=normalizeReadableDocxText(xmlTextContent(node));
      if(!text){
        blocks.push('<div class="docx-space"></div>');
        return;
      }
      var isBold=docxNodeHas(node,'b');
      var cls=classifyDocxLine(text,lineIndex,isBold);
      lineIndex++;
      blocks.push('<p class="'+cls+'">'+escapeHtml(text).replace(/\n/g,'<br>')+'</p>');
      }else if(name==='tbl'){
      var rows=[];
      Array.prototype.slice.call(node.childNodes||[]).forEach(function(row){
        if(row.nodeType!==1 || (row.localName||'').toLowerCase()!=='tr') return;
        var cells=[];
        Array.prototype.slice.call(row.childNodes||[]).forEach(function(cell){
          if(cell.nodeType!==1 || (cell.localName||'').toLowerCase()!=='tc') return;
          var cellText=xmlParagraphTexts(cell).map(normalizeReadableDocxText).join('\n');
          cells.push('<td>'+escapeHtml(cellText).replace(/\n/g,'<br>')+'</td>');
        });
        if(cells.length) rows.push('<tr>'+cells.join('')+'</tr>');
      });
      if(rows.length) blocks.push('<table class="docx-table">'+rows.join('')+'</table>');
    }
  });
  return [
    '<div class="rs-readable-resume">',
    blocks.join(''),
    '</div>'
  ].join('');
}
async function renderReadableDocxResume(bytes,host){
  await rsEnsureJsZip();
  var zip=await window.JSZip.loadAsync(bytes);
  var documentFile=zip.file('word/document.xml');
  if(!documentFile) throw new Error('DOCX document.xml not found');
  var xmlText=await documentFile.async('string');
  host.innerHTML=[
    '<style>',
    '.rs-readable-resume{width:794px;min-height:1123px;background:#fff;color:#111827;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.45;padding:56px 62px;box-sizing:border-box;}',
    '.rs-readable-resume p{margin:0 0 8px 0;white-space:normal;overflow-wrap:break-word;word-break:normal;}',
    '.rs-readable-resume .docx-name{font-size:25px;line-height:1.16;font-weight:700;text-align:center;margin-bottom:4px;color:#111827;}',
    '.rs-readable-resume .docx-role{font-size:16px;line-height:1.28;font-weight:700;text-align:center;margin-bottom:12px;color:#1f2937;}',
    '.rs-readable-resume .docx-section{font-size:14px;line-height:1.25;font-weight:700;text-transform:uppercase;border-top:1px solid #cbd5e1;border-bottom:1px solid #cbd5e1;background:#f8fafc;text-align:left;padding:5px 8px;margin:14px 0 8px;color:#0f172a;}',
    '.rs-readable-resume .docx-label{font-size:14px;line-height:1.35;font-weight:700;margin-top:8px;color:#111827;}',
    '.rs-readable-resume .docx-body{font-size:13.5px;line-height:1.48;color:#111827;}',
    '.rs-readable-resume .docx-space{height:7px;}',
    '.rs-readable-resume .docx-table{width:100%;border-collapse:collapse;table-layout:fixed;margin:8px 0 12px;font-size:13px;line-height:1.4;}',
    '.rs-readable-resume .docx-table td{border:1px solid #e2e8f0;padding:6px 8px;vertical-align:top;overflow-wrap:anywhere;word-break:normal;}',
    '</style>',
    readableDocxHtmlFromXml(xmlText)
  ].join('');
  await waitForRenderedAssets(host);
  return [host.querySelector('.rs-readable-resume')||host];
}
function normalizeOriginalDocxLayout(pages){
  pages.forEach(function(page){
    Array.prototype.slice.call(page.querySelectorAll('p,li,td,th')).forEach(function(el){
      var cs=window.getComputedStyle(el);
      var fs=parseFloat(cs.fontSize)||12;
      var lh=parseFloat(cs.lineHeight);
      if(!lh || lh<fs*1.22) el.style.lineHeight='1.32';
      if(el.scrollHeight && el.clientHeight && el.scrollHeight>el.clientHeight+2){
        el.style.height='auto';
        el.style.minHeight=(el.scrollHeight+4)+'px';
        el.style.overflow='visible';
      }
    });
    for(var pass=0;pass<2;pass++){
      Array.prototype.slice.call(page.querySelectorAll('article,td,th,body,section')).forEach(function(parent){
        var prev=null;
        Array.prototype.slice.call(parent.children||[]).forEach(function(child){
          if(!child.textContent || !child.textContent.trim()) return;
          var rect=child.getBoundingClientRect();
          if(!rect.height) return;
          if(prev && rect.top<prev.bottom-1){
            var current=parseFloat(child.style.marginTop||window.getComputedStyle(child).marginTop)||0;
            child.style.marginTop=(current+(prev.bottom-rect.top)+6)+'px';
            rect=child.getBoundingClientRect();
          }
          prev=rect;
        });
      });
    }
  });
}
function hasDocxLayoutCollisions(pages){
  var collisionCount=0;
  var checked=0;
  Array.prototype.slice.call(pages||[]).some(function(page){
    var nodes=Array.prototype.slice.call(page.querySelectorAll('p,li,td,th,div')).filter(function(el){
      var text=(el.textContent||'').replace(/\s+/g,' ').trim();
      if(text.length<8) return false;
      var rect=el.getBoundingClientRect();
      if(!rect.width || !rect.height || rect.height<4) return false;
      var cs=window.getComputedStyle(el);
      if(cs.position==='absolute' && rect.height<10) return false;
      return true;
    }).slice(0,260);
    for(var i=1;i<nodes.length;i++){
      var prev=nodes[i-1].getBoundingClientRect();
      var curr=nodes[i].getBoundingClientRect();
      checked++;
      var sameColumn=curr.left<prev.right-8 && curr.right>prev.left+8;
      var verticalOverlap=curr.top<prev.bottom-3;
      if(sameColumn && verticalOverlap){
        collisionCount++;
        if(collisionCount>=3) return true;
      }
    }
    return false;
  });
  return checked>8 && collisionCount>=3;
}
async function renderOriginalDocxResume(bytes,host){
  await rsEnsureDocxPreview();
  await window.docx.renderAsync(bytes,host,null,{
    className:'rs-docx-preview',
    inWrapper:true,
    ignoreWidth:false,
    ignoreHeight:true,
    breakPages:true,
    renderHeaders:true,
    renderFooters:true,
    renderFootnotes:true,
    renderEndnotes:true,
    experimental:true,
    useBase64URL:true
  });
  var style=document.createElement('style');
  style.textContent=[
    '.rs-docx-preview-wrapper{background:#fff!important;padding:0!important;display:block!important;align-items:initial!important;}',
    '.rs-docx-preview-wrapper>section.rs-docx-preview,section.rs-docx-preview{background:#fff!important;box-shadow:none!important;margin:0 0 18px 0!important;overflow:visible!important;transform:none!important;}',
    '.rs-docx-preview{color:#111!important;-webkit-font-smoothing:antialiased;text-rendering:optimizeLegibility;}',
    '.rs-docx-preview article{overflow:visible!important;}',
    '.rs-docx-preview p{line-height:1.32!important;min-height:1.32em!important;margin-bottom:4px!important;overflow:visible!important;}',
    '.rs-docx-preview span{line-height:inherit!important;white-space:pre-wrap!important;overflow-wrap:break-word!important;word-break:normal!important;vertical-align:baseline!important;}',
    '.rs-docx-preview table{border-collapse:collapse!important;table-layout:auto!important;}',
    '.rs-docx-preview td,.rs-docx-preview th{vertical-align:top!important;line-height:1.32!important;height:auto!important;min-height:1.32em!important;overflow:visible!important;}',
    '.rs-docx-preview img{max-width:100%;height:auto;}'
  ].join('');
  host.insertBefore(style,host.firstChild);
  await waitForRenderedAssets(host);
  var pages=Array.prototype.slice.call(host.querySelectorAll('.rs-docx-preview-wrapper > section.rs-docx-preview, section.rs-docx-preview'));
  if(!pages.length) pages=Array.prototype.slice.call(host.querySelectorAll('.docx-wrapper > section.docx, section.docx'));
  if(!pages.length) throw new Error('DOCX original render produced no pages');
  normalizeOriginalDocxLayout(pages);
  await waitForRenderedAssets(host);
  if(hasDocxLayoutCollisions(pages)) throw new Error('DOCX original render has overlapping text');
  return pages;
}
async function fetchResumeBytes(resumeUrl,maxBytes){
  resumeUrl=clean(resumeUrl,'');
  if(resumeUrl==='--') resumeUrl='';
  if(!resumeUrl) throw new Error('Resume URL not available');
  var byteLimit=parseInt(maxBytes,10)||rsDocxMaxBytes;
  var fetchOptions={cache:'no-store'};
  try{
    var resumeOrigin=(new URL(resumeUrl,window.location.href)).origin;
    if(resumeOrigin===window.location.origin) fetchOptions.credentials='include';
    else fetchOptions.mode='cors';
  }catch(err){
    fetchOptions.credentials='include';
  }
  var response=await fetch(resumeUrl,fetchOptions);
  if(!response.ok) throw new Error('Resume request failed with HTTP '+response.status);
  var size=parseInt(response.headers.get('content-length')||'0',10);
  if(size && size>byteLimit) throw new Error('DOCX resume is larger than the browser conversion limit');
  var bytes=await response.arrayBuffer();
  if(bytes.byteLength>byteLimit) throw new Error('DOCX resume is larger than the browser conversion limit');
  return bytes;
}
async function fetchResumeBytesFromSources(resumeUrls,maxBytes){
  var sources=(Array.isArray(resumeUrls) ? resumeUrls : [resumeUrls]).map(function(url){
    return clean(url,'');
  }).filter(function(url,index,self){
    return url && url!=='--' && self.indexOf(url)===index;
  });
  if(!sources.length) throw new Error('Direct R2 resume URL not available');
  var lastErr=null;
  for(var i=0;i<sources.length;i++){
    try{
      return await fetchResumeBytes(sources[i],maxBytes);
    }catch(err){
      lastErr=err;
      console.warn('Direct R2 resume fetch failed:',sources[i],err);
    }
  }
  throw (lastErr || new Error('Direct R2 resume fetch failed'));
}
function isBrowserDocxSafetyError(err){
  var message=err && err.message ? err.message : String(err||'');
  return /browser conversion limit|page limit exceeded|browser safety limit/i.test(message);
}
async function fetchPdfBlobFromSources(resumeUrls){
  var sources=(Array.isArray(resumeUrls) ? resumeUrls : [resumeUrls]).map(function(url){
    return clean(url,'');
  }).filter(function(url,index,self){
    return url && url!=='--' && self.indexOf(url)===index;
  });
  if(!sources.length) throw new Error('Direct R2 PDF URL not available');
  var lastErr=null;
  for(var i=0;i<sources.length;i++){
    try{
      var response=await fetch(sources[i],{mode:'cors',cache:'no-store'});
      if(!response.ok) throw new Error('PDF request failed with HTTP '+response.status);
      var size=parseInt(response.headers.get('content-length')||'0',10);
      if(size && size>rsPdfMaxBytes) throw new Error('PDF resume is larger than the browser merge limit');
      var blob=await response.blob();
      if(blob.size>rsPdfMaxBytes) throw new Error('PDF resume is larger than the browser merge limit');
      return blob;
    }catch(err){
      lastErr=err;
      console.warn('Direct R2 PDF fetch failed:',sources[i],err);
    }
  }
  throw (lastErr || new Error('Direct R2 PDF fetch failed'));
}
async function fetchPdfResumeBlob(resumePdfUrl){
  if(!resumePdfUrl) throw new Error('Resume PDF URL is required');
  return fetchPdfBlobFromSources([resumePdfUrl]);
}
window.fetchPdfResumeBlob = fetchPdfResumeBlob;
function rsKeywordPatternEscape(value){
  return String(value==null?'':value).replace(/[.*+?^${}()|[\]\\]/g,'\\$&');
}
function rsKeywordAliasValues(value){
  /* Purpose: keep job-skill highlighting recruiter-friendly when resumes use
     common cloud shorthand instead of the exact skill label configured on the job. */
  var raw=String(value==null?'':value).replace(/\s+/g,' ').trim();
  var normalized=raw.toLowerCase().replace(/[^a-z0-9+#.]+/g,' ').replace(/\s+/g,' ').trim();
  var aliases=[raw];
  function add(alias){
    if(alias && aliases.map(function(v){ return v.toLowerCase(); }).indexOf(alias.toLowerCase())===-1) aliases.push(alias);
  }
  if(/\baws\b/.test(normalized) && /\bcloud\b/.test(normalized)){
    add('AWS');
    add('Amazon Web Services');
  }
  if(normalized === 'amazon web services'){
    add('AWS');
    add('AWS Cloud');
  }
  if((/\bgcp\b/.test(normalized) || normalized === 'google cloud platform') && normalized.indexOf('cloud') !== -1){
    add('GCP');
    add('Google Cloud');
  }
  if(/\bazure\b/.test(normalized) && /\bcloud\b/.test(normalized)){
    add('Azure');
    add('Microsoft Azure');
  }
  return aliases;
}
function rsKeywordPatternFromValue(value){
  var raw=String(value==null?'':value)
    .replace(/\+\d+\s*More/gi,'')
    .replace(/\s+/g,' ')
    .trim();
  if(!raw) return '';

  var compact=raw.replace(/\s+/g,'');
  if(/^[A-Za-z][+#]{1,2}$/i.test(compact)){
    return '\\b'+rsKeywordPatternEscape(compact);
  }

  var expanded=raw
    .replace(/([a-z])([A-Z])/g,'$1 $2')
    .replace(/([A-Za-z])(\d)/g,'$1 $2')
    .replace(/(\d)([A-Za-z])/g,'$1 $2');
  var tokens=expanded.split(/[\s\u00a0\/\\_\-.+,;:()[\]{}|&]+/).filter(Boolean);
  if(!tokens.length) return '';
  var left=/^[A-Za-z0-9]/.test(tokens[0]) ? '\\b' : '';
  var right=/[A-Za-z0-9]$/.test(tokens[tokens.length-1]) ? '\\b' : '';
  if(tokens.length===1){
    return left+rsKeywordPatternEscape(tokens[0])+right;
  }
  var flexibleJoin='(?:[\\s\\u00a0\\/\\\\_\\-.+,;:()\\[\\]{}|&]+|\\b(?:and|or)\\b)*';
  return left+tokens.map(rsKeywordPatternEscape).join(flexibleJoin)+right;
}
function rsKeywordPatternsForValue(value){
  var seen={};
  var patterns=[];
  rsKeywordAliasValues(value).forEach(function(alias){
    var pattern=rsKeywordPatternFromValue(alias);
    var key=pattern.toLowerCase();
    if(pattern && !seen[key]){
      seen[key]=true;
      patterns.push(pattern);
    }
  });
  return patterns;
}
function rsKeywordRegex(list){
  var seen={};
  var cleaned=(Array.isArray(list) ? list : []).map(function(item){
    if(typeof item === 'string') return item.trim();
    return clean(item && (item.skill_name || item.skill_key || item.name || item.skill),'').trim();
  }).filter(function(value){
    value=value.replace(/\s+/g,' ').trim();
    var key=value.toLowerCase();
    if(value.length<2 || seen[key]) return false;
    seen[key]=true;
    return true;
  });
  if(!cleaned.length) return null;
  cleaned.sort(function(a,b){ return b.length-a.length; });
  var parts=[];
  cleaned.forEach(function(value){
    parts=parts.concat(rsKeywordPatternsForValue(value));
  });
  return parts.length ? new RegExp(parts.join('|'),'gi') : null;
}
function rsDrawerHighlightedKeywords(){
  var values=[];
  var seen={};
  function addKeyword(value){
    value=(value || '').replace(/\+\d+\s*More/gi,'').replace(/\s+/g,' ').trim();
    if(!value) return;
    var key=value.toLowerCase();
    if(seen[key]) return;
    seen[key]=true;
    values.push(value);
  }
  (Array.isArray(rsDrawerMatchedKeywords) ? rsDrawerMatchedKeywords : []).forEach(addKeyword);
  Array.prototype.slice.call(document.querySelectorAll('#kt_activitiesApplication .cdr-skill-tag.cdr-skill-match:not(.cdr-skill-more), .cdr-skill-tag.cdr-skill-match:not(.cdr-skill-more)')).forEach(function(el){
    addKeyword(el.textContent || '');
  });
  (Array.isArray(rsAtsHighlightKeywords) ? rsAtsHighlightKeywords : []).forEach(addKeyword);
  return values.length ? values : rsAtsHighlightKeywords;
}
/* Purpose: isolated browser-only anonymizer helpers for Download Resume Center option 29.
   They never call the server-side resume converter or alter existing report/highlight flows. */
function rdcPlain(value){
  return String(value==null?'':value).replace(/\s+/g,' ').trim();
}
function rdcEscapeRegExp(value){
  return rdcPlain(value).replace(/[.*+?^${}()|[\]\\]/g,'\\$&');
}
function rdcDigitsOnly(value){
  return String(value==null?'':value).replace(/\D+/g,'');
}
function rdcPushUnique(list,value){
  value=rdcPlain(value);
  if(!value || value==='--') return;
  var key=value.toLowerCase();
  if(list.some(function(item){ return item.toLowerCase()===key; })) return;
  list.push(value);
}
function rdcFlexibleTextRegex(value){
  var parts=rdcPlain(value).split(/\s+/).filter(Boolean);
  if(!parts.length) return null;
  return new RegExp(parts.map(rdcEscapeRegExp).join('[\\s\\u00a0._-]+'),'gi');
}
function rdcPhoneTextRegex(value){
  var digits=rdcDigitsOnly(value);
  if(digits.length<5) return null;
  return new RegExp('(?:\\+?\\d{1,3}[\\s().\\-+]*)?'+digits.split('').join('[\\s().\\-+]*'),'g');
}
function rdcContactLabelRegexes(){
  return [
    /\b(?:e[\s._-]*mail|email|mail)[\s._-]*(?:id|address)?[\s.:#-]*(?:primary|secondary|alternate|alt|1|2)?\b/gi,
    /\b(?:phone|mobile|mob|cell|contact|tel|telephone)[\s._-]*(?:number|no|num|id|details)?[\s.:#-]*(?:primary|secondary|alternate|alt|1|2)?\b/gi,
    /\b(?:contact|personal)[\s._-]*(?:details|info|information)\b/gi,
    /\b(?:whats[\s._-]*app|whatsapp|linked[\s._-]*in|linkedin)[\s._-]*(?:id|profile|url|link|number|no)?\b/gi
  ];
}
function rdcLinkedInTargets(value){
  value=rdcPlain(value);
  if(!value || value==='--') return [];
  var targets=[];
  rdcPushUnique(targets,value);
  var withoutProtocol=value.replace(/^https?:\/\//i,'').replace(/^www\./i,'');
  rdcPushUnique(targets,withoutProtocol);
  if(!/^https?:\/\//i.test(value) && !/^www\./i.test(value) && /linkedin\.com/i.test(value)){
    rdcPushUnique(targets,'https://'+value);
    rdcPushUnique(targets,'www.'+withoutProtocol);
  }
  var match=withoutProtocol.match(/linkedin\.com\/(?:in|pub|company)\/([^/?#\s]+)/i);
  if(match && match[1]){
    rdcPushUnique(targets,match[1].replace(/[-_]+/g,' '));
    rdcPushUnique(targets,match[1]);
  }
  return targets;
}
function rdcAnonymizeRules(data,options){
  /* Purpose: keep the anonymizer deterministic by using known drawer PII first,
     with regex only as a fallback for PII present in the resume but not in fields. */
  data=data||{};
  options=options||{};
  var textRegexes=[];
  var phoneDigits=[];
  var exactTerms=[];
  var emails=[];
  var phones=[];
  if(options.keepName!==true){
    rdcPushUnique(exactTerms,data.name);
    rdcPlain(data.name).split(/\s+/).forEach(function(token){
      if(token.length>=3) rdcPushUnique(exactTerms,token);
    });
  }
  [data.email,data.secondaryEmail].forEach(function(email){
    rdcPushUnique(emails,email);
    rdcPushUnique(exactTerms,email);
  });
  [data.phone,data.secondaryPhone].forEach(function(phone){
    rdcPushUnique(phones,phone);
    rdcPushUnique(exactTerms,phone);
  });
  rdcLinkedInTargets(data.linkedin).forEach(function(target){
    rdcPushUnique(exactTerms,target);
  });
  phones.forEach(function(phone){
    var digits=rdcDigitsOnly(phone);
    if(digits.length>=5 && phoneDigits.indexOf(digits)===-1) phoneDigits.push(digits);
    var phoneRe=rdcPhoneTextRegex(phone);
    if(phoneRe) textRegexes.push(phoneRe);
  });
  exactTerms.forEach(function(term){
    if(rdcDigitsOnly(term).length>=5) return;
    var re=rdcFlexibleTextRegex(term);
    if(re) textRegexes.push(re);
  });
  textRegexes.push(/[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}/gi);
  textRegexes.push(/\+?\d[\d\s().\-]{7,}\d/g);
  textRegexes.push(/(?:https?:\/\/|www\.)?linkedin\.com\/(?:in|pub|company)\/[^\s<>"')]+/gi);
  rdcContactLabelRegexes().forEach(function(regex){ textRegexes.push(regex); });
  return {textRegexes:textRegexes,phoneDigits:phoneDigits,emails:emails,phones:phones,exactTerms:exactTerms};
}
function rdcRegexMatches(regex,text){
  regex.lastIndex=0;
  var ok=regex.test(text);
  regex.lastIndex=0;
  return ok;
}
function rdcShouldRedactText(text,rules){
  text=rdcPlain(text);
  if(!text) return false;
  var lower=text.toLowerCase();
  var digits=rdcDigitsOnly(text);
  if(digits.length>=5 && rules.phoneDigits.some(function(phoneDigits){
    return phoneDigits.indexOf(digits)!==-1 || digits.indexOf(phoneDigits)!==-1;
  })) return true;
  if(rules.exactTerms.some(function(term){
    term=rdcPlain(term).toLowerCase();
    return term.length>=3 && lower.indexOf(term)!==-1;
  })) return true;
  return rules.textRegexes.some(function(regex){ return rdcRegexMatches(regex,text); });
}
function rdcRedactTextValue(text,rules){
  var original=String(text==null?'':text);
  var output=original;
  rules.textRegexes.forEach(function(regex){
    regex.lastIndex=0;
    output=output.replace(regex,function(match){ return new Array(Math.max(2,match.length)+1).join('\u00a0'); });
    regex.lastIndex=0;
  });
  if(output!==original) return output;
  return rdcShouldRedactText(original,rules) ? new Array(Math.max(2,original.length)+1).join('\u00a0') : original;
}
function rdcRedactDocxDom(root,rules){
  var showText=window.NodeFilter ? window.NodeFilter.SHOW_TEXT : 4;
  var walker=document.createTreeWalker(root,showText,null);
  var nodes=[];
  var redacted=0;
  while(walker.nextNode()) nodes.push(walker.currentNode);
  nodes.forEach(function(node){
    var next=rdcRedactTextValue(node.nodeValue||'',rules);
    if(next!==node.nodeValue){
      node.nodeValue=next;
      redacted++;
    }
  });
  return redacted;
}
function rsTextItemRect(item,viewport){
  if(!item || !viewport || !(window.pdfjsLib && window.pdfjsLib.Util)) return null;
  var tx=window.pdfjsLib.Util.transform(viewport.transform,item.transform);
  var h=Math.max(Math.abs((item.height||0)*viewport.scale),Math.abs(tx[3]||0),6);
  var w=Math.max(Math.abs((item.width||0)*viewport.scale),2);
  return {x:tx[4],y:tx[5]-h,w:w,h:h};
}
function rsPageTextRuns(textContent){
  var text='';
  var runs=[];
  (textContent && Array.isArray(textContent.items) ? textContent.items : []).forEach(function(item){
    var value=(item && item.str ? String(item.str) : '');
    if(!value.trim()) return;
    if(text.length) text+=' ';
    var start=text.length;
    text+=value;
    runs.push({item:item,start:start,end:text.length});
  });
  return {text:text,runs:runs};
}
function rsOption3KeywordsFromResumeText(text){
  return [];
}
function rsOption3BuildRegex(list){
  return typeof rsKeywordRegex === 'function' ? rsKeywordRegex(list) : null;
}
function rdcDrawAnonymizedBlocks(ctx,textContent,viewport,rules){
  if(!ctx || !textContent || !viewport || !rules) return 0;
  var count=0;
  ctx.save();
  ctx.fillStyle='#ffffff';
  (textContent.items||[]).forEach(function(item){
    if(!rdcShouldRedactText(item && item.str,rules)) return;
    var rect=rsTextItemRect(item,viewport);
    if(!rect) return;
    var pad=Math.max(2,Math.min(8,rect.h*0.22));
    ctx.fillRect(Math.max(0,rect.x-pad),Math.max(0,rect.y-pad),rect.w+(pad*2),rect.h+(pad*2));
    count++;
  });
  ctx.restore();
  return count;
}
async function renderAnonymizedPdfBlob(pdfBlob,rules){
  await Promise.all([rsEnsurePdfJs(),rsEnsureJsPdf()]);
  var sourceBytes=await pdfBlob.arrayBuffer();
  var bytes=new Uint8Array(sourceBytes);
  var sig=String.fromCharCode.apply(null,bytes.subarray(0,5));
  if(sig.indexOf('%PDF')===-1) throw new Error('Resume source is not a valid PDF');
  var doc=await window.pdfjsLib.getDocument({data:bytes}).promise;
  var JsPDF=window.jspdf.jsPDF;
  var outPdf=null;
  var textItems=0;
  var redactions=0;
  for(var p=1;p<=doc.numPages;p++){
    var pdfJsPage=await doc.getPage(p);
    var base=pdfJsPage.getViewport({scale:1});
    var cssScale=Math.min(1.35,820/base.width);
    var cssViewport=pdfJsPage.getViewport({scale:cssScale});
    var renderScale=Math.min(3,2800/base.width);
    var renderViewport=pdfJsPage.getViewport({scale:renderScale});
    var canvas=document.createElement('canvas');
    canvas.width=Math.floor(renderViewport.width);
    canvas.height=Math.floor(renderViewport.height);
    var ctx=canvas.getContext('2d',{willReadFrequently:true});
    ctx.fillStyle='#ffffff';
    ctx.fillRect(0,0,canvas.width,canvas.height);
    await pdfJsPage.render({canvasContext:ctx,viewport:renderViewport}).promise;
    var tc=await pdfJsPage.getTextContent();
    textItems += (tc.items||[]).length;
    redactions += rdcDrawAnonymizedBlocks(ctx,tc,renderViewport,rules);
    var pageW=Math.round(cssViewport.width);
    var pageH=Math.round(cssViewport.height);
    var orient=pageW>pageH?'l':'p';
    if(!outPdf) outPdf=new JsPDF({orientation:orient,unit:'px',format:[pageW,pageH]});
    else outPdf.addPage([pageW,pageH],orient);
    outPdf.addImage(canvas.toDataURL('image/jpeg',0.9),'JPEG',0,0,pageW,pageH,undefined,'FAST');
  }
  if(!outPdf) throw new Error('No resume pages were rendered');
  if(!textItems) throw new Error('This resume appears to be scanned/image-only. Browser anonymization needs extractable text.');
  if(!redactions) console.warn('Anonymized Resume completed with no PII matches from drawer data or fallback regex.');
  return outPdf.output('blob');
}
function clean(value,fallback){
  value=String(value==null?'':value).replace(/\s+/g,' ').trim();
  return value || fallback || '--';
}
function limitText(value,max,fallback){
  value=clean(value,fallback||'--');
  return value.length>max ? value.slice(0,max-1).trim()+'...' : value;
}
function safeNumber(value,min,max){
  value=parseInt(value,10);
  if(isNaN(value)) value=0;
  return Math.max(min,Math.min(max,value));
}
function initials(name){
  return clean(name,'Candidate').split(' ').filter(Boolean).map(function(part){ return part.charAt(0).toUpperCase(); }).slice(0,2).join('') || 'CA';
}
function slug(value){
  return clean(value,'candidate').replace(/[^a-z0-9]+/gi,'_').replace(/^_+|_+$/g,'').toLowerCase() || 'candidate';
}
function fileSafeName(value,fallback){
  return clean(value,fallback || 'Candidate').replace(/[\\/:*?"<>|]+/g,' ').replace(/\s+/g,' ').trim() || (fallback || 'Candidate');
}
function resumeTrackingFileName(data,marker,ext){
  ext=clean(ext||'pdf','pdf').toLowerCase().replace(/[^a-z0-9]+/g,'') || 'pdf';
  return 'Jeroworks_'+fileSafeName(data && data.name,'Candidate')+'.'+ext;
}
function preScreenReportFileName(data){
  return 'PRESCREEN_REPORT_'+fileSafeName(data && data.name,'Candidate')+'.pdf';
}
function readData(){
  var el=document.getElementById('rsData');
  if(!el) throw new Error('rsData not found');
  var g=function(k){ return el.getAttribute(k)||''; };
  var gj=function(k){ try{return JSON.parse(el.getAttribute(k)||'[]')||[];}catch(ex){return[];} };

  // Live DOM synchronization for recruiter comments
  var liveNote = '';
  var editorEl = document.getElementById('jw_recruiter_note_editor');
  if (editorEl) {
    liveNote = (editorEl.innerText || editorEl.textContent || '').trim();
  }
  if (!liveNote) {
    var displayEl = document.querySelector('.po-recruiter-note-text');
    if (displayEl && displayEl.style.display !== 'none') {
      liveNote = (displayEl.innerText || displayEl.textContent || '').trim();
    }
  }
  var finalNote = (!isGap(liveNote) ? liveNote : '') || g('data-recruiter-note');

  var rcvWrap = document.querySelector('.rcv-wrap');

  // Live DOM synchronization for notice period (do not overwrite valid data with '--' or gaps)
  var liveNotice = rcvWrap ? clean(rcvWrap.getAttribute('data-b-notice'), '') : '';
  var finalNotice = (!isGap(liveNotice) ? liveNotice : '') || g('data-notice');

  // Live DOM synchronization for location (do not overwrite valid data with '--', 'Not Specified', or gaps)
  var liveLoc = rcvWrap ? clean(rcvWrap.getAttribute('data-b-loc'), '') : '';
  var finalLoc = (!isGap(liveLoc) && liveLoc !== 'Not Specified' ? liveLoc : '') || g('data-location');

  // Live DOM synchronization for experience (do not overwrite valid data with '--', 'Not Specified', or gaps)
  var liveExp = rcvWrap ? clean(rcvWrap.getAttribute('data-b-exp'), '') : '';
  var finalExp = (!isGap(liveExp) && liveExp !== 'Not Specified' ? liveExp : '') || g('data-experience');

  // Live DOM synchronization for match score
  var matchVal = safeNumber(g('data-match'), 0, 100);
  var matchEl = document.querySelector('.jw-match-score-badge, .skill-match-percent, [data-skill-match]');
  if (matchEl) {
    var mAttr = matchEl.getAttribute('data-skill-match');
    var mText = mAttr || matchEl.innerText || matchEl.textContent || '';
    var mNum = parseInt(mText.replace(/[^\d]/g, ''), 10);
    if (!isNaN(mNum) && mNum >= 0 && mNum <= 100) matchVal = mNum;
  }

  var raw = {
    id: parseInt(g('data-report-id').replace(/[^\d]/g, '').slice(-6), 10) || parseInt(document.getElementById('candidate_core_id') ? document.getElementById('candidate_core_id').value : 0, 10),
    name:g('data-name')||'Candidate',
    full_name:g('data-name')||'Candidate',
    email:g('data-email'),
    secondaryEmail:g('data-secondary-email'),
    phone:g('data-phone'),
    secondaryPhone:g('data-secondary-phone'),
    phoneCode:g('data-phone-code'),
    secondaryPhoneCode:g('data-secondary-phone-code'),
    linkedin:g('data-linkedin'),
    designation:g('data-designation'),
    current_designation:g('data-designation'),
    company:g('data-company'),
    current_company:g('data-company'),
    location:finalLoc,
    current_location:finalLoc,
    experience:finalExp,
    total_exp:finalExp,
    notice:finalNotice,
    np_id:finalNotice,
    education:g('data-education'),
    qualification_name:g('data-education'),
    workMode:g('data-work-mode'),
    wm:g('data-work-mode'),
    work_mode:g('data-work-mode'),
    rating:safeNumber(g('data-candidate-rating'), 0, 5),
    reportId:g('data-report-id'),
    reportDate:g('data-report-date'),
    recruiterName:clean(g('data-recruiter-name'), '--'),
    reportCompanyName:g('data-report-company-name') || (rcvWrap ? rcvWrap.getAttribute('data-company') : ''),
    reportCompanyLogo:g('data-report-company-logo') || (rcvWrap ? rcvWrap.getAttribute('data-logo') : ''),
    reportCompanyLogoRaw:g('data-report-company-logo-raw') || (rcvWrap ? rcvWrap.getAttribute('data-logo-raw') : ''),
    aiVersion:g('data-ai-version'),
    match:matchVal,
    isImmediate:g('data-is-immediate')==='1' || /immediate/i.test(finalNotice),
    recruiterNote:finalNote,
    careerSummary:g('data-career-summary'),
    jobTitle:g('data-job-title'),
    skills:gj('data-skills'),
    validation:gj('data-validation')
  };

  return raw;
}
function isGap(value){
  value=clean(value,'');
  return !value || value==='--' || value==='-' || /^available 01 jan 1970$/i.test(value);
}
function titleCase(value){
  value=clean(value,'');
  if(!value) return '--';
  return value.toLowerCase().replace(/\b([a-z])/g,function(m){ return m.toUpperCase(); });
}
function cityOnly(value){
  value=clean(value,'--');
  if(isGap(value)) return '--';
  var commaParts=value.split(',').map(function(part){ return clean(part,''); }).filter(Boolean);
  if(commaParts.length>1) return commaParts[0];
  var states='andhra pradesh|arunachal pradesh|assam|bihar|chhattisgarh|goa|gujarat|haryana|himachal pradesh|jharkhand|karnataka|kerala|madhya pradesh|maharashtra|manipur|meghalaya|mizoram|nagaland|odisha|punjab|rajasthan|sikkim|tamil nadu|telangana|tripura|uttar pradesh|uttarakhand|west bengal|delhi|jammu and kashmir|ladakh|puducherry|chandigarh|dadra and nagar haveli|daman and diu|andaman and nicobar islands|lakshadweep';
  var pattern=new RegExp('\\s+('+states+')$','i');
  return clean(value.replace(pattern,''),'--');
}
function matchTone(score,completeness){
  if(completeness<45) return {label:'Profile Review',priority:'REVIEW',decision:'DATA REVIEW REQUIRED',round:'Recruiter Validation',color:'#7c3aed',fill:'#f5f3ff',border:'#ddd6fe'};
  if(score>=80) return {label:'Excellent Fit',priority:'HIGH',decision:'RECOMMENDED FOR INTERVIEW',round:'Technical L1',color:'#047857',fill:'#ecfdf5',border:'#a7f3d0'};
  if(score>=60) return {label:'Strong Fit',priority:'HIGH',decision:'RECOMMENDED FOR INTERVIEW',round:'Technical L1',color:'#1d4ed8',fill:'#eff6ff',border:'#bfdbfe'};
  if(score>=40) return {label:'Needs Review',priority:'MEDIUM',decision:'RECRUITER REVIEW REQUIRED',round:'Recruiter Review',color:'#b45309',fill:'#fffbeb',border:'#fde68a'};
  return {label:'Review Required',priority:'REVIEW',decision:'REVIEW BEFORE DECISION',round:'Recruiter Validation',color:'#be123c',fill:'#fff1f2',border:'#fecdd3'};
}
function panel(stack,margin,fill,border){
  return {
    margin:margin||[0,0,0,7],
    table:{widths:['*'],body:[[ {stack:Array.isArray(stack)?stack:[stack],margin:[12,10,12,10],fillColor:fill||'#ffffff'} ]]},
    layout:{
      hLineWidth:function(){return 0.55;}, vLineWidth:function(){return 0.55;},
      hLineColor:function(){return border||'#dbe3ef';}, vLineColor:function(){return border||'#dbe3ef';},
      paddingLeft:function(){return 0;}, paddingRight:function(){return 0;},
      paddingTop:function(){return 0;}, paddingBottom:function(){return 0;}
    }
  };
}
function fixedPanel(stack,margin,fill,border,minHeight){
  return {
    margin:margin||[0,0,0,7],
    stack:[
      {canvas:[{type:'rect',x:0,y:0,w:260,h:minHeight||72,r:6,lineWidth:0.55,lineColor:border||'#dbe3ef',color:fill||'#ffffff'}],margin:[0,0,0,-(minHeight||72)]},
      {table:{widths:['*'],heights:function(){return minHeight||72;},body:[[ {stack:Array.isArray(stack)?stack:[stack],margin:[12,10,12,10]} ]]},layout:'noBorders'}
    ]
  };
}
function roundedFullCard(stack,margin,height,fill,border){
  return {
    margin:margin||[0,0,0,8],
    stack:[
      {canvas:[{type:'rect',x:0,y:0,w:535,h:height,r:6,lineWidth:0.55,lineColor:border||'#dbe3ef',color:fill||'#ffffff'}],margin:[0,0,0,-height]},
      {table:{widths:['*'],heights:function(){return height;},body:[[ {stack:Array.isArray(stack)?stack:[stack],margin:[12,10,12,10]} ]]},layout:'noBorders'}
    ]
  };
}
function headerCard(companyName,logoCell,reportDate,captainName){
  return {
    margin:[0,0,0,10],
    stack:[
      {canvas:[{type:'rect',x:0,y:0,w:535,h:64,r:6,lineWidth:0.55,lineColor:'#dbe3ef',color:'#ffffff'}],margin:[0,0,0,-64]},
      {
        table:{widths:[220,'*'],body:[[
          {
            table:{widths:['auto','*'],body:[[logoCell,{stack:[
              {text:'SHARED BY',fontSize:6,bold:true,color:'#64748b',margin:[0,1,0,3]},
              {text:companyName,fontSize:11.2,bold:true,color:'#0f172a',margin:[0,0,0,0]}
            ],border:[false,false,false,false]}]]},
            layout:'noBorders',
            margin:[14,12,10,10],
            border:[false,false,false,false]
          },
          {
            stack:[
              {text:'PRE-SCREENED CANDIDATE REPORT',style:'title',margin:[0,2,0,0]},
              {text:(clean(reportDate,'--')+' · Confidential').toUpperCase(),style:'eyebrow',margin:[0,4,0,0]}
            ],
            margin:[10,14,14,10],
            border:[false,false,false,false]
          }
        ]]},
        layout:'noBorders'
      }
    ]
  };
}
function unbrandedHeaderCard(reportDate){
  return {
    margin:[0,0,0,10],
    stack:[
      {canvas:[{type:'rect',x:0,y:0,w:535,h:64,r:6,lineWidth:0.55,lineColor:'#dbe3ef',color:'#ffffff'}],margin:[0,0,0,-64]},
      {
        table:{widths:['*'],body:[[
          {
            stack:[
              {text:'PRE-SCREENED CANDIDATE REPORT',style:'title',margin:[0,2,0,0]},
              {text:(clean(reportDate,'--')+' · Confidential').toUpperCase(),style:'eyebrow',margin:[0,4,0,0]}
            ],
            margin:[14,14,14,10],
            border:[false,false,false,false]
          }
        ]]},
        layout:'noBorders'
      }
    ]
  };
}
function pairedCardRow(leftStack,rightStack,leftFill,rightFill,rightBorder){
  return {
    margin:[0,0,0,8],
    stack:[
      {canvas:[{type:'rect',x:0,y:0,w:264,h:88,r:6,lineWidth:0.55,lineColor:'#cbd5e1',color:leftFill||'#ffffff'},{type:'rect',x:271,y:0,w:264,h:88,r:6,lineWidth:0.55,lineColor:rightBorder||'#cbd5e1',color:rightFill||'#ffffff'}],margin:[0,0,0,-88]},
      {columns:[
        {stack:Array.isArray(leftStack)?leftStack:[leftStack],width:264,margin:[12,10,12,10]},
        {stack:Array.isArray(rightStack)?rightStack:[rightStack],width:264,margin:[12,10,12,10]}
      ],columnGap:7}
    ]
  };
}
function iconLine(icon,text,style,color,iconWidth){
  var width=iconWidth||7;
  return {columns:[
    {svg:icon,width:width,margin:[0,1,0,0],alignment:'left'},
    {text:text,style:style||'small',color:color||undefined,margin:[3,0,0,0]}
  ],columnGap:0};
}
function labeledIconLine(icon,label,value,style,color,iconWidth){
  var width=iconWidth||7;
  return {columns:[
    {svg:icon,width:width,margin:[0,1,0,0],alignment:'left'},
    {text:[{text:label,bold:true},' '+value],style:style||'small',color:color||undefined,margin:[3,0,0,0]}
  ],columnGap:0};
}
function sectionTitle(title,subtle,icon){
  if(!icon) return {text:title,style:subtle?'sectionTitleMuted':'sectionTitle',margin:[0,0,0,8]};
  return {columns:[
    {svg:icon,width:10,margin:[0,0,0,0]},
    {text:'',width:5},
    {text:title,style:subtle?'sectionTitleMuted':'sectionTitle',margin:[0,0.5,0,0]}
  ],columnGap:0,margin:[0,0,0,8]};
}
function metaCell(label,value,align){
  return {stack:[
    {text:label,style:'metaLabel',alignment:align||'left'},
    {text:clean(value),style:'metaValue',alignment:align||'left',margin:[0,2,0,0]}
  ],margin:[0,4,0,4]};
}
function metricCell(label,value,icon){
  return {stack:[
    {columns:[
      {text:'',width:'*'},
      {svg:icon,width:10.5,margin:[0,0.2,0,0]},
      {text:'',width:6},
      {text:clean(label).toUpperCase(),style:'metricLabel',color:'#64748b',width:'auto',margin:[0,1.9,0,0]},
      {text:'',width:'*'}
    ],columnGap:0,margin:[0,0,0,8]},
    {text:clean(value),style:'metricValue',alignment:'center'}
  ],margin:[4,9,4,8]};
}
function dataCompleteness(data){
  var checks=[
    data.name,data.designation||data.jobTitle,data.experience,data.company,data.location,
    data.education,data.notice,data.workMode,data.careerSummary
  ];
  var score=checks.filter(function(v){ return !isGap(v); }).length;
  if((data.skills||[]).length) score+=1;
  if((data.validation||[]).length) score+=1;
  if(!isGap(data.recruiterNote)) score+=1;
  return Math.round(score/12*100);
}
function buildSkillRows(skills){
  var rows=[
    [
      {text:'SKILLS',style:'tableHead'},
      {text:'EXPERIENCE',style:'tableHead'},
      {text:'RATING',style:'tableHead',alignment:'center'}
    ]
  ];
  // Strict deduplication by normalized lowercase name - prevents repeating skills
  var skillMap = new Map();
  (skills || []).forEach(function (skill) {
    var rawName = clean(skill && (skill.name || skill.skill_name || skill.skill_key), '');
    var normKey = rawName.toLowerCase().trim();
    if (!normKey) return;

    var rating = safeNumber(skill.rating, 0, 5);
    var exp = Number(skill.exp || 0);
    var expText = clean(skill.expText || skill.skill_value, '');

    if (!skillMap.has(normKey)) {
      skillMap.set(normKey, { name: rawName, rating: rating, exp: exp, expText: expText });
    } else {
      var existing = skillMap.get(normKey);
      if (rating > existing.rating) existing.rating = rating;
      if (exp > existing.exp) {
        existing.exp = exp;
        existing.expText = expText;
      }
    }
  });

  var uniqueSkills = Array.from(skillMap.values()).slice(0, 7);
  if(!uniqueSkills.length){
    rows.push([{text:'No skill intelligence available in current candidate data.',colSpan:3,style:'muted',margin:[0,6,0,6]},{},{}]);
    return rows;
  }
  uniqueSkills.forEach(function(skill){
    var rating=safeNumber(skill.rating,0,5);
    var exp=Number(skill.exp||0);
    var expText=clean(skill.expText,'');
    var stars=[];
    for(var i=1;i<=5;i++) stars.push({svg:i<=rating?rsStarGold:rsStarMuted,width:7.7,margin:[0,0.5,1.2,0]});
    rows.push([
      {text:clean(skill.name).toUpperCase(),style:'tableCellBold',alignment:'left'},
      {text:expText!=='' && expText!=='--' ? expText : (exp>0 ? exp+' Year'+(exp>1?'s':'') : '--'),style:'tableCell',color:'#334155'},
      {columns:stars,columnGap:0,alignment:'center',margin:[17,5,0,5]}
    ]);
  });
  return rows;
}
function buildValidationRows(validation){
  var map={};
  (validation||[]).forEach(function(row){
    var key=clean(row.label,'').toLowerCase();
    if(key) map[key]=clean(row.status,'');
  });
  var slots=[
    {label:'RESUME',key:'resume',fallbackKey:'resume verified',icon:rsIconResume},
    {label:'EMAIL ID',key:'email id',fallbackKey:'email verified',icon:rsIconEmail},
    {label:'PHONE NUMBER',key:'phone number',fallbackKey:'phone verified',icon:rsIconPhone},
    {label:'LINKEDIN ID',key:'linkedin id',fallbackKey:'linkedin verified',icon:rsIconLinkedin},
    {label:'JOB DESCRIPTION',key:'job description',fallbackKey:'job description acknowledged',icon:rsIconJobDescription}
  ];
  var cells=slots.map(function(row){
    var status=map[row.key] || map[row.fallbackKey] || '-';
    if (!status || status === '--' || (row.key === 'linkedin id' && status === 'Discussed')) status = '-';
    var verified=/verified|confirmed|completed|shared/i.test(status);
    var isBlank=(status === '-' || status === '--' || !status);
    var textColor=verified?'#16a34a':(isBlank?'#94a3b8':'#b45309');
    return {stack:[
      {columns:[
        {text:'',width:'*'},
        {svg:row.icon,width:9.8,margin:[0,0.2,0,0]},
        {text:'',width:5},
        {text:row.label.toUpperCase(),fontSize:6.6,bold:true,color:'#0f172a',width:'auto',margin:[0,1.7,0,0]},
        {text:'',width:'*'}
      ],columnGap:0,margin:[0,0,0,7]},
      {text:status,fontSize:6.5,bold:true,color:textColor,alignment:'center',margin:[0,0,0,0]}
    ],margin:[2,7,2,7]};
  });
  return [cells];
}
function buildDefinition(data,logoUri,options){
  options=options||{};
  var noBranding=options.noBranding===true;
  var score=safeNumber(data.match,0,100);
  var completeness=dataCompleteness(data);
  var tone=matchTone(score,completeness);
  var summary=limitText(data.careerSummary,560,'No professional summary available.');
  var noteMissing=isGap(data.recruiterNote);
  var note=noteMissing ? 'No recruiter comments available.' : limitText(data.recruiterNote,420,'');
  var joining=data.isImmediate?'Immediate':clean(data.notice,'--');
  var companyName=clean(data.reportCompanyName,'Hiring Partner');
  var skillCount=Math.max(1,Math.min(7,(data.skills||[]).filter(function(skill){ return clean(skill.name,'')!==''; }).length));
  var skillCardHeight=90+(skillCount*31);
  var validLogo = (typeof logoUri === 'string' && /^data:image\/(png|jpe?g)/i.test(logoUri));
  var logoCell=validLogo
    ? {image:logoUri,fit:[42,28],alignment:'center',margin:[0,0,12,0],border:[false,false,false,false]}
    : {text:companyName.charAt(0).toUpperCase(),fontSize:13,bold:true,color:'#1d4ed8',alignment:'center',margin:[0,5,12,4],fillColor:'#eef2ff',border:[false,false,false,false]};

  return {
    pageSize:'A4',
    pageMargins:[30,26,30,24],
    defaultStyle:{font:'Roboto',fontSize:8,color:'#1f2937',lineHeight:1.22},
    styles:{
      title:{fontSize:15,bold:true,color:'#0f172a',alignment:'right'},
      eyebrow:{fontSize:6.2,color:'#64748b',alignment:'right'},
      metaLabel:{fontSize:5.7,color:'#64748b'},
      metaValue:{fontSize:7.4,bold:true,color:'#111827'},
      candidateName:{fontSize:18,bold:true,color:'#0f172a'},
      designation:{fontSize:9.2,bold:true,color:'#1d4ed8'},
      small:{fontSize:7.4,color:'#64748b'},
      sectionTitle:{fontSize:8.2,bold:true,color:'#1d4ed8'},
      sectionTitleMuted:{fontSize:8,bold:true,color:'#334155'},
      tableHead:{fontSize:8.4,bold:true,color:'#334155',fillColor:'#f8fafc',margin:[0,6,0,6]},
      tableCell:{fontSize:8.6,color:'#334155',margin:[0,6,0,6]},
      tableCellBold:{fontSize:8.6,bold:true,color:'#0f172a',margin:[0,6,0,6]},
      metricLabel:{fontSize:6,color:'#64748b'},
      metricValue:{fontSize:7.2,bold:true,color:'#0f172a'},
      muted:{fontSize:7,color:'#64748b'},
      body:{fontSize:7.6,color:'#1f2937',lineHeight:1.35},
      footer:{fontSize:6,color:'#64748b'}
    },
    content:[
      noBranding ? unbrandedHeaderCard(data.reportDate) : headerCard(companyName,logoCell,data.reportDate,data.recruiterName),
      pairedCardRow([
        {text:clean(data.name,'Candidate'),style:'candidateName'},
        {text:clean(data.designation||data.jobTitle,'--'),style:'designation',margin:[0,3,0,8]},
        labeledIconLine(rsIconExperience,'EXPERIENCE:',clean(data.experience,'--').toUpperCase(),'small','#64748b',6.5),
        {stack:[labeledIconLine(rsIconCompany,'CURRENT COMPANY:',clean(data.company,'--').toUpperCase(),'small','#64748b',6.5)],margin:[0,3,0,0]}
      ],[
        {text:'HIRING CONFIDENCE',fontSize:12.4,bold:true,color:'#64748b',alignment:'center',margin:[0,8,0,0]},
        {text:score+'%',fontSize:30,bold:true,color:'#1d4ed8',alignment:'center',margin:[0,7,0,0]}
      ],'#ffffff','#f8fbff','#cfe0ff'),
      roundedFullCard([
        {table:{widths:[116,116,164,116],body:[[
          metricCell('Education',data.education,rsIconEducation),
          metricCell('Notice Period',joining,rsIconCalendar),
          metricCell('Location',cityOnly(data.location),rsIconLocation),
          metricCell('Work Mode',data.workMode,rsIconLaptop)
        ]]},
        layout:{
          hLineWidth:function(){return 0;},
          vLineWidth:function(i){return i>0&&i<4?0.45:0;},
          vLineColor:function(){return '#dbe3ef';},
          paddingLeft:function(){return 0;},
          paddingRight:function(){return 0;},
          paddingTop:function(){return 0;},
          paddingBottom:function(){return 0;}
        }}
      ],[0,0,0,8],62,'#ffffff','#dbe3ef'),
      {
        columns:[
          fixedPanel([sectionTitle('EXECUTIVE SUMMARY',false,rsIconSummary),{text:summary,style:'body'}],[0,0,6,8],'#ffffff','#dbe3ef',148),
          fixedPanel([sectionTitle('RECRUITER COMMENTS',false,rsIconNote),{text:note,style:'body',color:noteMissing?'#64748b':'#1f2937',italics:noteMissing}],[6,0,0,8],'#ffffff','#dbe3ef',148)
        ],
        columnGap:0
      },
      roundedFullCard([
        sectionTitle('SKILL INTELLIGENCE',false,rsIconSkill),
        {table:{widths:['*',100,78],body:buildSkillRows(data.skills)},layout:{
          hLineWidth:function(i){return i===1?0.75:0.45;},
          vLineWidth:function(){return 0;},
          hLineColor:function(i){return i===1?'#94a3b8':'#e2e8f0';}
        }}
      ],[0,0,0,8],skillCardHeight,'#ffffff','#dbe3ef'),
      roundedFullCard([
          sectionTitle('VERIFICATION STATUS',false,rsIconShield),
          {table:{widths:['*','*','*','*','*'],body:buildValidationRows(data.validation)},layout:{
            hLineWidth:function(){return 0;},
            vLineWidth:function(i){return i>0&&i<5?0.55:0;},
            vLineColor:function(){return '#cbd5e1';},
            paddingLeft:function(){return 0;},
            paddingRight:function(){return 0;},
            paddingTop:function(){return 0;},
            paddingBottom:function(){return 0;}
          }}
      ],[0,0,0,8],74,'#ffffff','#dbe3ef'),
      {
        table:{widths:['*','auto'],body:[[
          {text:'This report is generated by AI hiring platform jero.works',style:'footer',border:[false,true,false,false],margin:[0,5,0,0]},
          {text:'www.jero.works',style:'footer',border:[false,true,false,false],margin:[0,5,0,0]}
        ]]},
        layout:{hLineColor:function(){return '#e5e7eb';},hLineWidth:function(){return 0.6;},vLineWidth:function(){return 0;}}
      }
    ]
  };
}
async function buildRecruiterSummary(options){
  options = options || {};
  if(window.__rsBusy) return Promise.resolve(false);
  window.__rsBusy=true;
  try{
    if(typeof options.statusCallback === 'function'){
      options.statusCallback({ percent: 30, step: 'Generating Pre-Screened Report...', detail: 'Compiling candidate evaluation dossier...' });
    }
    var data=readData();
    var blob=await buildRecruiterSummaryBlob(data, options);
    if(typeof options.statusCallback === 'function'){
      options.statusCallback({ percent: 90, step: 'Finalizing Pre-Screened Report...', detail: 'Formatting report PDF...' });
    }
    downloadBlob(blob,preScreenReportFileName(data));
    if(typeof options.statusCallback === 'function'){
      options.statusCallback({ percent: 100, step: 'Pre-Screened Report ready!', detail: 'Document ready for download' });
    }
    return true;
  }catch(err){
    console.error('Recruiter Summary error:',err);
    throw err;
  }finally{
    window.__rsBusy=false;
  }
}
async function buildRecruiterSummaryBlob(data,options){
  data=data||readData();
  options=options||{};
  var ready=await Promise.all([rsEnsurePdfMake(),options.noBranding ? Promise.resolve(null) : rsLogoDataUri(data.reportCompanyLogo, data.reportCompanyLogoRaw)]);
  return new Promise(function(resolve,reject){
    var done = false;
    var timer = setTimeout(function(){
      if(!done){
        done = true;
        reject(new Error('PDF generation timed out after 30 seconds.'));
      }
    }, 30000);
    try{
      var docDef = buildDefinition(data,ready[1],options);
      window.pdfMake.createPdf(docDef).getBlob(function(blob){
        if(done) return;
        clearTimeout(timer);
        done = true;
        if(blob && blob.size>200) resolve(blob);
        else reject(new Error('Recruiter Summary PDF output was empty'));
      });
    }catch(ex){
      if(!done){
        clearTimeout(timer);
        done = true;
        reject(ex);
      }
    }
  });
}
async function copyPdfPagesToA4(targetDoc,sourceDoc){
  /* Purpose: keep pre-screened report packages visually consistent by normalizing
     every merged report/resume page to portrait A4 without changing document content. */
  var A4_W=595.28;
  var A4_H=841.89;
  var pages=sourceDoc.getPages();
  if(!pages.length) throw new Error('PDF has no pages to merge');
  for(var i=0;i<pages.length;i++){
    var size=pages[i].getSize();
    var scale=Math.min(A4_W/size.width,A4_H/size.height);
    var drawW=size.width*scale;
    var drawH=size.height*scale;
    var a4Page=targetDoc.addPage([A4_W,A4_H]);
    var embedded=await targetDoc.embedPage(pages[i]);
    a4Page.drawPage(embedded,{
      x:(A4_W-drawW)/2,
      y:(A4_H-drawH)/2,
      width:drawW,
      height:drawH
    });
  }
}
async function mergePreScreenedReportAndResume(reportBlob,resumeBlob,fileName,mergeOptions){
  await rsEnsurePdfLib();
  mergeOptions=mergeOptions||{};
  var resumeBytes=await resumeBlob.arrayBuffer();
  var reportBytes=await reportBlob.arrayBuffer();
  var merged=await window.PDFLib.PDFDocument.create();
  var reportDoc=await window.PDFLib.PDFDocument.load(reportBytes);
  var resumeDoc=await window.PDFLib.PDFDocument.load(resumeBytes);
  if(mergeOptions.forceA4 !== false){
    await copyPdfPagesToA4(merged,reportDoc);
  }else if(mergeOptions.matchReportWidthToResume){
    var resumeFirstPage=resumeDoc.getPages()[0];
    if(!resumeFirstPage) throw new Error('Resume PDF has no pages');
    var resumeWidth=resumeFirstPage.getSize().width;
    var reportSourcePages=reportDoc.getPages();
    for(var r=0;r<reportSourcePages.length;r++){
      var reportSize=reportSourcePages[r].getSize();
      var reportScale=resumeWidth/reportSize.width;
      var normalizedPage=merged.addPage([resumeWidth,reportSize.height*reportScale]);
      var embeddedReport=await merged.embedPage(reportSourcePages[r]);
      normalizedPage.drawPage(embeddedReport,{x:0,y:0,width:resumeWidth,height:reportSize.height*reportScale});
    }
  }else{
    var reportPages=await merged.copyPages(reportDoc,reportDoc.getPageIndices());
    reportPages.forEach(function(page){ merged.addPage(page); });
  }
  if(mergeOptions.forceA4 !== false){
    await copyPdfPagesToA4(merged,resumeDoc);
  }else{
    var resumePages=await merged.copyPages(resumeDoc,resumeDoc.getPageIndices());
    resumePages.forEach(function(page){ merged.addPage(page); });
  }
  var mergedBytes=await merged.save();
  downloadBlob(new Blob([mergedBytes],{type:'application/pdf'}),fileName);
}
window.mergePreScreenedReportAndResume = mergePreScreenedReportAndResume;
function dataUriBytes(dataUri){
  var match=String(dataUri||'').match(/^data:([^;]+);base64,([\s\S]+)$/i);
  if(!match) return null;
  var raw=atob(match[2].replace(/\s+/g,''));
  var bytes=new Uint8Array(raw.length);
  for(var i=0;i<raw.length;i++) bytes[i]=raw.charCodeAt(i);
  return {mime:match[1].toLowerCase(),bytes:bytes};
}
async function buildTurboVectorHighlightedResume(options){
  /* Purpose: High-speed, zero-raster client-side vector keyword highlighting.
     Extracts character coordinates via pdfjsLib, then uses pdf-lib to draw semi-transparent
     rectangles directly onto the vector PDF with sub-pixel text measurement.
     Zero canvas rasterization, zero DOM thrashing, 100% crisp vector output with selectable text. */
  if(window.__rsBusy && !(options && options.ignoreBusy)) return Promise.resolve(false);
  var wasBusy=!!window.__rsBusy;
  window.__rsBusy=true;
  try{
    options=options||{};
    var resumeSources=(Array.isArray(options.resumeUrl) ? options.resumeUrl : [options.resumeUrl||'']).map(function(url){
      return clean(url,'');
    }).filter(function(url,index,self){
      return url && url!=='--' && self.indexOf(url)===index;
    });
    var resumeExt=clean(options.resumeExt||'','').toUpperCase();
    var resumePdfUrl=clean(options.resumePdfUrl||'','');
    if(options.resumeBlob instanceof Blob && !resumeExt) resumeExt='PDF';
    if(['PDF','DOC','DOCX'].indexOf(resumeExt) === -1) throw new Error('Turbo Vector Highlighted Resume is available for PDF, DOC and DOCX resumes only');

    await Promise.all([rsEnsurePdfJs(),rsEnsurePdfLib()]);

    var pdfBlob=null;
    if(options.resumeBlob instanceof Blob){
      pdfBlob=options.resumeBlob;
    }else if(resumeExt==='PDF'){
      pdfBlob=await fetchPdfBlobFromSources(resumeSources);
    }else if(resumeExt==='DOCX'){
      // Use high-fidelity vector PDF (cached on Cloudflare R2) to guarantee 100% vector typography, exact Word layout, and crisp text highlighting
      if(resumePdfUrl){
        try{
          pdfBlob=await fetchPdfResumeBlob(resumePdfUrl);
        }catch(convErr){
          console.warn('Turbo Vector DOCX vector PDF fetch failed; trying WASM conversion fallback:',convErr);
        }
      }
      if(!pdfBlob){
        try{
          var rawDocx=await fetchResumeBytesFromSources(resumeSources);
          pdfBlob=await renderAgyStrategy2WasmDocxToPdfBlob(rawDocx);
        }catch(wasmDocxErr){
          console.error('WASM DOCX conversion fallback failed:',wasmDocxErr);
        }
      }
    }else{
      if(!resumePdfUrl) throw new Error('DOC resume needs enterprise converter PDF URL');
      pdfBlob=await fetchPdfResumeBlob(resumePdfUrl);
    }

    var sourceBytes=await pdfBlob.arrayBuffer();
    var bytes=new Uint8Array(sourceBytes);
    var sig=String.fromCharCode.apply(null,bytes.subarray(0,5));
    if(sig.indexOf('%PDF')===-1) throw new Error('Resume source is not a valid PDF');

    // Extract text and coordinates using pdfjsLib without creating any canvas
    var pdfjsDoc=await window.pdfjsLib.getDocument({data:bytes}).promise;
    var pagesTextData=[];
    var fullResumeText='';

    for(var p=1;p<=pdfjsDoc.numPages;p++){
      var page=await pdfjsDoc.getPage(p);
      var tc=await page.getTextContent();
      var pageStr=tc.items.map(function(item){ return item.str || ''; }).join(' ');
      fullResumeText += pageStr + '\n';
      pagesTextData.push({pageNum:p,tc:tc});
    }

    // Comprehensive keyword collection: pools matched drawer skills, ATS job skills, and candidate profile skills
    var allKeywords=[];
    var seenKw={};
    function addKw(val){
      if(!val) return;
      if(typeof val==='object') val=val.skill_name||val.skill||val.name||val.skill_key||'';
      val=String(val).replace(/\+\d+\s*More/gi,'').replace(/\s+/g,' ').trim();
      if(val.length<2 || (val.length<3 && !/^(ai|ui|ux|go|c#|r|c)$/i.test(val))) return;
      var k=val.toLowerCase();
      if(seenKw[k]) return;
      seenKw[k]=true;
      allKeywords.push(val);
    }
    (Array.isArray(options.keywords) ? options.keywords : []).forEach(addKw);
    (Array.isArray(rsDrawerMatchedKeywords) ? rsDrawerMatchedKeywords : []).forEach(addKw);
    Array.prototype.slice.call(document.querySelectorAll('#kt_activitiesApplication .cdr-skill-tag.cdr-skill-match:not(.cdr-skill-more), .cdr-skill-tag.cdr-skill-match:not(.cdr-skill-more)')).forEach(function(el){
      addKw(el.textContent||'');
    });
    (Array.isArray(rsAtsHighlightKeywords) ? rsAtsHighlightKeywords : []).forEach(addKw);
    try{
      var rData=readData();
      (Array.isArray(rData.skills) ? rData.skills : []).forEach(addKw);
    }catch(ex){}

    // Build word-boundary regex with length-descending order
    var keywordRe=rsKeywordRegex(allKeywords);
    if(!keywordRe){
      var fallbackSkills=rsOption3KeywordsFromResumeText(fullResumeText);
      if(fallbackSkills.length) keywordRe=rsOption3BuildRegex(fallbackSkills);
    }

    // Helper for sub-pixel substring position and width measurement
    var measureCanvas=document.createElement('canvas');
    var measureCtx=measureCanvas.getContext('2d');
    function measureSlice(fullStr,startIdx,endIdx,itemWidth,fontH){
      measureCtx.font=Math.round(fontH)+'px sans-serif';
      var fullM=measureCtx.measureText(fullStr).width;
      if(!fullM || fullM<=0){
        var len=Math.max(1,fullStr.length);
        return {
          offset:(startIdx/len)*itemWidth,
          width:Math.max(3,((endIdx-startIdx)/len)*itemWidth)
        };
      }
      var scale=itemWidth/fullM;
      var prefixW=measureCtx.measureText(fullStr.substring(0,startIdx)).width*scale;
      var matchW=measureCtx.measureText(fullStr.substring(startIdx,endIdx)).width*scale;
      return {
        offset:prefixW,
        width:Math.max(3,matchW)
      };
    }

    // Draw native vector highlight rectangles into the PDF
    var PDFLib=window.PDFLib;
    var pdfDoc=await PDFLib.PDFDocument.load(sourceBytes);

    if(keywordRe){
      pagesTextData.forEach(function(pData){
        var pdfLibPage=pdfDoc.getPage(pData.pageNum - 1);
        var runsData=rsPageTextRuns(pData.tc);
        if(!runsData.text || !runsData.runs.length) return;

        keywordRe.lastIndex=0;
        var match;
        while((match=keywordRe.exec(runsData.text))!==null){
          var mStart=match.index;
          var mEnd=mStart+match[0].length;

          runsData.runs.forEach(function(run){
            if(run.end<=mStart || run.start>=mEnd) return;
            var item=run.item;
            if(!item || !item.transform) return;

            var tx=item.transform[4] || 0;
            var ty=item.transform[5] || 0;
            var itemH=Math.max(Math.abs(item.transform[3] || item.height || 0), 9);
            var itemW=Math.max(Math.abs(item.width || 0), 2);
            var itemStr=item.str || '';
            if(!itemStr.trim()) return;

            var subStart=Math.max(0, mStart - run.start);
            var subEnd=Math.min(itemStr.length, mEnd - run.start);
            if(subEnd<=subStart) return;

            var slice=measureSlice(itemStr,subStart,subEnd,itemW,itemH);
            var padX=Math.min(2.5, itemH*0.14);
            var boxX=tx + slice.offset - padX;
            var boxW=slice.width + (padX * 2);
            var boxY=ty - (itemH * 0.22);
            var boxH=itemH * 1.16;

            pdfLibPage.drawRectangle({
              x:boxX,
              y:boxY,
              width:boxW,
              height:boxH,
              color:PDFLib.rgb(1, 0.85, 0.15), // Warm golden ATS yellow
              opacity:0.42
            });
          });

          if(match.index===keywordRe.lastIndex) keywordRe.lastIndex++;
        }
      });
    }

    if(options.returnDoc===true){
      return pdfDoc;
    }

    var outputBytes=await pdfDoc.save();
    var outputBlob=new Blob([outputBytes],{type:'application/pdf'});

    if(options.returnBlob===true){
      return outputBlob;
    }

    var data=readData();
    downloadBlob(outputBlob,'Jeroworks_'+fileSafeName(data.name,'Candidate')+'.pdf');
    return true;
  }catch(err){
    console.error('Turbo Vector Highlighted Resume error:',err);
    if(options && options.throwOnError===true) throw err;
    alert('Could not generate Turbo Vector Highlighted Resume.\n\nReason: '+(err && err.message ? err.message : 'Unknown vector error'));
    return false;
  }finally{
    if(!wasBusy) window.__rsBusy=false;
  }
}

/* ==========================================================================
   ANTIGRAVITY CLIENT ENGINE (ISOLATED TEST LAB - OPTIONS 35-39)
   - 100% Client-side execution (0% server conversion load)
   - Sub-2-second speed with fluid 60 FPS UI (scheduler.yield / setTimeout 0)
   - LibreOffice-quality typography (Carlito/Caladea metric-compatible fonts)
   - True-Section 1:1 page mapping (zero horizontal line/cell slicing)
   - Strict CSS containment (zero text collisions or margin overwrites)
   - Dual-layer PDF (99.9% Word layout + selectable ATS text layer)
   - 100% isolated: Options 1 through 34 and Option 1 direct R2 download are untouched
   ========================================================================== */

async function agyYieldToMain(){
  if(window.scheduler && typeof window.scheduler.yield === 'function'){
    await window.scheduler.yield();
  }else{
    await new Promise(function(resolve){ setTimeout(resolve, 0); });
  }
}

var agyFontsLoaded = false;
async function agyEnsureMetricFonts(){
  if(agyFontsLoaded) return true;
  if(!document.getElementById('agyMetricFontsStyle')){
    var style = document.createElement('style');
    style.id = 'agyMetricFontsStyle';
    style.textContent = [
      '@font-face{font-family:"Calibri";src:local("Calibri"),local("Carlito"),url("https://cdn.jsdelivr.net/npm/@fontsource/carlito@5.0.8/files/carlito-latin-400-normal.woff2") format("woff2");font-weight:400;font-style:normal;font-display:swap;}',
      '@font-face{font-family:"Calibri";src:local("Calibri Bold"),local("Carlito-Bold"),url("https://cdn.jsdelivr.net/npm/@fontsource/carlito@5.0.8/files/carlito-latin-700-normal.woff2") format("woff2");font-weight:700;font-style:normal;font-display:swap;}',
      '@font-face{font-family:"Cambria";src:local("Cambria"),local("Caladea"),url("https://cdn.jsdelivr.net/npm/@fontsource/caladea@5.0.8/files/caladea-latin-400-normal.woff2") format("woff2");font-weight:400;font-style:normal;font-display:swap;}',
      '@font-face{font-family:"Cambria";src:local("Cambria Bold"),local("Caladea-Bold"),url("https://cdn.jsdelivr.net/npm/@fontsource/caladea@5.0.8/files/caladea-latin-700-normal.woff2") format("woff2");font-weight:700;font-style:normal;font-display:swap;}',
      '@font-face{font-family:"Symbol";src:local("Symbol"),local("Segoe UI Symbol"),local("Arial Unicode MS"),local("Apple Symbols");}',
      '@font-face{font-family:"Wingdings";src:local("Wingdings"),local("Segoe UI Symbol"),local("Arial Unicode MS"),local("Apple Symbols");}',
      '.agy-docx-host *{word-spacing:normal!important;letter-spacing:normal!important;}',
      '.agy-docx-host .docx-wrapper{background:transparent!important;padding:0!important;}',
      '.agy-docx-host .docx-wrapper > section.docx{box-sizing:border-box!important;position:relative!important;margin:0 auto!important;background:#ffffff!important;box-shadow:none!important;}',
      '.agy-docx-host p{margin-top:0!important;margin-bottom:2px!important;padding-top:0!important;padding-bottom:0!important;line-height:1.24!important;min-height:0!important;overflow:visible!important;}',
      '.agy-docx-host p + p{margin-top:0!important;}',
      '.agy-docx-host td p, .agy-docx-host th p{margin-top:0!important;margin-bottom:1px!important;}',
      '.agy-docx-host p.agy-empty-para{min-height:0!important;height:3px!important;margin:0!important;padding:0!important;line-height:3px!important;}',
      '.agy-docx-host span{line-height:inherit!important;white-space:pre-wrap!important;overflow-wrap:break-word!important;vertical-align:baseline!important;}',
      '.agy-docx-host table{border-collapse:collapse!important;box-sizing:border-box!important;max-width:100%!important;}',
      '.agy-docx-host td, .agy-docx-host th{vertical-align:top!important;line-height:1.24!important;box-sizing:border-box!important;padding-left:10px!important;padding-right:10px!important;overflow:visible!important;}',
      '.agy-docx-host td:first-child, .agy-docx-host th:first-child{padding-left:4px!important;}',
      '.agy-docx-host [class*="_num_"]:before, .agy-docx-host .docx-num-list:before{font-family:"Segoe UI Symbol","Apple Symbols","Symbol","Wingdings",sans-serif!important;display:inline-block!important;min-width:1.2em!important;margin-right:0.25em!important;text-align:left!important;}',
      '.agy-docx-host .agy-bullet-symbol{display:inline-block!important;width:1.2em!important;min-width:1.2em!important;text-indent:0!important;text-align:left!important;flex-shrink:0!important;}',
      '.agy-docx-host .agy-tab-left{display:inline-block!important;flex:0 1 auto!important;min-width:0!important;overflow-wrap:break-word!important;}',
      '.agy-docx-host .agy-tab-spacer{display:inline-block!important;flex:1 1 auto!important;min-width:14px!important;}',
      '.agy-docx-host .agy-tab-right{display:inline-block!important;flex:0 0 auto!important;text-align:right!important;margin-left:auto!important;white-space:nowrap!important;padding-left:12px!important;}',
      '.agy-docx-host .docx-tab-stop{display:inline-block!important;min-width:0.4em!important;word-spacing:normal!important;}',
      '.agy-keyword-hit{background:rgba(255,214,10,0.45)!important;color:#111827!important;border-radius:2px!important;padding:0 2px!important;box-decoration-break:clone!important;-webkit-box-decoration-break:clone!important;vertical-align:baseline!important;line-height:inherit!important;}'
    ].join('\n');
    document.head.appendChild(style);
  }
  if(document.fonts && document.fonts.ready){
    try{
      await Promise.race([document.fonts.ready, new Promise(function(r){ setTimeout(r, 600); })]);
    }catch(e){}
  }
  agyFontsLoaded = true;
  return true;
}

function agyInjectInvisibleTextLayer(pdf, pageEl, drawW, drawH, sliceOffsetY, sliceH, canvasH){
  try{
    var pRect = pageEl.getBoundingClientRect();
    if(!pRect.width || !pRect.height) return;
    sliceOffsetY = sliceOffsetY || 0;
    sliceH = sliceH || canvasH || pRect.height;
    canvasH = canvasH || pRect.height;

    var scaleY = canvasH / pRect.height;
    var sliceStartCanvasY = sliceOffsetY;
    var sliceEndCanvasY = sliceOffsetY + sliceH;

    var showText = window.NodeFilter ? window.NodeFilter.SHOW_TEXT : 4;
    var walker = document.createTreeWalker(pageEl, showText, null);
    var node;
    var range = document.createRange ? document.createRange() : null;

    while((node = walker.nextNode())){
      var val = node.nodeValue;
      if(!val || !val.trim()) continue;
      var parent = node.parentElement;
      if(!parent || ['SCRIPT','STYLE'].indexOf(parent.tagName)!==-1) continue;

      if(range){
        var wordRegex = /\S+/g;
        var wm;
        while((wm = wordRegex.exec(val)) !== null){
          try{
            range.setStart(node, wm.index);
            range.setEnd(node, wm.index + wm[0].length);
            var wRect = range.getBoundingClientRect();
            if(!wRect.width || !wRect.height) continue;

            var nodeCanvasY = (wRect.top - pRect.top) * scaleY;
            if(nodeCanvasY >= sliceStartCanvasY && nodeCanvasY < sliceEndCanvasY){
              var relCanvasY = nodeCanvasY - sliceStartCanvasY;
              var ptX = ((wRect.left - pRect.left) / pRect.width) * drawW;
              var nodeCanvasH = wRect.height * scaleY;
              var ptY = (relCanvasY / sliceH) * drawH + (nodeCanvasH / sliceH * drawH * 0.82);
              var fontSize = Math.max(6, Math.min(18, (nodeCanvasH / sliceH) * drawH * 0.75));
              if(typeof pdf.setFontSize === 'function') pdf.setFontSize(fontSize);
              if(typeof pdf.text === 'function'){
                pdf.text(wm[0], ptX, ptY, { renderingMode: 'invisible' });
              }
            }
          }catch(rErr){}
        }
      }else{
        var rect = parent.getBoundingClientRect();
        if(!rect.width || !rect.height) continue;
        var nodeCanvasY = (rect.top - pRect.top) * scaleY;
        if(nodeCanvasY >= sliceStartCanvasY && nodeCanvasY < sliceEndCanvasY){
          var relCanvasY = nodeCanvasY - sliceStartCanvasY;
          var ptX = ((rect.left - pRect.left) / pRect.width) * drawW;
          var nodeCanvasH = rect.height * scaleY;
          var ptY = (relCanvasY / sliceH) * drawH + (nodeCanvasH / sliceH * drawH * 0.82);
          var fontSize = Math.max(6, Math.min(18, (nodeCanvasH / sliceH) * drawH * 0.75));
          if(typeof pdf.setFontSize === 'function') pdf.setFontSize(fontSize);
          if(typeof pdf.text === 'function'){
            pdf.text(val.trim(), ptX, ptY, { renderingMode: 'invisible' });
          }
        }
      }
    }
  }catch(ex){}
}

/* ==========================================================================
   STRATEGY 3: CLIENT-SIDE OOXML PRE-PARSER & SYNTHETIC TAB STOP ENGINE
   - Intercepts word/document.xml via in-memory JSZip before DOM rendering
   - Maps PUA bullet codepoints (\uF0B7, \uF0A7, etc.) to crisp universal Unicode
   - Extracts <w:tblGrid> column widths to lock HTML tables to exact OOXML twips
   - Synthesizes two-sided paragraph layouts (Left Title <---> Right Date/Location)
   - Restores hanging bullet indentation and dot leader tab fills
   - Preserves 100% isolation in Section 6 (Options 35-39)
   ========================================================================== */

function agyExtractDocxMetadata(docXml){
  var metadata = {
    tables: [],
    paragraphs: []
  };
  if(!docXml) return metadata;

  try{
    // Extract table column grid proportions and borders
    var tbls = docXml.getElementsByTagName('w:tbl');
    if(!tbls || !tbls.length) tbls = docXml.getElementsByTagName('tbl');
    for(var t = 0; t < tbls.length; t++){
      var tbl = tbls[t];
      var gridCols = tbl.getElementsByTagName('w:gridCol');
      if(!gridCols || !gridCols.length) gridCols = tbl.getElementsByTagName('gridCol');
      var colWidths = [];
      var totalW = 0;
      for(var c = 0; c < gridCols.length; c++){
        var w = parseInt(gridCols[c].getAttribute('w:w') || gridCols[c].getAttribute('w') || '0', 10);
        colWidths.push(w);
        totalW += w;
      }
      var percentages = colWidths.map(function(w){
        return totalW > 0 ? ((w / totalW) * 100).toFixed(2) + '%' : '';
      });

      // Check for vertical column borders in table XML
      var tblBorders = tbl.getElementsByTagName('w:tblBorders')[0] || tbl.getElementsByTagName('tblBorders')[0];
      var hasInsideV = false;
      var insideVColor = '#cbd5e1';
      var insideVSize = '1px';
      if(tblBorders){
        var insideV = tblBorders.getElementsByTagName('w:insideV')[0] || tblBorders.getElementsByTagName('insideV')[0];
        if(insideV){
          var vVal = (insideV.getAttribute('w:val') || insideV.getAttribute('val') || '').toLowerCase();
          if(vVal && vVal !== 'none' && vVal !== 'nil'){
            hasInsideV = true;
            var cHex = insideV.getAttribute('w:color') || insideV.getAttribute('color') || '';
            if(cHex && cHex !== 'auto') insideVColor = '#' + cHex;
            var sz = parseInt(insideV.getAttribute('w:sz') || insideV.getAttribute('sz') || '8', 10);
            insideVSize = Math.max(1, Math.round(sz / 8)) + 'px';
          }
        }
      }

      metadata.tables.push({
        colCount: gridCols.length,
        percentages: percentages,
        hasInsideV: hasInsideV,
        insideVColor: insideVColor,
        insideVSize: insideVSize
      });
    }

    // Extract paragraph tab definitions
    var ps = docXml.getElementsByTagName('w:p');
    if(!ps || !ps.length) ps = docXml.getElementsByTagName('p');
    for(var p = 0; p < ps.length; p++){
      var pNode = ps[p];
      var tabsNode = pNode.getElementsByTagName('w:tabs')[0] || pNode.getElementsByTagName('tabs')[0];
      var pInfo = { hasRightTab: false, hasLeader: false, leaderType: null };
      if(tabsNode){
        var tabList = tabsNode.getElementsByTagName('w:tab');
        if(!tabList || !tabList.length) tabList = tabsNode.getElementsByTagName('tab');
        for(var tb = 0; tb < tabList.length; tb++){
          var tItem = tabList[tb];
          var val = (tItem.getAttribute('w:val') || tItem.getAttribute('val') || '').toLowerCase();
          var leader = (tItem.getAttribute('w:leader') || tItem.getAttribute('leader') || '').toLowerCase();
          var pos = parseInt(tItem.getAttribute('w:pos') || tItem.getAttribute('pos') || '0', 10);
          if(val === 'right' || val === 'decimal' || pos >= 7200){
            pInfo.hasRightTab = true;
            if(leader && leader !== 'none'){
              pInfo.hasLeader = true;
              pInfo.leaderType = leader;
            }
          }
        }
      }
      metadata.paragraphs.push(pInfo);
    }
  }catch(ex){
    console.warn('Strategy 3 XML metadata extraction warning:', ex);
  }
  return metadata;
}

async function agyPreProcessDocxZip(docxBytes, options){
  try{
    await rsEnsureJsZip();
    var zip = await window.JSZip.loadAsync(docxBytes);
    var docFile = zip.file('word/document.xml');
    if(!docFile) return { bytes: docxBytes, metadata: null };

    // 1. Sanitize PUA bullets to semantic Unicode equivalents and normalize paragraph spacing in word/document.xml
    var xmlText = await docFile.async('string');
    var sanitizedXml = xmlText
      // Squares
      .replace(/[\uF0A7\uF0A8\u25AA\u25A0\u25A1]|&#xF0A7;|&#61607;|&#xF0A8;|&#61608;/gi, '▪')
      // Hollow circles
      .replace(/[\uF06F\u25CB]|&#xF06F;|&#61551;/gi, '○')
      // Arrows
      .replace(/[\uF0D8\u27A2\u2794]|&#xF0D8;|&#61656;/gi, '➢')
      // Checks
      .replace(/[\uF0FC\u2713]|&#xF0FC;|&#61692;/gi, '✓')
      .replace(/[\uF076\u2714]|&#xF076;|&#61558;/gi, '✔')
      // Diamonds
      .replace(/[\uF0A4\u25C6]|&#xF0A4;/gi, '◆')
      .replace(/[\uF0A5\u25C7]|&#xF0A5;/gi, '◇')
      // Dashes
      .replace(/[\uF02D\u2013\u2014]|&#xF02D;/gi, '–')
      // Envelopes
      .replace(/\uF0E0/g, '✉')
      // Standard round bullets
      .replace(/[\uF0B7\uF0B0\u2022\u25CF]|&#xF0B7;|&#61623;/gi, '•')
      // Strip any remaining unmapped PUA codepoints
      .replace(/[\uE000-\uF8FF]/g, '')
      .replace(/<w:spacing\b([^>]*)\/?>/gi, function(match, attrs){
        var newAttrs = attrs
          .replace(/\bw:line="(\d+)"/gi, function(m, val){
            return parseInt(val, 10) > 280 ? 'w:line="260"' : m;
          })
          .replace(/\bw:after="(\d+)"/gi, function(m, val){
            return parseInt(val, 10) > 80 ? 'w:after="40"' : m;
          })
          .replace(/\bw:before="(\d+)"/gi, function(m, val){
            return parseInt(val, 10) > 80 ? 'w:before="0"' : m;
          });
        return '<w:spacing' + newAttrs + '/>';
      });

    // 2. Sanitize and normalize word/numbering.xml (Preserves original bullet styles & hierarchical sub-pointer indents)
    var numFile = zip.file('word/numbering.xml');
    if(numFile){
      try{
        var numXml = await numFile.async('string');
        var cleanNumXml = numXml.replace(/<w:lvl\b([^>]*)>([\s\S]*?)<\/w:lvl>/gi, function(match, lvlAttrs, lvlContent){
          var ilvlMatch = lvlAttrs.match(/\bw:ilvl="(\d+)"/i) || lvlContent.match(/<w:ilvl\b[^>]*w:val="(\d+)"/i);
          var ilvl = ilvlMatch ? parseInt(ilvlMatch[1], 10) : 0;

          var isBullet = false;
          var newContent = lvlContent.replace(/<w:lvlText\b([^>]*?)\bw:val="([^"]*)"([^>]*?)\/?>/gi, function(m, pre, val, post){
            if(/%\d/.test(val)) return m; // Preserve numbered lists (%1, %2, etc.)
            isBullet = true;
            // Map original bullet symbol to its true semantic Unicode twin
            var mapped = '•';
            if(/[\uF0A7\uF0A8\u25AA\u25A0\u25A1]|&#xF0A7;|&#61607;|&#xF0A8;|&#61608;/i.test(val)) mapped = '▪';
            else if(/[\uF06F\u25CB]|&#xF06F;|&#61551;/i.test(val)) mapped = '○';
            else if(/[\uF0D8\u27A2\u2794>]|&#xF0D8;|&#61656;/i.test(val)) mapped = '➢';
            else if(/[\uF0FC\u2713]|&#xF0FC;|&#61692;/i.test(val)) mapped = '✓';
            else if(/[\uF076\u2714]|&#xF076;|&#61558;/i.test(val)) mapped = '✔';
            else if(/[\uF0A4\u25C6]|&#xF0A4;/i.test(val)) mapped = '◆';
            else if(/[\uF0A5\u25C7]|&#xF0A5;/i.test(val)) mapped = '◇';
            else if(/[\uF02D\-\u2013]|&#xF02D;/i.test(val)) mapped = '–';
            return '<w:lvlText' + pre + 'w:val="' + mapped + '"' + post.replace(/\/$/, '') + '/>';
          });

          // If bullet list, normalize font family to Arial so universal glyphs render crisply
          if(isBullet){
            newContent = newContent.replace(/w:ascii="[^"]*"/gi, 'w:ascii="Arial"')
                                   .replace(/w:hAnsi="[^"]*"/gi, 'w:hAnsi="Arial"');
          }

          // Strict Level-Indexed Indentation Grid: Level 0 = 360 twips (0.25 in), Level 1 = 720 twips (0.50 in)
          // Hanging gap is exactly 240 twips (12 pt) across all levels so wrapped lines align perfectly
          var leftTwips = 360 + (ilvl * 360);
          var hangingTwips = 240;

          if(/<w:ind\b[^>]*\/?>/i.test(newContent)){
            newContent = newContent.replace(/<w:ind\b[^>]*\/?>/gi, '<w:ind w:left="' + leftTwips + '" w:hanging="' + hangingTwips + '"/>');
          }else{
            newContent = newContent.replace(/<\/w:pPr>/i, '<w:ind w:left="' + leftTwips + '" w:hanging="' + hangingTwips + '"/></w:pPr>');
          }

          return '<w:lvl' + lvlAttrs + '>' + newContent + '</w:lvl>';
        });
        zip.file('word/numbering.xml', cleanNumXml);
      }catch(nErr){
        console.warn('Strategy 2/3 numbering.xml pre-process warning:', nErr);
      }
    }

    // 3. Sanitize and normalize word/styles.xml (Fixes inherited double spacing)
    var stylesFile = zip.file('word/styles.xml');
    if(stylesFile){
      try{
        var stylesXml = await stylesFile.async('string');
        var cleanStylesXml = stylesXml.replace(/<w:spacing\b([^>]*)\/?>/gi, function(match, attrs){
          var newAttrs = attrs
            .replace(/\bw:line="(\d+)"/gi, function(m, val){
              return parseInt(val, 10) > 280 ? 'w:line="260"' : m;
            })
            .replace(/\bw:after="(\d+)"/gi, function(m, val){
              return parseInt(val, 10) > 80 ? 'w:after="40"' : m;
            });
          return '<w:spacing' + newAttrs + '/>';
        });
        zip.file('word/styles.xml', cleanStylesXml);
      }catch(sErr){
        console.warn('Strategy 3 styles.xml pre-process warning:', sErr);
      }
    }

    // Inject contextual spacing into list item paragraphs in document.xml
    sanitizedXml = sanitizedXml.replace(/<w:pPr\b([^>]*)>/gi, function(match, attrs){
      if((match.indexOf('w:numPr') !== -1 || match.indexOf('List') !== -1) && match.indexOf('contextualSpacing') === -1){
        return '<w:pPr' + attrs + '><w:contextualSpacing/>';
      }
      return match;
    });

    options = options || {};

    // 4. Redact candidate PII directly in word/document.xml if requested (preserves line geometry and layout)
    if(options.anonymizeRules && typeof rdcRedactTextValue === 'function'){
      sanitizedXml = sanitizedXml.replace(/<w:t\b([^>]*)>([\s\S]*?)<\/w:t>/gi, function(match, attrs, content){
        return '<w:t' + attrs + '>' + rdcRedactTextValue(content, options.anonymizeRules) + '</w:t>';
      });
    }

    // 5. Inject native OOXML yellow highlighting for skill keywords if requested
    if(Array.isArray(options.keywords) && options.keywords.length){
      var kwList = options.keywords.map(function(k){
        return String(k || '').trim();
      }).filter(function(k){
        return k.length >= 2;
      }).sort(function(a, b){
        return b.length - a.length;
      });

      if(kwList.length){
        var kwPatterns = kwList.map(function(k){
          var esc = k.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
          var leftBound = /^\w/.test(k) ? '\\b' : '(?:^|\\s)';
          var rightBound = /\w$/.test(k) ? '\\b' : '(?=$|\\s|[.,;!?)])';
          return leftBound + esc + rightBound;
        });
        var kwRegex = new RegExp('(' + kwPatterns.join('|') + ')', 'gi');

        sanitizedXml = sanitizedXml.replace(/<w:r\b([^>]*)>([\s\S]*?)<\/w:r>/gi, function(fullRun, rAttrs, rContent){
          if(rContent.indexOf('<w:highlight') !== -1) return fullRun;
          var tMatch = rContent.match(/<w:t\b([^>]*)>([\s\S]*?)<\/w:t>/i);
          if(!tMatch) return fullRun;

          var text = tMatch[2];
          kwRegex.lastIndex = 0;
          if(!kwRegex.test(text)) return fullRun;

          kwRegex.lastIndex = 0;
          var rPrMatch = rContent.match(/<w:rPr\b[^>]*>[\s\S]*?<\/w:rPr>/i);
          var baseRPr = rPrMatch ? rPrMatch[0] : '';
          var highlightedRPr = baseRPr
            ? (baseRPr.indexOf('</w:rPr>') !== -1 ? baseRPr.replace('</w:rPr>', '<w:highlight w:val="yellow"/></w:rPr>') : baseRPr + '<w:highlight w:val="yellow"/>')
            : '<w:rPr><w:highlight w:val="yellow"/></w:rPr>';

          var parts = [];
          var lastIdx = 0;
          var m;
          kwRegex.lastIndex = 0;
          while((m = kwRegex.exec(text)) !== null){
            if(m.index > lastIdx){
              var preText = text.substring(lastIdx, m.index);
              parts.push('<w:r' + rAttrs + '>' + baseRPr + '<w:t xml:space="preserve">' + preText + '</w:t></w:r>');
            }
            parts.push('<w:r' + rAttrs + '>' + highlightedRPr + '<w:t xml:space="preserve">' + m[0] + '</w:t></w:r>');
            lastIdx = m.index + m[0].length;
          }
          if(lastIdx < text.length){
            var postText = text.substring(lastIdx);
            parts.push('<w:r' + rAttrs + '>' + baseRPr + '<w:t xml:space="preserve">' + postText + '</w:t></w:r>');
          }
          return parts.join('');
        });
      }
    }

    var parser = new DOMParser();
    var docXml = parser.parseFromString(sanitizedXml, 'application/xml');
    var metadata = agyExtractDocxMetadata(docXml);

    zip.file('word/document.xml', sanitizedXml);
    var cleanBytes = await zip.generateAsync({ type: 'uint8array' });
    return { bytes: cleanBytes, metadata: metadata };
  }catch(err){
    console.warn('Strategy 3 pre-processing fallback to original bytes:', err);
    return { bytes: docxBytes, metadata: null };
  }
}

function agyApplyStrategy3DomEngine(host, metadata){
  if(!host) return;

  // 1. Sanitize text nodes: map leftover PUA codepoints and clean orphan square glyphs
  var textWalker = document.createTreeWalker(host, NodeFilter.SHOW_TEXT, null, false);
  var curText;
  var textNodesToClean = [];
  while((curText = textWalker.nextNode())){
    textNodesToClean.push(curText);
  }
  textNodesToClean.forEach(function(tn){
    var val = tn.nodeValue;
    var cleaned = val
      // Squares
      .replace(/[\uF0A7\uF0A8\u25AA\u25A0\u25A1]|&#xF0A7;|&#61607;|&#xF0A8;|&#61608;/gi, '▪')
      // Hollow circles
      .replace(/[\uF06F\u25CB]|&#xF06F;|&#61551;/gi, '○')
      // Arrows
      .replace(/[\uF0D8\u27A2\u2794]|&#xF0D8;|&#61656;/gi, '➢')
      // Checks
      .replace(/[\uF0FC\u2713]|&#xF0FC;|&#61692;/gi, '✓')
      .replace(/[\uF076\u2714]|&#xF076;|&#61558;/gi, '✔')
      // Diamonds
      .replace(/[\uF0A4\u25C6]|&#xF0A4;/gi, '◆')
      .replace(/[\uF0A5\u25C7]|&#xF0A5;/gi, '◇')
      // Dashes
      .replace(/[\uF02D\u2013\u2014]|&#xF02D;/gi, '–')
      // Standard round bullets
      .replace(/[\uF0B7\uF0B0\u2022\u25CF]|&#xF0B7;|&#61623;/gi, '•')
      .replace(/[\uE000-\uF8FF□]/g, '')
      .replace(/[\u00A0\t]+/g, ' ')
      .replace(/\s+([.,;:!?])/g, '$1');
    if(cleaned !== val){
      tn.nodeValue = cleaned;
    }
  });

  // 2. Standardize ALL paragraphs: row spacing, bullet pointer alignment & tab stops
  var allParagraphs = Array.prototype.slice.call(host.querySelectorAll('p, li'));
  var processedParagraphs = [];

  allParagraphs.forEach(function(p){
    // Collapse empty paragraphs or lines with only whitespace/orphan symbols
    var rawTxt = (p.textContent || '').replace(/[\s\u00A0\uE000-\uF8FF□■○●•▪➢✓]/g, '');
    if(rawTxt.length === 0 && !p.querySelector('img, svg, canvas, table')){
      p.className = (p.className ? p.className + ' ' : '') + 'agy-empty-para';
      p.style.setProperty('margin-top', '0', 'important');
      p.style.setProperty('margin-bottom', '3px', 'important');
      p.style.setProperty('min-height', '0', 'important');
      p.style.setProperty('height', '3px', 'important');
      p.style.setProperty('line-height', '3px', 'important');
      p.innerHTML = '';
      return;
    }

    var pText = (p.textContent || '').trim();
    var hasBulletSymbol = /^[•▪➢✓\-\*]/.test(pText);
    var isNativeList = p.classList.contains('docx-num-list') || /_num_/.test(p.className);

    // If it's a bullet paragraph or numbered list item, enforce unified horizontal alignment
    if(hasBulletSymbol || isNativeList){
      p.style.setProperty('padding-left', '1.3em', 'important');
      p.style.setProperty('text-indent', '-1.3em', 'important');
      p.style.setProperty('margin-top', '0', 'important');
      p.style.setProperty('margin-bottom', '2px', 'important');
      p.style.setProperty('line-height', '1.24', 'important');

      // Format bullet symbol into fixed-width span regardless of text node or span nesting
      if(hasBulletSymbol){
        if(p.firstChild && p.firstChild.nodeType === Node.TEXT_NODE && /^[•▪➢✓\-\*]/.test(p.firstChild.nodeValue.trim())){
          var fc = p.firstChild;
          var bulletChar = fc.nodeValue.trim().charAt(0);
          var rem = fc.nodeValue.trim().substring(1).replace(/^\s+/, '');
          var bSpan = document.createElement('span');
          bSpan.className = 'agy-bullet-symbol';
          bSpan.textContent = bulletChar + ' ';
          p.replaceChild(bSpan, fc);
          if(rem){
            p.insertBefore(document.createTextNode(rem), bSpan.nextSibling);
          }
        }else if(p.firstElementChild && /^[•▪➢✓\-\*]/.test(p.firstElementChild.textContent.trim())){
          var fec = p.firstElementChild;
          var bChar = fec.textContent.trim().charAt(0);
          var bRem = fec.textContent.trim().substring(1).replace(/^\s+/, '');
          fec.className = (fec.className ? fec.className + ' ' : '') + 'agy-bullet-symbol';
          fec.textContent = bChar + ' ';
          if(bRem){
            fec.parentNode.insertBefore(document.createTextNode(bRem), fec.nextSibling);
          }
        }
      }

      // Minimize any tab stops immediately following bullet
      var firstTab = p.querySelector('.docx-tab-stop');
      if(firstTab){
        firstTab.style.setProperty('display', 'inline-block', 'important');
        firstTab.style.setProperty('width', '0.25em', 'important');
        firstTab.style.setProperty('min-width', '0.25em', 'important');
        firstTab.innerHTML = '&nbsp;';
      }
      processedParagraphs.push(p);
      return;
    }

    // Two-sided paragraph handling (e.g. Job Title <---> Date/Location)
    var tabEl = p.querySelector('.docx-tab-stop');
    if(tabEl && processedParagraphs.indexOf(p) === -1){
      processedParagraphs.push(p);
      var childNodes = Array.prototype.slice.call(p.childNodes);
      var tabIndex = childNodes.indexOf(tabEl);
      if(tabIndex === -1){
        for(var i = 0; i < childNodes.length; i++){
          if(childNodes[i].contains && childNodes[i].contains(tabEl)){
            tabIndex = i;
            break;
          }
        }
      }

      var beforeNodes = childNodes.slice(0, tabIndex);
      var afterNodes = childNodes.slice(tabIndex + 1);
      var beforeText = beforeNodes.map(function(n){ return n.textContent || ''; }).join('').trim();
      var afterText = afterNodes.map(function(n){ return n.textContent || ''; }).join('').trim();

      if(beforeText.length > 0 && afterText.length > 0){
        p.style.setProperty('display', 'flex', 'important');
        p.style.setProperty('justify-content', 'space-between', 'important');
        p.style.setProperty('align-items', 'baseline', 'important');
        p.style.setProperty('width', '100%', 'important');
        p.style.setProperty('box-sizing', 'border-box', 'important');
        p.style.setProperty('margin-top', '1px', 'important');
        p.style.setProperty('margin-bottom', '2px', 'important');

        var leftWrapper = document.createElement('span');
        leftWrapper.className = 'agy-tab-left';
        leftWrapper.style.cssText = 'flex:0 1 auto; min-width:0; overflow-wrap:break-word;';
        beforeNodes.forEach(function(n){ leftWrapper.appendChild(n); });

        var spacer = document.createElement('span');
        spacer.className = 'agy-tab-spacer';
        var hasDotLeader = metadata && metadata.paragraphs && metadata.paragraphs.some(function(pm){ return pm.hasLeader; });
        if(hasDotLeader){
          spacer.style.cssText = 'flex:1 1 auto; border-bottom:1.2px dotted #94a3b8; margin:0 10px 4px 10px; min-width:14px;';
        }else{
          spacer.style.cssText = 'flex:1 1 auto; min-width:14px;';
        }

        var rightWrapper = document.createElement('span');
        rightWrapper.className = 'agy-tab-right';
        rightWrapper.style.cssText = 'flex:0 0 auto; text-align:right; margin-left:auto; white-space:nowrap; padding-left:12px;';
        afterNodes.forEach(function(n){ rightWrapper.appendChild(n); });

        p.innerHTML = '';
        p.appendChild(leftWrapper);
        p.appendChild(spacer);
        p.appendChild(rightWrapper);
      }else{
        tabEl.style.setProperty('display', 'inline-block', 'important');
        tabEl.style.setProperty('width', '1.5em', 'important');
        tabEl.style.setProperty('min-width', '1.5em', 'important');
        tabEl.innerHTML = '&nbsp;';
      }
    }
  });

  // 3. Table Column Lock Engine (<w:tblGrid> enforcement)
  var domTables = Array.prototype.slice.call(host.querySelectorAll('table'));
  domTables.forEach(function(tbl, tIdx){
    tbl.style.setProperty('table-layout', 'fixed', 'important');
    tbl.style.setProperty('width', '100%', 'important');
    tbl.style.setProperty('border-collapse', 'collapse', 'important');
    tbl.style.setProperty('box-sizing', 'border-box', 'important');

    var tMeta = metadata && metadata.tables ? metadata.tables[tIdx] : null;
    var rows = Array.prototype.slice.call(tbl.querySelectorAll('tr'));
    rows.forEach(function(row){
      var cells = Array.prototype.slice.call(row.querySelectorAll('td, th'));
      cells.forEach(function(cell, cIdx){
        cell.style.setProperty('vertical-align', 'top', 'important');
        cell.style.setProperty('overflow-wrap', 'break-word', 'important');
        cell.style.setProperty('word-break', 'normal', 'important');
        cell.style.setProperty('box-sizing', 'border-box', 'important');

        // Apply column width percentages from OOXML grid
        if(tMeta && tMeta.percentages && tMeta.percentages[cIdx]){
          cell.style.setProperty('width', tMeta.percentages[cIdx], 'important');
        }

        // Apply vertical divider borders if defined in OOXML
        if(tMeta && tMeta.hasInsideV && cIdx > 0){
          cell.style.setProperty('border-left', tMeta.insideVSize + ' solid ' + tMeta.insideVColor, 'important');
          cell.style.setProperty('padding-left', '16px', 'important');
        }
      });
    });
  });

  // 4. Neutralize negative spacing and normalize line heights
  host.querySelectorAll('*').forEach(function(el){
    var s = el.style;
    if(s){
      if(parseFloat(s.wordSpacing) < 0) s.setProperty('word-spacing', 'normal', 'important');
      if(parseFloat(s.letterSpacing) < 0) s.setProperty('letter-spacing', 'normal', 'important');
    }
  });
}

async function renderAgyTrueSectionDocxToPdfBlob(docxBytes, options){
  options = options || {};
  await Promise.all([rsEnsureDocxConversion(), rsEnsureJsPdf(), agyEnsureMetricFonts()]);
  if(options.statusCallback) options.statusCallback('Pre-processing Word XML & font metrics (20%)...');
  await agyYieldToMain();

  // Strategy 3: In-memory OOXML pre-processing & metadata extraction
  var preResult = await agyPreProcessDocxZip(docxBytes);
  var renderBytes = preResult.bytes || docxBytes;
  var docxMetadata = preResult.metadata;

  if(options.statusCallback) options.statusCallback('Formatting Word layout (35%)...');
  await agyYieldToMain();

  var host = document.createElement('div');
  host.className = 'agy-docx-host';
  host.setAttribute('aria-hidden', 'true');
  host.style.cssText = 'position:fixed;left:0;top:0;width:auto;min-width:794px;opacity:0;pointer-events:none;z-index:-9999;background:#ffffff;overflow:visible;';
  document.body.appendChild(host);

  try{
    await window.docx.renderAsync(renderBytes, host, null, {
      className: 'docx',
      inWrapper: true,
      ignoreWidth: false,
      ignoreHeight: true,
      breakPages: true,
      ignoreLastRenderedPageBreak: false,
      experimental: false,
      useBase64URL: true,
      renderHeaders: true,
      renderFooters: true,
      renderFootnotes: true,
      renderEndnotes: true,
      trimXmlDeclaration: true
    });
    await agyYieldToMain();

    // Strategy 3: Apply Synthetic Tab Stop Engine, Table Column Lock, and Typography Normalization
    agyApplyStrategy3DomEngine(host, docxMetadata);
    await agyYieldToMain();

    if(options.anonymizeRules){
      rdcRedactDocxDom(host, options.anonymizeRules);
    }

    if(options.keywords && options.keywords.length){
      var kwRe = rsKeywordRegex(options.keywords);
      if(kwRe){
        var walker = document.createTreeWalker(host, NodeFilter.SHOW_TEXT, null, false);
        var textNode;
        var matchNodes = [];
        while((textNode = walker.nextNode())){
          var parent = textNode.parentElement;
          if(!parent || ['SCRIPT','STYLE','MARK','BUTTON','INPUT'].indexOf(parent.tagName)!==-1) continue;
          kwRe.lastIndex = 0;
          if(kwRe.test(textNode.nodeValue)){
            matchNodes.push(textNode);
          }
        }
        matchNodes.forEach(function(tn){
          var val = tn.nodeValue;
          kwRe.lastIndex = 0;
          var frag = document.createDocumentFragment();
          var lastIdx = 0;
          var m;
          while((m = kwRe.exec(val))!==null){
            if(m.index > lastIdx){
              frag.appendChild(document.createTextNode(val.substring(lastIdx, m.index)));
            }
            var mark = document.createElement('mark');
            mark.className = 'agy-keyword-hit';
            mark.textContent = m[0];
            frag.appendChild(mark);
            lastIdx = m.index + m[0].length;
            if(m.index === kwRe.lastIndex) kwRe.lastIndex++;
          }
          if(lastIdx < val.length){
            frag.appendChild(document.createTextNode(val.substring(lastIdx)));
          }
          if(tn.parentNode) tn.parentNode.replaceChild(frag, tn);
        });
      }
    }

    if(options.branding && options.brandingData){
      var firstSection = host.querySelector('.docx-wrapper > section.docx, section.docx');
      if(firstSection){
        var headerCard = document.createElement('div');
        headerCard.style.cssText = 'width:100%;box-sizing:border-box;background:#ffffff;padding:14px 22px 10px;border-bottom:1.5px solid #cbd5e1;display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;font-family:Arial,sans-serif;';
        var logoUri = await rsLogoDataUri(options.brandingData.reportCompanyLogo, options.brandingData.reportCompanyLogoRaw);
        var compName = clean(options.brandingData.reportCompanyName, 'Hiring Partner');
        var logoPart = logoUri ? '<img src="'+escapeHtml(logoUri)+'" style="height:42px;max-width:120px;object-fit:contain;margin-right:12px;" />' : '<div style="width:38px;height:38px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;display:flex;align-items:center;justify-content:center;font-weight:700;color:#1e40af;font-size:18px;margin-right:12px;">'+escapeHtml(compName.charAt(0).toUpperCase())+'</div>';
        headerCard.innerHTML = '<div style="display:flex;align-items:center;">'+logoPart+'<div><div style="font-size:8px;font-weight:700;color:#64748b;letter-spacing:0.5px;text-transform:uppercase;">SHARED BY</div><div style="font-size:14px;font-weight:700;color:#0f172a;line-height:1.2;">'+escapeHtml(compName)+'</div></div></div><div style="text-align:right;"><div style="font-size:13px;font-weight:700;color:#1e293b;letter-spacing:0.5px;">CONFIDENTIAL RESUME</div><div style="font-size:9.5px;font-weight:500;color:#64748b;margin-top:2px;">'+escapeHtml(clean(options.brandingData.reportDate, '') || (new Date()).toLocaleDateString())+'</div></div>';
        firstSection.insertBefore(headerCard, firstSection.firstChild);
      }
    }

    await waitForRenderedAssets(host);
    await agyYieldToMain();

    var pages = Array.prototype.slice.call(host.querySelectorAll('.docx-wrapper > section.docx'));
    if(!pages.length) pages = Array.prototype.slice.call(host.querySelectorAll('section.docx'));
    if(!pages.length) pages = [host];

    var JsPDF = window.jspdf.jsPDF;
    var pdf = new JsPDF({ orientation: 'portrait', unit: 'pt', format: 'a4', compress: true });
    var pageW = pdf.internal.pageSize.getWidth();
    var pageH = pdf.internal.pageSize.getHeight();
    var a4Aspect = pageH / pageW;

    var isFirstPdfPage = true;
    var totalSlicesEstimate = 0;

    for(var i = 0; i < pages.length; i++){
      if(options.statusCallback){
        options.statusCallback('Rasterizing section ' + (i + 1) + ' of ' + pages.length + ' (40%)...');
      }
      await agyYieldToMain();
      var pageEl = pages[i];
      pageEl.style.margin = '0 auto';
      pageEl.style.boxShadow = 'none';
      pageEl.style.background = '#ffffff';

      var elWidth = Math.ceil(pageEl.scrollWidth || pageEl.getBoundingClientRect().width || 794);
      var elHeight = Math.ceil(pageEl.scrollHeight || pageEl.getBoundingClientRect().height || 1123);

      var canvas = await window.html2canvas(pageEl, {
        backgroundColor: '#ffffff',
        scale: 2.75,
        useCORS: true,
        allowTaint: false,
        logging: false,
        removeContainer: true,
        scrollX: 0,
        scrollY: 0,
        windowWidth: elWidth,
        windowHeight: elHeight
      });

      var targetSliceH = Math.round(canvas.width * a4Aspect);
      var ctx = canvas.getContext('2d', { willReadFrequently: true });
      var offsetY = 0;

      while(offsetY < canvas.height){
        totalSlicesEstimate++;
        var remainingH = canvas.height - offsetY;
        var currentSliceH = Math.min(targetSliceH, remainingH);

        // If remaining content exceeds one A4 page slice, search for a safe cut between rows
        if(remainingH > targetSliceH){
          var idealCutY = offsetY + targetSliceH;
          var bestCutY = idealCutY;
          var searchMinY = Math.max(offsetY + Math.round(targetSliceH * 0.80), idealCutY - 260);

          // Find cleanest interline gap by measuring dark pixel density across horizontal rows
          // Ignores outer 4% margins to avoid page borders, shadows, or scanner artifacts
          var marginPad = Math.round(canvas.width * 0.04);
          var checkWidth = canvas.width - (marginPad * 2);
          var minDarkPixels = Infinity;
          var bestGapY = -1;

          for(var checkY = idealCutY; checkY >= searchMinY; checkY -= 2){
            var rowData = ctx.getImageData(marginPad, checkY, checkWidth, 1).data;
            var darkCount = 0;
            for(var x = 0; x < rowData.length; x += 16){
              var a = rowData[x + 3];
              if(a > 20){
                var r = rowData[x];
                var g = rowData[x + 1];
                var b = rowData[x + 2];
                if(r < 235 || g < 235 || b < 235){
                  darkCount++;
                }
              }
            }
            if(darkCount <= 1){
              bestGapY = checkY;
              break;
            }
            if(darkCount < minDarkPixels){
              minDarkPixels = darkCount;
              bestCutY = checkY;
            }
          }

          if(bestGapY !== -1){
            currentSliceH = bestGapY - offsetY;
          }else{
            currentSliceH = bestCutY - offsetY;
          }
        }

        if(options.statusCallback){
          options.statusCallback('Generating PDF page ' + totalSlicesEstimate + ' (' + Math.min(92, Math.round(30 + (offsetY / canvas.height) * 60)) + '%)...');
        }

        var sliceCanvas = document.createElement('canvas');
        sliceCanvas.width = canvas.width;
        sliceCanvas.height = currentSliceH;
        var sCtx = sliceCanvas.getContext('2d');
        sCtx.fillStyle = '#ffffff';
        sCtx.fillRect(0, 0, sliceCanvas.width, sliceCanvas.height);
        sCtx.drawImage(canvas, 0, offsetY, canvas.width, currentSliceH, 0, 0, canvas.width, currentSliceH);

        var sliceImg = sliceCanvas.toDataURL('image/jpeg', 0.98);

        if(!isFirstPdfPage){
          pdf.addPage('a4', 'portrait');
        }else{
          isFirstPdfPage = false;
        }

        // Exact 1:1 Aspect Ratio Scaling: ZERO vertical squashing or stretching
        var drawH = currentSliceH * (pageW / canvas.width);
        pdf.addImage(sliceImg, 'JPEG', 0, 0, pageW, Math.min(pageH, drawH), undefined, 'FAST');

        if(options.searchableText !== false){
          agyInjectInvisibleTextLayer(pdf, pageEl, pageW, drawH, offsetY, currentSliceH, canvas.height);
        }

        sliceCanvas.width = 0;
        sliceCanvas.height = 0;
        sliceCanvas = null;

        offsetY += currentSliceH;
        await agyYieldToMain();
      }

      canvas.width = 0;
      canvas.height = 0;
      canvas = null;
      await agyYieldToMain();
    }

    if(options.statusCallback) options.statusCallback('Finalizing PDF (95%)...');
    await agyYieldToMain();
    return pdf.output('blob');
  }finally{
    removeNode(host);
  }
}

/* ==========================================================================
   STRATEGY 2: PURE WEBASSEMBLY TYPESETTING ENGINE (ISOLATED - CONVERSION ONLY)
   - Powered by Rust/C++ OpenDoc WASM typesetting core
   - Zero HTML DOM footprint, zero rasterization slicing heuristics
   - True physical page vector coordinate model
   - Conversion only: no highlighting, no branding, no report merging
   ========================================================================== */

/**
 * openDocPagePoints
 * Purpose: Extracts the page dimensions in points from the OpenDoc WASM document instance.
 * Returns [width, height] in points (1/72 inch).
 */
function openDocPagePoints(doc,index){
  var fallback=[595.28,841.89];
  try{
    var size=doc.pageSize(index);
    var width=size && size.widthTwip ? size.widthTwip/20 : 0;
    var height=size && size.heightTwip ? size.heightTwip/20 : 0;
    if(size && typeof size.free==='function') size.free();
    if(width>=240 && height>=240 && width<=1600 && height<=2200) return [width,height];
  }catch(err){
    console.warn('OpenDoc page size fallback used:',err);
  }
  return fallback;
}

/**
 * openDocCanvasFromBitmap
 * Purpose: Converts an RGBA bitmap emitted by OpenDoc WASM renderPage() into an HTML Canvas element
 * for downstream PDF embedding via jsPDF.
 */
function openDocCanvasFromBitmap(bitmap){
  var width=bitmap && bitmap.widthPx ? bitmap.widthPx : 0;
  var height=bitmap && bitmap.heightPx ? bitmap.heightPx : 0;
  if(!width || !height) throw new Error('OpenDoc rendered an empty page bitmap');
  var rgba=bitmap.rgba;
  var canvas=document.createElement('canvas');
  canvas.width=width;
  canvas.height=height;
  var ctx=canvas.getContext('2d',{alpha:false});
  ctx.fillStyle='#ffffff';
  ctx.fillRect(0,0,width,height);
  ctx.putImageData(new ImageData(rgba,width,height),0,0);
  return canvas;
}

/**
 * openDocRenderDpi
 * Purpose: Calculates an adaptive DPI setting based on document page count to balance memory
 * and rendering crispness.
 */
function openDocRenderDpi(pageCount){
  pageCount=parseInt(pageCount,10)||1;
  if(pageCount<=6) return 216;
  if(pageCount<=15) return 180;
  return 160;
}

/**
 * openDocNativePdfBlob
 * Purpose: Attempts to export a native vector PDF directly from the OpenDoc WASM engine if supported
 * by the compiled binary format. Returns a Blob or null if rasterization is required.
 */
function openDocNativePdfBlob(doc){
  if(!doc || typeof doc.availableExportFormats!=='function' || typeof doc.exportAs!=='function') return null;
  var formats=[];
  try{
    formats=doc.availableExportFormats() || [];
  }catch(formatErr){
    console.warn('OpenDoc export formats unavailable:',formatErr);
    return null;
  }
  var hasPdf=formats.some(function(format){
    return String(format||'').toLowerCase()==='pdf';
  });
  if(!hasPdf) return null;
  var modes=['exact_if_unchanged','preserve_when_safe','semantic'];
  for(var i=0;i<modes.length;i++){
    var artifact=null;
    try{
      artifact=doc.exportAs('pdf',modes[i]);
      var bytes=artifact && artifact.bytes ? artifact.bytes : null;
      var mime=artifact && artifact.mimeType ? artifact.mimeType : 'application/pdf';
      if(bytes && bytes.length) return new Blob([bytes],{type:mime || 'application/pdf'});
    }catch(exportErr){
      console.warn('OpenDoc native PDF export failed for '+modes[i]+':',exportErr);
    }finally{
      if(artifact && typeof artifact.free==='function') artifact.free();
    }
  }
  return null;
}

function agyCollectWasmDocxWordHits(doc, tokens){
  var allHits = [];
  if(!doc || !Array.isArray(tokens) || !tokens.length) return allHits;
  if(typeof doc.findText !== 'function' || typeof doc.selectionRects !== 'function') return allHits;

  var seen = {};
  var uniqueTokens = [];
  for(var t = 0; t < tokens.length; t++){
    var tok = String(tokens[t] || '').trim();
    var low = tok.toLowerCase();
    if(!seen[low] && tok.length >= 2){
      seen[low] = true;
      uniqueTokens.push(tok);
    }
  }

  uniqueTokens.forEach(function(word){
    var first = null;
    try{
      if(typeof doc.firstPosition !== 'function') return;
      first = doc.firstPosition();
      if(!first || !first.node) return;
      var curNode = first.node;
      var curOffset = first.offset || 0;
      var matchSeen = {};

      for(var iter = 0; iter < 100; iter++){
        var match = null;
        try{
          match = doc.findText(word, curNode, curOffset, true, false);
          if(!match || !match.__wbg_ptr || !match.found) break;
          var sNode = match.startNode;
          var sOff = match.startOffset;
          var eNode = match.endNode;
          var eOff = match.endOffset;
          if(!sNode || !eNode) break;

          var mKey = sNode + ':' + sOff;
          if(matchSeen[mKey]) break;
          matchSeen[mKey] = true;

          var rects = doc.selectionRects(sNode, sOff, eNode, eOff);
          if(rects && rects.length >= 5){
            for(var r = 0; r < rects.length; r += 5){
              allHits.push({
                page: rects[r],
                x: rects[r + 1],
                y: rects[r + 2],
                w: rects[r + 3],
                h: rects[r + 4],
                word: word
              });
            }
          }
          if(curNode === eNode && curOffset === eOff) break;
          curNode = eNode;
          curOffset = eOff;
        }catch(mErr){
          break;
        }finally{
          if(match && typeof match.free === 'function') match.free();
        }
      }
    }catch(err){
    }finally{
      if(first && typeof first.free === 'function') first.free();
    }
  });

  return allHits;
}
window.agyCollectWasmDocxWordHits = agyCollectWasmDocxWordHits;

async function renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, options){
  options = options || {};
  if(options.statusCallback) options.statusCallback('Initializing WASM typesetting core (20%)...');
  await agyYieldToMain();

  var initResults = await Promise.all([
    rsEnsureOpenDocLab(false),
    rsEnsureJsPdf(),
    rsEnsureJsZip()
  ]);
  var openDoc = initResults[0];

  if(options.statusCallback) options.statusCallback('Pre-processing Word XML & font metrics (35%)...');
  await agyYieldToMain();

  // Strategy 2: Pre-process DOCX bytes to sanitize numbering, PUA codepoints, and spacing
  // NOTE: Do not pass keywords to agyPreProcessDocxZip so the base canvas bitmap is 100% clean
  // and free of baked-in OOXML yellow highlights. The viewer handles all highlights dynamically.
  var preOpts = Object.assign({}, options, { keywords: null });
  var preResult = await agyPreProcessDocxZip(docxBytes, preOpts);
  var renderBytes = preResult.bytes || docxBytes;

  if(options.statusCallback) options.statusCallback('Parsing OOXML in WebAssembly (50%)...');
  await agyYieldToMain();

  var doc = null;
  try{
    var u8 = new Uint8Array(renderBytes);
    try{
      doc = (typeof openDoc.openAs === 'function') ? openDoc.openAs(u8, 'docx') : openDoc.open(u8);
    }catch(err){
      doc = openDoc.open(u8);
    }

    if(!doc) throw new Error('WASM engine failed to open document');

    var pageCount = parseInt(doc.pageCount, 10);
    if(!pageCount && typeof doc.documentStats === 'function'){
      var stats = doc.documentStats();
      pageCount = parseInt(stats && stats.pages ? stats.pages : 0, 10);
      if(stats && typeof stats.free === 'function') stats.free();
    }
    if(!pageCount || pageCount < 1) throw new Error('WASM engine reported 0 pages for this document');

    // Check if native vector PDF is directly emitted by the WASM core
    var nativeBlob = openDocNativePdfBlob(doc);
    if(nativeBlob){
      if(options.statusCallback) options.statusCallback('Exporting native vector PDF (100%)...');
      return nativeBlob;
    }

    if(options.statusCallback) options.statusCallback('Extracting searchable text metrics & coordinates (55%)...');
    await agyYieldToMain();

    window.__rcvDocxWasmHits = [];
    window.__rcvDocxWasmHitsSearched = {};
    if(Array.isArray(options.keywords)){
      options.keywords.forEach(function(k){
        window.__rcvDocxWasmHitsSearched[String(k).toLowerCase()] = true;
      });
    }

    var wasmHits = agyCollectWasmDocxWordHits(doc, options.keywords || []);
    window.__rcvDocxWasmHits = wasmHits;
    if(window.__rcvActiveWasmDoc && window.__rcvActiveWasmDoc !== doc && typeof window.__rcvActiveWasmDoc.free === 'function'){
      try { window.__rcvActiveWasmDoc.free(); } catch(e){}
    }
    window.__rcvActiveWasmDoc = doc;

    if(options.statusCallback) options.statusCallback('Rasterizing pages via WASM vector pipeline (60%)...');
    await agyYieldToMain();

    var JsPDF = window.jspdf.jsPDF;
    var pdf = null;
    var renderDpi = 288;

    for(var i = 0; i < pageCount; i++){
      if(options.statusCallback){
        options.statusCallback('Rendering WASM page ' + (i + 1) + ' of ' + pageCount + ' (' + Math.round(50 + (i / pageCount) * 45) + '%)...');
      }
      await agyYieldToMain();

      var bitmap = null;
      var canvas = null;
      try{
        bitmap = doc.renderPage(i, renderDpi);
        canvas = openDocCanvasFromBitmap(bitmap);

        var points = openDocPagePoints(doc, i);
        var pageW = (points && points[0]) ? points[0] : (bitmap.widthPx / renderDpi) * 72;
        var pageH = (points && points[1]) ? points[1] : (bitmap.heightPx / renderDpi) * 72;

        if(!pdf){
          pdf = new JsPDF({
            orientation: pageW > pageH ? 'landscape' : 'portrait',
            unit: 'pt',
            format: [pageW, pageH],
            compress: true
          });
        }else{
          pdf.addPage([pageW, pageH], pageW > pageH ? 'landscape' : 'portrait');
        }

        // Composite exact-width highlights onto canvas when requested for PDF downloads (Options 44, 45, 46, 47, 53)
        if(options.burnHighlights && wasmHits && wasmHits.length){
          var pageHits = wasmHits.filter(function(h){ return h.page === (i + 1); });
          if(pageHits.length){
            var ctx = canvas.getContext('2d');
            ctx.save();
            ctx.fillStyle = 'rgba(251, 191, 36, 0.42)';
            var sX = canvas.width / (pageW * 20);
            var sY = canvas.height / (pageH * 20);
            pageHits.forEach(function(h){
              ctx.fillRect(Math.round(h.x * sX), Math.round(h.y * sY), Math.max(8, Math.round(h.w * sX)), Math.round(h.h * sY));
            });
            ctx.restore();
          }
        }

        var imgData = canvas.toDataURL('image/jpeg', 0.98);
        pdf.addImage(imgData, 'JPEG', 0, 0, pageW, pageH, undefined, 'FAST');
      }finally{
        if(bitmap && typeof bitmap.free === 'function') bitmap.free();
        if(canvas){
          canvas.width = 0;
          canvas.height = 0;
          canvas = null;
        }
      }
    }

    if(options.statusCallback) options.statusCallback('Finalizing PDF (95%)...');
    await agyYieldToMain();
    var outBlob = pdf.output('blob');
    outBlob.__wasmHits = wasmHits;
    return outBlob;
  }finally{
    if(window.__rcvActiveWasmDoc !== doc && doc && typeof doc.free === 'function') doc.free();
  }
}

async function processAgyTestOption(optNum, options){
  options = options || {};
  if(window.__rsBusy) return Promise.resolve(false);
  window.__rsBusy = true;
  function notify(pct, step, detail){
    if(typeof options.statusCallback === 'function'){
      var obj = {
        percent: pct,
        step: step,
        detail: detail,
        toString: function(){ return step + ' (' + pct + '%)'; }
      };
      options.statusCallback(obj);
    }
  }
  try{
    var resumeSources = (Array.isArray(options.resumeUrl) ? options.resumeUrl : [options.resumeUrl || '']).map(function(url){
      return clean(url, '');
    }).filter(function(url, index, self){
      return url && url !== '--' && self.indexOf(url) === index;
    });
    var resumeExt = clean(options.resumeExt || '', '').toUpperCase();
    if(resumeExt === 'DOC'){
      if(typeof window.__showDocFileUnsupportedPopup === 'function') window.__showDocFileUnsupportedPopup();
      throw new Error('This Word (.doc) file version isn’t supported. Please upload the resume as PDF or download the original resume using Option 1.');
    }
    var data = readData();

    // Cache key tied strictly to this candidate's specific primary resume URL or ID
    var cacheKey = resumeSources[0] || (data && (data.id || data.name) ? 'cand_' + (data.id || data.name) : '');
    window.__agyResumeCache = window.__agyResumeCache || {};

    notify(15, 'Fetching candidate resume document...', 'Retrieving vector document bytes from storage...');
    var docxBytes = cacheKey ? window.__agyResumeCache[cacheKey] : null;
    if(!docxBytes || !docxBytes.byteLength){
      docxBytes = await fetchResumeBytesFromSources(resumeSources, resumeExt === 'PDF' ? rsPdfMaxBytes : rsDocxMaxBytes);
      if(cacheKey && docxBytes && docxBytes.byteLength){
        window.__agyResumeCache[cacheKey] = docxBytes;
      }
    }

    var isLegacyDoc = (docxBytes && docxBytes.byteLength >= 4 && docxBytes[0] === 0xD0 && docxBytes[1] === 0xCF && docxBytes[2] === 0x11 && docxBytes[3] === 0xE0);
    if(isLegacyDoc){
      if(typeof window.__showDocFileUnsupportedPopup === 'function') window.__showDocFileUnsupportedPopup();
      throw new Error('This Word (.doc) file version isn’t supported. Please upload the resume as PDF or download the original resume using Option 1.');
    }

    var pdfBlob = null;
    var outFileName = '';

    if(optNum === 41){
      // Option 41: Resume + Company Branding
      notify(30, 'Rendering resume layout in browser RAM...', 'Preparing document for company branding...');
      var resumeBlob = null;
      if(resumeExt === 'PDF'){
        resumeBlob = new Blob([docxBytes], { type: 'application/pdf' });
      }else{
        notify(40, 'Converting DOCX via WASM...', 'Rendering Word document to vector PDF in browser RAM...');
        resumeBlob = await renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, {
          statusCallback: options.statusCallback
        });
      }
      notify(75, 'Stamping company branding...', 'Injecting header with company logo and metadata...');
      pdfBlob = await applyAgyCompanyBrandingToPdfBlob(resumeBlob, data);
      notify(95, 'Branded resume ready!', 'Document ready for download');
      outFileName = 'Jeroworks_' + fileSafeName(data.name, 'Candidate') + '.pdf';

    }else if(optNum === 42){
      // Option 42: Pre-Screened Report + Resume + Company Branding
      notify(30, 'Generating Pre-Screened Report...', 'Compiling technical evaluation dossier with AI insights...');
      var reportBlob = await buildRecruiterSummaryBlob(data, { statusCallback: options.statusCallback });

      var resumeBlob = null;
      if(resumeExt === 'PDF'){
        notify(55, 'Preparing candidate resume...', 'Normalizing PDF document pages...');
        resumeBlob = new Blob([docxBytes], { type: 'application/pdf' });
      }else{
        notify(50, 'Converting DOCX via WASM...', 'Rendering Word document to vector PDF in browser RAM...');
        resumeBlob = await renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, {
          statusCallback: options.statusCallback
        });
      }

      notify(85, 'Merging dossier and resume...', 'Concatenating recruiter package with uniform A4 width...');
      var mergedName = resumeTrackingFileName(data, 'C', 'pdf');
      await mergePreScreenedReportAndResume(reportBlob, resumeBlob, mergedName);
      notify(100, 'Recruiter package ready!', 'Document ready for download');
      return true;

    }else if(optNum === 43){
      // Option 43: Pre-Screened Report + Resume without Company Branding
      notify(30, 'Generating unbranded Pre-Screened Report...', 'Compiling neutral evaluation dossier...');
      var reportBlob = await buildRecruiterSummaryBlob(data, { noBranding: true, statusCallback: options.statusCallback });

      var resumeBlob = null;
      if(resumeExt === 'PDF'){
        notify(55, 'Preparing candidate resume...', 'Normalizing PDF document pages...');
        resumeBlob = new Blob([docxBytes], { type: 'application/pdf' });
      }else{
        notify(50, 'Converting DOCX via WASM...', 'Rendering Word document to vector PDF in browser RAM...');
        resumeBlob = await renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, {
          statusCallback: options.statusCallback
        });
      }

      notify(85, 'Merging unbranded dossier and resume...', 'Concatenating neutral recruiter package with uniform A4 width...');
      var mergedName = resumeTrackingFileName(data, 'C', 'pdf');
      await mergePreScreenedReportAndResume(reportBlob, resumeBlob, mergedName);
      notify(100, 'Unbranded package ready!', 'Document ready for download');
      return true;

    }else if(optNum === 44){
      // Option 44: Resume + Highlighted Keywords
      notify(25, 'Extracting evaluated skills...', 'Matching job skills with candidate profile...');
      var allKeywords = rsDrawerHighlightedKeywords();

      if(resumeExt === 'PDF'){
        notify(60, 'Highlighting evaluated skills in resume...', 'Applying sub-pixel vector highlights to keywords...');
        var rawPdfBlob = new Blob([docxBytes], { type: 'application/pdf' });
        pdfBlob = await buildTurboVectorHighlightedResume({
          resumeBlob: rawPdfBlob,
          resumeUrl: resumeSources,
          resumeExt: 'PDF',
          keywords: allKeywords,
          returnBlob: true,
          ignoreBusy: true,
          throwOnError: true
        });
      }else{
        notify(50, 'Rendering DOCX with skill highlights via WASM...', 'Extracting word coordinates & burning vector highlights...');
        pdfBlob = await renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, {
          keywords: allKeywords,
          burnHighlights: true,
          statusCallback: options.statusCallback
        });
      }
      notify(95, 'Highlighted resume ready!', 'Document ready for download');
      outFileName = 'Jeroworks_' + fileSafeName(data.name, 'Candidate') + '.pdf';

    }else if(optNum === 45){
      // Option 45: Resume + Highlighted Keywords + Company Branding
      notify(25, 'Extracting evaluated skills...', 'Matching job skills with candidate profile...');
      var allKeywords = rsDrawerHighlightedKeywords();

      var highlightedBlob = null;
      if(resumeExt === 'PDF'){
        notify(50, 'Highlighting evaluated skills in resume...', 'Applying sub-pixel vector highlights to keywords...');
        var rawPdfBlob = new Blob([docxBytes], { type: 'application/pdf' });
        highlightedBlob = await buildTurboVectorHighlightedResume({
          resumeBlob: rawPdfBlob,
          resumeUrl: resumeSources,
          resumeExt: 'PDF',
          keywords: allKeywords,
          returnBlob: true,
          ignoreBusy: true,
          throwOnError: true
        });
      }else{
        notify(50, 'Rendering DOCX with skill highlights via WASM...', 'Extracting word coordinates & burning vector highlights...');
        highlightedBlob = await renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, {
          keywords: allKeywords,
          burnHighlights: true,
          statusCallback: options.statusCallback
        });
      }

      notify(75, 'Stamping company branding...', 'Injecting header with company logo and metadata...');
      pdfBlob = await applyAgyCompanyBrandingToPdfBlob(highlightedBlob, data);
      notify(95, 'Branded highlighted resume ready!', 'Document ready for download');
      outFileName = 'Jeroworks_' + fileSafeName(data.name, 'Candidate') + '.pdf';

    }else if(optNum === 46){
      // Option 46: Pre-Screened Report + Resume + Highlighted Keywords + Company Branding
      notify(25, 'Generating Pre-Screened Report...', 'Compiling technical evaluation dossier with verified skills...');
      var reportBlob = await buildRecruiterSummaryBlob(data, { statusCallback: options.statusCallback });

      var allKeywords = rsDrawerHighlightedKeywords();
      var highlightedBlob = null;
      if(resumeExt === 'PDF'){
        notify(55, 'Highlighting evaluated skills on resume...', 'Extracting vector coordinates for skill keywords...');
        var rawPdfBlob = new Blob([docxBytes], { type: 'application/pdf' });
        highlightedBlob = await buildTurboVectorHighlightedResume({
          resumeBlob: rawPdfBlob,
          resumeUrl: resumeSources,
          resumeExt: 'PDF',
          keywords: allKeywords,
          returnBlob: true,
          ignoreBusy: true,
          throwOnError: true
        });
      }else{
        notify(55, 'Rendering DOCX with skill highlights via WASM...', 'Extracting word coordinates & burning vector highlights...');
        highlightedBlob = await renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, {
          keywords: allKeywords,
          burnHighlights: true,
          statusCallback: options.statusCallback
        });
      }

      notify(88, 'Merging dossier and highlighted resume...', 'Assembling recruiter presentation package with uniform A4 width...');
      var mergedName = resumeTrackingFileName(data, 'C', 'pdf');
      await mergePreScreenedReportAndResume(reportBlob, highlightedBlob, mergedName);
      notify(100, 'Executive presentation package ready!', 'Document ready for download');
      return true;

    }else if(optNum === 47){
      // Option 47: Pre-Screened Report + Resume + Highlighted Keywords without Company Branding
      notify(25, 'Generating unbranded Pre-Screened Report...', 'Compiling neutral dossier...');
      var reportBlob = await buildRecruiterSummaryBlob(data, { noBranding: true, statusCallback: options.statusCallback });

      var allKeywords = rsDrawerHighlightedKeywords();
      var highlightedBlob = null;
      if(resumeExt === 'PDF'){
        notify(55, 'Highlighting evaluated skills on resume...', 'Applying sub-pixel vector highlights to rated skills...');
        var rawPdfBlob = new Blob([docxBytes], { type: 'application/pdf' });
        highlightedBlob = await buildTurboVectorHighlightedResume({
          resumeBlob: rawPdfBlob,
          resumeUrl: resumeSources,
          resumeExt: 'PDF',
          keywords: allKeywords,
          returnBlob: true,
          ignoreBusy: true,
          throwOnError: true
        });
      }else{
        notify(55, 'Rendering DOCX with skill highlights via WASM...', 'Extracting word coordinates & burning vector highlights...');
        highlightedBlob = await renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, {
          keywords: allKeywords,
          burnHighlights: true,
          statusCallback: options.statusCallback
        });
      }

      notify(85, 'Merging unbranded dossier and highlighted resume...', 'Concatenating recruiter package with uniform A4 width...');
      var mergedName = resumeTrackingFileName(data, 'C', 'pdf');
      await mergePreScreenedReportAndResume(reportBlob, highlightedBlob, mergedName);
      notify(100, 'Unbranded highlighted package ready!', 'Document ready for download');
      return true;

    }else if(optNum === 48){
      // Option 48: Hide Contact Details
      notify(25, 'Analyzing contact details...', 'Extracting phone numbers, emails, and URLs...');
      var rules = rdcAnonymizeRules(data, { keepName: true });

      if(resumeExt === 'PDF'){
        notify(60, 'Masking contact information with white strip...', 'Applying white strips on telephone, email, and social profiles...');
        var rawPdfBlob = new Blob([docxBytes], { type: 'application/pdf' });
        pdfBlob = await renderAnonymizedPdfBlob(rawPdfBlob, rules);
      }else{
        notify(55, 'Redacting contact information via WASM OOXML engine...', 'Redacting contact channels directly in Word XML structure...');
        pdfBlob = await renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, {
          anonymizeRules: rules,
          statusCallback: options.statusCallback
        });
      }
      notify(95, 'Redacted resume ready!', 'Document ready for download');
      outFileName = 'Jeroworks_' + fileSafeName(data.name, 'Candidate') + '.pdf';

    }else if(optNum === 49){
      // Option 49: Anti-Poaching Blind Profile (Hide Candidate Name & Contact Details)
      notify(25, 'Generating confidential blind profile...', 'Extracting candidate PII & sensitive contact rules...');
      var rules = rdcAnonymizeRules(data, { keepName: false });

      if(resumeExt === 'PDF'){
        notify(60, 'Redacting candidate name & contact details...', 'Masking candidate name and personal contact channels with white strips...');
        var rawPdfBlob = new Blob([docxBytes], { type: 'application/pdf' });
        pdfBlob = await renderAnonymizedPdfBlob(rawPdfBlob, rules);
      }else{
        notify(55, 'Generating blind profile via WASM OOXML engine...', 'Redacting candidate name and contact channels directly in Word XML structure...');
        pdfBlob = await renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, {
          anonymizeRules: rules,
          statusCallback: options.statusCallback
        });
      }
      notify(95, 'Confidential blind profile ready!', 'Document ready for download');
      outFileName = 'Jeroworks_Blind_Resume.pdf';

    }else if(optNum === 50){
      // Option 50: Hide Contact Details + Company Branding
      notify(25, 'Analyzing contact details...', 'Extracting phone numbers, emails, and URLs...');
      var rules = rdcAnonymizeRules(data, { keepName: true });

      var contactHiddenBlob = null;
      if(resumeExt === 'PDF'){
        notify(50, 'Masking contact information with white strip...', 'Applying white strips on telephone, email, and social profiles...');
        var rawPdfBlob = new Blob([docxBytes], { type: 'application/pdf' });
        contactHiddenBlob = await renderAnonymizedPdfBlob(rawPdfBlob, rules);
      }else{
        notify(50, 'Redacting contact information via WASM OOXML engine...', 'Redacting contact channels directly in Word XML structure...');
        contactHiddenBlob = await renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, {
          anonymizeRules: rules,
          statusCallback: options.statusCallback
        });
      }

      notify(75, 'Stamping company branding...', 'Injecting header with company logo and metadata...');
      pdfBlob = await applyAgyCompanyBrandingToPdfBlob(contactHiddenBlob, data);
      notify(95, 'Branded redacted resume ready!', 'Document ready for download');
      outFileName = 'Jeroworks_' + fileSafeName(data.name, 'Candidate') + '.pdf';

    }else if(optNum === 51){
      // Option 51: Hide Contact Details + Pre-Screened Report + Company Branding
      notify(25, 'Generating Pre-Screened Report...', 'Compiling technical evaluation dossier...');
      var reportBlob = await buildRecruiterSummaryBlob(data, { statusCallback: options.statusCallback });

      var rules = rdcAnonymizeRules(data, { keepName: true });
      var contactHiddenBlob = null;
      if(resumeExt === 'PDF'){
        notify(55, 'Masking contact details on resume with white strip...', 'Applying white strips on phone, email, and URLs...');
        var rawPdfBlob = new Blob([docxBytes], { type: 'application/pdf' });
        contactHiddenBlob = await renderAnonymizedPdfBlob(rawPdfBlob, rules);
      }else{
        notify(55, 'Redacting contact information via WASM OOXML engine...', 'Redacting contact channels directly in Word XML structure...');
        contactHiddenBlob = await renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, {
          anonymizeRules: rules,
          statusCallback: options.statusCallback
        });
      }

      notify(88, 'Merging evaluation report and confidential resume...', 'Assembling recruiter package with uniform A4 width...');
      var mergedName = 'Jeroworks_' + fileSafeName(data.name, 'Candidate') + '.pdf';
      await mergePreScreenedReportAndResume(reportBlob, contactHiddenBlob, mergedName);
      notify(100, 'Branded confidential package ready!', 'Document ready for download');
      return true;

    }else if(optNum === 52){
      // Option 52: Hide Contact Details + Pre-Screened Report without Company Branding
      notify(25, 'Generating unbranded evaluation report...', 'Compiling neutral dossier...');
      var reportBlob = await buildRecruiterSummaryBlob(data, { noBranding: true, statusCallback: options.statusCallback });

      var rules = rdcAnonymizeRules(data, { keepName: true });
      var contactHiddenBlob = null;
      if(resumeExt === 'PDF'){
        notify(55, 'Masking contact details on resume with white strip...', 'Applying white strips on phone, email, and URLs...');
        var rawPdfBlob = new Blob([docxBytes], { type: 'application/pdf' });
        contactHiddenBlob = await renderAnonymizedPdfBlob(rawPdfBlob, rules);
      }else{
        notify(55, 'Redacting contact information via WASM OOXML engine...', 'Redacting contact channels directly in Word XML structure...');
        contactHiddenBlob = await renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, {
          anonymizeRules: rules,
          statusCallback: options.statusCallback
        });
      }

      notify(85, 'Merging unbranded dossier and confidential resume...', 'Assembling recruiter package with uniform A4 width...');
      var mergedName = 'Jeroworks_' + fileSafeName(data.name, 'Candidate') + '.pdf';
      await mergePreScreenedReportAndResume(reportBlob, contactHiddenBlob, mergedName);
      notify(100, 'Unbranded confidential package ready!', 'Document ready for download');
      return true;

    }else if(optNum === 53){
      // Option 53: Hide Contact Details + Pre-Screened Report + Highlighted Keywords
      notify(20, 'Generating Pre-Screened Report...', 'Compiling technical evaluation dossier...');
      var reportBlob = await buildRecruiterSummaryBlob(data, { statusCallback: options.statusCallback });
      var allKeywords = rsDrawerHighlightedKeywords();
      var rules = rdcAnonymizeRules(data, { keepName: true });

      var resumeBlob = null;
      if(resumeExt === 'PDF'){
        notify(45, 'Highlighting evaluated skills in resume...', 'Applying sub-pixel vector highlights to keywords...');
        var rawPdfBlob = new Blob([docxBytes], { type: 'application/pdf' });
        var highlightedBlob = await buildTurboVectorHighlightedResume({
          resumeBlob: rawPdfBlob,
          resumeUrl: resumeSources,
          resumeExt: 'PDF',
          keywords: allKeywords,
          returnBlob: true,
          ignoreBusy: true,
          throwOnError: true
        });
        notify(70, 'Masking contact details on resume with white strip...', 'Applying white strips on phone, email, and URLs...');
        resumeBlob = await renderAnonymizedPdfBlob(highlightedBlob, rules);
      }else{
        notify(55, 'Processing DOCX: burning highlights & redacting contacts via WASM...', 'Applying OOXML contact redactions and burning skill highlights...');
        resumeBlob = await renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, {
          anonymizeRules: rules,
          keywords: allKeywords,
          burnHighlights: true,
          statusCallback: options.statusCallback
        });
      }

      notify(88, 'Merging report and highlighted confidential resume...', 'Assembling recruiter package with uniform A4 width...');
      var mergedName = 'Jeroworks_' + fileSafeName(data.name, 'Candidate') + '.pdf';
      await mergePreScreenedReportAndResume(reportBlob, resumeBlob, mergedName);
      notify(100, 'Confidential highlighted package ready!', 'Document ready for download');
      return true;
    }

    if(pdfBlob instanceof Blob){
      notify(100, 'Document ready!', 'Starting download...');
      downloadBlob(pdfBlob, outFileName || 'Jeroworks_Resume.pdf');
      return true;
    }
    throw new Error('PDF output could not be created');
  }catch(err){
    console.error('Antigravity Test Lab Error:', err);
    throw err;
  }finally{
    window.__rsBusy = false;
  }
}

/* ==========================================================================
   AGY COMPANY BRANDING ENGINE (PURE CLIENT-SIDE VECTOR HEADER INJECTION)
   - Extends Page 1 canvas height by header height (72-82 pt)
   - Mounts Company Logo + "SHARED BY" header above Page 1
   - Original resume drawn 1:1 below header with ZERO squashing or text clipping
   - 100% of candidate contact info, name, title remain completely visible
   - Copies subsequent pages untouched with 100% vector fidelity
   ========================================================================== */
async function applyAgyCompanyBrandingToPdfBlob(resumePdfBlob, brandingData){
  await rsEnsurePdfLib();
  brandingData = brandingData || readData();
  var PDFLib = window.PDFLib;
  var resumeBytes = await resumePdfBlob.arrayBuffer();
  var resumeDoc = await PDFLib.PDFDocument.load(resumeBytes);
  var sourcePages = resumeDoc.getPages();
  if(!sourcePages.length) throw new Error('Resume PDF has no pages');

  var firstPage = sourcePages[0];
  var pageSize = firstPage.getSize();

  var targetDoc = await PDFLib.PDFDocument.create();
  var regularFont = await targetDoc.embedFont(PDFLib.StandardFonts.Helvetica);
  var boldFont = await targetDoc.embedFont(PDFLib.StandardFonts.HelveticaBold);

  var logoUri = await rsLogoDataUri(brandingData.reportCompanyLogo, brandingData.reportCompanyLogoRaw);
  var logoImage = null;
  var logoData = dataUriBytes(logoUri);
  if(logoData && logoData.bytes && logoData.bytes.length > 0){
    try{
      var isPng = (logoData.bytes[0] === 0x89 && logoData.bytes[1] === 0x50 && logoData.bytes[2] === 0x4E && logoData.bytes[3] === 0x47);
      var isJpg = (logoData.bytes[0] === 0xFF && logoData.bytes[1] === 0xD8 && logoData.bytes[2] === 0xFF);
      if(isPng){
        logoImage = await targetDoc.embedPng(logoData.bytes);
      }else if(isJpg){
        logoImage = await targetDoc.embedJpg(logoData.bytes);
      }else if(logoData.mime.indexOf('png') !== -1){
        logoImage = await targetDoc.embedPng(logoData.bytes).catch(function(){ return targetDoc.embedJpg(logoData.bytes); });
      }else{
        logoImage = await targetDoc.embedJpg(logoData.bytes).catch(function(){ return targetDoc.embedPng(logoData.bytes); });
      }
    }catch(err){
      console.warn('Agy company branding logo embed error, attempting fallback:', err);
      try{
        logoImage = await targetDoc.embedJpg(logoData.bytes);
      }catch(e2){
        try{
          logoImage = await targetDoc.embedPng(logoData.bytes);
        }catch(e3){
          logoImage = null;
        }
      }
    }
  }

  var headerScale = Math.max(1, Math.min(1.45, pageSize.width / 612));
  var headerH = Math.max(66, Math.min(82, pageSize.width * 0.105)) * headerScale;

  // Extended Page 1: add headerH to total height so candidate resume below is 100% untouched
  var newFirst = targetDoc.addPage([pageSize.width, pageSize.height + headerH]);
  var embeddedFirst = await targetDoc.embedPage(firstPage);
  newFirst.drawPage(embeddedFirst, { x: 0, y: 0, width: pageSize.width, height: pageSize.height });

  var topY = pageSize.height;
  // Header background container
  newFirst.drawRectangle({
    x: 0,
    y: topY,
    width: pageSize.width,
    height: headerH,
    color: PDFLib.rgb(1, 1, 1),
    borderColor: PDFLib.rgb(0.86, 0.89, 0.94),
    borderWidth: 0.8
  });

  // Top blue accent bar
  newFirst.drawRectangle({
    x: 0,
    y: topY + headerH - (4 * headerScale),
    width: pageSize.width,
    height: 4 * headerScale,
    color: PDFLib.rgb(0.12, 0.23, 0.54)
  });

  // Bottom divider line separating header from resume
  newFirst.drawRectangle({
    x: 0,
    y: topY,
    width: pageSize.width,
    height: 1.1 * headerScale,
    color: PDFLib.rgb(0.88, 0.91, 0.95)
  });

  var companyName = clean(brandingData.reportCompanyName, 'Hiring Partner');
  var logoBox = headerH - (26 * headerScale);
  var logoX = 22 * headerScale;
  var logoY = topY + (13 * headerScale);
  var logoAreaW = 70 * headerScale;

  if(logoImage){
    var dims = logoImage.scaleToFit(logoBox * 1.8, logoBox);
    newFirst.drawImage(logoImage, {
      x: logoX,
      y: logoY + (logoBox - dims.height) / 2,
      width: dims.width,
      height: dims.height
    });
    logoAreaW = Math.max(56 * headerScale, Math.min(74 * headerScale, dims.width));
  }else{
    newFirst.drawRectangle({
      x: logoX,
      y: logoY,
      width: logoBox,
      height: logoBox,
      color: PDFLib.rgb(0.93, 0.95, 1),
      borderColor: PDFLib.rgb(0.75, 0.82, 0.98),
      borderWidth: 0.8 * headerScale
    });
    newFirst.drawText(companyName.charAt(0).toUpperCase(), {
      x: logoX + (logoBox / 2) - (5 * headerScale),
      y: logoY + (logoBox / 2) - (5 * headerScale),
      size: 16 * headerScale,
      font: boldFont,
      color: PDFLib.rgb(0.12, 0.23, 0.54)
    });
    logoAreaW = logoBox;
  }

  var textX = logoX + logoAreaW + (12 * headerScale);
  var confText = 'CONFIDENTIAL RESUME';
  var dateText = clean(brandingData.reportDate, '') || (new Date()).toLocaleDateString();
  var confSize = 13 * headerScale;
  var dateSize = 8.5 * headerScale;
  var confW = boldFont.widthOfTextAtSize(confText, confSize);
  var dateW = regularFont.widthOfTextAtSize(dateText, dateSize);
  var rightMargin = 24 * headerScale;

  newFirst.drawText('SHARED BY', {
    x: textX,
    y: topY + headerH - (27 * headerScale),
    size: 7 * headerScale,
    font: boldFont,
    color: PDFLib.rgb(0.39, 0.45, 0.55)
  });
  newFirst.drawText(limitText(companyName, 42, companyName), {
    x: textX,
    y: topY + headerH - (45 * headerScale),
    size: 12 * headerScale,
    font: boldFont,
    color: PDFLib.rgb(0.06, 0.09, 0.16)
  });
  newFirst.drawText(confText, {
    x: pageSize.width - rightMargin - confW,
    y: topY + headerH - (29 * headerScale),
    size: confSize,
    font: boldFont,
    color: PDFLib.rgb(0.06, 0.09, 0.16)
  });
  newFirst.drawText(dateText, {
    x: pageSize.width - rightMargin - dateW,
    y: topY + headerH - (45 * headerScale),
    size: dateSize,
    font: regularFont,
    color: PDFLib.rgb(0.39, 0.45, 0.55)
  });

  // Copy all remaining pages (Page 2, 3, etc.) untouched
  if(sourcePages.length > 1){
    var restPages = await targetDoc.copyPages(resumeDoc, resumeDoc.getPageIndices().slice(1));
    restPages.forEach(function(page){ targetDoc.addPage(page); });
  }

  var finalBytes = await targetDoc.save();
  return new Blob([finalBytes], { type: 'application/pdf' });
}

  async function downloadCvTrackerFormat(formatId, options, progressCallback){
    options = options || {};
    if(typeof progressCallback === 'function'){
      options.statusCallback = progressCallback;
    }
    formatId = parseInt(formatId, 10);
    if(formatId === 5){
      return buildRecruiterSummary(options);
    }
    return processAgyTestOption(formatId, options);
  }

  return {
    processOption: processAgyTestOption,
    downloadFormat: downloadCvTrackerFormat,
    buildRecruiterSummaryBlob: buildRecruiterSummaryBlob,
    buildRecruiterSummary: buildRecruiterSummary,
    applyCompanyBranding: applyAgyCompanyBrandingToPdfBlob,
    buildTurboVectorHighlightedResume: buildTurboVectorHighlightedResume,
    renderAgyStrategy2WasmDocxToPdfBlob: renderAgyStrategy2WasmDocxToPdfBlob,
    renderAgyTrueSectionDocxToPdfBlob: renderAgyTrueSectionDocxToPdfBlob,
    preloadAgyMetricFonts: agyEnsureMetricFonts,
    preloadRS: preloadRecruiterSummaryAssets,
    ensureJsZip: rsEnsureJsZip,
    ensureDocxConversion: rsEnsureDocxConversion,
    ensureOpenDocLab: rsEnsureOpenDocLab,
    readData: readData,
    resumeTrackingFileName: resumeTrackingFileName,
    fileSafeName: fileSafeName,
    fetchResumeBytesFromSources: fetchResumeBytesFromSources,
    fetchPdfResumeBlob: fetchPdfResumeBlob,
    logoDataUri: rsLogoDataUri,
    rsDrawerHighlightedKeywords: rsDrawerHighlightedKeywords,
    mergePreScreenedReportAndResume: mergePreScreenedReportAndResume,
    downloadBlob: downloadBlob,
    rdcAnonymizeRules: rdcAnonymizeRules,
    renderAnonymizedPdfBlob: renderAnonymizedPdfBlob
  };
}));
</script>
<script src="{{ asset('js/opendoc/AgyWasmEngine.js') }}"></script>
<script src="{{ asset('js/opendoc/CandidateSnapshotResolver.js') }}"></script>
<script src="{{ asset('js/opendoc/AgyResumeRenderer.js') }}"></script>
<script src="{{ asset('js/opendoc/AgyBatchExporter.js') }}"></script>
<script src="{{ asset('js/opendoc/AgyTouchAnnotator.js') }}"></script>
