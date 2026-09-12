/**
 * AgyBatchExporter — JERO High-Throughput In-Memory Candidate Batch Exporter
 * 
 * Capabilities:
 * - Client-side batch processing with concurrency limit = 2
 * - In-memory ZIP archive assembly via JSZip (zero server disk storage)
 * - Deterministic memory recycling to prevent browser memory bloat
 * - Recruiter-friendly progress reporting: "Downloading Resumes (X of Y completed - Z%)..."
 * 
 * Part of JERO GOD-Level ATS & Marketplace Engine (Phase 3)
 */
(function(root, factory){
  if(typeof define === 'function' && define.amd){
    define(['JSZip'], factory);
  }else if(typeof module === 'object' && module.exports){
    module.exports = factory(require('jszip'));
  }else{
    root.AgyBatchExporter = factory(root.JSZip);
  }
})(typeof self !== 'undefined' ? self : this, function(JSZip){
  'use strict';

  function safeString(val, fallback){
    return String(val == null ? (fallback || '') : val).trim();
  }

  function fileSafeName(val, fallback){
    var cleaned = safeString(val, fallback || 'Candidate').replace(/[^\w\s.-]/g, '').replace(/\s+/g, '_');
    return cleaned || 'Candidate';
  }

  var AgyBatchExporter = {
    version: '1.0.0-batch',

    /**
     * Ensures JSZip is loaded in the browser
     * @returns {Promise<Object>}
     */
    ensureJsZip: async function(){
      if(typeof window !== 'undefined' && window.JSZip) return window.JSZip;
      if(typeof JSZip !== 'undefined') return JSZip;
      if(typeof window.__rsEnsureJsZip === 'function'){
        return window.__rsEnsureJsZip();
      }
      return new Promise(function(resolve, reject){
        var s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js';
        s.onload = function(){ resolve(window.JSZip); };
        s.onerror = function(){ reject(new Error('Could not load JSZip library.')); };
        document.head.appendChild(s);
      });
    },

    /**
     * Executes a batch export of multiple candidates into a single client-side ZIP file
     * @param {Array<Object>} candidates
     * @param {Object} [options]
     * @param {Function} [onProgress]
     * @returns {Promise<Blob>}
     */
    exportZip: async function(candidates, options, onProgress){
      candidates = Array.isArray(candidates) ? candidates : [];
      if(!candidates.length) throw new Error('No candidates selected for batch export.');

      options = options || {};
      var zipLib = await this.ensureJsZip();
      var zip = new zipLib();
      var total = candidates.length;
      var completed = 0;
      var successful = 0;
      var failures = [];

      var concurrency = Math.max(1, Math.min(2, options.concurrency || 2));
      var folderName = 'Jeroworks_Batch_Export_' + (new Date()).toISOString().slice(0, 10);
      var folder = zip.folder(folderName);

      function updateProgress(){
        if(typeof onProgress === 'function'){
          var pct = Math.round((completed / total) * 100);
          onProgress('Downloading Resumes (' + completed + ' of ' + total + ' completed - ' + pct + '%)...', pct, completed, total);
        }
      }

      updateProgress();

      // Worker queue with controlled concurrency limit
      var index = 0;
      async function worker(){
        while(index < candidates.length){
          var i = index++;
          var cand = candidates[i];
          try{
            var pdfBlob = null;
            var ext = safeString(cand.resumeExt, 'PDF').toUpperCase();

            if(typeof cand.getPdfBlob === 'function'){
              pdfBlob = await cand.getPdfBlob();
            }else if(cand.pdfBlob instanceof Blob){
              pdfBlob = cand.pdfBlob;
            }else if(cand.docxBytes && typeof window.__renderAgyStrategy2WasmDocxToPdfBlob === 'function'){
              pdfBlob = await window.__renderAgyStrategy2WasmDocxToPdfBlob(cand.docxBytes, {
                burnHighlights: !!cand.burnHighlights,
                keywords: cand.keywords || []
              });
            }else if(cand.resumeUrl){
              var res = await fetch(cand.resumeUrl, { cache: 'force-cache' });
              if(res.ok){
                var ab = await res.arrayBuffer();
                if(ext === 'PDF'){
                  pdfBlob = new Blob([ab], { type: 'application/pdf' });
                }else if(ext === 'DOCX' && typeof window.__renderAgyStrategy2WasmDocxToPdfBlob === 'function'){
                  pdfBlob = await window.__renderAgyStrategy2WasmDocxToPdfBlob(ab, {
                    burnHighlights: !!cand.burnHighlights,
                    keywords: cand.keywords || []
                  });
                }
              }
            }

            if(pdfBlob instanceof Blob){
              var fileName = fileSafeName(cand.name, 'Candidate_' + (i + 1)) + (cand.role ? '_' + fileSafeName(cand.role) : '') + '.pdf';
              folder.file(fileName, pdfBlob);
              successful++;
            }else{
              failures.push({ name: cand.name || 'Candidate #' + (i + 1), reason: 'Document unavailable or unsupported' });
            }
          }catch(err){
            console.warn('Batch candidate ' + (i + 1) + ' failed:', err);
            failures.push({ name: cand.name || 'Candidate #' + (i + 1), reason: err.message || 'Conversion error' });
          }finally{
            completed++;
            updateProgress();
            // Yield to main thread for responsive browser UI
            await new Promise(function(r){ setTimeout(r, 10); });
          }
        }
      }

      var workers = [];
      for(var w = 0; w < concurrency; w++){
        workers.push(worker());
      }
      await Promise.all(workers);

      // Create summary manifest inside the ZIP
      var manifest = 'JERO ATS - BATCH EXPORT MANIFEST\n' +
        'Export Date: ' + (new Date()).toLocaleString() + '\n' +
        'Total Candidates Selected: ' + total + '\n' +
        'Successfully Packaged: ' + successful + '\n' +
        'Skipped / Errors: ' + failures.length + '\n\n';

      if(failures.length){
        manifest += '--- Skipped Candidates ---\n';
        failures.forEach(function(f){
          manifest += '- ' + f.name + ': ' + f.reason + '\n';
        });
      }
      folder.file('manifest.txt', manifest);

      if(typeof onProgress === 'function'){
        onProgress('Downloading Resumes (Compressing ZIP archive)...', 98, completed, total);
      }

      var zipBlob = await zip.generateAsync({
        type: 'blob',
        compression: 'DEFLATE',
        compressionOptions: { level: 6 }
      });

      if(options.autoDownload !== false){
        var zipName = folderName + '.zip';
        var url = URL.createObjectURL(zipBlob);
        var a = document.createElement('a');
        a.href = url;
        a.download = zipName;
        document.body.appendChild(a);
        a.click();
        setTimeout(function(){
          URL.revokeObjectURL(url);
          if(a.parentNode) a.parentNode.removeChild(a);
        }, 2000);
      }

      if(typeof onProgress === 'function'){
        onProgress('Downloading Resumes (100%)...', 100, completed, total);
      }

      return zipBlob;
    }
  };

  return AgyBatchExporter;
});
