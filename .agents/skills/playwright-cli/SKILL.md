---
name: playwright-cli
description: Token-efficient interactive browser automation using Microsoft Playwright CLI (@playwright/cli) for web inspection, UI/UX verification, screenshots, and deterministic agent browser actions.
---

# 🎭 Playwright CLI Agent Skill

This skill guides AI agents on using **Microsoft Playwright CLI (`@playwright/cli`)** for browser automation, visual UI verification, responsive layout checks (15.6" screens / 1366x768 / 1920x1080), network inspection, and token-efficient web task execution.

---

## 💡 Why Playwright CLI?

Unlike heavy Model Context Protocol (MCP) browser servers or manual puppeteer scripts:
1. **Token Efficiency**: Instead of dumping thousands of lines of raw HTML/DOM into LLM context, `playwright-cli` writes snapshots to disk and returns a concise, semantic **accessibility tree** with element references (`refs`).
2. **Persistent Daemon IPC**: A background daemon keeps the browser session, cookies, and local state warm over local sockets, eliminating cold-start browser boot delays.
3. **Deterministic Interaction**: Commands target explicit references (`refs` like `e3`, `e12`) instead of guessing brittle CSS or XPath selectors.

---

## 🛠️ When to Use

- **UI/UX & Card-Subcard Verification**: Verify that refactored views, drawers, and headers render accurately with correct background colors, badge colors, and layout structure.
- **15.6" Screen Fit Audit**: Test viewport sizes (`1366x768` and `1920x1080`) to ensure zero horizontal scrollbars or element clipping.
- **Form & Flow Testing**: Automate multi-step form fills, drawer open/close actions, tab switching, and button clicks.
- **Screenshot Evidence Generation**: Capture screenshots directly into test artifact directories for verification documentation.
- **Network & Console Diagnostics**: Inspect network requests (`requests`, `request <n>`) and console errors (`console error`) directly without heavy DevTools streaming.

---

## 🚀 Core Command Reference (v0.1.x)

### 1. Launch & Session Management
```bash
# Open URL in browser (starts daemon automatically)
playwright-cli open http://localhost:8000

# Open with headed browser (when visible interaction is needed)
playwright-cli open http://localhost:8000 --headed

# Use an isolated named session (prevents state collision between agents)
playwright-cli -s=agent_a open http://localhost:8000

# Close active browser session
playwright-cli close
playwright-cli -s=agent_a close

# Kill all orphan or stale browser daemon sessions
playwright-cli close-all
playwright-cli kill-all
```

### 2. Observing the Page (Snapshots, Search & Screenshots)
```bash
# Capture page accessibility snapshot to obtain element refs
playwright-cli snapshot

# Search snapshot for text or regex with surrounding context
playwright-cli find "Submit Application"

# Capture screenshot to file
playwright-cli screenshot storage/logs/screen.png

# Capture element-specific screenshot
playwright-cli screenshot e5 storage/logs/element-e5.png

# Save page as PDF
playwright-cli pdf storage/logs/page.pdf
```

### 3. Interacting with Elements via `refs`
```bash
# Click an element using ref
playwright-cli click e4

# Fill text into editable element
playwright-cli fill e7 "admin@jero.works"

# Type sequentially (for debounced inputs / autocompletes)
playwright-cli type "Senior Laravel Engineer"

# Keyboard keys
playwright-cli press Enter
playwright-cli press Tab
playwright-cli press Escape

# Checkbox & Radio Buttons
playwright-cli check e10
playwright-cli uncheck e10

# Dropdown selection
playwright-cli select e12 "USD"

# Hover & Drag
playwright-cli hover e15
playwright-cli drag e2 e8

# Resize viewport (e.g. 15.6" test 1366x768 or 1920x1080)
playwright-cli resize 1366 768
playwright-cli resize 1920 1080
```

### 4. Tabs & Navigation
```bash
# Navigation
playwright-cli goto http://localhost:8000/service-company/dashboard
playwright-cli reload
playwright-cli go-back
playwright-cli go-forward

# Multi-tab management
playwright-cli tab-list
playwright-cli tab-new http://localhost:8000/admin
playwright-cli tab-select 1
playwright-cli tab-close 1
```

### 5. Storage & Authentication State
```bash
# Save/load entire auth session state
playwright-cli state-save storage/auth/session.json
playwright-cli state-load storage/auth/session.json

# Cookies
playwright-cli cookie-list
playwright-cli cookie-clear

# LocalStorage & SessionStorage
playwright-cli localstorage-list
playwright-cli localstorage-get token
```

### 6. Network & Console Diagnostics
```bash
# List all network requests since load
playwright-cli requests

# Inspect specific request headers/body/response
playwright-cli request 3

# View browser console errors and logs
playwright-cli console error
playwright-cli console warn
```

---

## ⚠️ Critical Agent Rules & Best Practices

1. **Stale Ref Invalidation**:
   - `refs` (e.g. `e1`, `e5`) are strictly bound to the immediate snapshot state.
   - Any navigation, AJAX update, modal opening, or drawer sliding invalidates existing `refs`.
   - **Always run `playwright-cli snapshot` again** after any action that mutates the DOM before issuing the next click/fill.

2. **Always Terminate Sessions**:
   - When finished with a verification or automation sequence, always run `playwright-cli close` (or `playwright-cli kill-all` in case of lingering processes) to release memory and socket locks.

3. **Multi-Tenant & Session Isolation**:
   - When testing concurrent scenarios (e.g. Service Company vs. Admin vs. Staffing Company), use distinct named sessions (`-s=service_co`, `-s=admin`) to keep authentication cookies completely separate.
