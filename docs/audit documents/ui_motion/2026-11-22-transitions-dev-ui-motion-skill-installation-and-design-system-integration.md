# Transitions.dev UI Motion Suite & Agent Skill Integration Standard

## Metadata
- **Module:** UI Motion System & Micro-Interactions (`transitions.dev`)
- **Author:** Antigravity AI God-Level Engineer
- **Date:** 2026-11-22
- **Scope:** Workspace `.agents/skills/`, Global User Skills `~/.gemini/config/skills/`, Design System Motion Tokens
- **Architecture Compliance:** Rule 27 (Mandatory Production-Ready UI Transitions & Motion Standard), Rule 20 (Card + Sub-Card Design System), Rule 26 (/unslop UI/UX Craft Standard), Directive 19 in `CLAUDE.md`, Rule 3 (Client-First Execution).

---

## Executive Summary

To eliminate janky, arbitrary AI-generated CSS animations (`transition: all 0.3s ease`, layout thrashing on `height`/`width`/`top`/`left`, jarring un-accelerated drawer transitions), JERO has formally integrated the **`transitions.dev` UI motion suite** across both the project codebase and the AI Brain memory.

The installation comprises:
1. **`transitions-dev` skill**: 43+ curated, production-ready CSS transition recipes with dedicated markdown reference guides (`01-card-resize.md` to `36-number-subtle-change.md`), unified `:root` motion tokens (`_root.css`), and accessibility guards.
2. **`transitions-polish` skill**: Automated static audit tool and refactoring recipes that scan existing stylesheets to identify ad-hoc durations or non-compositor properties and map them cleanly to shared design system tokens.
3. **`transitions-ui-generation` skill**: Motion-first component generator that pairs structural HTML/Blade with fluid, GPU-accelerated CSS micro-interactions from inception.

---

## Architecture & Motion Token Standards

### 1. Compositor-Only Hardware Acceleration
In compliance with JERO's 10K concurrency & zero-strain mandate, all UI motion must run on the browser's GPU compositor thread:
- **Allowed Motion Properties**: `transform` (`translate3d`, `scale`, `rotate`), `opacity`, and localized `filter` (`blur`).
- **Forbidden on Transition Paths**: `top`, `left`, `right`, `bottom`, `width`, `height`, `margin`, `padding` (these trigger expensive browser reflow and layout thrashing).
- Dynamic height expands (e.g. accordions, drawers) must use CSS Grid `grid-template-rows: 0fr -> 1fr` or modern CSS `@starting-style` with `transition-behavior: allow-discrete`.

### 2. Canonical Motion Scale Tokens (`_root.css`)

```css
:root {
  /* Durations */
  --duration-stagger: 40ms;       /* Per-item list/card stagger */
  --duration-micro: 80ms;         /* Tooltip delay, shake segment */
  --duration-quick: 150ms;        /* Modal/dropdown close, text swap */
  --duration-fast: 250ms;         /* Icon swap, dropdown open, tabs slide */
  --duration-medium: 350ms;       /* Drawer close, toast close */
  --duration-slow: 400ms;         /* Drawer open, skeleton reveal */
  --duration-very-slow: 500ms;    /* Badge pop, checkmark celebration */

  /* Easings */
  --ease-smooth-out: cubic-bezier(0.22, 1, 0.36, 1);    /* Open/close, page slide, resize */
  --ease-in-out: ease-in-out;                           /* Text swaps, skeleton reveal */
  --ease-out: ease-out;                                 /* Tooltips, hints */
  --ease-bounce: cubic-bezier(0.34, 1.36, 0.64, 1);     /* Badge pop, success check */
  --ease-bounce-strong: cubic-bezier(0.34, 3.85, 0.64, 1); /* Interactive hover lift return */

  /* Distances & Scales */
  --distance-micro: 4px;
  --distance-base: 8px;
  --distance-medium: 12px;
  --scale-large: 0.96;
  --scale-medium: 0.97;
  --blur-small: 2px;
}
```

### 3. Mandatory Accessibility Guard
All custom animations must gracefully degrade for users with motion sensitivities:

```css
@media (prefers-reduced-motion: reduce) {
  .t-animated-element {
    animation: none !important;
    transition: opacity 150ms ease-out !important;
    transform: none !important;
  }
}
```

---

## File Changes & Installation Matrix

| Scope | Location | Description |
| :--- | :--- | :--- |
| **Workspace Skills** | `.agents/skills/transitions-dev/` | Core catalog with 36 reference pattern guides and `_root.css` |
| **Workspace Skills** | `.agents/skills/transitions-polish/` | Codebase audit and token-mapping agent skill |
| **Workspace Skills** | `.agents/skills/transitions-ui-generation/` | Component generator skill with motion baked in |
| **Global Skills** | `~/.gemini/config/skills/transitions-dev/` | Cross-workspace persistent capability |
| **Global Skills** | `~/.gemini/config/skills/transitions-polish/` | Cross-workspace persistent capability |
| **Global Skills** | `~/.gemini/config/skills/transitions-ui-generation/` | Cross-workspace persistent capability |
| **AI Knowledge Base** | `AGENTS.md` | Added **Rule 27: Mandatory Production-Ready UI Transitions & Motion Standard** |
| **AI Knowledge Base** | `CLAUDE.md` | Added **Directive 19: Mandatory Production-Ready UI Transitions & Motion Standard** |
| **AI Living Memory** | `.ai/PROJECT_MEMORY.md` | Added snapshot entry documenting installation and architectural guidance |

---

## 15.6" Screen Fit & Responsiveness
- Transitions must never cause horizontal layout overflow or trigger body scrollbars during transforms.
- Drawer animations (e.g. `.jero-drawer-slide`) must use `transform: translateX(100%) -> translateX(0)` with `will-change: transform` to prevent repainting the underlying 1366x768 / 1920x1080 viewport.

---

## Verification Evidence
1. Skills directory verification: `Get-ChildItem .agents/skills/transitions*` confirmed present with full documentation and token assets.
2. Global skills registration confirmed in `~/.gemini/config/skills/`.
3. Living memory (`.ai/PROJECT_MEMORY.md`), `AGENTS.md`, and `CLAUDE.md` updated with exact rule definitions and trigger phrases (`transitions reveal`, `transitions review`, `transitions apply`, `transitions refine`).
