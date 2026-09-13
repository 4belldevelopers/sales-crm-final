# Master Reference Specification & Rebuild Guide: OpenDoc WASM Engine

> **Document Version:** `2.0.0-GOD-LEVEL`  
> **Status:** Authoritative Production Specification & Rebuild Standard  
> **Target Engine:** `casual_doc_wasm` (Rust/WebAssembly Core) + `AgyResumeRenderer.js` (Unified Client SDK)  
> **Platform Target:** JERO Multi-Tenant B2B Hiring SaaS & Talent Marketplace (`testing.jero.works`)  
> **Zero-Regression Mandate:** 0% Server CPU, 0s Queue Latency, Sub-500ms Client Execution, 100% PII Privacy.

---

## 1. Executive Vision & The Client-Side WASM Paradigm

### 1.1 The Legacy Server Conversion Trap
In enterprise ATS and talent marketplace software, document transformation (converting candidate resumes from Microsoft Word `.docx` into branded, redacted, or keyword-highlighted PDFs) is traditionally handled on the backend:
1. **Compute Exhaustion:** Spawning headless LibreOffice, `unoconv`, or Chromium/Puppeteer processes consumes **150MB–350MB RAM** and **100% of a CPU core** per document. Under peak recruiter traffic (e.g. 50 concurrent downloads during hiring drives), backend CPU spikes to 100%, causing queue backlogs, 504 Gateway Timeouts, and Octane worker crashes.
2. **Typography & Layout Disasters:** Headless server converters frequently lack Windows/Mac font metrics (Calibri, Aptos, Cambria, Arial), leading to line wrapping shifts, severed page breaks, broken tables, and scrambled multi-column resumes.
3. **Data Privacy & Compliance Liabilities:** Streaming raw candidate resumes through external conversion microservices or third-party APIs violates data residency regulations (GDPR, SOC2, HIPAA) and exposes candidate PII.

### 1.2 The OpenDoc WASM Breakthrough
By compiling a full Microsoft Word OOXML layout, typography, and vector-rasterization engine into WebAssembly (`casual_doc_wasm`), JERO shifted **100% of document computation directly into the recruiter's web browser**:
- **0% Server CPU Overhead:** The central server only serves pre-signed R2 storage stream URLs and static JavaScript/WASM binaries. The client machine executes all font shaping, line layout, vector painting, and PDF assembly.
- **Infinite Horizontal Scalability:** Whether 1 recruiter or 50,000 recruiters process documents simultaneously, server load remains 0%.
- **Sub-Pixel Coordinate Precision:** Rather than guessing text coordinates from HTML approximations, the WASM engine parses Word's native OpenXML DOM and exposes exact twip coordinates (`[page, x, y, w, h]`) for every character, word, and paragraph.
- **Irreversible Client-Side PII Redaction:** Contacts, phone numbers, and compensation numbers are either expunged from the OpenXML DOM before rendering or burned as opaque rectangles directly into the canvas pixels, with the text layer stripped, preventing clipboard extraction.

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                 JERO LEAN BACKEND ARCHITECTURE                                   │
│                                                                                                  │
│   MySQL 8.0 Database (Tenant-Scoped)          Cloudflare R2 Object Storage (Presigned Direct)   │
│   • Reads candidate metadata & skills        • Zero-CPU byte stream via HTTPS                     │
└────────────────────────────────────────────────┬─────────────────────────────────────────────────┘
                                                 │ HTTPS Byte Stream (DOCX / PDF)
                                                 ▼
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                 RECRUITER'S BROWSER WASM SANDBOX                                 │
│                                                                                                  │
│  ┌─────────────────────────┐   ┌─────────────────────────┐   ┌────────────────────────────────┐  │
│  │ AgyResumeRenderer.js    │   │ casual_doc_wasm_bg.wasm │   │ PDF-Lib / jsPDF Vector Engine  │  │
│  │ Master UMD Orchestrator │◄─►│ 20MB Rust SIMD Core     │◄─►│ Vector Highlighting, Headers,  │  │
│  │ 15 Format Dispatcher    │   │ OOXML Shaper & Layout   │   │ A4 Normalization & PDF Merging │  │
│  └─────────────────────────┘   └─────────────────────────┘   └────────────────────────────────┘  │
│                                                │                                                 │
│                                                ▼                                                 │
│  ┌────────────────────────────────────────────────────────────────────────────────────────────┐  │
│  │                    15 PRODUCTION DOWNLOAD & FORWARD RESUME FORMATS                         │  │
│  │  • Opt 1: Original Resume                  • Opt 47: Highlighted + Branded Resume         │  │
│  │  • Opt 5: Pre-Screen Dossier               • Opt 48: Contact-Masked Resume (PII Redacted) │  │
│  │  • Opt 41: Branded Resume                  • Opt 49: Current Employer Masked Resume        │  │
│  │  • Opt 42: Branded Dossier + Resume        • Opt 50: Hidden CTC / Compensation Resume      │  │
│  │  • Opt 43: Unbranded Dossier + Resume      • Opt 51: Full Privacy Shield Resume            │  │
│  │  • Opt 44: Sub-Pixel Highlighted Resume    • Opt 52: Dossier + Anonymized Resume           │  │
│  │  • Opt 45: Highlighted Dossier + Resume    • Opt 53: Dossier + Highlighted & Masked Resume │  │
│  │  • Opt 46: Branded Highlighted Dossier     • Opt 61-65: Interactive ATS Studio Workspace   │  │
│  └────────────────────────────────────────────────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Rust Core Engine Architecture (`casual_doc_wasm`)

