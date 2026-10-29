---
name: card-subcard
description: Generates or refactors views strictly into the Modern Card + Sub-Card Enterprise Design System (Rule 20).
---

# 🎨 Modern Card + Sub-Card Design Standard (Rule 20)

This skill enforces the Modern Card + Sub-Card enterprise layout across all entity listings, drawer tabs, and overview dashboards.

## Core Hierarchy & Dimensions

### Tier 1: Toolbar Header Card
- Background: `#ffffff`, Border: `1px solid #e2e8f0`, Border-radius: `10px`.
- Box Shadow: `0 1px 3px rgba(0, 0, 0, 0.05)`.
- Left Side: Entity title, live count pill badge, refresh button.
- Right Side: Dynamic live search bar (300ms debounce + clear 'X' button) and primary CTA button.

### Tier 2: Dedicated Sub-Tabs Card
- Background: `#ffffff`, Border: `1px solid #e2e8f0`, Border-radius: `10px`.
- Active Tab Highlight: Single primary blue accent (`#009ef7`), soft background tint (`#f1faff`), font-weight 600.
- Counter Badges: Crisp secondary badges for tab items.
- Live Status: Real-time sync indicator where applicable.

### Tier 3: Primary Card Stream & Discrete Sub-Cards
- Layout: CSS Grid with `minmax(0, ...)` fractional units preventing horizontal scroll on 1366x768 and 1920x1080 screens.
- Discrete upper sub-cards (e.g. Profile/Entity, Comp/Location, Status/Pipeline, Actions).
- Left-border status accent: `border-left: 3.5px solid #10b981` (active) or `#cbd5e1` (inactive).
- Bottom Activity Strip: Discreet metadata, timestamp, author, and quick action icons.

## Strict UI Guidelines
- **Font**: Poppins font family strictly throughout. Maximum font size 14px for body text.
- **Numbers**: Counters and currency must use `font-variant-numeric: tabular-nums;`.
- **No 3D / No Sprawling Raw Tables**: Unstyled floating sub-tabs, sprawling tables, and 3D icons are strictly forbidden.
- **Screen Responsiveness**: Must fit 15.6" screens with zero horizontal window overflow and internal container scrolling where necessary.
