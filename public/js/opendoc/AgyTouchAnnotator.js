/**
 * AgyTouchAnnotator — JERO Touch-Native Viewer & In-Browser Stylus Annotator (Phase 7)
 * 
 * Provides:
 * - Dual-touch pinch-to-zoom (1.0x - 3.0x) with hardware-accelerated CSS transforms
 * - Double-tap to reset zoom
 * - Non-destructive transparent vector ink overlay (<canvas class="rcv-annotation-canvas">)
 * - Pen, Highlighter, Precision Eraser, and Undo tools
 * - Stylus pressure/touch support for live interview feedback and scorecards
 * - Tenancy-isolated vector stroke export/import (preserves original resume binary untouched)
 */
(function(root, factory){
  if(typeof define === 'function' && define.amd){
    define([], factory);
  }else if(typeof module === 'object' && module.exports){
    module.exports = factory();
  }else{
    root.AgyTouchAnnotator = factory();
  }
})(typeof self !== 'undefined' ? self : this, function(){
  'use strict';

  var _activeInstance = null;

  function AgyTouchAnnotator(containerEl, options){
    this.container = typeof containerEl === 'string' ? document.getElementById(containerEl) : containerEl;
    if(!this.container){
      throw new Error('AgyTouchAnnotator: target container not found.');
    }
    this.options = options || {};
    this.scale = 1.0;
    this.panX = 0;
    this.panY = 0;
    this.currentTool = 'pen'; // 'pen', 'highlighter', 'eraser'
    this.strokeColor = '#2563eb'; // Royal Blue
    this.strokeSize = 2.5;
    this.isDrawing = false;
    this.strokes = []; // Array of { page, tool, color, size, points:[[x,y],...] }
    this.currentStroke = null;
    this.isMarkupActive = false;
    this.touchPinchStartDist = 0;
    this.touchPinchStartScale = 1.0;
    this.lastTapTime = 0;

    this.init();
  }

  AgyTouchAnnotator.prototype.init = function(){
    this.bindTouchGestures();
    this.createToolbar();
    _activeInstance = this;
  };

  /**
   * Binds pinch-to-zoom and pan gestures
   */
  AgyTouchAnnotator.prototype.bindTouchGestures = function(){
    var self = this;
    var el = this.container;

    el.addEventListener('touchstart', function(e){
      if(e.touches.length === 2){
        // Pinch gesture start
        var t1 = e.touches[0];
        var t2 = e.touches[1];
        self.touchPinchStartDist = Math.hypot(t2.clientX - t1.clientX, t2.clientY - t1.clientY);
        self.touchPinchStartScale = self.scale;
      } else if(e.touches.length === 1 && !self.isMarkupActive){
        // Check for double tap to reset zoom
        var now = Date.now();
        if(now - self.lastTapTime < 300){
          self.resetZoom();
          e.preventDefault();
        }
        self.lastTapTime = now;
      }
    }, { passive: false });

    el.addEventListener('touchmove', function(e){
      if(e.touches.length === 2){
        e.preventDefault();
        var t1 = e.touches[0];
        var t2 = e.touches[1];
        var dist = Math.hypot(t2.clientX - t1.clientX, t2.clientY - t1.clientY);
        if(self.touchPinchStartDist > 0){
          var newScale = (dist / self.touchPinchStartDist) * self.touchPinchStartScale;
          self.scale = Math.min(3.0, Math.max(1.0, newScale));
          self.applyTransform();
        }
      }
    }, { passive: false });

    el.addEventListener('touchend', function(e){
      if(e.touches.length < 2 && self.touchPinchStartDist > 0){
        self.touchPinchStartDist = 0;
        if(self.scale < 1.05){
          self.resetZoom();
        }
      }
    }, { passive: true });
  };

  AgyTouchAnnotator.prototype.applyTransform = function(){
    var pages = this.container.querySelectorAll('.rcv-page');
    var val = this.scale === 1.0 ? '' : 'scale(' + this.scale + ')';
    for(var i = 0; i < pages.length; i++){
      pages[i].style.transform = val;
      pages[i].style.transformOrigin = 'top center';
      pages[i].style.transition = this.touchPinchStartDist > 0 ? 'none' : 'transform 0.15s ease-out';
    }
  };

  AgyTouchAnnotator.prototype.resetZoom = function(){
    this.scale = 1.0;
    this.panX = 0;
    this.panY = 0;
    this.applyTransform();
  };

  /**
   * Mounts annotation overlays over all rendered pages in the container
   */
  AgyTouchAnnotator.prototype.mountPageOverlays = function(){
    var self = this;
    var pages = this.container.querySelectorAll('.rcv-page');
    
    pages.forEach(function(pageEl, idx){
      var pageNum = idx + 1;
      var existing = pageEl.querySelector('.rcv-annotation-canvas');
      if(existing) return;

      var baseCanvas = pageEl.querySelector('canvas');
      if(!baseCanvas) return;

      var w = parseInt(pageEl.style.width, 10) || baseCanvas.clientWidth || 800;
      var h = parseInt(pageEl.style.height, 10) || baseCanvas.clientHeight || 1100;

      var overlay = document.createElement('canvas');
      overlay.className = 'rcv-annotation-canvas';
      overlay.width = w * (window.devicePixelRatio || 1);
      overlay.height = h * (window.devicePixelRatio || 1);
      overlay.style.position = 'absolute';
      overlay.style.top = '0';
      overlay.style.left = '0';
      overlay.style.width = w + 'px';
      overlay.style.height = h + 'px';
      overlay.style.zIndex = '5';
      overlay.style.pointerEvents = self.isMarkupActive ? 'auto' : 'none';
      overlay.style.touchAction = 'none';

      pageEl.style.position = 'relative';
      pageEl.appendChild(overlay);

      self.bindCanvasEvents(overlay, pageNum);
    });
  };

  AgyTouchAnnotator.prototype.bindCanvasEvents = function(canvas, pageNum){
    var self = this;
    var ctx = canvas.getContext('2d');
    var dpr = window.devicePixelRatio || 1;

    function getPos(e){
      var rect = canvas.getBoundingClientRect();
      var clientX = e.touches && e.touches[0] ? e.touches[0].clientX : e.clientX;
      var clientY = e.touches && e.touches[0] ? e.touches[0].clientY : e.clientY;
      return {
        x: (clientX - rect.left) / (rect.width || 1),
        y: (clientY - rect.top) / (rect.height || 1)
      };
    }

    function startStroke(pos){
      self.isDrawing = true;
      self.currentStroke = {
        page: pageNum,
        tool: self.currentTool,
        color: self.currentTool === 'highlighter' ? 'rgba(254, 240, 138, 0.45)' : self.strokeColor,
        size: self.currentTool === 'highlighter' ? 16 : self.strokeSize,
        points: [pos]
      };
      self.redrawPage(canvas, pageNum);
    }

    function moveStroke(pos){
      if(!self.isDrawing || !self.currentStroke) return;
      self.currentStroke.points.push(pos);
      self.redrawPage(canvas, pageNum);
    }

    function endStroke(){
      if(!self.isDrawing) return;
      self.isDrawing = false;
      if(self.currentStroke && self.currentStroke.points.length > 1){
        self.strokes.push(self.currentStroke);
      }
      self.currentStroke = null;
      self.redrawPage(canvas, pageNum);
    }

    // Pointer / Touch Events
    canvas.addEventListener('pointerdown', function(e){
      if(!self.isMarkupActive) return;
      e.preventDefault();
      canvas.setPointerCapture(e.pointerId);
      startStroke(getPos(e));
    });

    canvas.addEventListener('pointermove', function(e){
      if(!self.isDrawing || !self.isMarkupActive) return;
      e.preventDefault();
      moveStroke(getPos(e));
    });

    canvas.addEventListener('pointerup', function(e){
      if(self.isMarkupActive) endStroke();
    });

    canvas.addEventListener('pointercancel', function(e){
      if(self.isMarkupActive) endStroke();
    });
  };

  AgyTouchAnnotator.prototype.redrawPage = function(canvas, pageNum){
    var ctx = canvas.getContext('2d');
    var w = canvas.width;
    var h = canvas.height;
    ctx.clearRect(0, 0, w, h);

    var allStrokes = this.strokes.filter(function(s){ return s.page === pageNum; });
    if(this.currentStroke && this.currentStroke.page === pageNum){
      allStrokes = allStrokes.concat([this.currentStroke]);
    }

    for(var i = 0; i < allStrokes.length; i++){
      var stroke = allStrokes[i];
      if(!stroke.points || stroke.points.length < 2) continue;

      ctx.save();
      ctx.beginPath();
      ctx.strokeStyle = stroke.color;
      ctx.lineWidth = stroke.size * (window.devicePixelRatio || 1);
      ctx.lineCap = 'round';
      ctx.lineJoin = 'round';

      if(stroke.tool === 'highlighter'){
        ctx.globalCompositeOperation = 'multiply';
      } else if(stroke.tool === 'eraser'){
        ctx.globalCompositeOperation = 'destination-out';
      } else {
        ctx.globalCompositeOperation = 'source-over';
      }

      ctx.moveTo(stroke.points[0].x * w, stroke.points[0].y * h);
      for(var p = 1; p < stroke.points.length; p++){
        ctx.lineTo(stroke.points[p].x * w, stroke.points[p].y * h);
      }
      ctx.stroke();
      ctx.restore();
    }
  };

  /**
   * Builds the clean Metronic floating pill toolbar
   */
  AgyTouchAnnotator.prototype.createToolbar = function(){
    var self = this;
    var existing = document.getElementById('rcvTouchMarkupToolbar');
    if(existing) existing.remove();

    var bar = document.createElement('div');
    bar.id = 'rcvTouchMarkupToolbar';
    bar.style.position = 'sticky';
    bar.style.top = '12px';
    bar.style.zIndex = '30';
    bar.style.display = 'none';
    bar.style.alignItems = 'center';
    bar.style.justifyContent = 'center';
    bar.style.gap = '8px';
    bar.style.margin = '0 auto 12px auto';
    bar.style.padding = '6px 14px';
    bar.style.background = 'rgba(255, 255, 255, 0.96)';
    bar.style.backdropFilter = 'blur(8px)';
    bar.style.border = '1px solid #e2e8f0';
    bar.style.borderRadius = '9999px';
    bar.style.boxShadow = '0 4px 14px rgba(0, 0, 0, 0.08)';
    bar.style.fontFamily = 'Poppins, sans-serif';
    bar.style.fontSize = '12px';
    bar.style.maxWidth = 'fit-content';

    bar.innerHTML = [
      '<button type="button" id="rcvBtnPen" class="btn btn-sm btn-icon btn-active-light-primary" title="Ballpoint Pen" style="border-radius: 9999px; width: 32px; height: 32px; background: #e0f2fe; color: #0284c7;">',
      '  <i class="fa fa-pen" style="font-size: 11px;"></i>',
      '</button>',
      '<button type="button" id="rcvBtnHighlighter" class="btn btn-sm btn-icon btn-active-light-warning" title="Highlighter" style="border-radius: 9999px; width: 32px; height: 32px; color: #d97706;">',
      '  <i class="fa fa-highlighter" style="font-size: 11px;"></i>',
      '</button>',
      '<button type="button" id="rcvBtnEraser" class="btn btn-sm btn-icon btn-active-light-danger" title="Eraser" style="border-radius: 9999px; width: 32px; height: 32px; color: #dc2626;">',
      '  <i class="fa fa-eraser" style="font-size: 11px;"></i>',
      '</button>',
      '<div style="width: 1px; height: 18px; background: #e2e8f0; margin: 0 4px;"></div>',
      '<button type="button" id="rcvBtnUndo" class="btn btn-sm btn-light btn-active-secondary" style="border-radius: 9999px; padding: 4px 10px; font-size: 11px; font-weight: 500;">',
      '  <i class="fa fa-undo" style="font-size: 10px; margin-right: 4px;"></i> Undo',
      '</button>',
      '<button type="button" id="rcvBtnClear" class="btn btn-sm btn-light-danger" style="border-radius: 9999px; padding: 4px 10px; font-size: 11px; font-weight: 500;">',
      '  Clear Notes',
      '</button>'
    ].join('');

    this.container.parentNode.insertBefore(bar, this.container);
    this.toolbarEl = bar;

    // Button event listeners
    bar.querySelector('#rcvBtnPen').addEventListener('click', function(){
      self.currentTool = 'pen';
      self.highlightActiveTool('rcvBtnPen');
    });

    bar.querySelector('#rcvBtnHighlighter').addEventListener('click', function(){
      self.currentTool = 'highlighter';
      self.highlightActiveTool('rcvBtnHighlighter');
    });

    bar.querySelector('#rcvBtnEraser').addEventListener('click', function(){
      self.currentTool = 'eraser';
      self.highlightActiveTool('rcvBtnEraser');
    });

    bar.querySelector('#rcvBtnUndo').addEventListener('click', function(){
      self.undo();
    });

    bar.querySelector('#rcvBtnClear').addEventListener('click', function(){
      if(confirm('Clear all interview notes on this resume?')){
        self.clear();
      }
    });
  };

  AgyTouchAnnotator.prototype.highlightActiveTool = function(btnId){
    var btns = ['rcvBtnPen', 'rcvBtnHighlighter', 'rcvBtnEraser'];
    var self = this;
    btns.forEach(function(id){
      var btn = self.toolbarEl.querySelector('#' + id);
      if(!btn) return;
      if(id === btnId){
        btn.style.background = '#e0f2fe';
        btn.style.boxShadow = '0 0 0 2px #0284c7';
      } else {
        btn.style.background = 'transparent';
        btn.style.boxShadow = 'none';
      }
    });
  };

  AgyTouchAnnotator.prototype.toggleMarkup = function(active){
    this.isMarkupActive = typeof active === 'boolean' ? active : !this.isMarkupActive;
    if(this.toolbarEl){
      this.toolbarEl.style.display = this.isMarkupActive ? 'flex' : 'none';
    }

    var overlays = this.container.querySelectorAll('.rcv-annotation-canvas');
    for(var i = 0; i < overlays.length; i++){
      overlays[i].style.pointerEvents = this.isMarkupActive ? 'auto' : 'none';
    }
  };

  AgyTouchAnnotator.prototype.undo = function(){
    if(this.strokes.length === 0) return;
    this.strokes.pop();
    var self = this;
    var pages = this.container.querySelectorAll('.rcv-page');
    pages.forEach(function(pageEl, idx){
      var canvas = pageEl.querySelector('.rcv-annotation-canvas');
      if(canvas) self.redrawPage(canvas, idx + 1);
    });
  };

  AgyTouchAnnotator.prototype.clear = function(){
    this.strokes = [];
    var self = this;
    var pages = this.container.querySelectorAll('.rcv-page');
    pages.forEach(function(pageEl, idx){
      var canvas = pageEl.querySelector('.rcv-annotation-canvas');
      if(canvas){
        var ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
      }
    });
  };

  AgyTouchAnnotator.prototype.exportStrokes = function(){
    return JSON.stringify(this.strokes);
  };

  AgyTouchAnnotator.prototype.importStrokes = function(strokesJson){
    try {
      this.strokes = typeof strokesJson === 'string' ? JSON.parse(strokesJson) : strokesJson;
      var self = this;
      var pages = this.container.querySelectorAll('.rcv-page');
      pages.forEach(function(pageEl, idx){
        var canvas = pageEl.querySelector('.rcv-annotation-canvas');
        if(canvas) self.redrawPage(canvas, idx + 1);
      });
    } catch(e){
      console.warn('AgyTouchAnnotator: import failed:', e);
    }
  };

  return AgyTouchAnnotator;
});