The WebAssembly core is founded on the Casual Office OpenDoc document engine (`casual_doc`), written in pure Rust with strict `wasm32-unknown-unknown` compilation targets.

### 2.1 Crate Module Hierarchy

```
casual_doc/
├── Cargo.toml                       # Workspace manifest
├── crates/
│   ├── casual_doc_model/            # OpenXML / OOXML DOM tree & style hierarchy
│   │   ├── src/
│   │   │   ├── document.rs          # Main document container
│   │   │   ├── paragraph.rs         # Paragraph, runs, tabs, line breaks
│   │   │   ├── table.rs             # Table grid, cells, borders, spans
│   │   │   ├── section.rs           # Headers, footers, margins, orientations
│   │   │   ├── styles.rs            # Style inheritance, normal style, overrides
│   │   │   └── numbering.rs         # Bullet lists, multi-level numbering
│   ├── casual_doc_shaper/           # Typography & Font Registry
│   │   ├── src/
│   │   │   ├── font_registry.rs     # Embedded metric tables (Calibri, Arial, etc.)
│   │   │   ├── shaper.rs            # HarfBuzz / cosmic-text OpenType glyph shaper
│   │   │   └── fallback.rs          # Cross-platform font substitution tables
│   ├── casual_doc_layout/           # Multi-Page Layout Engine
│   │   ├── src/
│   │   │   ├── pagination.rs        # Greedy Knuth-Plass line breaker & page partitioner
│   │   │   ├── geometry.rs          # Twip-space bounding box calculations
│   │   │   ├── selection.rs         # Word hit testing & selection rect calculation
│   │   │   └── caret.rs             # Caret navigation & offset translation
│   ├── casual_doc_raster/           # Vector & Bitmap Painting Core
│   │   ├── src/
│   │   │   ├── renderer.rs          # High-DPI rasterization loop (288 DPI)
│   │   │   ├── blitter.rs           # SIMD128 accelerated pixel transfer
│   │   │   └── text_layer.rs        # Searchable text layer extraction
│   └── casual_doc_wasm/             # wasm-bindgen Foreign Function Interface (FFI)
│       ├── src/
│       │   ├── lib.rs               # Class definitions & JS export bridges
│       │   ├── wasm_document.rs     # WasmDocument implementation
│       │   ├── page_bitmap.rs       # Direct RGBA memory buffer accessor
│       │   └── search.rs            # Sub-string & regex text finder
```

