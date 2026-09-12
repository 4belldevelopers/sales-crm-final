/**
 * AgyWasmEngine — JERO Client-Side OpenDoc WebAssembly Document Engine
 * 
 * Provides high-fidelity client-side document processing:
 * - Word (.docx) to PDF conversion via OpenDoc WASM at 288 DPI
 * - Exact-coordinate keyword highlight burning without character bleed
 * - Vector Page 1 corporate branding header injection without content downscaling
 * - Client-side PII masking and redaction
 * - Deterministic linear memory freeing to prevent browser memory leaks
 * 
 * Part of JERO GOD-Level ATS & Marketplace Engine (Phase 3)
 */
(function(root, factory){
  var mod = factory();
  if(typeof define === 'function' && define.amd){
    try { define([], function(){ return mod; }); } catch(e){}
  }
  if(typeof module === 'object' && module.exports){
    module.exports = mod;
  }
  root.AgyWasmEngine = mod;
  if(typeof window !== 'undefined'){
    window.AgyWasmEngine = mod;
  }
})(typeof self !== 'undefined' ? self : this, function(){
  'use strict';

  var _wasmModule = null;
  var _initPromise = null;

  var AgyWasmEngine = {
    version: '2.0.0-wasm',

    /**
     * Initializes the OpenDoc WebAssembly runtime (cached after first call)
     * @param {Object} [options]
     * @returns {Promise<Object>}
     */
    init: function(options){
      if(_wasmModule) return Promise.resolve(_wasmModule);
      if(_initPromise) return _initPromise;

      options = options || {};
      var openDocSources = [
        {
          label: 'Local OpenDoc runtime',
          moduleUrl: '/js/opendoc/casual_doc_wasm.js',
          wasmUrl: '/js/opendoc/casual_doc_wasm_bg.wasm'
        },
        {
          label: 'Cloudflare OpenDoc runtime',
          moduleUrl: 'https://file.jero.works/js/opendoc/casual_doc_wasm.js?v=opendoc-20260903',
          wasmUrl: 'https://file.jero.works/js/opendoc/casual_doc_wasm_bg.wasm?v=opendoc-20260903'
        }
      ];

      async function loadModule(src){
        var module = null;
        var blobUrl = '';
        try{
          module = await import(src.moduleUrl);
        }catch(e){
          var res = await fetch(src.moduleUrl, { mode: 'cors', cache: 'force-cache' });
          if(!res.ok) throw new Error('Failed to load module script: ' + res.status);
          var code = await res.text();
          code = code.replace(/new URL\(['"]casual_doc_wasm_bg\.wasm['"],\s*import\.meta\.url\)/g, JSON.stringify(src.wasmUrl));
          blobUrl = URL.createObjectURL(new Blob([code], { type: 'application/javascript' }));
          module = await import(blobUrl);
        }
        if(typeof module.default === 'function'){
          var wasmRes = await fetch(src.wasmUrl, { mode: 'cors', cache: 'force-cache' });
          if(!wasmRes.ok) throw new Error('Failed to load wasm binary: ' + wasmRes.status);
          var wasmBytes = await wasmRes.arrayBuffer();
          await module.default({ module_or_path: wasmBytes });
        }
        if(blobUrl) setTimeout(function(){ URL.revokeObjectURL(blobUrl); }, 30000);
        return module;
      }

      _initPromise = (async function(){
        var lastError = null;
        for(var i = 0; i < openDocSources.length; i++){
          try{
            _wasmModule = await loadModule(openDocSources[i]);
            return _wasmModule;
          }catch(err){
            lastError = err;
            console.warn('AgyWasmEngine source [' + openDocSources[i].label + '] failed:', err);
          }
        }
        throw (lastError || new Error('OpenDoc WASM runtime failed to initialize.'));
      })().catch(function(err){
        _initPromise = null;
        throw err;
      });

      return _initPromise;
    },

    /**
     * Frees a WASM document instance deterministically to release linear memory
     * @param {Object} doc
     */
    free: function(doc){
      if(!doc) return;
      try{
        if(typeof doc.free === 'function') doc.free();
        else if(typeof doc.delete === 'function') doc.delete();
      }catch(e){
        console.warn('AgyWasmEngine: memory free ignored:', e);
      }
    },

    /**
     * Yield execution to browser event loop to ensure smooth 60 FPS rendering
     * @returns {Promise<void>}
     */
    yieldToMain: function(){
      if(typeof window !== 'undefined' && window.scheduler && typeof window.scheduler.yield === 'function'){
        return window.scheduler.yield().catch(function(){
          return new Promise(function(r){ setTimeout(r, 0); });
        });
      }
      return new Promise(function(r){ setTimeout(r, 0); });
    },

    /**
     * Converts a DOCX binary array into a high-fidelity PDF Blob via client-side OpenDoc WASM
     * @param {Uint8Array|ArrayBuffer} docxBytes
     * @param {Object} [options]
     * @returns {Promise<Blob>}
     */
    renderDocxToPdfBlob: async function(docxBytes, options){
      options = options || {};
      if(typeof window.__renderAgyStrategy2WasmDocxToPdfBlob === 'function'){
        return window.__renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, options);
      }
      throw new Error('AgyWasmEngine: renderer bridge not mounted in active window.');
    },

    /**
     * Injects corporate branding header on Page 1 without squashing or clipping resume content
     * @param {Blob} pdfBlob
     * @param {Object} brandingData
     * @returns {Promise<Blob>}
     */
    applyCompanyBranding: async function(pdfBlob, brandingData){
      if(typeof window.__applyAgyCompanyBrandingToPdfBlob === 'function'){
        return window.__applyAgyCompanyBrandingToPdfBlob(pdfBlob, brandingData);
      }
      throw new Error('AgyWasmEngine: branding bridge not mounted in active window.');
    },

    /**
     * Anonymizes candidate PII (name, phone, email, contacts) client-side
     * @param {Blob} pdfBlob
     * @param {Object} rules
     * @returns {Promise<Blob>}
     */
    anonymizePdf: async function(pdfBlob, rules){
      if(typeof window.renderAnonymizedPdfBlob === 'function'){
        return window.renderAnonymizedPdfBlob(pdfBlob, rules);
      }
      throw new Error('AgyWasmEngine: anonymizer bridge not mounted in active window.');
    },

    /**
     * Computes the exact responsive scaling and dimensions for mobile/tablet screens
     * @param {number} baseW - Original page width in twips or pts
     * @param {number} baseH - Original page height in twips or pts
     * @param {number} containerW - Available container width in pixels
     * @param {boolean} [isMobile] - True if rendering on mobile viewport
     * @returns {{scale: number, width: number, height: number}}
     */
    calculateResponsiveViewport: function(baseW, baseH, containerW, isMobile){
      var padding = isMobile ? 8 : 24;
      var targetW = Math.max(300, Math.min(containerW - padding, 860));
      var scale = targetW / baseW;
      return {
        scale: scale,
        width: Math.floor(baseW * scale),
        height: Math.floor(baseH * scale)
      };
    },

    /**
     * Converts a document (PDF or DOCX) into an array of high-resolution page image blobs
     * @param {Uint8Array|ArrayBuffer|Blob} source
     * @param {Object} [options] - { dpi: 150, format: 'image/png'|'image/webp', quality: 0.92 }
     * @returns {Promise<Array<{page: number, blob: Blob, dataUrl: string, width: number, height: number}>>}
     */
    renderToPageImages: async function(source, options){
      options = options || {};
      var dpi = options.dpi || 150;
      var format = options.format || 'image/png';
      var quality = typeof options.quality === 'number' ? options.quality : 0.92;

      var pdfBytes = null;
      if(source instanceof Blob){
        var ab = await source.arrayBuffer();
        pdfBytes = new Uint8Array(ab);
      } else if(source instanceof ArrayBuffer){
        pdfBytes = new Uint8Array(source);
      } else if(source instanceof Uint8Array){
        pdfBytes = source;
      }

      // Check if DOCX (starts with PK\x03\x04)
      if(pdfBytes && pdfBytes[0] === 0x50 && pdfBytes[1] === 0x4B){
        var pdfBlob = await AgyWasmEngine.renderDocxToPdfBlob(pdfBytes, options);
        var pAb = await pdfBlob.arrayBuffer();
        pdfBytes = new Uint8Array(pAb);
      }

      if(typeof pdfjsLib === 'undefined'){
        throw new Error('AgyWasmEngine: PDF.js library required for page image rendering.');
      }

      var doc = await pdfjsLib.getDocument({ data: pdfBytes }).promise;
      var pages = [];
      var scale = dpi / 72;

      for(var p = 1; p <= doc.numPages; p++){
        var page = await doc.getPage(p);
        var viewport = page.getViewport({ scale: scale });
        var canvas = document.createElement('canvas');
        canvas.width = Math.floor(viewport.width);
        canvas.height = Math.floor(viewport.height);
        var ctx = canvas.getContext('2d');

        await page.render({ canvasContext: ctx, viewport: viewport }).promise;

        var blob = await new Promise(function(resolve){
          canvas.toBlob(resolve, format, quality);
        });

        pages.push({
          page: p,
          blob: blob,
          dataUrl: canvas.toDataURL(format, quality),
          width: canvas.width,
          height: canvas.height
        });

        // Deterministic memory cleanup
        canvas.width = 0;
        canvas.height = 0;
        await AgyWasmEngine.yieldToMain();
      }

      return pages;
    },

    /**
     * Extracts structured text from a document in browser memory
     * @param {Uint8Array|ArrayBuffer|Blob} source
     * @returns {Promise<{text: string, pages: string[], wordCount: number}>}
     */
    extractStructuredText: async function(source){
      var bytes = null;
      if(source instanceof Blob){
        bytes = new Uint8Array(await source.arrayBuffer());
      } else if(source instanceof ArrayBuffer){
        bytes = new Uint8Array(source);
      } else if(source instanceof Uint8Array){
        bytes = source;
      }

      // If DOCX and helper is available
      if(bytes && bytes[0] === 0x50 && bytes[1] === 0x4B && typeof window.extractDocxText === 'function'){
        var dText = await window.extractDocxText(bytes);
        return {
          text: dText || '',
          pages: [(dText || '')],
          wordCount: (dText || '').trim().split(/\s+/).filter(Boolean).length
        };
      }

      if(typeof pdfjsLib === 'undefined'){
        throw new Error('AgyWasmEngine: PDF.js library required for text extraction.');
      }

      var doc = await pdfjsLib.getDocument({ data: bytes }).promise;
      var pageTexts = [];
      var fullText = '';

      for(var p = 1; p <= doc.numPages; p++){
        var page = await doc.getPage(p);
        var tc = await page.getTextContent();
        var pText = tc.items.map(function(i){ return i.str; }).join(' ');
        pageTexts.push(pText);
        fullText += pText + '\n';
      }

      return {
        text: fullText.trim(),
        pages: pageTexts,
        wordCount: fullText.trim().split(/\s+/).filter(Boolean).length
      };
    }
  };

  return AgyWasmEngine;
});
