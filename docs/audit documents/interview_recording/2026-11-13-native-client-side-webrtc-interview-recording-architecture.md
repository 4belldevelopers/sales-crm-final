# Architecture Specification: Native In-App WebRTC Video Room with 100% Client-Side Recording & Cloudflare R2 Upload

> **Document Type:** System Architecture & Technical Implementation Blueprint  
> **Module / Feature:** Native Video Interviewing & Client-Side Recording (`service_company/interview-schedule`, `routes/web.php`, `resources/views/interview/room.blade.php`)  
> **Storage Location:** `docs/audit documents/interview_recording/2026-11-13-native-client-side-webrtc-interview-recording-architecture.md`  
> **Standards:** Rule 1 & 2 (CQRS MySQL Writes / Elasticsearch Reads), Rule 3 (Client-First Execution in User RAM & Browser MediaRecorder), Rule 4 (10K Concurrency / 0% Server Strain Target), Rule 6 (Multi-Tenant Atomic Cache Invalidation), Rule 20 (Card + Sub-Card Enterprise Design System), Rule 21 (Module Subfolder Documentation Standard), Rule 22 (Zero-Cron / JIT Lifecycle Standard).  
> **Date:** 2026-11-13  
> **Author:** Antigravity God-Mode Architecture Team  

---

## 1. Executive Summary & Breakthrough Value

By hosting video interviews directly inside JERO at `jero.works/interview/{token}` (using native browser WebRTC), JERO eliminates 100% of the operational friction, cost, and complexity of third-party platforms (Google Meet, MS Teams, Zoom):

1. **100% Client-Side Execution (Rule 3):** Recording executes **entirely inside browser RAM** via the HTML5 `MediaRecorder` API and `AudioContext` stream mixer.
2. **Zero Headless Bots Required:** No headless Chromium containers, no Xvfb virtual screens, and zero third-party bot provider fees ($0.00/hour bot cost).
3. **0% JERO Server Media Strain (Rule 4):**
   - Media streams flow **Peer-to-Peer (WebRTC P2P)** directly between candidate and interviewer.
   - Recording chunks upload **directly from the recruiter's browser into Cloudflare R2** via presigned S3 URLs.
   - JERO FrankenPHP servers handle only lightweight WebSocket signaling (via existing **Laravel Reverb**) and final 2 KB metadata persistence.
4. **All-in-One ATS Interview Workspace:** Unlike external Meet/Zoom calls where recruiters must juggle multiple windows, the JERO Interview Room displays the **Live Video Call + Candidate Resume Viewer + Job Description + Scoring Rubric / Feedback Form** side-by-side in one unified screen.

---

## 2. End-to-End System Architecture

```mermaid
flowchart TD
    subgraph Signaling_JERO["JERO Platform (Reverb WebSockets + Laravel)"]
        A["Recruiter Schedules Interview<br/>(Selects 'JERO Native HD Video')"] --> B["Generate Cryptographic Room Token<br/>(UUIDv4 + HMAC SHA-256)"]
        B --> C["Send Candidate Invite & Recruiter Link<br/>jero.works/interview/{token}"]
        D["Laravel Reverb Channel<br/>presence-interview.{token}"] <--> E["Signaling Handshake<br/>(SDP Offer, SDP Answer, ICE Candidates)"]
    end

    subgraph Browser_P2P["Peer-to-Peer WebRTC Media Session (User Devices)"]
        F["Candidate Device (Mic + Cam)"] <-->|Direct Encrypted P2P Media Stream| G["Interviewer Device (Mic + Cam)"]
    end

    subgraph Client_Recorder["100% Client-Side Recording Engine (Interviewer Browser RAM)"]
        G --> H["AudioContext Multi-Stream Mixer<br/>(Local Mic + Remote Candidate Audio)"]
        G --> I["Canvas Stream Mixer (30 FPS)<br/>(Dual Video / Speaker Spotlight / Screen Share)"]
        H & I --> J["HTML5 MediaRecorder API<br/>(video/webm or video/mp4; codecs=h264/opus)"]
        J --> K["IndexedDB Crash-Proof Ring Buffer<br/>(5-Second Recovery Chunks in User RAM)"]
    end

    subgraph Direct_Cloudflare["Cloudflare Global Network ($0 Egress Bandwidth)"]
        K -->|Direct S3 Multipart PUT Chunks| L["Cloudflare R2 Bucket: jero-interview-recordings<br/>Path: company_{id}/jobs/{job_id}/interview_{id}_{hash}.mp4"]
        L -.-> M["Cloudflare Stream / HLS Adaptive Bitrate Delivery"]
    end

    subgraph Finalization_CQRS["JERO CQRS Persistence & Cache Lifecycle"]
        K -->|POST /api/interview/finalize (2 KB)| N["InterviewRecordingController"]
        N --> O["MySQL Write: interview_recordings"]
        N --> P["Elasticsearch Index Sync: interview_schedules"]
        N --> Q["Redis Tenant Cache Evicted (company_id)"]
        N --> R["Subscription / Coin Deducted<br/>(package_active / package_transactions)"]
    end
```

