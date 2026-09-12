# JERO OpenDoc WASM Engine — Phase 7 Detailed Implementation Plan
## Mobile/Tablet Touch-Native Document Reader, In-Browser Stylus Annotation & Universal Document Converter

> **Document Status:** Complete Architecture & Implementation Specification  
> **Target Version:** Laravel 11 / OpenDoc WASM 2.0 / Client-Side Engine  
> **Target Audience:** Core Engineering, Mobile/Tablet UX, ATS Architects, Compliance  
> **Master Reference:** [docs/OPENDOC_WASM_MASTER_IMPLEMENTATION_PLAN.md](OPENDOC_WASM_MASTER_IMPLEMENTATION_PLAN.md)  
> **Phase 3 Reference:** [docs/OPENDOC_WASM_PHASE_3_DETAILED_IMPLEMENTATION_PLAN.md](OPENDOC_WASM_PHASE_3_DETAILED_IMPLEMENTATION_PLAN.md)  
> **Phase 4 Reference:** [docs/OPENDOC_WASM_PHASE_4_DETAILED_IMPLEMENTATION_PLAN.md](OPENDOC_WASM_PHASE_4_DETAILED_IMPLEMENTATION_PLAN.md)  
> **Phase 5 Reference:** [docs/OPENDOC_WASM_PHASE_5_DETAILED_IMPLEMENTATION_PLAN.md](OPENDOC_WASM_PHASE_5_DETAILED_IMPLEMENTATION_PLAN.md)  
> **Phase 6 Reference:** [docs/OPENDOC_WASM_PHASE_6_DETAILED_IMPLEMENTATION_PLAN.md](OPENDOC_WASM_PHASE_6_DETAILED_IMPLEMENTATION_PLAN.md)  
> **Rule Adherence:** Multi-Tenancy Scoped (`company_id`), Zero LibreOffice Server Load, Metronic UI Standard, Poppins Font $\le$ 14px

---

## 1. Executive Summary & Core Objectives

Phase 7 brings high-fidelity, touch-native document viewing, stylus ink markup, and universal client-side format conversion to JERO across mobile devices, iPads, and touch-enabled laptops.

In modern hiring workflows, hiring managers, interviewing executives, and senior recruiters frequently review candidate resumes while commuting on smartphones or during live panel interviews using tablets (e.g. Apple iPad with Apple Pencil, Microsoft Surface, Android tablets).

Phase 7 resolves three systemic mobile limitations:
1. **Elimination of Third-Party Viewers:** Renders DOCX and PDF documents natively via WebAssembly directly onto HTML5 `<canvas>` elements, eliminating buggy third-party iframes (`docs.google.com/gview`, Office Online) that fail on corporate VPNs or leak candidate data.
2. **In-Browser Stylus & Touch Annotator:** Equips interviewers with a lightweight vector ink overlay layer. Interviewers can handwrite interview notes, underline key skills, and draw margin checkmarks during interviews without altering the candidate's original resume file.
3. **Universal Client-Side Document Converter:** Extends `AgyWasmEngine.js` with instant in-browser conversion primitives:
   - DOCX $\rightarrow$ Vector PDF
   - DOCX/PDF $\rightarrow$ PNG/WebP page image array (for instant thumbnail cards)
   - DOCX $\rightarrow$ Structured clean plain text (for mobile low-bandwidth reading mode)

---

## 2. Feature 1: Touch-Native Reader with Pinch-to-Zoom & Inertia Pan

### 2.1 The Mobile Reading Problem
Standard mobile browsers often struggle with complex canvas rendering: pinch-zooming can scale the entire browser viewport (breaking UI headers and buttons) or produce pixelated, blurry text.

### 2.2 Dual-Touch Gesture Engine Architecture

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                       TOUCH GESTURE EVENT PIPELINE                          │
├─────────────────────────────────────────────────────────────────────────────┤
│ 1. User touches resume page canvas with 1 or 2 fingers                      │
│       │                                                                     │
│       ▼                                                                     │
│ 2. Touch Event Classifier:                                                  │
│    ├── Single Touch:                                                        │
│    │     - If Markup Mode == OFF: Standard vertical inertia scrolling       │
│    │     - If Markup Mode == ON: Stylus / Finger ink drawing                │
│    │                                                                        │
│    └── Dual Touch (Pinch / Pan):                                            │
│          - Calculate initial Euclidean distance:                            │
│            dist0 = Math.hypot(x2 - x1, y2 - y1)                             │
│          - Calculate midpoint center of pinch:                              │
│            midX = (x1 + x2) / 2; midY = (y1 + y2) / 2                       │
│       │                                                                     │
│       ▼                                                                     │
│ 3. Continuous TouchMove (60 FPS Hardware Accelerated):                      │
│    - Compute dynamic scale factor: currentScale = clamp(scale * (dist/dist0), 1.0, 3.0)│
│    - Apply CSS transform matrix: transform: scale(...) translate3d(...)     │
│       │                                                                     │
│       ▼                                                                     │
│ 4. TouchEnd (Gesture Settle):                                               │
│    - If scale < 1.05: Smooth spring animation back to 1.0 (auto-fit width)  │
│    - If scale >= 1.05: Keep zoomed state with pan bounds enforcement        │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. Feature 2: In-Browser Stylus & Touch Annotator (Interview Markup)