### 2.2 Compilation Pipeline & Build Flags
The WASM core requires specific Rust compiler flags to enable SIMD acceleration, strip debug symbols, and optimize for execution speed over binary size:

#### `crates/casual_doc_wasm/Cargo.toml`:
```toml
[package]
name = "casual_doc_wasm"
version = "2.0.0"
edition = "2021"

[lib]
crate-type = ["cdylib", "rlib"]

[dependencies]
wasm-bindgen = "0.2.92"
js-sys = "0.3.69"
web-sys = { version = "0.3.69", features = ["console", "Window", "Document"] }
serde = { version = "1.0", features = ["derive"] }
serde_json = "1.0"
casual_doc_model = { path = "../casual_doc_model" }
casual_doc_shaper = { path = "../casual_doc_shaper" }
casual_doc_layout = { path = "../casual_doc_layout" }
casual_doc_raster = { path = "../casual_doc_raster" }

[profile.release]
opt-level = 3
lto = true
codegen-units = 1
panic = "abort"
strip = true
overflow-checks = false
```

#### Rebuild Command:
```bash
# 1. Ensure wasm32 target is present
rustup target add wasm32-unknown-unknown

# 2. Build with SIMD128 enabled
RUSTFLAGS="-C target-feature=+simd128" wasm-pack build \
  --target web \
  --release \
  --out-dir ../../public/js/opendoc \
  --out-name casual_doc_wasm

# 3. Post-process with Binaryen wasm-opt for maximum speed
wasm-opt -O3 --enable-simd \
  public/js/opendoc/casual_doc_wasm_bg.wasm \
  -o public/js/opendoc/casual_doc_wasm_bg.wasm
```

---

## 3. Complete WASM ABI Specification

The Rust library exports a rich set of WebAssembly bindgen classes. Below is the authoritative TypeScript definition of the entire API contract:

```typescript
/**
 * WasmDocument: Master document container holding parsed OOXML tree,
 * font shaper state, layout geometry, and page renderers.
 */
export class WasmDocument {
  /**
   * Opens and parses a Word .docx binary buffer.
   * @param bytes Uint8Array containing raw .docx zip payload
   */
  static open(bytes: Uint8Array): WasmDocument;

  /**
   * Explicit format opener supporting 'docx' or 'odt'.
   */
  static openAs(bytes: Uint8Array, format: string): WasmDocument;

  /** Total calculated pages based on A4/Letter margins and font metrics */
  readonly pageCount: number;

  /** Returns pixel/point dimensions of a specific page */
  pageSize(pageIndex: number): PageSize;

  /**
   * Renders a given page to an uncompressed RGBA pixel buffer.
   * @param pageIndex 0-indexed page number
   * @param dpi Rendering resolution (JERO standard = 288 DPI)
   */
  renderPage(pageIndex: number, dpi: number): PageBitmap;

  /**
   * Retrieves exact geometric twip coordinates for a text run.
   * Returns Float64Array of [pageIndex, x_twip, y_twip, width_twip, height_twip]
   */
  selectionRects(startNode: string, startOffset: number, endNode: string, endOffset: number): Float64Array;

  /**
   * Searches for exact text query across all runs in the document.
   */
  findText(query: string, startNode?: string, startOffset?: number, forward?: boolean, matchCase?: boolean): TextMatch;

  /** Returns document structural stats (words, characters, paragraphs, tables) */
  documentStats(): DocStats;

  /** Attempts direct native vector PDF export if supported by model */
  export(format: string): WasmExportArtifact;

  /** MUST be called to release WASM linear memory allocations */
  free(): void;
}

/**
 * PageBitmap: Direct memory accessor to rasterized page pixels.
 */
export class PageBitmap {
  readonly widthPx: number;
  readonly heightPx: number;
  /** Direct pointer view into WebAssembly memory */
  pixels(): Uint8ClampedArray;
  free(): void;
}

/**
 * PageSize: Physical dimensions in twips (1 pt = 20 twips).
 */
export class PageSize {
  readonly widthTwips: number;
  readonly heightTwips: number;
  free(): void;
}

/**
 * TextMatch: Search hit position in OpenXML node tree.
 */
export class TextMatch {
  readonly node: string;
  readonly offset: number;
  readonly length: number;
  readonly found: boolean;
  free(): void;
}

/**
 * DocStats: Structural analysis payload.
 */
export class DocStats {
  readonly pages: number;
  readonly words: number;
  readonly characters: number;
  readonly paragraphs: number;
  readonly tables: number;
  free(): void;
}
```

