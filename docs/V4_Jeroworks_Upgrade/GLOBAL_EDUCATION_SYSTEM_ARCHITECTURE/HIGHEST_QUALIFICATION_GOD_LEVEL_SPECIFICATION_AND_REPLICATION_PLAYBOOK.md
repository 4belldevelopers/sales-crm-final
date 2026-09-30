# Highest Qualification System: God-Level Specification & Cross-Page Replication Playbook

**Product:** JERO — Multi-Tenant B2B Hiring SaaS & Global Talent Marketplace  
**Document Type:** Authoritative Architecture Specification, Error Audit & Replication Guide  
**File Location:** `docs/V4_Jeroworks_Upgrade/GLOBAL_EDUCATION_SYSTEM_ARCHITECTURE/HIGHEST_QUALIFICATION_GOD_LEVEL_SPECIFICATION_AND_REPLICATION_PLAYBOOK.md`  
**Target:** 195+ Countries, UNESCO ISCED-2011 Compliant, CQRS-Aligned, Zero MySQL Read Strain, 15.6" Screen Responsive  

---

## 1. Executive Summary & Architecture Profile

In the JERO multi-tenant B2B hiring platform, candidates trade on a global talent marketplace across the US, UK, Europe, Middle East, and Asia. A candidate's **Highest Qualification** is one of the top 5 hard filters used by recruiters, hiring managers, visa assessments, and billing rate calculations.

### Core Non-Negotiables Maintained
1. **CQRS Architecture (Rule 1 & Rule 2):** MySQL is strictly for writes (`INSERT`/`UPDATE`/`DELETE`). 0% MySQL reads on web/API request paths. Elasticsearch serves 100% of candidate search, filtering, and talent pool queries.
2. **Client-First In-Browser RAM Execution (Rule 3 & Rule 4):** Filtering, search typeaheads, regional adaptation, and UI rendering execute directly in user browser memory (< 1ms latency), targeting 10,000 concurrent users with 0% server strain.
3. **Database Schema Truth (Rule 8):** Preserves 100% of historical `qualification_master` primary keys (IDs 1 to 60) without breaking foreign keys or existing Elasticsearch documents.
4. **Complete Page Isolation (Rule 17):** Every page's styles and scripts are completely encapsulated, preventing cross-page regressions.

---

## 2. Complete Error Audit & Solutions Applied