### 3.1 Non-Destructive Vector Overlay Layer
Annotations must never modify the candidate's original resume binary stored on Cloudflare R2.
Phase 7 mounts a dedicated `<canvas class="rcv-annotation-canvas">` over each page in the document stack:

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                      PAGE CONTAINER STACK ARCHITECTURE                      │
├─────────────────────────────────────────────────────────────────────────────┤
│ <div class="rcv-page" style="position: relative;">                          │
│   ├── <canvas class="rcv-base-canvas"> <!-- Base Rendered Resume -->        │
│   ├── <div class="rcv-textlayer">      <!-- Selectable Text Layer -->       │
│   └── <canvas class="rcv-annotation-canvas" style="position: absolute;">   │
│           <!-- Transparent Stylus Ink & Highlight Overlay -->               │
│ </div>                                                                      │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 3.2 Stylus & Pen Toolset
An unobtrusive floating pill toolbar is available on touch devices or via a "Markup & Notes" toggle:
- ✏️ **Ballpoint Pen (Blue `#2563eb` or Black `#0f172a`):** For handwriting candidate feedback and comments.
- 🖊️ **Vector Highlighter (`rgba(254, 240, 138, 0.45)`):** Semi-transparent marker for emphasizing resume claims.
- 🧹 **Precision Stroke Eraser:** Removes intersecting vector stroke paths.
- 🗑️ **Clear Page Notes:** Discards all annotations on the current page with confirmation.

### 3.3 Stroke Serialization & Tenancy Scoping
Strokes are captured as resolution-independent normalized coordinates ($0.0 \rightarrow 1.0$ relative to page width and height):
```json
{
  "page": 1,
  "tool": "pen",
  "color": "#2563eb",
  "width": 2,
  "points": [
    [0.142, 0.251],
    [0.145, 0.254],
    [0.148, 0.259]
  ]
}
```
- **Tenancy Scoping:** Annotations are associated with `job_application_id` and the interviewer's `company_id`. Partner companies on the talent marketplace can never view another company's interview markup.

---

## 4. Feature 3: Universal Client-Side Multi-Format Converter

Phase 7 adds standard, production-ready conversion methods to `AgyWasmEngine.js`:

```javascript
/**
 * Universal Document Converter API in AgyWasmEngine
 */

// 1. Convert any DOCX or PDF into an array of high-resolution PNG/WebP page blobs
const pageImages = await AgyWasmEngine.renderToPageImages(documentBytes, {
  dpi: 150, // Rapid rendering for mobile cards
  format: 'image/webp' // Ultra-compressed mobile format
});

// 2. Extract structured plain text and headings in browser memory
const structuredText = await AgyWasmEngine.extractStructuredText(documentBytes);

// 3. Compute exact responsive viewport dimensions for mobile devices
const viewport = AgyWasmEngine.calculateResponsiveViewport(
  pageBaseW, 
  pageBaseH, 
  window.innerWidth, 
  isMobileDevice
);
```

---

## 5. UI/UX Specifications (Metronic Standard)

- **Mobile Viewport Optimization:** Auto-scales document page width to `100vw - 16px` on screens $< 768\text{px}$, eliminating horizontal scrollbars on default view.
- **Floating Markup Toolbar:** Sleek, semi-transparent pill toolbar (`background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(8px); border: 1px solid #e2e8f0; border-radius: 9999px`) pinned to the top right of the viewer.
- **Typography:** Poppins font family across all tooltips, modals, and labels ($\le 14\text{px}$).
- **SVG Iconography:** Clean vector icons for Pen, Highlighter, Eraser, Zoom-In, and Zoom-Out. Zero 3D elements.

---

## 6. Performance Budget & Memory Management

| Metric | Target Budget | Enforcement Mechanism |
| :--- | :--- | :--- |
| **Touch Drawing Frame Rate** | $60\text{ FPS}$ | Uses `requestAnimationFrame` for stroke interpolation; avoids canvas re-allocations |
| **Pinch-to-Zoom Latency** | $< 16\text{ms}$ | Uses CSS3 hardware-accelerated transforms during active pinch; re-renders only on settle |
| **Annotation Memory Overhead** | $< 2\text{MB}$ per document | Stores vector points in compact Float32 arrays |
| **Page Image Conversion** | $< 400\text{ms}$ per page | Offscreen canvas rendering with deterministic memory release |

---

## 7. Verification & Acceptance Checklist

- [ ] **Mobile Responsive Auto-Fit:** Verify on mobile/tablet viewports that document pages auto-fit to container width without horizontal clipping.
- [ ] **Dual-Touch Pinch-to-Zoom:** Verify that pinching two fingers smoothly scales document canvas up to 300% and snaps cleanly back without page jitter.
- [ ] **Stylus / Touch Ink Drawing:** Confirm that writing with a stylus or finger produces smooth, anti-aliased vector strokes on the annotation canvas.
- [ ] **Non-Destructive Guarantee:** Verify that closing and reopening the resume or clicking Option 1 downloads the pristine, unmodified original resume from Cloudflare R2.
- [ ] **Universal Converter Engine:** Confirm that `AgyWasmEngine.renderToPageImages` produces clean page image blobs in browser memory.
- [ ] **Zero Server CPU:** Verify that all gesture processing, canvas scaling, and ink drawings run 100% on the client.