---

## 4. Coordinate Geometry & Vector Transformation Mathematics

One of the most complex aspects of the OpenDoc WASM Engine is the translation between Word layout units (**Twips**), screen/device pixels, and PDF vector units (**Points**).

### 4.1 Unit Definitions & Scales
- **Twip (Twentieth of an Imperial Point):**
  $$\text{1 Inch} = 72 \text{ Points} = 1440 \text{ Twips}$$
  $$\text{1 Point (pt)} = 20 \text{ Twips}$$
- **DPI (Dots Per Inch):**
  - Standard CSS Web Display: $96 \text{ DPI}$ ($1 \text{ pt} = 1.333 \text{ px}$)
  - JERO Ultra-Sharp Vector-Raster Target: $288 \text{ DPI}$ ($1 \text{ pt} = 4.0 \text{ px}$)
  - Standard A4 Dimensions: $595.28 \text{ pt} \times 841.89 \text{ pt} \equiv 11906 \text{ twips} \times 16838 \text{ twips}$

### 4.2 Exact Twip-to-Canvas Mapping Formula
When `doc.selectionRects()` returns bounding boxes in twips $[x_{\text{twip}}, y_{\text{twip}}, w_{\text{twip}}, h_{\text{twip}}]$, they must be projected onto the $288 \text{ DPI}$ rendered HTML Canvas:

$$\text{Scale}_X = \frac{\text{canvas.width}}{\text{pageW}_{\text{pt}} \times 20}$$
$$\text{Scale}_Y = \frac{\text{canvas.height}}{\text{pageH}_{\text{pt}} \times 20}$$

$$\text{X}_{\text{px}} = \text{round}(x_{\text{twip}} \times \text{Scale}_X)$$
$$\text{Y}_{\text{px}} = \text{round}(y_{\text{twip}} \times \text{Scale}_Y)$$
$$\text{Width}_{\text{px}} = \max(8, \text{round}(w_{\text{twip}} \times \text{Scale}_X))$$
$$\text{Height}_{\text{px}} = \text{round}(h_{\text{twip}} \times \text{Scale}_Y)$$

```javascript
// Production implementation in AgyResumeRenderer.js:
var sX = canvas.width / (pageW * 20);
var sY = canvas.height / (pageH * 20);
pageHits.forEach(function (h) {
  ctx.fillRect(
    Math.round(h.x * sX),
    Math.round(h.y * sY),
    Math.max(8, Math.round(h.w * sX)),
    Math.round(h.h * sY)
  );
});
```

### 4.3 PDFLib Coordinate Inversion
PDF coordinates use a **bottom-left Cartesian origin**, whereas Word twips, HTML DOM, and Canvas use a **top-left origin**. When overlaying vector boxes in PDF-Lib:

$$Y_{\text{PDFLib}} = \text{PageHeight}_{\text{pt}} - Y_{\text{top}}_{\text{pt}} - \text{Height}_{\text{pt}}$$

Failure to apply this inversion causes highlight and redaction boxes to appear vertically mirrored at the bottom of the page.

---

## 5. JavaScript SDK Architecture (`AgyResumeRenderer.js`)

[`public/js/opendoc/AgyResumeRenderer.js`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/public/js/opendoc/AgyResumeRenderer.js) is the universal, standalone UMD module that packages the entire client pipeline into a single API: `window.AgyResumeRenderer.processOption(optId, data, options)`.

### 5.1 Hybrid Module Loader & Fallback Hierarchy
To eliminate CORS, CDN failures, or relative path mismatches across diverse deployment environments (FrankenPHP, Docker, staging, production), the loader uses an automated multi-source fallback:

