# 100% Free Open-Source LinkedIn Automation Partner & India Client Acquisition Stack

> **Document Type:** Architectural Blueprint & Production Guide  
> **Module:** Lead Generation & Outreach Automation (`lead_generation`)  
> **Date:** 2026-11-17  
> **Target ICP:** India B2B Decision Makers (HR Heads, TA Directors, CHROs, Recruitment Managers)  
> **Status:** Deployed & Verified Production Architecture  

---

## 1. Executive Summary

Enterprise LinkedIn automation tools (Expandi, Dripify, PhantomBuster, Waalaxy) charge upwards of **$60–$120/month per seat** while imposing arbitrary limitations and risking account suspensions through shared datacenter IP pools.

This document establishes a **100% Free, Open-Source, and Custom-Engineered LinkedIn Automation Partner** specifically architected for the Indian B2B HR/Talent Acquisition ecosystem. It combines:
1. **Zero-Cost Prospect Discovery:** Google X-Ray Dorking to extract thousands of verified Indian HR decision-makers without consuming LinkedIn's monthly commercial search limits.
2. **Stealth Puppeteer Execution Engine:** Local or proxy-routed headless browser runner utilizing `puppeteer-extra-plugin-stealth` and bezier-curve human mouse movement to bypass LinkedIn bot detection algorithms.
3. **Session Cookie (`li_at`) Authentication:** Eliminates password-handling security risks and bypasses 2FA/CAPTCHA challenges.
4. **Autonomous Inbound-Outbound Synchronization:** Orchestrated by self-hosted **n8n**, synced with **Twenty CRM**, verified by **Reacher** (`verify.zily.in`), and scheduled via self-hosted **Cal.com** (`book.zily.in`).

---

## 2. The Free Open-Source Architecture Stack

```
+-----------------------------------------------------------------------------------+
|                        1. DISCOVERY (100% FREE & UNLIMITED)                       |
|  Google X-Ray Dorking Engine (Search Indian HR Heads, TA Leads, CHROs via Google) |
|      Queries: site:in.linkedin.com/in/ ("Head of HR" OR "CHRO") ("Bengaluru"...) |
+-----------------------------------------------------------------------------------+
                                         |
                                         v
+-----------------------------------------------------------------------------------+
|                     2. ENRICHMENT & VERIFICATION GATEWAY                          |
|  - Domain Identification -> Pattern Generator ({first}.{last}@company.com)       |
|  - Reacher Engine (https://verify.zily.in) -> Port 25 Direct SMTP Handshake       |
|  - Result: 100% Deliverable Corporate Email List                                 |
+-----------------------------------------------------------------------------------+
                                         |
                                         v
+-----------------------------------------------------------------------------------+
|                     3. MULTI-CHANNEL OUTREACH ENGINE                              |
|  Channel A: Cold Email Cadence      Channel B: Stealth LinkedIn Engine            |
|  - Powered by Warmbly               - Puppeteer Stealth + li_at Session           |
|  - Custom Tracking Domain           - Indian Residential IP Execution             |
|  - Deliverability Score > 95%       - 15-20 Personalized Invites/Day              |
+-----------------------------------------------------------------------------------+
                                         |
                                         v
+-----------------------------------------------------------------------------------+
|                     4. CONVERSION & SCHEDULING (CAL.COM)                          |
|  - Inbound Link: https://book.zily.in/<slug>                                      |
|  - Automated Google/Outlook Calendar Integration                                  |
|  - WhatsApp / SMS Confirmation & 1-Hour Reminder ping                             |
+-----------------------------------------------------------------------------------+
                                         |
                                         v
+-----------------------------------------------------------------------------------+
|                     5. PIPELINE CRM & INTELLIGENCE                                |
|  - Twenty CRM (Lead -> Opportunity -> Client Agreement)                           |
|  - Automated Meeting Dossier generated via n8n                                    |
+-----------------------------------------------------------------------------------+
```

---

## 3. LinkedIn Anti-Ban Protection Protocol (India ICP Safe Limits)

LinkedIn employs strict machine learning behavioral analysis (tracking keystroke velocity, mouse movement acceleration, click heatmaps, and request cadence). Operating safely without paying for tools requires adhering to these hard rules:

| Parameter | Recommended Safe Threshold | Hard Risk Boundary (Account Ban Warning) |
| :--- | :--- | :--- |
| **Daily Connection Invites** | **15 – 20 per day** | > 30 per day (Triggers weekly cap of 100) |
| **Daily Profile Views** | **35 – 50 per day** | > 80 per day |
| **Action Interval (Delay)** | **45 – 120 seconds (Random Jitter)** | Fixed intervals (e.g. exactly 10s, 30s) |
| **Operating Hours** | **09:30 AM – 06:30 PM IST (Mon–Fri)** | Midnight / 24x7 automated runs |
| **Connection Note Acceptance**| **> 35% with Personalized Icebreaker** | < 15% with generic sales pitches |
| **Pending Invites Cleanup** | **Withdraw invites pending > 21 days** | > 500 unaccepted pending invitations |
| **IP Geolocation** | **Indian ISP IP (Local Machine or SOCKS5)** | US/EU Cloud Datacenter IPs (AWS, Hetzner) |

---

## 4. Google X-Ray Discovery Engine (Unlimited Free Leads)

Searching directly within LinkedIn on a free account rapidly consumes the monthly "Commercial Use Limit". Instead, use Google X-Ray dorking to discover decision-makers without consuming any LinkedIn query limits.

### High-Impact Indian HR Dorking Formulas

#### Formula 1: Tech & Startup HR Heads in Tier-1 Hubs
```text
site:in.linkedin.com/in/ ("Head of Talent Acquisition" OR "Director of Recruitment" OR "VP HR") ("Bangalore" OR "Bengaluru" OR "Gurugram" OR "Noida" OR "Hyderabad") ("hiring" OR "talent")
```

#### Formula 2: Enterprise CHROs & HR VPs
```text
site:in.linkedin.com/in/ ("CHRO" OR "Chief Human Resources Officer" OR "Chief People Officer") ("Mumbai" OR "Delhi" OR "Pune") -intitle:"dir"
```

#### Formula 3: Fast-Growing Mid-Market Companies
```text
site:in.linkedin.com/in/ ("Talent Acquisition Lead" OR "HR Business Partner") ("Series A" OR "Series B" OR "Funded" OR "High Growth") India
```

---

## 5. Deployment & Execution Strategy

### Option A: Local Desktop Runner (Zero Setup, Highest Safety)
- Runs directly on your Windows PC in background via Node.js or Playwright.
- Leverages your native Indian home/office ISP connection (Airtel, Jio, ACT, Tata), guaranteeing **zero datacenter IP flags**.
- Connects directly to your logged-in Chrome profile.

### Option B: Cloud VPS with Webshare Proxy (24/7 Automated Service)
- Runs inside a Docker container on the VPS (`189.72.155.127`).
- Routes outbound traffic through Webshare rotating residential proxies (`p.webshare.io:1080`).
- Exposes a webhook endpoint to receive lead lists from **n8n** or Google Sheets.

---

## 6. Live Infrastructure Status & Verification Evidence

All auxiliary components required for this autonomous engine are deployed, configured, and verified live:

1. **Cal.com Meeting Engine:**
   - **URL:** [https://book.zily.in](https://book.zily.in)
   - **Status:** HTTP 200 / 307 (Active setup page)
   - **SSL:** Let's Encrypt TLS v1.3 Verified
   - **Services:** `calcom`, `calcom-db` (Postgres 15), `calcom-redis` (Redis 7)

2. **Reacher Email Deliverability Engine:**
   - **URL:** [https://verify.zily.in](https://verify.zily.in)
   - **Status:** Healthy RFC 2821 SMTP Handshake Engine
   - **Test Verification:** `VIbhanshu@4bell.com` returned `is_reachable: "safe"`, `can_connect_smtp: true`, `is_deliverable: true`.

3. **Warmbly Email Warmup & Campaign Engine:**
   - **URL:** [https://warmup.zily.in](https://warmup.zily.in)
   - **Admin Account:** `vibhu01m@gmail.com`
   - **Session Link:** Generated and active for direct admin onboarding.

4. **Twenty CRM & n8n Orchestrator:**
   - **Twenty CRM:** Active on internal Coolify network.
   - **n8n Automation:** Active on internal Coolify network with scheduled webhooks.
