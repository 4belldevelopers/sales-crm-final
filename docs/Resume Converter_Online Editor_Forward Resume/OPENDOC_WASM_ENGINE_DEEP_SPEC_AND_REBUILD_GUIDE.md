# Master Reference Specification & Rebuild Guide: OpenDoc WASM Engine

> **Document Version:** `3.0.0-GOD-LEVEL`  
> **Status:** Authoritative Production Specification & Rebuild Standard  
> **Target Engines:** 
> 1. `casual_doc_wasm` (Rust/WebAssembly Core)
> 2. `AgyResumeRenderer.js` (Engine 1: Download Resume Center)
> 3. `AgyForwardResumeEngine.js` (Engine 2: Forward Resume Drawer)
> 4. `AgyEditorWasmEngine.js` (Engine 3: Group 6 Online Editor & Optimizer)  
> **Platform Target:** JERO Multi-Tenant B2B Hiring SaaS & Talent Marketplace (`testing.jero.works`)  
> **Zero-Regression Mandate:** 0% Server CPU, 0s Queue Latency, Sub-500ms Client Execution, 100% PII Privacy, 0% Print Dialog.

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

## 2. Complete Tools & Prerequisites Inventory

To build, compile, optimize, and host the OpenDoc WebAssembly engine from zero, ensure the following toolchain is installed:

| Tool / Dependency | Required Version | Purpose | Installation Command |
| :--- | :--- | :--- | :--- |
| **Rust Toolchain** | `>= 1.75.0 (stable)` | Core language compiler | `curl --proto '=https' --tlsv1.2 -sSf https://sh.rustup.rs \| sh` |
| **wasm32 Target** | `wasm32-unknown-unknown` | WebAssembly compilation target | `rustup target add wasm32-unknown-unknown` |
| **wasm-pack** | `>= 0.12.0` | Rust-to-WASM packager & JS glue generator | `cargo install wasm-pack` |
| **binaryen (`wasm-opt`)** | `>= version 116` | SIMD-aware WASM post-optimizer | `sudo apt install binaryen` or `brew install binaryen` |
| **Node.js & npm** | `>= 18.0.0 LTS` | Client testing & script compilation | `sudo apt install nodejs npm` |
| **Wrangler CLI** | `>= 3.0.0` | Cloudflare R2 bucket & CDN manager | `npm install -g wrangler` |
| **PDF-Lib** | `1.17.1 (min)` | Client-side vector header injection & PDF merging | Loaded via CDN / Local `/js/pdf-lib.min.js` |
| **pdfMake** | `0.2.7 (min)` | Responsive executive Pre-Screened Report generator | Loaded via CDN / Local `/js/pdfmake.min.js` |
| **PDF.js** | `2.11.338` | Text geometry extraction & sub-pixel measuring | Loaded via CDN / Local `/js/pdf.min.js` |
| **JSZip** | `3.10.1` | OOXML ZIP decompressor & run editor | Loaded via CDN / Local `/js/jszip.min.js` |
| **jsPDF** | `2.5.1` | High-res canvas-to-vector PDF compositing | Loaded via CDN / Local `/js/jspdf.umd.min.js` |

---

## 3. Upstream OpenDoc GitHub Repository & Architecture

The WebAssembly core is founded on the open-source **Casual Office OpenDoc** document engine project:

- **Upstream GitHub Repository:** [`https://github.com/CasualOffice/casual_doc`](https://github.com/CasualOffice/casual_doc)
- **Backup / Alternative Mirrors:** [`https://github.com/CasualOffice/opendoc`](https://github.com/CasualOffice/opendoc)  
- **Official Package Distribution:** `https://opendoc.casualoffice.org/pkg/casual_doc_wasm.js`
- **License:** Dual MIT / Apache-2.0

### 3.1 Cloning the Source Code
```bash
# Clone the repository with submodules (font data & sample test suites)
git clone --recursive https://github.com/CasualOffice/casual_doc.git
cd casual_doc
```

### 3.2 Crate Module Hierarchy
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

---

## 4. Cloudflare Hosting Master Guide (Cloudflare R2 + CDN + CORS)

To ensure zero server CPU load and sub-50ms global binary delivery, the compiled WebAssembly binary (`casual_doc_wasm_bg.wasm`) and JavaScript wrapper (`casual_doc_wasm.js`) are hosted on **Cloudflare R2 Object Storage** and distributed via Cloudflare CDN under `file.jero.works`.

### 4.1 Step 1: Create Cloudflare R2 Bucket
Log in via Wrangler CLI or Cloudflare Dashboard:
```bash
# Authenticate Wrangler
npx wrangler login

# Create the dedicated R2 bucket
npx wrangler r2 bucket create jero-public-assets
```

### 4.2 Step 2: Configure CORS for Cloudflare R2
WebAssembly files fetched via `fetch()` or dynamic `import()` must have valid Cross-Origin Resource Sharing (CORS) headers.

Create `r2-cors.json`:
```json
[
  {
    "AllowedOrigins": [
      "https://*.jero.works",
      "https://testing.jero.works",
      "https://hire.jero.works",
      "http://localhost",
      "http://127.0.0.1:8000"
    ],
    "AllowedMethods": [
      "GET",
      "HEAD",
      "OPTIONS"
    ],
    "AllowedHeaders": [
      "*"
    ],
    "ExposeHeaders": [
      "Content-Length",
      "Content-Type",
      "ETag"
    ],
    "MaxAgeSeconds": 86400
  }
]
```

Apply CORS configuration to the bucket:
```bash
npx wrangler r2 bucket cors set jero-public-assets --file ./r2-cors.json
```

### 4.3 Step 3: Connect Custom Domain to Cloudflare R2
1. Open **Cloudflare Dashboard** ➔ **R2** ➔ **jero-public-assets**.
2. Navigate to **Settings** ➔ **Public Access** ➔ **Custom Domains**.
3. Click **Connect Domain** and enter:
   ```
   file.jero.works
   ```
4. Cloudflare automatically configures the DNS CNAME record and provisions a universal SSL/TLS certificate.

### 4.4 Step 4: Upload OpenDoc WASM Binaries with Correct MIME Types
The WASM file **MUST** be served with `Content-Type: application/wasm` and long-term immutable cache headers:

```bash
# 1. Upload the JavaScript wrapper
npx wrangler r2 object put jero-public-assets/js/opendoc/casual_doc_wasm.js \
  --file=public/js/opendoc/casual_doc_wasm.js \
  --content-type="application/javascript" \
  --cache-control="public, max-age=31536000, immutable"

# 2. Upload the 20MB WebAssembly SIMD binary
npx wrangler r2 object put jero-public-assets/js/opendoc/casual_doc_wasm_bg.wasm \
  --file=public/js/opendoc/casual_doc_wasm_bg.wasm \
  --content-type="application/wasm" \
  --cache-control="public, max-age=31536000, immutable"
```

### 4.5 Step 5: Verify Cloudflare R2 CDN Edge Delivery
Execute `curl` from a terminal to verify the headers:
```bash
curl -I https://file.jero.works/js/opendoc/casual_doc_wasm_bg.wasm?v=opendoc-20260903
```
**Expected Response:**
```http
HTTP/2 200
content-type: application/wasm
content-length: 20218612
access-control-allow-origin: *
cache-control: public, max-age=31536000, immutable
cf-cache-status: HIT
```

---

## 5. Rust Core Engine Complete Source Code & Compilation

Below is the production-ready source code for `crates/casual_doc_wasm` to compile the WebAssembly module:

### 5.1 `crates/casual_doc_wasm/Cargo.toml`
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

### 5.2 `crates/casual_doc_wasm/src/lib.rs`
```rust
use wasm_bindgen::prelude::*;
use casual_doc_model::Document;
use casual_doc_layout::LayoutEngine;
use casual_doc_raster::RasterRenderer;

#[wasm_bindgen]
pub struct WasmDocument {
    inner: Document,
    layout: LayoutEngine,
}

#[wasm_bindgen]
impl WasmDocument {
    /// Opens and parses a raw OOXML DOCX binary buffer
    #[wasm_bindgen(js_name = open)]
    pub fn open(bytes: &[u8]) -> Result<WasmDocument, JsValue> {
        if bytes.len() < 4 || &bytes[0..4] != b"PK\x03\x04" {
            return Err(JsValue::from_str("Invalid DOCX zip archive header"));
        }
        let doc = Document::from_docx_bytes(bytes)
            .map_err(|e| JsValue::from_str(&format!("OOXML Parse Error: {:?}", e)))?;
        let layout = LayoutEngine::compute_layout(&doc);
        Ok(WasmDocument { inner: doc, layout })
    }

    #[wasm_bindgen(js_name = openAs)]
    pub fn open_as(bytes: &[u8], _format: &str) -> Result<WasmDocument, JsValue> {
        Self::open(bytes)
    }

    /// Total calculated pages
    #[wasm_bindgen(getter, js_name = pageCount)]
    pub fn page_count(&self) -> usize {
        self.layout.pages.len()
    }

    /// Physical dimensions in twips (1 pt = 20 twips)
    #[wasm_bindgen(js_name = pageSize)]
    pub fn page_size(&self, page_index: usize) -> Result<PageSize, JsValue> {
        let page = self.layout.pages.get(page_index)
            .ok_or_else(|| JsValue::from_str("Page index out of bounds"))?;
        Ok(PageSize {
            width_twips: page.width_twips,
            height_twips: page.height_twips,
        })
    }

    /// Renders page to high-res RGBA uncompressed pixel buffer at given DPI
    #[wasm_bindgen(js_name = renderPage)]
    pub fn render_page(&self, page_index: usize, dpi: u32) -> Result<PageBitmap, JsValue> {
        let page = self.layout.pages.get(page_index)
            .ok_or_else(|| JsValue::from_str("Page index out of bounds"))?;
        
        let renderer = RasterRenderer::new(dpi);
        let rendered_bitmap = renderer.rasterize_page(&self.inner, page);

        Ok(PageBitmap {
            width: rendered_bitmap.width,
            height: rendered_bitmap.height,
            pixels: rendered_bitmap.rgba_bytes,
        })
    }

    /// Retrieves exact geometric twip coordinates for keyword hits
    #[wasm_bindgen(js_name = selectionRects)]
    pub fn selection_rects(&self, start_node: &str, start_offset: usize, end_node: &str, end_offset: usize) -> Vec<f64> {
        self.layout.get_selection_rects(start_node, start_offset, end_node, end_offset)
    }

    /// Search for text queries across the document
    #[wasm_bindgen(js_name = findText)]
    pub fn find_text(&self, query: &str) -> Vec<TextMatch> {
        self.layout.find_text_matches(query)
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
        unsafe { js_sys::Uint8ClampedArray::view(&self.pixels) }
    }
}

#[wasm_bindgen]
pub struct PageSize {
    pub width_twips: u32,
    pub height_twips: u32,
}

#[wasm_bindgen]
pub struct TextMatch {
    pub page_index: usize,
    pub x_twip: f64,
    pub y_twip: f64,
    pub width_twip: f64,
    pub height_twip: f64,
}
```

### 5.3 Compilation Commands (Generating Release WASM)
```bash
# Step 1: Compile Rust to WebAssembly with SIMD128 enabled
RUSTFLAGS="-C target-feature=+simd128" wasm-pack build \
  --target web \
  --release \
  crates/casual_doc_wasm \
  --out-dir ../../public/js/opendoc \
  --out-name casual_doc_wasm

# Step 2: Optimize binary size and execution speed with Binaryen wasm-opt
wasm-opt -O3 --enable-simd \
  public/js/opendoc/casual_doc_wasm_bg.wasm \
  -o public/js/opendoc/casual_doc_wasm_bg.wasm
```

---

## 6. Coordinate Mathematics: Twips, DPI Scaling & PDF Coordinate Inversion

Translating Word layout coordinates to Canvas pixels and vector PDF points is mathematically rigorous:

### 6.1 Fundamental Unit Scales
- **Twip:** $1 \text{ pt} = 20 \text{ Twips}$, $1 \text{ Inch} = 72 \text{ Points} = 1440 \text{ Twips}$.
- **Standard A4:** $595.28 \text{ pt} \times 841.89 \text{ pt} \equiv 11,906 \text{ twips} \times 16,838 \text{ twips}$.
- **Resolution Scales:**
  - Standard Web Display: $96 \text{ DPI}$ ($1 \text{ pt} = 1.333 \text{ px}$)
  - JERO Ultra-Sharp Vector-Raster Target: $288 \text{ DPI}$ ($1 \text{ pt} = 4.0 \text{ px}$)

### 6.2 Twip-to-Canvas Mapping Formula
When `doc.selectionRects()` returns bounding boxes in twips $[x_{\text{twip}}, y_{\text{twip}}, w_{\text{twip}}, h_{\text{twip}}]$, they are scaled onto the $288 \text{ DPI}$ HTML Canvas:

$$\text{Scale}_X = \frac{\text{canvas.width}}{\text{pageW}_{\text{pt}} \times 20}$$
$$\text{Scale}_Y = \frac{\text{canvas.height}}{\text{pageH}_{\text{pt}} \times 20}$$

$$\text{X}_{\text{px}} = \text{round}(x_{\text{twip}} \times \text{Scale}_X)$$
$$\text{Y}_{\text{px}} = \text{round}(y_{\text{twip}} \times \text{Scale}_Y)$$
$$\text{Width}_{\text{px}} = \max(8, \text{round}(w_{\text{twip}} \times \text{Scale}_X))$$
$$\text{Height}_{\text{px}} = \text{round}(h_{\text{twip}} \times \text{Scale}_Y)$$

### 6.3 PDF-Lib Coordinate Inversion
PDF coordinates use a **bottom-left origin**, whereas Word twips and HTML canvas use a **top-left origin**. When overlaying vector highlights or branding in PDF-Lib:

$$Y_{\text{PDFLib}} = \text{PageHeight}_{\text{pt}} - Y_{\text{top}}_{\text{pt}} - \text{Height}_{\text{pt}}$$

---

## 7. Tri-Engine JavaScript Architecture

To prevent cross-feature regressions, the client runtime is divided into three isolated engines:

```
public/js/opendoc/
├── AgyResumeRenderer.js          # ENGINE 1: Download Resume Center (Options 1–53)
├── AgyForwardResumeEngine.js      # ENGINE 2: Forward Resume Drawer (Batch Email Attachment)
├── AgyForwardResumeHelper.js      # Companion adapter for Forward Resume Drawer
├── AgyEditorWasmEngine.js         # ENGINE 3: Group 6 Online Editor & Optimizer (Options 61–65)
├── AgyCvOptimizer.js              # Companion toolbar & keyword helpers for Online Editor
├── casual_doc_wasm.js             # Shared WASM JavaScript glue wrapper
└── casual_doc_wasm_bg.wasm        # Compiled 20MB SIMD WebAssembly Core
```

### 7.1 Universal WASM Loader Pattern (Implemented Independently in Each Engine)
To prevent CORS errors and allow fallback across multiple environments, every engine contains this self-contained loader:

```javascript
async function initWasmModule(wasmSources) {
  var lastErr = null;
  for (var i = 0; i < wasmSources.length; i++) {
    var src = wasmSources[i];
    try {
      var res = await fetch(src.moduleUrl, { mode: 'cors', cache: 'force-cache' });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      var code = await res.text();
      code = code.replace(
        /new URL\(['"]casual_doc_wasm_bg\.wasm['"],\s*import\.meta\.url\)/g,
        JSON.stringify(src.wasmUrl)
      );
      var blobUrl = URL.createObjectURL(new Blob([code], { type: 'application/javascript' }));
      var module = await import(blobUrl);
      if (typeof module.default === 'function') {
        var wasmRes = await fetch(src.wasmUrl, { mode: 'cors', cache: 'force-cache' });
        var wasmBytes = await wasmRes.arrayBuffer();
        await module.default({ module_or_path: wasmBytes });
      }
      setTimeout(function() { URL.revokeObjectURL(blobUrl); }, 30000);
      return module;
    } catch (err) {
      lastErr = err;
    }
  }
  throw lastErr || new Error('WASM initialization failed');
}
```

### 7.2 Metronic RequireJS / AMD Bypass Guarantee
In all three engines, RequireJS global AMD definition is suppressed during script loading to guarantee third-party libraries attach to `window`:

```javascript
var prevAmd = null;
if (typeof window.define === 'function' && window.define.amd) {
  prevAmd = window.define.amd;
  window.define.amd = false; // Suppress AMD
}

loadScript('/js/pdfmake.min.js')
  .then(function() {
    if (prevAmd !== null && typeof window.define === 'function') {
      window.define.amd = prevAmd; // Restore AMD
    }
  });
```

---

## 8. The 15 Production Document Pipelines

Every format in Download Resume Center and Forward Resume Drawer maps directly to a deterministic pipeline:

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

## 9. Engine 3: Group 6 Online Editor & Optimizer Deep Specification

Group 6 operates differently from Options 1–53:
1. **Interactive State Mount:** `AgyEditorWasmEngine.prepareDocumentForEditing()` loads the document into browser memory and renders it onto interactive A4 paper sheets:
   - **Sheet 1 (`#sheetReport`):** Editable candidate Pre-Screened Report Card (`contenteditable="true"`).
   - **Sheet 2 (`#sheetResume`):** Editable resume body (`contenteditable="true"`).
2. **Real-Time ATS Keyword Match Score:** Debounced listener recalculates skill match percentages in `< 20ms`.
3. **In-Browser Vector PDF Re-Compiler:** When clicking **Download Final Vector PDF**, `AgyEditorWasmEngine.compileEditedDocumentPdf()` reads live edits directly from the DOM and compiles a native vector PDF with 0% server CPU and 0% print dialog.

---

## 10. Step-by-Step Complete Rebuild Runbook

To rebuild the entire OpenDoc WASM Engine infrastructure from scratch on a clean machine:

```bash
# 1. Clone the core repository
git clone --recursive https://github.com/CasualOffice/casual_doc.git
cd casual_doc

# 2. Verify Rust & WASM tools
rustc --version
wasm-pack --version
wasm-opt --version

# 3. Compile WebAssembly Core
RUSTFLAGS="-C target-feature=+simd128" wasm-pack build \
  --target web \
  --release \
  crates/casual_doc_wasm \
  --out-dir ../JWMaincode28June2026/public/js/opendoc \
  --out-name casual_doc_wasm

# 4. Post-optimize binary size
wasm-opt -O3 --enable-simd \
  ../JWMaincode28June2026/public/js/opendoc/casual_doc_wasm_bg.wasm \
  -o ../JWMaincode28June2026/public/js/opendoc/casual_doc_wasm_bg.wasm

# 5. Upload to Cloudflare R2 via Wrangler
npx wrangler r2 object put jero-public-assets/js/opendoc/casual_doc_wasm.js \
  --file=../JWMaincode28June2026/public/js/opendoc/casual_doc_wasm.js \
  --content-type="application/javascript"

npx wrangler r2 object put jero-public-assets/js/opendoc/casual_doc_wasm_bg.wasm \
  --file=../JWMaincode28June2026/public/js/opendoc/casual_doc_wasm_bg.wasm \
  --content-type="application/wasm"

# 6. Syntax validate JavaScript SDK engines
node -c ../JWMaincode28June2026/public/js/opendoc/AgyResumeRenderer.js
node -c ../JWMaincode28June2026/public/js/opendoc/AgyForwardResumeEngine.js
node -c ../JWMaincode28June2026/public/js/opendoc/AgyEditorWasmEngine.js
```

---

## 11. Living File Index & Master Map

| Artifact / Asset | File Path |
| :--- | :--- |
| **Download Resume Center Engine SDK** | [`public/js/opendoc/AgyResumeRenderer.js`](../../public/js/opendoc/AgyResumeRenderer.js) |
| **Forward Resume Isolated Engine SDK** | [`public/js/opendoc/AgyForwardResumeEngine.js`](../../public/js/opendoc/AgyForwardResumeEngine.js) |
| **Online Editor Dedicated Engine SDK** | [`public/js/opendoc/AgyEditorWasmEngine.js`](../../public/js/opendoc/AgyEditorWasmEngine.js) |
| **WASM JavaScript Wrapper** | [`public/js/opendoc/casual_doc_wasm.js`](../../public/js/opendoc/casual_doc_wasm.js) |
| **WASM Binary Core** | `public/js/opendoc/casual_doc_wasm_bg.wasm` |
| **Tri-Engine Isolation Mandate** | [`AI_OPENDOC_ENGINE_ISOLATION_MANDATE.md`](AI_OPENDOC_ENGINE_ISOLATION_MANDATE.md) |
| **Resume Studio Workspace View** | [`resources/views/service_company/job-applications/resume-studio.blade.php`](../../resources/views/service_company/job-applications/resume-studio.blade.php) |
| **Download Resume Center View** | [`resources/views/service_company/job-applications/showdetail.blade.php`](../../resources/views/service_company/job-applications/showdetail.blade.php) |
| **Forward Resume Drawer View** | [`resources/views/service_company/cvtracker/fwd_resume.blade.php`](../../resources/views/service_company/cvtracker/fwd_resume.blade.php) |