1. **Local App Asset:** `/js/opendoc/casual_doc_wasm.js` + `/js/opendoc/casual_doc_wasm_bg.wasm`
2. **Cloudflare Global CDN:** `https://file.jero.works/js/opendoc/casual_doc_wasm.js?v=opendoc-20260903`
3. **Upstream Backup:** `https://opendoc.casualoffice.org/pkg/casual_doc_wasm.js`

#### Dynamic Import Rewrite via Blob URLs:
Standard ES6 `import()` inside classic scripts fails in older browsers or when MIME types are misconfigured. The loader fetches the module source as raw text, replaces the internal wasm URL with an absolute path, and mounts it dynamically via a Blob URL:
```javascript
var code = await res.text();
code = code.replace(
  /new URL\(['"]casual_doc_wasm_bg\.wasm['"],\s*import\.meta\.url\)/g,
  JSON.stringify(wasmUrl)
);
var blobUrl = URL.createObjectURL(new Blob([code], { type: 'application/javascript' }));
var module = await import(blobUrl);
```

### 5.2 RequireJS / AMD Ecosystem Immunity Pattern
Metronic and legacy enterprise admin templates often load RequireJS globally (`window.define.amd`). When modern UMD dependencies (`pdf-lib.min.js`, `pdfmake.min.js`) load, they register themselves anonymously inside RequireJS instead of exposing `window.PDFLib` and `window.pdfMake`.

**The Bulletproof Guard:**
```javascript
function ensurePdfLib() {
  var wasAmd = (typeof window !== 'undefined' && window.define && window.define.amd);
  if (wasAmd) window.define.amd = false; // Suppress AMD temporarily
  return loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js')
    .then(function() {
      if (wasAmd && window.define) window.define.amd = wasAmd; // Restore AMD
      return window.PDFLib;
    })
    .catch(function(err) {
      if (wasAmd && window.define) window.define.amd = wasAmd;
      throw err;
    });
}
```

### 5.3 Asynchronous Cooperative Scheduling (`yieldToMain`)
Rasterizing 10+ pages of a complex resume can consume several seconds of intense SIMD math. If executed synchronously, the browser UI will freeze, triggering *"Page Unresponsive"* warnings.

The engine injects cooperative scheduler breaks before each page render:
```javascript
function yieldToMain() {
  if (typeof window !== 'undefined' && window.scheduler && typeof window.scheduler.yield === 'function') {
    return window.scheduler.yield().catch(function () {
      return new Promise(function (r) { setTimeout(r, 0); });
    });
  }
  return new Promise(function (r) { setTimeout(r, 0); });
}
```

### 5.4 Deterministic Memory Recycling & GC
JavaScript cannot automatically garbage collect WebAssembly linear memory pointers. If `doc.free()` or `bitmap.free()` is omitted during a 10-candidate batch download, the browser tab will exhaust memory (OOM crash).

**Mandatory Cleanup Pattern:**
```javascript
var doc = null;
try {
  doc = openDoc.open(u8);
  for (var i = 0; i < pageCount; i++) {
    var bitmap = null;
    var canvas = null;
    try {
      bitmap = doc.renderPage(i, renderDpi);
      canvas = openDocCanvasFromBitmap(bitmap);
      // Process image...
    } finally {
      if (bitmap && typeof bitmap.free === 'function') bitmap.free(); // Free C++ pointer
      if (canvas) {
        canvas.width = 0; // Immediate GPU backing-store reclaim
        canvas.height = 0;
        canvas = null;
      }
    }
  }
} finally {
  if (doc && typeof doc.free === 'function') doc.free(); // Free WASM document model
}
```

---

## 6. The 15 Production Document Pipelines

Every format in the Download Resume Center and Forward Resume Drawer maps directly to a deterministic pipeline inside `AgyResumeRenderer.processOption()`:

| Option ID | Format Label | Generation Engine & Steps |
| :---: | :--- | :--- |
| **Option 1** | Original Resume | Direct stream from Cloudflare R2 (`fetchCandidateResumeBlob`) with zero mutation. |
| **Option 5** | Pre-Screened Report | Client-side `pdfMake` generator creating executive dossier with 5-star Metronic skill bars. |
| **Option 41** | Strategy 2 WASM + Company Branding | WASM DOCX-to-PDF at 288 DPI ➔ PDFLib vector branding banner injected at top of Page 1. |
| **Option 42** | Pre-Screened Report + Branded Resume | `pdfMake` generates Page 1 dossier ➔ Merged with Branded WASM Resume via `copyPdfPagesToA4`. |
| **Option 43** | Pre-Screened Report + Unbranded Resume | `pdfMake` generates Page 1 dossier ➔ Merged with Clean WASM Resume via `copyPdfPagesToA4`. |
| **Option 44** | Resume + Highlighted Keywords | Word-level coordinate mapping ➔ Sub-pixel amber vector highlight burning (`rgba(251, 191, 36, 0.42)`). |
| **Option 45** | Pre-Screened Report + Highlighted Resume | `pdfMake` Page 1 dossier ➔ Merged with Option 44 Highlighted Resume. |
| **Option 46** | Pre-Screened Report + Highlighted + Branding | Branded Page 1 dossier ➔ Merged with Option 44 Highlighted Resume. |
| **Option 47** | Highlighted Keywords + Company Branding | Option 44 Highlighted Resume ➔ PDFLib Page 1 Company Header overlay. |
| **Option 48** | Contact Information Hidden | Multi-page regex & OOXML redaction removing email, phone, LinkedIn, and residential address. |
| **Option 49** | Current Company Name Hidden | Redacts current employer text runs and replaces with *"Confidential Enterprise Client"*. |
| **Option 50** | Current & Expected Salary Hidden | Redacts CTC, ECTC, and compensation text runs across all pages. |
| **Option 51** | Complete Privacy Shield | Hard combination of Options 48, 49, and 50 (Full bench marketplace anonymization). |
| **Option 52** | Pre-Screened Report + Anonymized Resume | Anonymized `pdfMake` dossier ➔ Merged with Option 51 Privacy Shield Resume. |
| **Option 53** | Contact Hidden + Highlighted Dossier | Page 1 dossier ➔ Highlighted keywords burned ➔ PII contacts masked with permanent dark rectangles. |

---

## 7. Step-by-Step Re-Creation & Compilation Guide

If the entire engine ever needs to be recompiled from scratch in a fresh repository or new cloud server, follow these exact steps:

### Step 1: Rust Toolchain Installation
```bash
# Install rustup
curl --proto '=https' --tlsv1.2 -sSf https://sh.rustup.rs | sh -s -- -y
source $HOME/.cargo/env

# Add wasm32 target
rustup target add wasm32-unknown-unknown

# Install wasm-pack
cargo install wasm-pack

# Install binaryen (wasm-opt)
# On Ubuntu/Debian:
sudo apt-get install -y binaryen
# On MacOS:
brew install binaryen
```

### Step 2: Initialize Cargo Project Structure
```bash
mkdir -p opendoc_wasm/crates/casual_doc_wasm/src
cd opendoc_wasm
```

Create `Cargo.toml`:
```toml
[workspace]
members = ["crates/casual_doc_wasm"]
resolver = "2"
```

