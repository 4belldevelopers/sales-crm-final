# jer.works website redesign and implementation plan

**Brand and intended public domain:** Jero.works  
**Prepared:** 10 October 2026  
**Status:** Implementation specification; website implementation and publishing are separate work.  
**Purpose:** Translate the supplied JERO.WORKS landing-page brief into a concrete design, content, animation, engineering, and acceptance plan.

## 1. Outcome and scope

### Live website review and expanded redesign scope — 10 October 2026

This revision follows direct browser inspection of Jero.works: Home, Find Talent, Find Agency, Find Client, ATS, Pricing, Reviews, About, Careers, and Book a Demo, plus the linked Privacy, Refund, and Terms pages on hire.jero.works. This is the public navigation-linked inventory, not proof of an exhaustive crawl of every unlinked URL. Observed `.php` page URLs navigated to extensionless equivalents. Review included homepage visuals at 1366×768 and 390×844 and successful mobile-menu expansion. No forms were submitted, files uploaded, or accounts created. No live Lighthouse, field performance, full keyboard audit, backend audit, or end-to-end booking test was performed.

**Verdict:** Retain the immediate-talent and company-to-company collaboration story, but rebuild its presentation as a coherent yellow-themed marketing site. The principal weaknesses are incomplete company/proof pages, template residue, conflicting commercial messages, unclear conversion handoffs, and inconsistent heading/accessibility semantics. A color change alone will not resolve them.

The scope now expands from the original home-page specification to the linked public marketing website. Later sections remain the shared engineering/design specification; this live-review section governs page inventory, migration, and revised delivery scope. Do not introduce a live job board, candidate directory, or ATS connection.

#### Page review and replacement plan