During the evolution of this component, 7 critical UI, UX, and architectural issues were audited and definitively resolved:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                               7 CRITICAL ISSUES RESOLVED                               │
├─────────────────────────┬──────────────────────────────────┬───────────────────────────┤
│ Error / Bug Symptom     │ Root Cause                       │ Production Solution       │
├─────────────────────────┼──────────────────────────────────┼───────────────────────────┤
│ 1. Dropdown Hidden /    │ Parent card container had        │ Applied `overflow:        │
│    Clipped Behind Card  │ `overflow: hidden` + low z-index │ visible` and dynamic      │
│                         │ stacking context.                │ `z-index: 1050` on card.  │
├─────────────────────────┼──────────────────────────────────┼───────────────────────────┤
│ 2. Dropdown Search      │ Select2 default input was        │ Replaced with custom      │
│    Unusable / Broken    │ scrolling away out of viewport.  │ sticky-search combobox.   │
├─────────────────────────┼──────────────────────────────────┼───────────────────────────┤
│ 3. Gender Box           │ Overly broad CSS hidden selector │ Scoped combobox hiding to │
│    Disappeared          │ `.bd-control select { display:   │ `.bd-control--has-        │
│                         │ none }` targeted all selects.    │ combobox select` only.    │
├─────────────────────────┼──────────────────────────────────┼───────────────────────────┤
│ 4. Gender Select Box    │ 46px height vs 42px Date of      │ Synchronized height to    │
│    Height Mismatch      │ Birth input height.              │ exact 42px + 6px radius.  │
├─────────────────────────┼──────────────────────────────────┼───────────────────────────┤
│ 5. Star Ratings & Icons │ Missing FA6 font assets caused   │ Inlined crisp SVG icons   │
│    Showed Tofu (▯)      │ empty glyph tofu boxes.          │ with zero font dependency.│
├─────────────────────────┼──────────────────────────────────┼───────────────────────────┤
│ 6. Duplicate Demonym    │ Text concatenation in selected   │ Normalization regex on    │
│    String Concatenation │ label state.                     │ selected item text.       │
├─────────────────────────┼──────────────────────────────────┼───────────────────────────┤
│ 7. Search Exclusion for │ Unlisted degrees picked "Others" │ Added Tier-Specific       │
│    Unlisted Degrees     │ and were dropped from filters.   │ "Other" IDs (61–64).      │
└─────────────────────────┴──────────────────────────────────┴───────────────────────────┘
```

---

## 3. The 7-Category Global Education Standard (UNESCO ISCED-2011)

All worldwide educational degrees are organized into **7 standard optgroups**, ensuring clean cognitive grouping for recruiters on 15.6" screens (1366×768 / 1080p):

```
├── 1. 🎓 Bachelor's / Undergraduate Degrees (ISCED Level 6)
│   ├── B.Tech / B.E (Engineering & Technology) [ID: 13]
│   ├── B.S. / B.Sc (Science, IT & Computing / Licence) [ID: 12]
│   ├── BCA (Computer Applications / Software) [ID: 16]
│   ├── BBA / BMS (Business Administration) [ID: 5]
│   ├── B.Com (Commerce, Accounting & Finance) [ID: 6]
│   ├── B.A (Arts, Humanities & Social Sciences) [ID: 3]
│   ├── B.Des (Design & UX/UI) [ID: 7]
│   ├── BFA (Fine Arts & Digital Media) [ID: 18]
│   ├── B.Arch (Architecture & Urban Planning) [ID: 4]
│   ├── B.Pharma / Nursing / Allied Health (Healthcare & Pharma) [ID: 11]
│   ├── BHM (Hotel & Hospitality Management) [ID: 19]
│   ├── B.Ed (Education & Pedagogy) [ID: 8]
│   ├── B.El.Ed (Elementary Education) [ID: 9]
│   ├── B.P.Ed (Physical Education & Sports) [ID: 10]
│   ├── BVSC / DVM (Veterinary Science & Medicine) [ID: 21]
│   └── Other Bachelor's / Undergraduate Degree [ID: 61]
│
├── 2. 🎓 Master's / Postgraduate Degrees (ISCED Level 7)
│   ├── M.S. / M.Sc / M.Eng (Science, IT & Engineering / Master 2) [ID: 39]
│   ├── M.Tech / M.E (Engineering & Advanced Tech) [ID: 40]
│   ├── MBA / PGDM (Business Administration & Strategy) [ID: 41]
│   ├── MCA (Master of Computer Applications) [ID: 42]
│   ├── MCM (Master of Computer Management) [ID: 43]
│   ├── M.Com (Commerce, Economics & Finance) [ID: 35]
│   ├── M.A (Arts, Humanities & Social Sciences) [ID: 32]
│   ├── M.Des (Master of Design / UX) [ID: 36]
│   ├── MFA (Fine Arts & Creative Media) [ID: 45]
│   ├── M.Arch (Master of Architecture) [ID: 33]
│   ├── M.Pharma / Pharm.D / MPT (Advanced Healthcare & Pharma) [ID: 38]
│   ├── M.Ed (Master of Education) [ID: 37]
│   ├── MVSC (Master of Veterinary Science) [ID: 47]
│   ├── Integrated PG / Dual Degree (B.Tech+M.Tech / BS+MS) [ID: 30]
│   └── Other Master's / Postgraduate Degree [ID: 62]
│
├── 3. 🏛️ Doctorate & Research Degrees (ISCED Level 8)
│   ├── Ph.D. / Doctorate / Post-Doc (Doctor of Philosophy) [ID: 52]
│   └── Other Doctorate / Post-Doctoral Degree [ID: 64]
│
├── 4. 📜 Associate Degrees & Diplomas (ISCED Level 5)
│   ├── Associate Degree / Polytechnic Diploma (A.A. / A.S. / Diploma) [ID: 22]
│   ├── PG Diploma (Post Graduate Diploma / Certificate) [ID: 48]
│   ├── HND / HNC (Higher National Diploma / Certificate) [ID: 25]
│   └── Other Associate / Diploma / Vocational Degree [ID: 63]
│
├── 5. ⚖️ Professional, Legal & Medical Degrees
│   ├── CA / CPA / CFA / ACCA (Finance & Accounting) [ID: 26]
│   ├── CS (Company Secretary / Corporate Governance) [ID: 27]
│   ├── ICWA / CMA (Cost & Management Accounting) [ID: 29]
│   ├── LLB / J.D. (Law Degree / Juris Doctor) [ID: 23]
│   ├── LLM (Master of Laws / Legal Postgrad) [ID: 31]
│   ├── MBBS / M.D. / D.O. (General Medicine & Surgery) [ID: 24]
│   ├── MD / MS (Medical Specialty / Master of Surgery) [ID: 46]
│   ├── DM (Doctor of Medicine - Medical Super Specialty) [ID: 28]
│   ├── M.Ch (Surgical Super Specialty) [ID: 34]
│   ├── BDS / DDS (Dental Surgery & Medicine) [ID: 17]
│   ├── MDS (Master of Dental Surgery) [ID: 44]
│   ├── BAMS (Ayurvedic Medicine & Surgery) [ID: 15]
│   ├── BHMS (Homeopathic Medicine & Surgery) [ID: 20]
│   └── B.U.M.S (Unani Medicine & Surgery) [ID: 14]
│
├── 6. 🏫 Secondary & High School (ISCED Level 3)
│   ├── 12th Standard / High School Diploma / A-Levels / GED [ID: 2]
│   ├── 10th Standard / Matriculation / Secondary School [ID: 1]
│   ├── A Levels / Advanced Level (UK / International) [ID: 49]
│   ├── GCSE / O-Levels (UK / International) [ID: 51]
│   └── Vocational Secondary / GNVQ / Technical Certificate [ID: 50]
│
└── 7. 🌐 Other Qualifications
    └── Others / Intensive Bootcamp / Specialized Certification [ID: 60]