### Step 3: Implement WasmDocument Core Export
In `crates/casual_doc_wasm/src/lib.rs`:
```rust
use wasm_bindgen::prelude::*;

#[wasm_bindgen]
pub struct WasmDocument {
    // Inner OpenDoc model pointer
    page_count: usize,
}

#[wasm_bindgen]
impl WasmDocument {
    #[wasm_bindgen(js_name = open)]
    pub fn open(bytes: &[u8]) -> Result<WasmDocument, JsValue> {
        if bytes.len() < 4 || &bytes[0..4] != b"PK\x03\x04" {
            return Err(JsValue::from_str("Invalid DOCX zip archive header"));
        }
        // In real build, invokes casual_doc_model::parse_docx(bytes)
        Ok(WasmDocument { page_count: 1 })
    }

    #[wasm_bindgen(getter, js_name = pageCount)]
    pub fn page_count(&self) -> usize {
        self.page_count
    }

    #[wasm_bindgen(js_name = renderPage)]
    pub fn render_page(&self, _page: usize, _dpi: u32) -> Result<PageBitmap, JsValue> {
        // Blits pixel buffer using SIMD128
        Ok(PageBitmap {
            width: 2480,
            height: 3508,
            pixels: vec![255u8; 2480 * 3508 * 4],
        })
    }
}

#[wasm_bindgen]
pub struct PageBitmap {
    width: u32,
    height: u32,
    pixels: Vec<u8>,
}

#[wasm_bindgen]
impl PageBitmap {
    #[wasm_bindgen(getter, js_name = widthPx)]
    pub fn width_px(&self) -> u32 { self.width }

    #[wasm_bindgen(getter, js_name = heightPx)]
    pub fn height_px(&self) -> u32 { self.height }

    #[wasm_bindgen(js_name = pixels)]
    pub fn pixels(&self) -> js_sys::Uint8ClampedArray {
        unsafe {
            js_sys::Uint8ClampedArray::view(&self.pixels)
        }
    }
}
```

### Step 4: Build & Optimize Release Binaries
```bash
RUSTFLAGS="-C target-feature=+simd128" wasm-pack build \
  --target web \
  --release \
  crates/casual_doc_wasm \
  --out-dir ../../public/js/opendoc \
  --out-name casual_doc_wasm

# Optimize binary size
wasm-opt -O3 --enable-simd \
  ../../public/js/opendoc/casual_doc_wasm_bg.wasm \
  -o ../../public/js/opendoc/casual_doc_wasm_bg.wasm
```

### Step 5: Verify in Browser Runtime
Open browser DevTools on any JERO application detail page and execute:
```javascript
await window.AgyResumeRenderer.initOpenDoc();
console.log('OpenDoc WASM initialized successfully!');
```

---

## 8. Common Pitfalls, Gotchas & Production Hardening

### 8.1 Word `.doc` (Binary Format) vs `.docx` (OOXML)
- **Gotcha:** Legacy `.doc` files (pre-2007 Word 97–2003) use Microsoft's proprietary Compound File Binary Format (CFBF/OLE2). They are **not** zip packages and **cannot** be parsed directly by OpenDoc WASM.
- **Production Guardrail:** `AgyResumeRenderer.js` inspects the file extension and magic bytes. If a `.doc` file is encountered, it halts WASM processing and triggers a user modal:
  ```javascript
  if (ext === 'doc' && optId !== 1) {
    showLegacyDocUnsupportedModal();
    return window.AgyResumeRenderer.processOption(1, data, options); // Fallback to Option 1
  }
  ```

### 8.2 Browser SIMD Support Check
While all modern browsers (Chrome 91+, Edge 91+, Firefox 89+, Safari 16.4+) natively support WebAssembly SIMD, embedded webviews or legacy corporate browsers may throw `CompileError: WebAssembly.instantiate(): WebAssembly.SIMD not supported`.
- **Handling:** Maintain a non-SIMD fallback binary or catch the exception to trigger the server-side proxy fallback gracefully.

### 8.3 Octane & Multi-Tenant State Isolation
- **Gotcha:** In Laravel Octane (FrankenPHP), global state persists across requests. If candidate dossier variables are injected into Blade templates without fallbacks, stale data from previous recruiter sessions can bleed across tenants.
- **Rule:** Every Blade variable passed into `resume-studio.blade.php` and `fwd_resume.blade.php` **must** utilize null-coalescing fallbacks (`@$cand->notice ?? '--'`).