---

## 3. The 100% Client-Side Media Mixing & Recording Pipeline

Inside `resources/js/interview/jero-client-recorder.js`:

### 1. Dual-Stream Audio Mixing (Zero Echo / Separate Tracks)
```javascript
class JeroAudioMixer {
    constructor() {
        this.ctx = new (window.AudioContext || window.webkitAudioContext)();
        this.dest = this.ctx.createMediaStreamDestination();
    }

    addStream(stream, pan = 0) {
        const source = this.ctx.createMediaStreamSource(stream);
        const panner = this.ctx.createStereoPanner ? this.ctx.createStereoPanner() : null;
        if (panner) {
            panner.pan.value = pan; // -1 for Recruiter (Left), +1 for Candidate (Right)
            source.connect(panner);
            panner.connect(this.dest);
        } else {
            source.connect(this.dest);
        }
    }

    getMixedTrack() {
        return this.dest.stream.getAudioTracks()[0];
    }
}
```
*Benefits:* Stereo track separation allows downstream speech-to-text engines to attribute 100% of spoken words accurately to Candidate vs. Interviewer without voice bleed.

### 2. Multi-Video Canvas Compositing (Speaker Spotlight / Split Screen)
- Combines candidate video, interviewer video, and screen share on an `OffscreenCanvas` rendered at 30 FPS (`1280x720` or `1920x1080`).
- Dynamically resizes layout when candidate or interviewer shares screen (presentation mode).
- Extracts mixed video track: `const mixedVideoTrack = canvas.captureStream(30).getVideoTracks()[0];`.

### 3. MediaRecorder & IndexedDB Crash-Proof Buffer
- Streams audio + video into `MediaRecorder`:
  ```javascript
  const combinedStream = new MediaStream([mixedVideoTrack, audioMixer.getMixedTrack()]);
  const recorder = new MediaRecorder(combinedStream, {
      mimeType: MediaRecorder.isTypeSupported('video/webm;codecs=vp9,opus') 
          ? 'video/webm;codecs=vp9,opus' 
          : 'video/mp4;codecs=avc1.42E01E,mp4a.40.2',
      videoBitsPerSecond: 1500000 // 1.5 Mbps high fidelity
  });
  ```
- **Crash Protection:** Every 5 seconds, `recorder.ondataavailable` appends the chunk to browser **IndexedDB**. If the recruiter accidentally closes the tab or refreshes, the pending chunks are automatically recovered and uploaded upon reconnection.

### 4. Direct Chunked Multipart Upload to Cloudflare R2
- The recruiter's browser requests presigned S3 URLs from JERO:
  `GET /api/interview/{token}/presigned-urls?parts=N`
- The browser uploads chunks directly to Cloudflare R2 via HTTP `PUT`:
  `PUT https://<account_id>.r2.cloudflarestorage.com/jero-interview-recordings/...`
- **Result:** Heavy video data never passes through JERO Laravel servers. 0% network bandwidth or CPU strain on JERO.

---

## 4. Signaling & NAT Traversal (WebSockets + STUN/TURN)