| Reviewed page | Observed evidence | Redesign action |
|---|---|---|
| [Home](https://jero.works/) | Immediate-candidate hero with rotating occupations; logo strip and five metrics; talent-sharing benefits. Multiple H1 elements including an empty one. Template tab names and CTA accessible names remain. | Keep availability as a differentiator beneath a stable positioning headline. Replace generic feature tabs with connected-ecosystem story and a labeled local demo. Consolidate proof and CTAs. |
| [Find Talent](https://jero.works/talent) | Bench discovery; contract/C2H/permanent and hourly/monthly/remote/onsite hiring. 3,000+ candidates and 10,000+ companies hired claims; unrelated Avada documentation/feature links and image descriptions. | Build a focused talent-discovery marketing page: availability, skill relevance, engagement models, collaboration steps, illustrated candidate preview, FAQ, Get Started. Keep contractual models as approved descriptive content. |
| [Find Agency](https://jero.works/find-agency) | AI agency matching, six-step explanation, outsourcing and agency signup cards. Mixes hire.jero.works and ats.jero.works links; template labels remain. | Separate buyer journey from agency signup. Show expertise, geography, engagement model, and collaboration using synthetic agency cards. Consolidate CTA destinations after approval. |
| [Find Client](https://jero.works/find-client) | Agency sales story, AI matching, zero commissions, credits, and sample-looking “Recently Posted Jobs” with salaries and relative timestamps. First substantive title is H2; H1 is the jobs section. | Position as the agency/client-opportunity solution. Use a strong H1, three-step demand story, bench collaboration and illustrated requirements labeled as examples. Never portray local mock jobs as recent live opportunities. |
| [ATS](https://jero.works/applicant-tracking-system) | Recruitment automation hero and 10X claim; 1,000+ teams/50+ countries claim; template feature-tab names. | Explain workflow through approved capability groups: requirements, candidate organization, collaboration, interview/pipeline visibility. Use synthetic conceptual UI; one H1 and concise outcome copy. |
| [Pricing](https://jero.works/pricing) | Coin model, four plans, USD/INR switch, monthly labels with annual billing, user/job/candidate limits, competitor comparison. | Rebuild with approved static commercial data, clear annual payable amount, currency labels, coins explanation, limits, taxes/renewal and FAQ. Do not infer conversion rates. Remove competitor claims unless dated and substantiated. |
| [Reviews](https://jero.works/reviews) | Inspection exposed shared final CTA/footer but no substantive reviews content. | Publish only approved attributable proof. Until supplied, omit Reviews from primary discovery; retain a useful neutral explanation or appropriate redirect rather than an empty shell. |
| [About](https://jero.works/about-us) | Inspection exposed shared final CTA/footer but no substantive company story. | Create mission, problem addressed, ecosystem model, verified company details and contact links. Do not fabricate founders, offices, timeline, or team photos. |
| [Careers](https://jero.works/careers) | Two advertised roles, application form, resume upload, hourly rate, WordPress username, malformed developer-link label. | Confirm actual vacancies; replace irrelevant fields and generic template copy. Under static scope, publish approved role descriptions and an approved external application/email handoff. If no openings, say so clearly. |
| [Book a Demo](https://jero.works/book-a-demo) | Name/company/email/phone form, capability links to ats.jero.works, ambitious fastest/secure wording. | Create a concise “what you will see” page with approved scheduling destination or email handoff. Do not reproduce a backend submission form within the standalone site. |
| [Privacy](https://hire.jero.works/privacy-policy) | ATS policy; browser title says Terms & Conditions; contact uses support@jeroapp.com rather than footer address. | Have the owner approve policy applicability, entity, contact, effective date and marketing-specific practices. Preserve approved outbound policy link or publish approved static text. |
| [Refund](https://hire.jero.works/refund-policy) | Subscription cancellation/refund wording and unresolved `[email/contact information]` placeholder. | Resolve placeholder and commercial consistency through owner review; avoid changing obligations through a design task. |
| [Terms](https://hire.jero.works/terms-and-conditions) | Long platform agreement spanning multiple Jero domains. | Improve navigation/readability only after approved copy and scope; do not silently rewrite legal obligations. |

#### Prioritized findings

| Priority | Finding | Evidence and implication | Required change |
|---|---|---|---|
| P1 | Template residue damages comprehension | Browser accessibility trees expose Live Visual Builder, Layout Builder, Buy/Purchase Avada, WooCommerce Builder and irrelevant image descriptions; talent page links to Avada documentation paths. Some labels are accessible names rather than visible button text. | Audit visible copy, alt text, ARIA labels, links and tab semantics; replace every template artifact. |
| P1 | Proof lacks visible substantiation | Home claims 15,000+ bench candidates, 5,000+ enterprises, 10,000+ placements, 1,000+ five-star reviews, 80% savings and one-day hiring; ATS claims 10X speed. Reviews offers no substantive supporting content in the inspected state. | Claims register with definition, date, evidence and approval. Unsupported does not mean false; withhold until verified. |
| P1 | Commercial messaging conflicts | Find Client says no per-user charges; Pricing lists user caps. Zero commission/platform fee and free-forever language coexist with paid coin plans. | Explain precisely what is free, subscription limits, coin consumption, agency commissions and direct agency fees using approved terms. |
| P1 | Conversion paths are fragmented | Main signup points to hire.jero.works/register; some agency/demo links point to ats.jero.works/register. | Record each intent and approve one destination per journey; destination presence does not prove successful signup. |
| P1 | Critical company pages lack content | About and Reviews render shared CTA/footer without their expected substantive content. | Supply credible company information and proof, or remove misleading navigation entries with a migration decision. |
| P2 | Footer actions are misleading | Contact Us has javascript href; India/Singapore link to a terms path; copyright ends in 2025. | Use an actual Contact page/email, verified location text, current copyright, and accurate Jero logo description. Javascript href alone does not prove no handler exists; behavior needs verification. |
| P2 | Heading and image semantics need correction | Multiple/empty H1s on Home and ATS; template image names; repeated page titles. | One useful H1 per page, ordered headings, truthful image alternatives, distinct titles/descriptions. |
| P2 | Mobile story loses space to presentation | At 390×844 the hero stacks large words, animated occupation and supporting text; CTA is visible but metrics extend below the first screen. Expanded menu opens successfully. | Stable shorter heading, concise support copy, 44px controls, compact visual; retain working mobile-menu behavior with tested focus management. |
| P2 | Forms conflict with standalone delivery | Careers and Demo collect/send visitor information; receiving behavior was not tested. | Explicit approved offsite handoff or a later authorized service integration. No dummy success state. |

#### Site map and migration decisions

Recommended navigation: **Product, Solutions, Marketplace, Pricing, Company**, with **Sign In** and yellow **Get Started** on the right. Product links to ATS; Solutions exposes Find Talent, Find Agency, and Find Client using clear buyer/agency descriptions. Marketplace opens the home ecosystem story initially, unless enough approved content exists for a separate static page. Company contains About, Careers and Contact. Reviews becomes Customer Stories only when evidence exists. Do not add empty Resources navigation.

Retain existing extensionless public paths where practical: `/talent`, `/find-agency`, `/find-client`, `/applicant-tracking-system`, `/pricing`, `/about-us`, `/careers`, `/book-a-demo`. Add `/contact` as a static information page with verified email links. Handle `/reviews` according to approved proof readiness. Keep both current `.php` aliases and extensionless URLs in the migration manifest. Configure permanent redirects at the CDN only after confirming current behavior and canonical ownership; do not assume observed navigation is an HTTP 301 without header verification. Each replacement page needs its own title, description, H1 and canonical.

The actual live website exposes Avada/Fusion names and WordPress-style asset paths. This is evidence of template lineage, not a verified hosting/framework inventory. Do not treat the checkout’s legacy `saas-front/index.blade.php` as the live marketing source. Deployment ownership, current URL inventory and existing hosting must be confirmed before replacement.

#### Refined homepage design

1. **Navigation:** preserve recognizable Jero branding; single yellow primary action, quieter Sign In, demo link within the conversion flow.
2. **Hero:** “Hiring, Reimagined.” Supporting copy explains companies, recruiters, agencies and talent. Yellow/ivory canvas; charcoal typography; Get Started and Explore Jero. Keep “Find talent ready for your next opportunity” as supporting positioning, without guaranteeing joining dates.
3. **Fragmentation → connection:** short static sequence showing disconnected hiring inputs becoming one ecosystem; animation enhances the same readable story.
4. **Ecosystem:** charcoal panel with yellow paths and clear actor labels; no meaningless network particles.
5. **Three outcome pillars:** discover relevant talent, collaborate with hiring partners, organize the hiring workflow. Explain immediate availability and bench talent here.
6. **Illustrative matching demo:** deterministic local skills/experience/availability presets, visible simulation label, accessible reset and explanation.
7. **Audience routes:** Companies → Find Talent/Agency; Agencies → Find Client; Recruiters → ATS; Talent → informational pathway only until an approved destination exists.
8. **How it works:** define need, connect, coordinate. No guaranteed one-day outcome.
9. **Verified proof:** approved logo strip/case story only; omission is preferable to unsupported metrics.
10. **FAQ and final CTA:** clarify marketplace versus ATS, fees, availability and next step; finish with yellow panel and dark CTA.

Yellow theme stays `#F5C518`, charcoal `#202018`, ivory `#FFFCF3`, soft yellow `#FFF3BF`. Existing umbrella/wordmark should be evaluated for reuse, not replaced without approval. Dark text on yellow; yellow highlights on charcoal; dark outlines on light backgrounds. Motion stays purposeful and reduced-motion compatible; Remotion is reserved for optional separately scoped videos.

#### Revised implementation sequence

**Milestone 1 — truth and destinations:** reconcile claims/pricing, approve customer logo rights, verify signup/login/booking handoffs and legal contacts; capture old URL aliases and redirects. Exit: approved content/link register.

**Milestone 2 — visual specification:** yellow component system, 1366×768 hero, 390px layout, all page wireframes, actual company story and approved pricing presentation. UI/UX Pro Max recommendations are advisory; user-selected Poppins/yellow/Motion requirements win over generic font or palette suggestions.

**Milestone 3 — independent static build:** build the homepage plus Talent, Agency, Client, ATS, Pricing, About, Careers, Demo and Contact templates. Produce static HTML for each page, isolated section state and local fixtures; no root Laravel configuration changes.

**Milestone 4 — content and motion:** approved content, evidence, legal links, restrained ecosystem animation and local demonstrations. Pricing switches use approved currency-specific price objects, not an invented exchange rate; changing currency updates amounts and billing explanations consistently.

**Milestone 5 — verification:** desktop/mobile screenshots for every unique template; full link crawl of the generated artifact; keyboard/menu/focus/reduced-motion checks; pricing switch tests; no-JS story; no overflow; network-origin restrictions; static SEO/canonical/redirect validation; bundle/lab performance measurements. Retest the current currency interaction separately: the attempted toggle during this review timed out and no switched-price state was verified, so it is not classified as a proven website defect.

**Milestone 6 — reviewable release:** static artifact, URL migration table, claims/pricing approval, QA evidence and rollback artifact. Production replacement requires explicit authorization. Initial full-site planning allowance: 20–30 working days with prepared content, approximately 4–6 weeks, replacing the earlier homepage-only 12–18-day allowance. Unverified content and integration decisions can extend it.

**Acceptance additions:** No Avada/template copy or documentation links; no empty About/Reviews pages; consistent commercial explanations; all current public intents mapped; no local mock job presented as live; working approved application/booking handoffs; distinct page SEO; legal contacts and placeholders resolved. Do not claim complete accessibility, performance, 10K capacity, or actual form delivery until measured/tested.

Create a premium recruitment technology marketing website that explains Jero within 5–10 seconds and guides visitors toward **Get Started**. Positioning: **Jero is the smarter way to hire.** The secondary action is **Explore Jero**.

The experience connects companies, recruiters, agencies, and talent through a clear story. It uses conceptual product illustrations and local demonstrations, with no production records. All runtime interactions execute in the browser. Hosting serves static files; build-time compilation is permitted and does not introduce a runtime backend.

Deliver the home page, responsive navigation, local matching demonstration, ecosystem story, audience pathways, FAQ, conversion panel, footer, static error page, SEO assets, and verified legal pages where supplied. Do not expand the initial release into an ATS, authenticated marketplace, blog CMS, pricing engine, or recruitment portal.

## 2. Current project inspection and reuse decision

The inspected checkout is Laravel/Blade with Vite, Tailwind, Alpine.js, Axios, and application realtime dependencies. Root `package.json` does not declare React or Motion. Root `vite.config.js` uses the Laravel Vite plugin and builds `resources/css/app.css` and `resources/js/app.js`.

`resources/views/saas-front/index.blade.php` contains a legacy marketing page with a Talentmily title, empty description, Blade asset/URL helpers, Bootstrap styles, and preloader markup. Its existence does not establish that it serves the current public domain. `resources/views/front/` contains public job/application views. `front/assets/` contains CSS, fonts, images, JavaScript, and PHP. The root fonts directory inspected contains DejaVu files; approved Poppins assets still need locating or sourcing.

**Decision:** Plan an independent `marketing-site/` directory within this checkout, with its own package manifest, static build, CSS, tests, and asset inventory. Do not change root build configuration, Laravel routes, existing Blade files, controllers, or application dependencies. This retains the working application infrastructure while satisfying the explicit standalone architecture.

Before implementation, audit relevant public assets by path, size, license, provenance, and brand correctness. Reuse an approved Jero wordmark, favicon, Poppins files, and accurate public copy where suitable. Do not import legacy CSS bundles, PHP, application scripts, dynamic Blade helpers, customer data, or credentials. Existing legal text requires confirmation that it applies to this marketing property. The current live website, DNS, analytics, conversion destinations, and asset licensing have not been verified in this planning task.

## 3. Architecture and isolation contract

Recommended implementation: TypeScript, React, Vite, CSS custom properties, and selectively loaded Motion for React. Validate compatible package versions at implementation time and commit a lockfile. Use semantic static HTML generated at build time for the main story and metadata; hydrate interactive regions progressively. Avoid a blank client-rendered shell for search crawlers and visitors with JavaScript unavailable. A build-time prerender tool must be proven in a small spike before committing to it.

```text
Independent marketing source → build-time static HTML/assets → static CDN
                                                        ↓
                                      browser UI + local mock demonstrations
```

No runtime Laravel, PHP, database, Elasticsearch, Redis, NATS, Horizon, Reverb, ATS API, authenticated session, or server business logic. No application environment variables or copied `.env` files. No API fallback for demos. Mock data ships as local objects or static files on the same origin.

Outbound navigation to an approved signup, sign-in, or booking URL may hand the visitor to a separate service; the landing page does not call that service or read its data. Such destinations must be explicitly verified before launch. Static delivery still consumes CDN bandwidth: zero ATS dependency does not establish unlimited capacity or zero infrastructure cost.

## 4. Experience strategy and visual direction

**Direction: Connected hiring, clearly presented. Yellow theme selected by the user.** Use a warm ivory canvas, charcoal typography, confident yellow accents, fine connecting lines, and deliberately composed recruitment cards. Every line and card should explain demand, supply, matching, or availability. Avoid decorative floating cards, stock photos, 3D, glowing blobs, fake charts, and oversized AI labels.

Primary visual motif: a company requirement connects through Jero to recruiter and agency contributions, then to relevant talent. Show this as a flat, ordered composition rather than an admin dashboard. Place one slightly darker ecosystem section in the page to establish rhythm; keep most reading surfaces light.

Research activities: compare information hierarchy and navigation on premium SaaS sites; use Mobbin, Godly, and Land-book for reference; inspect React Bits, 21st.dev, and Uiverse only for relevant patterns. Record the reason for each selected pattern, its accessibility behavior, and licensing. Do not copy site composition or install entire component collections. Reference browsing is a future design activity, not a completed research claim.

## 5. Proposed design system

Yellow is the selected color direction. Exact values below are proposed implementation tokens, not existing brand standards.

| Token | Proposal | Usage |
|---|---|---|
| Canvas | `#FFFCF3` | Warm ivory page background |
| Surface | `#FFFFFF` | Cards and reading panels |
| Ink | `#202018` | Main text and text on yellow buttons |
| Muted text | `#625E50` | Supporting copy; verify contrast |
| Accent | `#F5C518` | Primary button backgrounds and meaningful highlights |
| Accent hover | `#E5B510` | Hover background; retain charcoal label |
| Soft accent | `#FFF3BF` | Selected surfaces and callouts |
| Dark surface | `#202018` | Ecosystem section with ivory text and yellow connections |
| Border | `#E4DDCA` | Decorative dividers |
| Control border / focus | `#766629` / `#202018` | Visible controls and focus outlines on light surfaces |
| Spacing | 4, 8, 12, 16, 24, 32, 48, 64, 88px | Consistent layout rhythm |
| Radius | 8px controls, 12px cards, 20px feature panels | Restrained hierarchy |
| Container | max 1200px; 24–40px desktop gutters, 16–20px mobile | Predictable reading width |

Use self-hosted licensed Poppins WOFF2, initially weights 400, 500, and 600. Avoid loading unnecessary weights. Body and controls: 14–15px with 1.55–1.7 line height; supporting labels: 12–13px. Hero: responsive 36–64px with controlled line length; section headings: 26–40px. The supplied marketing brief permits display typography; application table/form typography rules remain untouched. Test text resizing and readability rather than reducing text to force a layout.

Primitives: Container, Button, TextLink, SectionHeading, Badge, DemoCard, Icon, FocusRing, Disclosure, and MotionReveal. Components share stateless appearance within the marketing project; each interactive section owns its state. No globals or imports from ATS modules. Define loading, focus, pressed, disabled, empty, and failure states as appropriate.

### Yellow theme application

Keep reading sections ivory or white. Use a pale yellow hero backdrop with charcoal headline and a solid yellow Get Started button; secondary actions use charcoal outlines. Recruitment cards remain white with restrained yellow availability or selection highlights. Use yellow connector lines on the charcoal ecosystem section, and dark amber/charcoal lines on light surfaces. The final CTA can use a solid yellow panel with a charcoal button and yellow label.

Do not use white text on yellow buttons, yellow body text on white, or yellow alone to communicate selection or status. Use dark labels, explicit state text, and visible borders. Provide charcoal focus rings on light/yellow surfaces and ivory rings on dark surfaces. Verify all final text and control contrast combinations before implementation sign-off. Keep animation aligned with these tokens; do not introduce unrelated purple or teal accents.

## 6. Information architecture and draft copy

Combine related sections to avoid an unnecessarily long 19-section page.

| Order | Section | Message and interaction | Next action |
|---|---|---|---|
| 1 | Navigation | Jero; Product, Solutions, Marketplace, About; verified Sign In | Get Started |
| 2 | Hero | **Hiring, Reimagined.** “A smarter hiring platform connecting companies, recruiters, agencies and exceptional talent.” | Get Started / Explore Jero |
| 3 | Problem → solution | **Hiring shouldn’t feel this complicated.** Fragmented channels become one connected ecosystem | Continue story |
| 4 | Ecosystem | **One connected hiring ecosystem.** Company → Jero → recruiters/agencies/talent → hiring | Explore Marketplace |
| 5 | Why Jero | Faster coordination, relevant talent, connected collaboration, visibility into availability | Try demonstration |
| 6 | Matching demonstration | **See how a stronger match comes together.** Clearly marked illustrative data | Change requirement |
| 7 | Marketplace + availability | **Connect hiring demand with talent supply.** Explain bench talent and immediate availability without guarantees | Explore audience pathway |
| 8 | Audience pathways | Companies, Recruiters, Agencies, Talent; concise benefit and conceptual illustration | Audience-specific Get Started |
| 9 | How it works | Define requirement → connect with relevant supply → coordinate the next step | Get Started |
| 10 | Trust | Approved evidence or neutral explanation of Jero’s approach | Review evidence if supplied |
| 11 | FAQ | Clarify platform, marketplace, audiences, and simulated demo | Resolve objections |
| 12 | Final CTA + footer | **Make your next hire a better connection.** Legal and approved contact links | Get Started |

Use short paragraphs and purposeful illustrations. Mention AI matching as a capability only after capability wording is verified. Avoid quantified speed promises or suggesting the marketing demo reproduces production scoring. Immediate joiner copy should describe availability visibility, not guaranteed instant hiring.

Do not publish the brief’s Alibaba, Amazon, Flipkart, Jio, Nykaa, Paytm, Byju’s, Upland Software, or Espire InfoLabs references as customers without explicit marketing approval and evidence. Omit the logo strip if approval is absent. Do not show placeholders on the published site. Maintain an internal claim register: wording, supporting source, rights, owner, approval date, and expiry/review date.

## 7. Hero and responsive composition

At 1366×768, use a balanced two-column hero with headline, description, and both actions visible without scrolling through decoration. Navigation approximately 64–72px; hero content height approximately 500–580px, adapting to available space. Do not require a full-screen minimum height. The visual contains one requirement card, a Jero connection point, and two or three relevant profile cards with clear relationships.

At 1920×1080, retain the container maximum instead of stretching cards. At tablet widths, simplify the connections. At mobile widths, place copy and CTA first, then a compact vertical sequence. Replace complex diagrams with accessible ordered steps when space is insufficient. No hover-only information or horizontal page scrolling.

Navigation collapses into a keyboard-accessible menu. If implemented as a modal, manage focus, Escape, scroll lock, and return focus correctly. Anchor navigation accounts for sticky header height. Reading and core links remain available without JavaScript; enhanced controls have a clear static fallback.

## 8. Client-side demonstrations

Matching demo uses synthetic candidates with fictional names or initials, no contact information, approved skill labels, experience, and availability. Job presets: Senior Java Developer, Frontend Engineer, and QA Engineer. Avoid implying these are real open roles.

Interaction: select a requirement → see required skills → candidate ordering updates → select a profile to inspect the illustrated reason for the match → reset. Use a deterministic, documented demo-only scoring function or curated preset scores. A displayed “96%” must be labeled **Illustrative match**, never a measured AI output. Present contributing factors clearly; a visible “Simulated demo • No live candidate data” label stays close to the interaction.

States: initial, selected, no matching mock profiles, reset, and script unavailable. No fake server spinner, artificial network delay, account requirement, storage of visitor input, or API call. Announce the changed result count politely without announcing every animation frame. Provide equivalent keyboard controls and retain focus after updates.

Marketplace demo shows an ordered relationship story, optionally controlled by Next/Previous buttons. Do not build a second complex mini-application. Keep core explanations readable without running animation.

## 9. Motion specification

Use CSS for simple hover/focus feedback. Use Motion for coordinated reveals, card transitions, and ecosystem choreography only where it earns its bundle cost. Motion is an implementation library, not a Codex skill. Do not use Remotion for normal UI.

| Element | Behavior | Timing proposal | Reduced-motion behavior |
|---|---|---|---|
| Hero copy | Visible on first paint; optional subtle enhancement | 250–400ms | Immediately visible |
| Section reveal | Once on entry; 8–16px translation and opacity | 250–450ms | Static content |
| Related cards | Small bounded stagger | 40–60ms spacing; total <600ms | All visible together |
| Ecosystem | Trace connections in story order once; replay control | 600–1000ms sequence | Completed static diagram |
| Buttons | Color/border and optional 1–2px movement | 120–180ms | Color/focus feedback |
| Demo updates | Stable card layout with short transition | 180–250ms | Immediate state change |
| FAQ | Short expansion without clipping text | 150–220ms | Immediate expansion |

Avoid scroll hijacking, forced smooth scrolling under reduced motion, looping motion behind text, autoplay carousels, animated font size, and long staggered headings. Prefer transform/opacity; profile SVG path drawing and restrict it to small diagrams. Stop unnecessary activity offscreen or when the document is hidden. Clean up listeners and observers. Important content must never depend on an observer firing.

Honor both CSS `prefers-reduced-motion` and the Motion reduced-motion API. Reference: [Motion reduced motion](https://motion.dev/docs/react-use-reduced-motion), [Motion documentation](https://motion.dev/docs/).

## 10. Conversion behavior and content dependencies

Maintain one primary CTA label. Explore Jero scrolls to the ecosystem or matching section. Get Started can open an accessible browser-only audience selector, then navigate to approved destinations. If destinations are unavailable during development, use clearly labeled preview behavior; do not publish inert buttons or invented URLs.

Sign In is an outbound link only after verification. Book a Demo requires an approved booking destination; no local fake submission confirmation. Do not add a contact form that implies information is delivered without a receiving service. An approved email link is a possible static alternative. Public contact details, social links, legal text, and pricing must be provided or verified.

Define events conceptually: primary CTA click, audience chosen, demo interacted, outbound booking click. Analytics are excluded by default because the brief specifies standalone operation. A future analytics integration needs an explicit scope decision, privacy review, and consent behavior where applicable. Local clicks are not proof of completed signup or hiring.

## 11. Performance and capacity plan

Project budgets, to be measured rather than claimed: initial compressed JS ≤120KB, CSS ≤30KB, font payload ≤120KB, hero assets ≤150KB, and initial transferred page ≤500KB. Revisit with recorded evidence if design needs a different budget. Lazy-load below-fold demonstrations and reserve media dimensions. Preload only critical assets. Render hero text immediately, avoid blocking preloaders, and avoid importing the ATS asset bundle.

Target good Core Web Vitals at the 75th percentile: LCP ≤2.5s, INP ≤200ms, CLS ≤0.1. Lighthouse ≥90 performance is a useful lab gate, not field proof. Profile interactions on a modest mobile device and throttled network; investigate tasks exceeding 50ms. Optimize images through appropriate AVIF/WebP or SVG, preserve dimensions, and use font-display with metric-compatible fallback.

Use CDN compression and immutable caching for fingerprinted assets. HTML should revalidate so releases do not stay stale. Test 10,000 concurrent visitor delivery against the selected hosting plan, approved load-test limits, bandwidth, cache hit rate, error rate, and latency. No 10K guarantee follows from this document or static architecture alone.

Reference: [Core Web Vitals thresholds](https://web.dev/articles/defining-core-web-vitals-thresholds).

## 12. SEO, accessibility, privacy, and security

Build the headline, story, navigation, and FAQ into static HTML. One H1, ordered heading hierarchy, descriptive links, useful image alternatives, and empty alt text for decoration. Proposed title: **Jero | Smarter Hiring, Connected Talent**. Proposed description: **Discover Jero, a connected hiring platform bringing companies, recruiters, agencies and talent together for simpler recruitment.** Verify positioning before release.

Canonical domain is Jero.works once ownership and preferred host are confirmed; do not use the document filename’s “jer.works” spelling as a canonical. Create Open Graph and X metadata, an approved 1200×630 social image, favicon set, robots.txt, sitemap.xml, and a useful static 404. Include only actual published URLs. Use Organization/WebSite structured data with verified properties; no invented reviews or aggregate ratings. Verify legal pages and redirect requirements against the actual current site before replacing it.

Target WCAG 2.2 AA. Test contrast, keyboard-only navigation, screen-reader headings and controls, 200% zoom, reflow at 320 CSS pixels, focus visibility and sticky-header obstruction. Prefer 44px touch targets; WCAG 2.2 AA includes a 24px minimum subject to specified exceptions. Use native controls where possible, accessible tabs only when necessary, and no color-only statuses. Automated checks supplement manual testing.

Set static hosting security headers appropriate to the build: CSP, frame-ancestors, nosniff, Referrer-Policy, and HTTPS. Begin with `connect-src 'none'` if all demo data is bundled; explicitly permit only necessary same-origin static fetches if used. Verify the policy does not block functionality. Sanitize any future rich text; use normal escaped rendering for mock content. Audit dependency licenses and production artifacts for secrets. Do not persist personal data or add tracking by default.

References: [WCAG 2.2](https://www.w3.org/TR/WCAG22/), [Target Size Minimum](https://www.w3.org/WAI/WCAG22/Understanding/target-size-minimum).

## 13. Proposed source structure

```text
marketing-site/
  package.json / lockfile / vite.config.ts
  index.html
  public/                 # static legal assets, favicon, robots, sitemap
  src/
    components/           # stateless marketing primitives
    sections/             # independently owned interactive state
    animations/           # marketing-only motion utilities
    mock-data/            # synthetic jobs, candidates, marketplace presets
    design-system/        # tokens and documented component variants
    assets/               # approved branding and optimized illustrations
    styles/               # isolated marketing CSS
    utils/                # local demo helpers
  tests/                  # browser, accessibility, interaction, network checks
  scripts/                # static generation and artifact verification
  dist/                   # generated deployment artifact
```

Document intent for new files and nontrivial functions. Use no production-shaped fixtures containing real identities. Do not create mock testimonials. Shared reusable components stay inside this independent website; stateful ATS scripts remain separate.

## 14. Implementation phases and exit criteria

| Phase | Work | Deliverable / exit gate |
|---|---|---|
| 0. Discovery | Confirm current public site, deployment ownership, assets, claims, destinations | Asset/link/claim inventory and independent scope |
| 1. Visual research | Review references and establish original direction | Desktop/mobile moodboard with rationale |
| 2. Design system | Tokens, typography, buttons, cards, navigation, focus states | Component sheet with checked contrast |
| 3. Architecture and copy | Consolidate sections and audience journeys | Wireframes, copy deck, CTA destination map |
| 4. Static foundation | Independent build and prerender proof | HTML story usable without JS; no ATS imports |
| 5. Hero and story | Build hero, ecosystem, benefits, audience, FAQ, footer | Responsive complete page with static diagrams |
| 6. Demonstrations | Local fixtures, preset selection, explanation, reset | Keyboard-usable deterministic demo |
| 7. Motion | Add only specified choreography | Reduced-motion equivalence and lifecycle cleanup |
| 8. Performance | Bundle review, media/fonts, CDN delivery | Budget report and mobile profiles |
| 9. SEO/accessibility | Metadata, static pages, structured data, manual checks | Crawlable artifact and accessibility findings resolved |
| 10. QA and release preparation | Browser matrix, network isolation, approved content | Reviewable static artifact and signed-off release checklist |

Planning allowance: approximately 12–18 working days for a designer/developer workflow, subject to asset readiness, review cycles, and verified content. This is an estimate, not a completion commitment. Website publishing and changes to production remain a separate explicitly authorized action. Keep the previous static artifact available for rollback.

## 15. QA matrix and evidence

Test 1366×768, 1920×1080, 1024×768, 768×1024, 390×844, 360×800, and 320px reflow. Test Chromium, Firefox, WebKit, and representative actual mobile devices where available. Screen diagonal alone does not determine CSS viewport; record OS scaling and browser zoom.

Required checks: all links and CTAs; menu open/close/focus/Escape; demo preset changes and reset; FAQ controls; reduced-motion mode; no-JS experience; slow network; font fallback; long text; keyboard and screen-reader flow; no horizontal overflow; image loading; console errors; anchor positions; static 404; social metadata; structured data; and legal destinations.

Use Playwright to test interactions, deterministic mock output, overflow, and forbidden network calls. Capture and inspect screenshots rather than relying only on successful assertions. Record all request origins and verify no ATS endpoint, websocket, credential, or application session dependency. Check build artifacts and bundle imports. Run an accessibility scanner, then manual checks. Repeat performance measurements and report the conditions. Save findings and resolved defects alongside release evidence.

## 16. Skills and tooling installed

Official `openai/skills` curated skills installed for this request:

| Skill | Role | Prerequisites / limits |
|---|---|---|
| figma | Read design context and asset specifications | Requires configured Figma connection and accessible design file when used |
| figma-implement-design | Translate approved Figma design into accurate code | Requires design reference and connection; installation does not grant access |
| playwright | Repeatable browser interaction and regression checks | Browser/runtime setup must be verified during implementation |
| playwright-interactive | Iterative visual and interaction review | Follow its runtime instructions when invoked |

Installed under the user’s `.codex/skills/` directory and available from the next turn. No dedicated Motion skill appeared in the official catalog inspected. Use the verified Motion documentation and the explicit animation specification above; avoid installing an unrelated skill just to increase the tool count. Existing image generation is optional for an approved social image; use SVG/HTML/CSS for recruitment diagrams. A skill does not replace design judgment or guarantee premium results.

## 17. Launch acceptance checklist

### Added design intelligence and reference workflow

**UI/UX Pro Max** installed from [the upstream repository](https://github.com/nextlevelbuilder/ui-ux-pro-max-skill) into `.codex/skills/ui-ux-pro-max`. Use it for design-system recommendations, responsive layout, typography, accessibility, and interaction review. Its recommendations remain subordinate to the approved Jero brief: Poppins, restrained flat visuals, local mock data, and Motion for animations. Installation does not authorize replacing those choices with generic generated styles.

| Resource | Intended role in the Jero workflow | Integration approach |
|---|---|---|
| [21st.dev](https://21st.dev/) | React component and layout references | Evaluate individual components for design fit, license, accessibility, and dependencies before adapting |
| [SceneAI](https://sceneai.art/) | Landing-page and section prompt references | Use as inspiration; check asset license and access rights before reuse |
| [Transitions.dev](https://transitions.dev/) | UI transition references | Adapt relevant transition patterns to the existing Motion specification |
| [React Bits](https://reactbits.dev/) | Animated React component references | Select only meaningful, lightweight effects; test reduced motion and mobile performance |

These four resources are websites/component references; they are not automatically installed as Codex skills or production dependencies. Do not add their entire libraries, external runtime services, or paid subscriptions. Workflow: consult UI/UX Pro Max → choose a relevant reference → adapt to Jero tokens → implement locally → verify accessibility, reduced motion, and performance. Figma remains optional for approved design handoff; Playwright supports browser QA.

- [ ] A first-time reviewer can explain Jero and identify the primary action in 5–10 seconds.
- [ ] Every visible claim, logo, testimonial, destination, and legal statement is verified.
- [ ] Hero and complete page work at laptop/mobile sizes, zoom, and reduced motion.
- [ ] Matching and marketplace interactions are visibly simulated and entirely local.
- [ ] Core story and navigation render as static HTML without JavaScript.
- [ ] No Laravel/ATS/runtime backend or application credential dependency exists.
- [ ] Keyboard/screen-reader checks pass; documented accessibility defects are resolved.
- [ ] Browser tests, screenshots, console checks, and network-origin checks pass.
- [ ] Bundle budgets and lab measurements are recorded; field results are distinguished.
- [ ] Canonicals, metadata, sitemap, favicon, social image, and 404 are verified.
- [ ] Hosting, cache headers, security headers, redirects, rollback, and link destinations are ready.
- [ ] A separate production publishing authorization exists before deployment.

## 18. Inputs to resolve before release

Approved Jero wordmark and palette; public capability wording; customer proof rights; signup/sign-in/booking URLs; public contact/social details; legal entity and policies; ownership of Jero.works and preferred canonical host; existing URL redirects; hosting provider; and whether any analytics are desired. Design and local prototype work can proceed with labeled internal placeholders, but a public release must not publish invented facts or destinations.

**Definition of completion:** A visually reviewed, accessible, responsive, fast static marketing artifact with approved content, working conversion destinations, evidenced isolation from the ATS, and a reproducible build. This document specifies that outcome; it does not claim the website has already been built or tested.