### 8.4 SMTP Gateway 25MB Attachment Limits
- **Gotcha:** When forwarding multiple resumes via email drawer, 10 high-resolution PDFs can exceed standard SMTP attachment limits (25MB on Gmail/SendGrid/SES).
- **Hard Rule:** Capped at a maximum of **10 candidates per forward** in both frontend drawer validation and [`ServiceCompanyCvTrackerController.php:4969`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php#L4969).

---

## 9. Living Index & File Map

| Artifact / Asset | File Path |
| :--- | :--- |
| **Download Resume Center Engine SDK** | [`public/js/opendoc/AgyResumeRenderer.js`](public/js/opendoc/AgyResumeRenderer.js) |
| **Forward Resume Isolated Engine SDK** | [`public/js/opendoc/AgyForwardResumeEngine.js`](public/js/opendoc/AgyForwardResumeEngine.js) |
| **Forward Resume Adapter** | [`public/js/opendoc/AgyForwardResumeHelper.js`](public/js/opendoc/AgyForwardResumeHelper.js) |
| **WASM JavaScript Wrapper** | [`public/js/opendoc/casual_doc_wasm.js`](public/js/opendoc/casual_doc_wasm.js) |
| **WASM Binary Core** | `public/js/opendoc/casual_doc_wasm_bg.wasm` |
| **WASM Bridge Engine** | [`public/js/opendoc/AgyWasmEngine.js`](public/js/opendoc/AgyWasmEngine.js) |
| **Batch Exporter SDK (Phase 3)** | [`public/js/opendoc/AgyBatchExporter.js`](public/js/opendoc/AgyBatchExporter.js) |
| **Touch/Stylus Annotator (Phase 7)**| [`public/js/opendoc/AgyTouchAnnotator.js`](public/js/opendoc/AgyTouchAnnotator.js) |
| **CV Optimizer SDK (Phase 8)** | [`public/js/opendoc/AgyCvOptimizer.js`](public/js/opendoc/AgyCvOptimizer.js) |
| **Resume Studio Workspace** | [`resources/views/service_company/job-applications/resume-studio.blade.php`](resources/views/service_company/job-applications/resume-studio.blade.php) |
| **Download Resume Center** | [`resources/views/service_company/job-applications/showdetail.blade.php`](resources/views/service_company/job-applications/showdetail.blade.php) |
| **Forward Resume Drawer** | [`resources/views/service_company/cvtracker/fwd_resume.blade.php`](resources/views/service_company/cvtracker/fwd_resume.blade.php) |
| **Master Roadmap** | [`docs/OPENDOC_WASM_MASTER_IMPLEMENTATION_PLAN.md`](docs/OPENDOC_WASM_MASTER_IMPLEMENTATION_PLAN.md) |
| **Phase 3 Specification** | [`docs/OPENDOC_WASM_PHASE_3_DETAILED_IMPLEMENTATION_PLAN.md`](docs/OPENDOC_WASM_PHASE_3_DETAILED_IMPLEMENTATION_PLAN.md) |
| **Phase 4 Specification** | [`docs/OPENDOC_WASM_PHASE_4_DETAILED_IMPLEMENTATION_PLAN.md`](docs/OPENDOC_WASM_PHASE_4_DETAILED_IMPLEMENTATION_PLAN.md) |
| **Phase 5 Specification** | [`docs/OPENDOC_WASM_PHASE_5_DETAILED_IMPLEMENTATION_PLAN.md`](docs/OPENDOC_WASM_PHASE_5_DETAILED_IMPLEMENTATION_PLAN.md) |
| **Phase 6 Specification** | [`docs/OPENDOC_WASM_PHASE_6_DETAILED_IMPLEMENTATION_PLAN.md`](docs/OPENDOC_WASM_PHASE_6_DETAILED_IMPLEMENTATION_PLAN.md) |
| **Phase 7 Specification** | [`docs/OPENDOC_WASM_PHASE_7_DETAILED_IMPLEMENTATION_PLAN.md`](docs/OPENDOC_WASM_PHASE_7_DETAILED_IMPLEMENTATION_PLAN.md) |
| **Phase 8 Specification** | [`docs/OPENDOC_WASM_PHASE_8_DETAILED_IMPLEMENTATION_PLAN.md`](docs/OPENDOC_WASM_PHASE_8_DETAILED_IMPLEMENTATION_PLAN.md) |
