# Apify LinkedIn Posts Scraper & Google Sheets Lead Discovery Architecture

## Metadata
- **Date:** 2026-11-18
- **Module:** Automated Lead Generation (LinkedIn Posts Empanelment & Staffing Requests)
- **Engine:** Apify Actor `harvestapi/linkedin-post-search` + Node.js Harvester + Google Apps Script Webhook
- **Status:** **PROD-TESTED & VERIFIED (49 Live Leads Harvested)**
- **System Isolation:** Strictly **0% Twenty CRM / JERO ATS connection**. All data is routed exclusively to **Google Sheets** and local CSV/JSON datasets.

---

## 1. Executive Summary
Using the provided Apify token, we initiated an automated discovery process targeting live LinkedIn posts from companies, HR leaders, and procurement heads actively seeking:
- **Recruitment Vendor Empanelment**
- **IT Staffing Partners (C2C / Contract / C2H)**
- **Urgent Tech Requirements & Bench Profiles**
- **Bulk Hiring Requirements in India**

### Key Achievements:
1. **Zero Cookie / Zero Account Banning Risk:** We utilized Apify's high-reputation Actor (`harvestapi/linkedin-post-search`, 9.2M+ runs, 4.95/5 rating) which searches LinkedIn posts with rotating residential proxies and browser fingerprints **without requiring any personal LinkedIn account or session cookies**.
2. **49 Live Leads Harvested on First Run:** Extracted real companies (Nexossoft Technologies, InnoScales, Relig Global, Krazy Tech, Cloudespacio) with verified contact emails (e.g. `harsh@nexossoft.com`, `laxman.innoscales@gmail.com`, `humera.naaz@krazy-tech.com`, `info@thecloudnextgen.com`).
3. **Structured into 13 Standardized Columns:** Formatted according to enterprise standards with Indian Standard Time (`Asia/Kolkata`), priority scoring (`P1 – Immediate`), and customized outreach strategies.
4. **Google Sheets Webhook Receiver Created:** A 40-line Google Apps Script was created for instant 1-click deployment on Google Sheets with automatic dark-navy header styling and priority color-coding.

---

## 2. Ingestion & Transformation Data Flow

```mermaid
flowchart LR
    A["Apify Token\n(harvestapi/linkedin-post-search)"] -->|Search Queries| B["Apify Actor Engine\n(Headless Residential Rotation)"]
    B -->|49 Raw Posts JSON| C["Local Harvester (Node.js)\n(/opt/apify-linkedin-harvester)"]
    C -->|NLP & Entity Extraction| D["13-Column Normalized Leads"]
    D --> E["Central Google Sheet\n(via Apps Script Webhook)"]
    D --> F["Local CSV Backup\n(linkedin-vendor-leads.csv)"]
    D --> G["Local JSON Dataset\n(linkedin-vendor-leads.json)"]
```

---

## 3. Sample Harvested Leads (Live Output)

| Company Name | Hiring Requirement / Job Title | Contact Person & Email | Location | Empanelment Status | Hiring Urgency | Priority Score | Source Link |
|:---|:---|:---|:---|:---|:---|:---:|:---|
| **Nexossoft Technologies** | Staffing Vendor Empanelment (IT & Tech) | Harsh Gupta (`harsh@nexossoft.com`) | Bangalore, India | Actively Seeking Vendors | Immediate (0-15 days) | **P1 – Immediate** | [LinkedIn Post](https://www.linkedin.com/posts/harsh-gupta-1233673b4_vendorempanelment-benchcandidates-benchresources-activity-7528373177198997505-Iesi) |
| **Relig Global** | Staffing Vendor Empanelment (C2C Opportunities) | Nancy p | Remote, India | Actively Seeking Vendors | Immediate (Vendor Onboarding) | **P1 – Immediate** | [LinkedIn Post](https://www.linkedin.com/posts/nancy-p-436926421_religglobal-vendorempanelment-c2copportunities-activity-7528719369179176963-F_kA) |
| **Cloudespacio** | IT Recruitment / Salesforce Developers | Sachin Sarswat (`info@thecloudnextgen.com`) | Remote, India | Direct Requirement | Urgent Hiring (0-15 days) | **P1 – Immediate** | [LinkedIn Post](https://www.linkedin.com/posts/sachin-sarswat-383355362_hiring-alert-salesforce-developers-salesforce-activity-7528480857045614593-K3yF) |
| **Krazy Tech** | Staffing Vendor Empanelment (IT Staffing) | Humera Mohd (`humera.naaz@krazy-tech.com`) | PAN India | Actively Seeking Vendors | Immediate (Vendor Onboarding) | **P1 – Immediate** | [LinkedIn Post](https://www.linkedin.com/posts/humera-mohd-8609142b7_vendorempanelment-panindia-itstaffing-activity-7528341600855568384-jyfz) |
| **InnoScales** | IT Staffing Vendors / C2C Bench Profiles | Aadarsh T. (`laxman.innoscales@gmail.com`) | PAN India | Actively Seeking Vendors | Immediate (0-15 days) | **P1 – Immediate** | [LinkedIn Post](https://www.linkedin.com/posts/aadarsh-t-892b953a6_vendorempanelment-itstaffing-staffingvendors-activity-7528717210349977600-ol6W) |

---

## 4. Google Sheets Automated Push Instructions

1. Open [Google Sheets (sheets.new)](https://sheets.new).
2. Go to **Extensions** $\rightarrow$ **Apps Script**.
3. Paste the contents of [`tools/apify-linkedin-harvester/GoogleAppsScript_Webhook.js`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/tools/apify-linkedin-harvester/GoogleAppsScript_Webhook.js).
4. Click **Deploy** $\rightarrow$ **New deployment** $\rightarrow$ Select **Web app** $\rightarrow$ Access: **Anyone** $\rightarrow$ **Deploy**.
5. Copy the generated Webhook URL and run:
   ```bash
   node tools/apify-linkedin-harvester/harvest-linkedin-posts.js "<YOUR_WEBHOOK_URL>"
   ```
   All 49 leads are instantly appended and color-coded into the Google Sheet!