### 1. Signaling via Existing Laravel Reverb
JERO already has Laravel Reverb installed and configured ([config/broadcasting.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/config/broadcasting.php#L39-L49), [config/reverb.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/config/reverb.php)).
- Reverb channel: `presence-interview-room.{token}`
- Exchanged events:
  - `client-signal-offer` (Interviewer sends WebRTC SDP Offer)
  - `client-signal-answer` (Candidate sends WebRTC SDP Answer)
  - `client-signal-ice` (Exchanges candidate network paths)
  - `client-state-change` (Mute, Camera off, Screen share start/stop)

### 2. STUN / TURN Traversal
- **Free Google STUN Servers:** `stun:stun.l.google.com:19302`, `stun:stun1.l.google.com:19302` (resolves ~85% of P2P connections).
- **Cloudflare Calls TURN (Symmetric NAT Fallback):** Cloudflare offers 1 TB/month free TURN service. Provides guaranteed connection even on restrictive corporate firewalls.

---

## 5. UI/UX Layout: Unified ATS Interview Workspace (Rule 20 & 15.6" Screen Fit)

Unlike third-party meeting apps, JERO's room layout integrates recruitment operations directly into the video screen:

```
+-----------------------------------------------------------------------------------------------------------------------------------+
| [HEADER: JERO Interview Room | Senior Full Stack Engineer | Candidate: Rohan Sharma | Duration: 24:18 | 🟢 RECORDING (User RAM)]   |
+--------------------------------------------------------------------+--------------------------------------------------------------+
| [LEFT TIER: VIDEO & SCREEN STREAM]                                 | [RIGHT TIER: ATS INTELLIGENCE & EVALUATION WORKSPACE]       |
| +----------------------------------------------------------------+ | +----------------------------------------------------------+ |
| |                                                                | | | TAB BAR: [Resume Viewer] [Job Description] [Live Score]  | |
| |                  CANDIDATE LIVE VIDEO                          | | +----------------------------------------------------------+ |
| |                                                                | | | CANDIDATE RESUME PREVIEW (OpenDoc WASM Engine):        | |
| |                                                                | | | • 5+ Years PHP, Laravel, Elasticsearch, WebRTC         | |
| |                                                                | | | • Ex-Razorpay Senior Software Engineer                  | |
| |                                                                | | |----------------------------------------------------------| |
| +----------------------------------------------------------------+ | | LIVE INTERVIEW SCORING RUBRIC:                           | |
| | [INTERVIEWER PiP]      | [Mute] [Cam] [Share Screen] [End Call] | | | • Technical Architecture: [ ★ ★ ★ ★ ☆ ] (4/5)            | |
| +----------------------------------------------------------------+ | | • Problem Solving:        [ ★ ★ ★ ★ ★ ] (5/5)            | |
|                                                                    | | • Communication:          [ ★ ★ ★ ★ ☆ ] (4/5)            | |
|                                                                    | | • Recruiter Notes: [Candidate explained CQRS cleanly...] | |
|                                                                    | | +----------------------------------------------------------+ |
|                                                                    | | [SUBMIT FEEDBACK & MOVE STAGE]                             | |
+--------------------------------------------------------------------+--------------------------------------------------------------+
```

### Screen Standard (Rule 14 & Rule 20):
- Split layout: `60% Video Stage` (Left), `40% ATS Workspace` (Right).
- Fully responsive on **15.6" screens (`1366x768` and `1920x1080`)** with internal scrolling.

---

## 6. Comparison: Native JERO WebRTC Room vs. External Meeting Bots

| Dimension | Native JERO WebRTC Interview Room (`jero.works/interview/{token}`) | External Meeting Bot (Meet / Teams / Zoom) |
| :--- | :--- | :--- |
| **Where Recording Executes** | **100% Client-Side in Browser RAM (Rule 3)** | Ephemeral Docker Bot Worker in Cloud |
| **Server / Bot Cost** | **\$0.00 / hour (Zero Bot Infrastructure Cost)** | \$0.05 to \$0.50 / hour per bot VM |
| **Recruiter Experience** | **All-in-One ATS Screen:** Video + Resume + Job Specs + Scorecard all visible on one screen. | Fragmented: Video in external app, Recruiter must switch windows to view ATS. |
| **JERO Server Load** | **0% Media Load** (P2P WebRTC + Direct Cloudflare R2 Upload) | **0% Media Load** (Bot streams directly to R2) |
| **Audio/Video Separation** | Perfect stereo separation (Left: Recruiter, Right: Candidate) | Mixed audio track (Requires AI diarization) |
| **Candidate Access** | Instant 1-click web link (No app install, no login needed) | Requires Google account, Teams app, or Zoom app |
| **Subscription Value** | Premium branded feature: *"JERO HD Video ATS"* | Standard recording add-on |

---

## 7. Recommended Dual-Mode Strategy

JERO can offer the best of both worlds in `interview-create.blade.php`:

1. **Default & Recommended Option:** **"JERO Native HD Video Call"**
   - 100% Client-Side.
   - Zero bot fees.
   - Built-in resume viewer and live scoring rubric.
   - Direct Cloudflare R2 storage.
2. **Alternative Option:** **"External Link (Google Meet / MS Teams / Zoom)"**
   - For corporate clients who require their own enterprise Google Meet or Teams corporate calendar.
   - Dispatches the open-source bot worker (Attendee / Vexa) to record the external room.