```

---

## 4. Database & Backend Architecture

### 4.1 Master Table Schema (`qualification_master`)
```sql
CREATE TABLE `qualification_master` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(55) NOT NULL,
  `display_order` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 4.2 Seed Migration (`database/migrations/2026_09_30_000001_seed_tier_specific_other_qualifications.php`)
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('qualification_master')) {
            $records = [
                ['id' => 61, 'name' => "Other Bachelor's Degree", 'display_order' => 56],
                ['id' => 62, 'name' => "Other Master's Degree", 'display_order' => 57],
                ['id' => 63, 'name' => 'Other Associate / Diploma', 'display_order' => 58],
                ['id' => 64, 'name' => 'Other Doctorate / Post-Doctoral', 'display_order' => 59],
            ];

            foreach ($records as $rec) {
                DB::table('qualification_master')->updateOrInsert(
                    ['id' => $rec['id']],
                    [
                        'name' => $rec['name'],
                        'display_order' => $rec['display_order'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            Cache::forget('qualification_master_list');
            Cache::forget('global_qualification_master');
            Cache::forget('qualification_master');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('qualification_master')) {
            DB::table('qualification_master')->whereIn('id', [61, 62, 63, 64])->delete();

            Cache::forget('qualification_master_list');
            Cache::forget('global_qualification_master');
            Cache::forget('qualification_master');
        }
    }
};
```

### 4.3 Controller Caching Contract
```php
// In BenchCandidateController / JobController / AppTrackerController
$this->qualification = Cache::remember('qualification_master_list', 86400, function () {
    return DB::table('qualification_master')
        ->select('*')
        ->whereNotIn('id', [57, 58, 59]) // Wildcards reserved for Job Requirements
        ->orderBy('qualification_master.display_order', 'ASC')
        ->get();
});
```

### 4.4 Elasticsearch Sync Bridge (`GeneralMethod::elasticBenchCandidateAddEditIndex`)
```php
// Stored in job_applications.qualification (integer foreign key ID)
// Left joined to qualification_master on index:
->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
->select(
    'job_applications.qualification',
    'qualification_master.name as qualification_name'
)
```

---

## 5. Complete Frontend Implementation Blueprint

### 5.1 Blade View Markup (`create.blade.php`)
```php
{{-- Highest Qualification: Adaptive Country Curation + Global Alias Escape Search --}}
<div class="fg fv-row bd-field">
  <label>Highest Qualification <span class="rq">*</span></label>
  <div class="bd-control bd-control--select">
    <i class="fa-solid fa-graduation-cap"></i>
    <select name="qualification" id="qualification">
      <option value="">Select qualification</option>
      @php
        // Map incoming database qualification records by ID
        $qualMap = [];
        if (!empty($qualification)) {
            foreach ($qualification as $qItem) {
                $qualMap[$qItem->id] = $qItem;
            }
        }

        // 7-Category Global Education Standard with Regional Prov & Global Alias Taxonomy
        $educationCategories = [
            "🎓 Bachelor's / Undergraduate Degrees" => [
                13 => ['label' => 'B.Tech / B.E (Engineering & Technology)', 'badge' => 'Engineering / Tech', 'regions' => 'Indian,Commonwealth,Global', 'aliases' => 'btech,b.tech,b tech,be,b.e,bachelor of technology,bachelor of engineering,beng,b.eng,bse,bs engineering,diplome ingenieur,diplom ingenieur,software engineer,computer science engineering,cse,mechanical,electrical,civil'],
                12 => ['label' => 'B.S. / B.Sc (Science, IT & Computing / Licence)', 'badge' => 'USA / Global / EU', 'regions' => 'American,Canadian,British,Australian,European,Indian,Global', 'aliases' => 'bs,b.s.,bsc,b.sc,bachelor of science,bachelor science,bs cs,bsc cs,b sc,bsc it,bs it,information technology,computer science,data science,cybersecurity,cyber security,cloud computing,artificial intelligence,software development,licence,licenciatura,bac+3,grado'],
                16 => ['label' => 'BCA (Computer Applications / Software)', 'badge' => 'IT / Software', 'regions' => 'Indian,Global', 'aliases' => 'bca,b.c.a.,bachelor of computer applications,bachelor computer applications,computer programming,web development'],
                5  => ['label' => 'BBA / BMS (Business Administration)', 'badge' => 'Business / Global', 'regions' => 'Indian,American,Canadian,British,Australian,Global', 'aliases' => 'bba,b.b.a.,bms,b.m.s.,bachelor of business administration,bachelor business,management studies,business management,bba marketing,bba finance'],
                6  => ['label' => 'B.Com (Commerce, Accounting & Finance)', 'badge' => 'Commerce / Finance', 'regions' => 'Indian,British,Canadian,Australian,Commonwealth,Global', 'aliases' => 'bcom,b.com,bachelor of commerce,bachelor commerce,commerce,accounting,finance,banking'],
                3  => ['label' => 'B.A (Arts, Humanities & Social Sciences)', 'badge' => 'Humanities / Global', 'regions' => 'Indian,American,Canadian,British,Australian,European,Global', 'aliases' => 'ba,b.a.,bachelor of arts,bachelor arts,humanities,economics,psychology,english,journalism,mass communication,history'],
                7  => ['label' => 'B.Des (Design & UX/UI)', 'badge' => 'Design / Global', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'bdes,b.des,bachelor of design,bachelor design,ui ux,product design,graphic design,fashion design,industrial design'],
                18 => ['label' => 'BFA (Fine Arts & Digital Media)', 'badge' => 'Arts / Media', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'bfa,b.f.a.,bachelor of fine arts,visual arts,animation,digital media,illustration'],
                4  => ['label' => 'B.Arch (Architecture & Urban Planning)', 'badge' => 'Architecture', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'barch,b.arch,bachelor of architecture,architectural design,urban planning'],
                11 => ['label' => 'B.Pharma / Nursing / Allied Health (Healthcare & Pharma)', 'badge' => 'Healthcare / Pharma', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'bpharma,b.pharma,b pharm,b.pharm,bachelor of pharmacy,pharmacy,nursing,b.sc nursing,bsc nursing,rn,registered nurse,bpt,physiotherapy,bachelor of physiotherapy,allied health,paramedical,pharm d,pharm.d'],
                19 => ['label' => 'BHM (Hotel & Hospitality Management)', 'badge' => 'Hospitality', 'regions' => 'Indian,Global', 'aliases' => 'bhm,b.h.m.,bachelor of hotel management,hospitality,tourism,culinary arts'],
                8  => ['label' => 'B.Ed (Education & Pedagogy)', 'badge' => 'Education', 'regions' => 'Indian,British,Commonwealth,Global', 'aliases' => 'bed,b.ed,bachelor of education,teaching,pedagogy'],
                9  => ['label' => 'B.El.Ed (Elementary Education)', 'badge' => 'Education', 'regions' => 'Indian', 'aliases' => 'beled,b.el.ed,bachelor of elementary education,primary teaching'],
                10 => ['label' => 'B.P.Ed (Physical Education & Sports)', 'badge' => 'Sports Science', 'regions' => 'Indian,Global', 'aliases' => 'bped,b.p.ed,bachelor of physical education,sports science,kinesiology'],
                21 => ['label' => 'BVSC / DVM (Veterinary Science & Medicine)', 'badge' => 'Veterinary', 'regions' => 'Indian,British,American,Commonwealth,Global', 'aliases' => 'bvsc,b.v.sc,bachelor of veterinary science,dvm,veterinary medicine,veterinarian'],
                61 => ['label' => 'Other Bachelor\'s / Undergraduate Degree', 'badge' => 'Bachelor\'s / Other', 'regions' => 'Global', 'aliases' => 'other bachelor,other bachelors,other undergraduate,bachelors other,custom bachelor,unlisted bachelor'],
            ],
            "🎓 Master's / Postgraduate Degrees" => [
                39 => ['label' => 'M.S. / M.Sc / M.Eng (Science, IT & Engineering / Master 2)', 'badge' => 'USA / Global / EU', 'regions' => 'American,Canadian,British,Australian,European,Indian,Global', 'aliases' => 'ms,m.s.,msc,m.sc,master of science,master science,ms cs,msc cs,m s,m sc,meng,m.eng,master of engineering,data science,ai,artificial intelligence,machine learning,deep learning,cybersecurity,cloud architecture,big data,bioinformatics,master 2,bac+5,magister'],
                40 => ['label' => 'M.Tech / M.E (Engineering & Advanced Tech)', 'badge' => 'Engineering / Postgrad', 'regions' => 'Indian,Global', 'aliases' => 'mtech,m.tech,m tech,me,m.e,master of technology,master of engineering,meng,m.eng,postgraduate engineering,vlsi,embedded,robotics'],
                41 => ['label' => 'MBA / PGDM (Business Administration & Strategy)', 'badge' => 'Management / Global', 'regions' => 'Indian,American,Canadian,British,Australian,European,Global', 'aliases' => 'mba,m.b.a.,pgdm,p.g.d.m.,master of business administration,master business administration,executive mba,emba,insead,wharton,hbs,iim,post graduate diploma in management,business management,strategy,consulting'],
                42 => ['label' => 'MCA (Master of Computer Applications)', 'badge' => 'IT / Software', 'regions' => 'Indian,Global', 'aliases' => 'mca,m.c.a.,master of computer applications,master computer applications,software engineering,computer science'],
                43 => ['label' => 'MCM (Master of Computer Management)', 'badge' => 'Management & IT', 'regions' => 'Indian', 'aliases' => 'mcm,m.c.m.,master of computer management,it management'],
                35 => ['label' => 'M.Com (Commerce, Economics & Finance)', 'badge' => 'Commerce / Finance', 'regions' => 'Indian,British,Commonwealth,Global', 'aliases' => 'mcom,m.com,master of commerce,economics,financial management,accounting'],
                32 => ['label' => 'M.A (Arts, Humanities & Social Sciences)', 'badge' => 'Humanities / Global', 'regions' => 'Indian,American,Canadian,British,Australian,European,Global', 'aliases' => 'ma,m.a.,master of arts,humanities,sociology,psychology,political science,international relations,literature'],
                36 => ['label' => 'M.Des (Master of Design / UX)', 'badge' => 'Design / Postgrad', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'mdes,m.des,master of design,interaction design,ux design,product design'],
                45 => ['label' => 'MFA (Fine Arts & Creative Media)', 'badge' => 'Fine Arts / Media', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'mfa,m.f.a.,master of fine arts,creative media,visual arts,cinematography'],
                33 => ['label' => 'M.Arch (Master of Architecture)', 'badge' => 'Architecture', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'march,m.arch,master of architecture,landscape architecture,urban design'],
                38 => ['label' => 'M.Pharma / Pharm.D / MPT (Advanced Healthcare & Pharma)', 'badge' => 'Healthcare / Pharma', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'mpharma,m.pharma,m pharm,m.pharm,master of pharmacy,pharm d,pharm.d,doctor of pharmacy,mpt,master of physiotherapy,pharmacology,clinical research'],
                37 => ['label' => 'M.Ed (Master of Education)', 'badge' => 'Education', 'regions' => 'Indian,British,Commonwealth,Global', 'aliases' => 'med,m.ed,master of education,educational leadership,curriculum'],
                47 => ['label' => 'MVSC (Master of Veterinary Science)', 'badge' => 'Veterinary Postgrad', 'regions' => 'Indian,British,Commonwealth,Global', 'aliases' => 'mvsc,m.v.sc,master of veterinary science,veterinary surgery'],
                30 => ['label' => 'Integrated PG / Dual Degree (B.Tech+M.Tech / BS+MS)', 'badge' => 'Integrated / Dual', 'regions' => 'Indian,European,American,Global', 'aliases' => 'integrated pg,dual degree,integrated mtech,integrated btech mtech,5 year integrated,bs ms,b.s. m.s.,btech mtech'],
                62 => ['label' => 'Other Master\'s / Postgraduate Degree', 'badge' => 'Master\'s / Other', 'regions' => 'Global', 'aliases' => 'other master,other masters,other postgraduate,masters other,custom master,unlisted master,mph,msw,mphil,m.phil'],
            ],
            "🏛️ Doctorate & Research Degrees" => [
                52 => ['label' => 'Ph.D. / Doctorate / Post-Doc (Doctor of Philosophy)', 'badge' => 'Doctorate / Global', 'regions' => 'Indian,American,Canadian,British,Australian,European,Global', 'aliases' => 'phd,ph.d.,ph.d,doctorate,doctor of philosophy,dphil,d.phil,postdoc,post doc,post doctoral,fellowship,research scholar,promotion,dr'],
                64 => ['label' => 'Other Doctorate / Post-Doctoral Degree', 'badge' => 'Doctorate / Other', 'regions' => 'Global', 'aliases' => 'other doctorate,other phd,postdoc,post-doctoral,dba,d.sc,doctor of business administration,research fellow'],
            ],
            "📜 Associate Degrees & Diplomas" => [
                22 => ['label' => 'Associate Degree / Polytechnic Diploma (A.A. / A.S. / Diploma)', 'badge' => 'Polytechnic / Assoc', 'regions' => 'Indian,American,Canadian,Australian,European,Global', 'aliases' => 'diploma,polytechnic,associate degree,associate of science,associate of arts,aa,a.a.,as,a.s.,aas,a.a.s.,community college,2 year degree,bts,dut,fachhochschule'],
                48 => ['label' => 'PG Diploma (Post Graduate Diploma / Certificate)', 'badge' => 'PG Diploma', 'regions' => 'Indian,Canadian,British,Commonwealth,Global', 'aliases' => 'pg diploma,pgd,pgdiploma,post graduate diploma,postgraduate certificate,post-degree diploma,canadian pg diploma'],
                25 => ['label' => 'HND / HNC (Higher National Diploma / Certificate)', 'badge' => 'UK / Commonwealth', 'regions' => 'British,Commonwealth,Global', 'aliases' => 'hnd,hnc,higher national diploma,higher national certificate,vocational qualification,btec'],
                63 => ['label' => 'Other Associate / Diploma / Vocational Degree', 'badge' => 'Diploma / Other', 'regions' => 'Global', 'aliases' => 'other diploma,other associate,vocational diploma,technical certificate,custom diploma,unlisted diploma'],
            ],
            "⚖️ Professional, Legal & Medical Degrees" => [
                26 => ['label' => 'CA / CPA / CFA / ACCA (Finance & Accounting)', 'badge' => 'Finance / Global', 'regions' => 'Indian,American,Canadian,British,Australian,European,Global', 'aliases' => 'ca,cpa,cfa,acca,chartered accountant,chartered accountancy,certified public accountant,chartered financial analyst,cima,cost accountant,accounting,finance,audit'],
                27 => ['label' => 'CS (Company Secretary / Corporate Governance)', 'badge' => 'Corporate Law', 'regions' => 'Indian,British,Commonwealth', 'aliases' => 'cs,c.s.,company secretary,company secretaryship,corporate governance,icsi'],
                29 => ['label' => 'ICWA / CMA (Cost & Management Accounting)', 'badge' => 'Cost Accounting', 'regions' => 'Indian,British,Commonwealth,Global', 'aliases' => 'icwa,cma,cost accountant,cost and management accountant,cma usa,icmai'],
                23 => ['label' => 'LLB / J.D. (Law Degree / Juris Doctor)', 'badge' => 'Legal / Global', 'regions' => 'Indian,British,American,Commonwealth,Global', 'aliases' => 'llb,l.l.b.,bachelor of laws,jd,j.d.,juris doctor,law degree,bar admitted,attorney,lawyer,solicitor,advocate'],
                31 => ['label' => 'LLM (Master of Laws / Legal Postgrad)', 'badge' => 'Legal / Postgrad', 'regions' => 'Indian,American,Canadian,British,European,Commonwealth,Global', 'aliases' => 'llm,l.l.m.,master of laws,corporate law,international law,intellectual property'],
                24 => ['label' => 'MBBS / M.D. / D.O. (General Medicine & Surgery)', 'badge' => 'Medical / Surgery', 'regions' => 'Indian,British,American,Commonwealth,Global', 'aliases' => 'mbbs,m.b.b.s.,bachelor of medicine,md,m.d.,doctor of medicine,do,d.o.,doctor of osteopathic medicine,physician'],
                46 => ['label' => 'MD / MS (Medical Specialty / Master of Surgery)', 'badge' => 'Medical Specialty', 'regions' => 'Indian,American,Canadian,British,Commonwealth,Global', 'aliases' => 'md,m.d.,ms general surgery,master of surgery,doctor of medicine,residency,medical specialty'],
                28 => ['label' => 'DM (Doctor of Medicine - Medical Super Specialty)', 'badge' => 'Super Specialty', 'regions' => 'Indian,Global', 'aliases' => 'dm,d.m.,super specialty,cardiology,neurology,oncology'],
                34 => ['label' => 'M.Ch (Surgical Super Specialty)', 'badge' => 'Surgical Specialty', 'regions' => 'Indian,Global', 'aliases' => 'mch,m.ch,magister chirurgiae,neurosurgery,cardiothoracic'],
                17 => ['label' => 'BDS / DDS (Dental Surgery & Medicine)', 'badge' => 'Dental / Global', 'regions' => 'Indian,British,American,Commonwealth,Global', 'aliases' => 'bds,b.d.s.,dds,d.d.s.,dmd,d.m.d.,dental surgery,dentist,oral surgery'],
                44 => ['label' => 'MDS (Master of Dental Surgery)', 'badge' => 'Dental Postgrad', 'regions' => 'Indian,British,Commonwealth,Global', 'aliases' => 'mds,m.d.s.,master dental surgery,orthodontics,prosthodontics'],
                15 => ['label' => 'BAMS (Ayurvedic Medicine & Surgery)', 'badge' => 'Ayurveda', 'regions' => 'Indian', 'aliases' => 'bams,b.a.m.s.,ayurveda,ayush'],
                20 => ['label' => 'BHMS (Homeopathic Medicine & Surgery)', 'badge' => 'Homeopathy', 'regions' => 'Indian', 'aliases' => 'bhms,b.h.m.s.,homeopathy,ayush'],
                14 => ['label' => 'B.U.M.S (Unani Medicine & Surgery)', 'badge' => 'Unani', 'regions' => 'Indian', 'aliases' => 'bums,b.u.m.s.,unani,ayush'],
            ],
            "🏫 Secondary & High School" => [
                2  => ['label' => '12th Standard / High School Diploma / A-Levels / GED', 'badge' => 'High School', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => '12th,12th standard,hsc,intermediate,high school,high school diploma,ged,abitur,baccalaureat,senior secondary'],
                1  => ['label' => '10th Standard / Matriculation / Secondary School', 'badge' => 'Secondary', 'regions' => 'Indian,Global', 'aliases' => '10th,10th standard,ssc,matric,matriculation,secondary school'],
                49 => ['label' => 'A Levels / Advanced Level (UK / International)', 'badge' => 'UK A-Levels', 'regions' => 'British,Commonwealth,European,Global', 'aliases' => 'a levels,a-levels,alevel,advanced level,gce a levels'],
                51 => ['label' => 'GCSE / O-Levels (UK / International)', 'badge' => 'UK GCSE', 'regions' => 'British,Commonwealth,Global', 'aliases' => 'gcse,o-levels,o levels,olevel,igcse,gce o level'],
                50 => ['label' => 'Vocational Secondary / GNVQ / Technical Certificate', 'badge' => 'Vocational', 'regions' => 'British,Commonwealth,Global', 'aliases' => 'gnvq,vocational secondary,nvq,btec level 2,technical certificate'],
            ],
            "🌐 Other Qualifications" => [
                60 => ['label' => 'Others / Intensive Bootcamp / Specialized Certification', 'badge' => 'Other / Bootcamp', 'regions' => 'Global,Indian,American,Canadian,British,Australian,European', 'aliases' => 'other,others,non disclosed,bootcamp,coding bootcamp,full stack,certifications,aws certified,pmp,scrum master'],
            ],
        ];

        $selectedQualId = @$application->qualification;
        $renderedIds = [];
      @endphp

      @foreach($educationCategories as $categoryLabel => $catItems)
        <optgroup label="{{ $categoryLabel }}">
          @foreach($catItems as $qId => $itemMeta)
            @if(isset($qualMap[$qId]))
              @php
                $renderedIds[] = $qId;
                $isSelected = ((string)$selectedQualId === (string)$qId);
              @endphp
              <option value="{{ $qId }}" 
                      data-regions="{{ $itemMeta['regions'] }}"
                      data-aliases="{{ $itemMeta['aliases'] }}"
                      data-badge="{{ $itemMeta['badge'] }}"
                      {{ $isSelected ? 'selected="selected"' : '' }}>
                {{ $itemMeta['label'] }}
              </option>
            @endif
          @endforeach
        </optgroup>
      @endforeach

      {{-- Fallback safety for any unmapped database record --}}
      @php
        $remainingItems = [];
        foreach ($qualMap as $qId => $qObj) {
            if (!in_array($qId, $renderedIds)) {
                $remainingItems[] = $qObj;
            }
        }
      @endphp
      @if(count($remainingItems) > 0)
        <optgroup label="Additional Qualifications">
          @foreach($remainingItems as $remItem)
            <option value="{{ $remItem->id }}" data-regions="Global" data-aliases="" data-badge="Other" {{ ((string)$selectedQualId === (string)$remItem->id) ? 'selected="selected"' : '' }}>
              {{ $remItem->name }}
            </option>
          @endforeach
        </optgroup>
      @endif
    </select>
  </div>
</div>
```

---

### 5.2 CSS Architecture (`_form_styles.blade.php`)
```css
/* Card Container Overflow Fix: Prevents Dropdown Clipping */
.bd-card {
    overflow: visible !important;
    position: relative;
    z-index: 1;
}

.bd-card.has-active-combobox {
    z-index: 1050 !important;
}

/* Custom Combobox Trigger Container */
.jero-custom-select {
    position: relative;
    width: 100%;
    font-family: inherit;
    font-size: 13px;
    user-select: none;
}

.bd-control--has-combobox select {
    display: none !important;
}

.jero-cs-trigger {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    min-height: 42px;
    height: 42px;
    padding: 0 12px;
    background: #ffffff;
    border: 1px solid #d8dbe0;
    border-radius: 6px;
    color: #1e293b;
    cursor: pointer;
    box-sizing: border-box;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
    text-align: left;
}

.jero-cs-trigger:hover {
    border-color: #94a3b8;
}

.jero-custom-select.is-open .jero-cs-trigger {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
}

.jero-cs-left {
    display: flex;
    align-items: center;
    gap: 8px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    flex: 1;
}

.jero-cs-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    font-size: 13px;
    flex-shrink: 0;
}

.jero-cs-value {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #0f172a;
    font-weight: 500;
}

.jero-cs-value.jero-cs-placeholder {
    color: #94a3b8;
    font-weight: 400;
}

.jero-cs-right {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}

.jero-cs-badge {
    display: inline-block;
    padding: 2px 7px;
    font-size: 10px;
    font-weight: 600;
    background: #eff6ff;
    color: #2563eb;
    border-radius: 4px;
    border: 1px solid #dbeafe;
}

.jero-cs-arrow {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    color: #64748b;
    transition: transform 0.2s ease;
}

.jero-custom-select.is-open .jero-cs-arrow {
    transform: rotate(180deg);
}

/* Floating Sticky Dropdown */
.jero-cs-dropdown {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    z-index: 1060;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
    display: none;
    overflow: hidden;
    animation: jeroCsFadeIn 0.15s ease-out;
}

.jero-custom-select.is-open .jero-cs-dropdown {
    display: block;
}

/* Sticky Search Box Header */
.jero-cs-search-box {
    position: sticky;
    top: 0;
    z-index: 2;
    display: flex;
    align-items: center;
    padding: 8px 10px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.jero-cs-search-icon {
    font-size: 12px;
    color: #94a3b8;
    margin-right: 8px;
}

.jero-cs-search-input {
    width: 100%;
    border: none;
    outline: none;
    background: transparent;
    font-size: 12px;
    color: #0f172a;
    font-family: inherit;
}

.jero-cs-search-input::placeholder {
    color: #94a3b8;
}

.jero-cs-search-clear {
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    font-size: 14px;
    padding: 0 4px;
}

/* Scrollable Options List */
.jero-cs-list {
    max-height: 240px;
    overflow-y: auto;
    overscroll-behavior: contain;
}

.jero-cs-group-title {
    padding: 6px 12px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    background: #f8fafc;
    border-top: 1px solid #f1f5f9;
    border-bottom: 1px solid #f1f5f9;
}

.jero-cs-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    font-size: 12px;
    color: #1e293b;
    cursor: pointer;
    transition: background-color 0.12s ease;
}

.jero-cs-item:hover,
.jero-cs-item.is-focused {
    background-color: #f1f5f9;
    color: #0f172a;
}

.jero-cs-item.is-selected {
    background-color: #eff6ff;
    color: #1d4ed8;
    font-weight: 600;
}

.jero-cs-item.is-hidden {
    display: none !important;
}

.jero-cs-item-badge {
    padding: 2px 6px;
    font-size: 9px;
    font-weight: 600;
    background: #f1f5f9;
    color: #475569;
    border-radius: 4px;
    margin-left: 8px;
    white-space: nowrap;
}

.jero-cs-empty {
    padding: 16px;
    text-align: center;
    color: #64748b;
    font-size: 12px;
}

@keyframes jeroCsFadeIn {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
}
```

---

### 5.3 JavaScript Execution Engine
```javascript
(function($) {
    'use strict';

    // 1. Regional Bucket Resolver for Adaptive Idle Curation
    function getRegionalBucket(nationality) {
        var nat = (nationality || '').trim();
        if (!nat || nat === 'Indian') return ['Indian'];
        if (nat === 'American') return ['American'];
        if (nat === 'Canadian') return ['Canadian', 'American'];
        if (nat === 'British') return ['British', 'Commonwealth'];
        if (nat === 'Australian') return ['Australian', 'Commonwealth', 'British'];
        if (['German', 'French', 'Dutch', 'Irish', 'Italian', 'Spanish', 'Swedish', 'Swiss', 'Austrian', 'Belgian', 'Polish'].indexOf(nat) !== -1) {
            return ['European'];
        }
        if (['Emirati', 'Saudi', 'Qatari', 'Kuwaiti', 'Bahraini', 'Omani'].indexOf(nat) !== -1) {
            return ['Indian', 'British', 'American'];
        }
        return ['all']; // Default: unlocks all international options
    }

    // 2. Token-Based, Accent-Insensitive String Normalizer
    function jeroCleanStr(str) {
        if (!str) return '';
        return str.toString()
            .toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[.,\/#!$%\^&\*;:{}=\-_`~()]/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();
    }

    // 3. Close All Comboboxes Helper
    function closeAllComboboxes() {
        $('.jero-custom-select').removeClass('is-open open-up');
        $('.jero-cs-trigger').attr('aria-expanded', 'false');
        $('.bd-card').removeClass('has-active-combobox');
    }

    // 4. Build Custom Combobox DOM from native <select id="qualification">
    function buildQualificationCombobox() {
        var $select = $('#qualification');
        if (!$select.length) return;
        $('#jero-cs-qualification').remove();

        var placeholder = 'Select qualification';
        var currentVal = ($select.val() || '').trim();
        var currentLabel = placeholder;
        var currentBadge = '';

        if (currentVal) {
            var $selOpt = $select.find('option:selected').first();
            if ($selOpt.length && $selOpt.val()) {
                currentLabel = $selOpt.text().trim();
                currentBadge = $selOpt.attr('data-badge') || '';
            }
        }

        var html = '<div class="jero-custom-select" id="jero-cs-qualification">' +
            '<button type="button" class="jero-cs-trigger" aria-haspopup="listbox" aria-expanded="false">' +
                '<div class="jero-cs-left">' +
                    '<span class="jero-cs-icon"><i class="fa-solid fa-graduation-cap"></i></span>' +
                    '<span class="jero-cs-value ' + (!currentVal ? 'jero-cs-placeholder' : '') + '">' + currentLabel + '</span>' +
                '</div>' +
                '<div class="jero-cs-right">' +
                    '<span class="jero-cs-badge" style="' + (currentBadge ? '' : 'display:none;') + '">' + currentBadge + '</span>' +
                    '<span class="jero-cs-arrow"><i class="fa-solid fa-chevron-down"></i></span>' +
                '</div>' +
            '</button>' +
            '<div class="jero-cs-dropdown">' +
                '<div class="jero-cs-search-box">' +
                    '<i class="fa-solid fa-magnifying-glass jero-cs-search-icon"></i>' +
                    '<input type="text" class="jero-cs-search-input" placeholder="Search degree or acronym (e.g. B.Tech, MS, HND, CA, MBA)..." spellcheck="false" autocomplete="off">' +
                    '<button type="button" class="jero-cs-search-clear" style="display:none;" title="Clear search">&times;</button>' +
                '</div>' +
                '<div class="jero-cs-list"></div>' +
                '<div class="jero-cs-empty" style="display:none;"><i class="fa-solid fa-graduation-cap"></i> No matching degrees found</div>' +
            '</div>' +
        '</div>';

        var $cs = $(html);
        var $list = $cs.find('.jero-cs-list');
        var selectedItemDone = false;

        $select.children().each(function() {
            var $node = $(this);
            if ($node.is('optgroup')) {
                var groupLabel = $node.attr('label') || '';
                var $group = $('<div class="jero-cs-group"><div class="jero-cs-group-title">' + groupLabel + '</div></div>');
                $node.find('option').each(function() {
                    var $opt = $(this);
                    var val = $opt.val();
                    if (!val) return;
                    var text = $opt.text().trim();
                    var regions = $opt.attr('data-regions') || 'Global';
                    var aliases = $opt.attr('data-aliases') || '';
                    var badge = $opt.attr('data-badge') || '';
                    var isSelected = (val === currentVal && !selectedItemDone);
                    if (isSelected) selectedItemDone = true;
                    var badgeHtml = badge ? '<span class="jero-cs-item-badge">' + badge + '</span>' : '';
                    var $item = $('<div class="jero-cs-item ' + (isSelected ? 'is-selected' : '') + '" data-val="' + val + '" data-regions="' + regions + '" data-aliases="' + aliases + '" data-badge="' + badge + '">' +
                        '<span class="jero-cs-item-text">' + text + '</span>' +
                        badgeHtml +
                    '</div>');
                    $group.append($item);
                });
                $list.append($group);
            }
        });

        $select.closest('.bd-control').addClass('bd-control--has-combobox');
        $select.after($cs);
    }

    // 5. Intelligent Filtering (Idle Regional Curation vs Global Search Unlock)
    function filterQualificationItems() {
        var $cs = $('#jero-cs-qualification');
        if (!$cs.length) return;
        var selectedNat = ($('#nationality').val() || '').trim();
        var selectedQual = ($('#qualification').val() || '').trim();
        var bucket = getRegionalBucket(selectedNat);
        var rawQuery = $cs.find('.jero-cs-search-input').val() || '';
        var cleanQ = jeroCleanStr(rawQuery);
        var compactQ = cleanQ.replace(/\s+/g, '');
        var queryWords = cleanQ.split(' ').filter(Boolean);
        var totalVisible = 0;

        $cs.find('.jero-cs-group').each(function() {
            var $group = $(this);
            var groupVisible = 0;

            $group.find('.jero-cs-item').each(function() {
                var $item = $(this);
                var val = String($item.attr('data-val') || '');
                var text = $item.find('.jero-cs-item-text').text() || '';
                var aliases = $item.attr('data-aliases') || '';
                var badge = $item.attr('data-badge') || '';
                var regions = ($item.attr('data-regions') || 'Global').split(',').map(function(r) { return r.trim(); });

                var isMatch = false;

                if (cleanQ.length > 0) {
                    // Global Search Mode: Unlocks all degrees worldwide regardless of nationality
                    var cleanText = jeroCleanStr(text);
                    var cleanAliases = jeroCleanStr(aliases);
                    var cleanBadge = jeroCleanStr(badge);
                    var combined = cleanText + ' ' + cleanAliases + ' ' + cleanBadge;
                    var combinedCompact = combined.replace(/\s+/g, '');

                    if (cleanText.indexOf(cleanQ) !== -1 || cleanAliases.indexOf(cleanQ) !== -1 || cleanBadge.indexOf(cleanQ) !== -1) {
                        isMatch = true;
                    } else if (combinedCompact.indexOf(compactQ) !== -1) {
                        isMatch = true;
                    } else if (queryWords.length > 0) {
                        var allWordsMatch = true;
                        for (var w = 0; w < queryWords.length; w++) {
                            if (combined.indexOf(queryWords[w]) === -1 && combinedCompact.indexOf(queryWords[w]) === -1) {
                                allWordsMatch = false;
                                break;
                            }
                        }
                        if (allWordsMatch) isMatch = true;
                    }
                } else {
                    // Idle Mode: Regionally curates degrees based on selected nationality
                    if (val === selectedQual) {
                        isMatch = true;
                    } else if (bucket.indexOf('all') !== -1) {
                        isMatch = true;
                    } else if (regions.indexOf('Global') !== -1) {
                        isMatch = true;
                    } else {
                        for (var k = 0; k < bucket.length; k++) {
                            if (regions.indexOf(bucket[k]) !== -1) {
                                isMatch = true;
                                break;
                            }
                        }
                    }
                }

                if (isMatch) {
                    $item.removeClass('is-hidden');
                    groupVisible++;
                    totalVisible++;
                } else {
                    $item.addClass('is-hidden');
                }
            });

            if (groupVisible > 0) {
                $group.show();
            } else {
                $group.hide();
            }
        });

        if (totalVisible === 0) {
            $cs.find('.jero-cs-empty').show();
        } else {
            $cs.find('.jero-cs-empty').hide();
        }
    }

    // 6. Event Listeners & Two-Way Native Synchronization
    $(document).on('click', '#jero-cs-qualification .jero-cs-trigger', function(e) {
        e.stopPropagation();
        var $cs = $(this).closest('.jero-custom-select');
        var wasOpen = $cs.hasClass('is-open');
        closeAllComboboxes();

        if (!wasOpen) {
            $cs.addClass('is-open');
            $(this).attr('aria-expanded', 'true');
            $cs.closest('.bd-card').addClass('has-active-combobox');
            filterQualificationItems();
            setTimeout(function() {
                $cs.find('.jero-cs-search-input').focus();
            }, 50);
        }
    });

    $(document).on('click', '#jero-cs-qualification .jero-cs-item', function(e) {
        e.stopPropagation();
        var $item = $(this);
        var val = $item.attr('data-val');
        var text = $item.find('.jero-cs-item-text').text().trim();
        var badge = $item.attr('data-badge') || '';
        var $cs = $item.closest('.jero-custom-select');

        // Update trigger presentation
        $cs.find('.jero-cs-value').removeClass('jero-cs-placeholder').text(text);
        if (badge) {
            $cs.find('.jero-cs-badge').text(badge).show();
        } else {
            $cs.find('.jero-cs-badge').hide();
        }

        $cs.find('.jero-cs-item').removeClass('is-selected');
        $item.addClass('is-selected');

        closeAllComboboxes();

        // Two-way synchronization with native <select> for form validation & submit
        var $native = $('#qualification');
        if ($native.val() !== val) {
            $native.val(val).trigger('change');
            if (window.benchFormValidator && typeof window.benchFormValidator.revalidateField === 'function') {
                window.benchFormValidator.revalidateField('qualification');
            }
        }
    });

    // Real-Time Typeahead Search Input
    $(document).on('input', '#jero-cs-qualification .jero-cs-search-input', function() {
        var query = $(this).val();
        $(this).siblings('.jero-cs-search-clear').toggle(query.length > 0);
        filterQualificationItems();
    });

    // Clear Search Button
    $(document).on('click', '#jero-cs-qualification .jero-cs-search-clear', function(e) {
        e.stopPropagation();
        var $input = $(this).siblings('.jero-cs-search-input');
        $input.val('').focus();
        $(this).hide();
        filterQualificationItems();
    });

    // Global Click-Outside & Esc Key Handlers
    $(document).on('click', function() {
        closeAllComboboxes();
    });

    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAllComboboxes();
        }
    });

    // Initialize on DOM Ready
    $(document).ready(function() {
        buildQualificationCombobox();
    });

})(jQuery);
```

---

## 6. How to Replicate on Any Other Page (Step-by-Step AI Guide)

When replicating the Highest Qualification dropdown on another page (e.g., Job Creation, Career Apply, Candidate Edit, or ATS Drawer):

1. **Step 1 (Controller):** Ensure the controller fetches `$qualification` via `Cache::remember('qualification_master_list', ...)` excluding `[57, 58, 59]`.
2. **Step 2 (Blade Markup):** Copy Section **5.1** into the target form container. Ensure `<select name="qualification" id="qualification">` exists.
3. **Step 3 (CSS Styles):** Include the CSS classes from Section **5.2** in the page's isolated stylesheet or partial. Ensure the card container has `.bd-card { overflow: visible !important; }`.
4. **Step 4 (JavaScript Engine):** Include the JS logic from Section **5.3**. The script automatically renders `#jero-cs-qualification`, binds two-way events, and triggers validation.
5. **Step 5 (Validation):** If using FormValidation.io or Metronic validator, ensure field name `'qualification'` has `{ validators: { notEmpty: { message: 'Highest qualification is required' } } }`.
6. **Step 6 (Verification):** Run `php -l <path-to-blade>` and `./vendor/bin/pint --test <path-to-blade>` to ensure 100% PSR-12 and syntax compliance.
