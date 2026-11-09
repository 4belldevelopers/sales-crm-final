#!/usr/bin/env node
/**
 * scripts/adr-manager.js
 *
 * Architecture Decision Record (ADR) Living Ledger Manager
 * Integrates EGM consensus with JERO's permanent ADR ledger in docs/adr/
 *
 * Usage:
 *   node scripts/adr-manager.js list
 *   node scripts/adr-manager.js verify
 *   node scripts/adr-manager.js new "Title of the Decision" [--status Accepted]
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const rootDir = path.resolve(__dirname, '..');
const adrDir = path.join(rootDir, 'docs', 'adr');
const readmePath = path.join(adrDir, 'README.md');

const args = process.argv.slice(2);
const command = args[0] || 'list';

function getAdrFiles() {
    if (!fs.existsSync(adrDir)) return [];
    const files = fs.readdirSync(adrDir);
    return files
        .filter(f => /^\d{4}-.+\.md$/.test(f))
        .sort();
}

function parseAdr(filename) {
    const filePath = path.join(adrDir, filename);
    const content = fs.readFileSync(filePath, 'utf8');
    const match = filename.match(/^(\d{4})-(.+)\.md$/);
    const number = match ? match[1] : '????';
    const slug = match ? match[2] : filename;

    const titleMatch = content.match(/^#\s+(?:ADR\s+\d+\s+[—-]\s+)?(.+)$/m);
    const title = titleMatch ? titleMatch[1].trim() : slug;

    const statusMatch = content.match(/\*\*Status:\*\*\s*([^\n\r·]+)/i);
    const status = statusMatch ? statusMatch[1].trim() : 'Unknown';

    return { filename, number, slug, title, status, filePath, content };
}

switch (command) {
    case 'list': {
        console.log('\x1b[36m%s\x1b[0m', '════════════════════════════════════════════════════════════════════');
        console.log('\x1b[36m%s\x1b[0m', ' 🏛️  JERO ARCHITECTURE DECISION RECORDS (ADR LEDGER)');
        console.log('\x1b[36m%s\x1b[0m', '════════════════════════════════════════════════════════════════════');
        const adrs = getAdrFiles().map(parseAdr);
        if (adrs.length === 0) {
            console.log('No ADR files found in docs/adr/');
            break;
        }

        adrs.forEach(adr => {
            console.log(`\x1b[33m${adr.number}\x1b[0m | \x1b[32m${adr.status.padEnd(25)}\x1b[0m | \x1b[1m${adr.title}\x1b[0m`);
            console.log(`     \x1b[90mFile: docs/adr/${adr.filename}\x1b[0m`);
        });
        console.log('\x1b[36m%s\x1b[0m', '────────────────────────────────────────────────────────────────────');
        console.log(`Total Active Records: ${adrs.length}`);
        break;
    }

    case 'verify': {
        console.log('\x1b[36m%s\x1b[0m', '🔍 Verifying ADR Ledger Integrity...');
        let hasError = false;
        const adrs = getAdrFiles().map(parseAdr);
        const readmeContent = fs.existsSync(readmePath) ? fs.readFileSync(readmePath, 'utf8') : '';

        // Check sequential numbering
        adrs.forEach((adr, index) => {
            const expectedNum = String(index + 1).padStart(4, '0');
            if (adr.number !== expectedNum) {
                console.log(`\x1b[31m❌ Gap or out of sequence: Found ${adr.number}, expected ${expectedNum}\x1b[0m`);
                hasError = true;
            }

            // Check if present in README.md
            if (!readmeContent.includes(adr.filename) && !readmeContent.includes(`[${adr.number}]`)) {
                console.log(`\x1b[33m⚠️  Warning: ADR ${adr.number} (${adr.filename}) is not referenced in docs/adr/README.md\x1b[0m`);
                hasError = true;
            }

            // Check for required sections
            const requiredSections = ['## Decision', '## Consequences'];
            requiredSections.forEach(sec => {
                if (!adr.content.includes(sec)) {
                    console.log(`\x1b[33m⚠️  ADR ${adr.number} missing required section '${sec}'\x1b[0m`);
                }
            });
        });

        if (hasError) {
            console.log('\x1b[31m🛑 ADR integrity check failed.\x1b[0m');
            process.exit(1);
        } else {
            console.log('\x1b[32m🟢 ADR integrity verified: All records sequential, documented, and intact.\x1b[0m');
            process.exit(0);
        }
        break;
    }

    case 'new': {
        const title = args[1];
        if (!title) {
            console.error('\x1b[31mUsage: node scripts/adr-manager.js new "Decision Title" [--status Accepted]\x1b[0m');
            process.exit(1);
        }

        const adrs = getAdrFiles();
        const nextNum = String(adrs.length + 1).padStart(4, '0');
        const slug = title
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-|-$/g, '');
        const filename = `${nextNum}-${slug}.md`;
        const filePath = path.join(adrDir, filename);

        const statusIdx = args.indexOf('--status');
        const status = statusIdx !== -1 && args[statusIdx + 1] ? args[statusIdx + 1] : 'Active (EGM Consensus)';

        const template = `# ADR ${nextNum} — ${title}

**Status:** ${status} · **Date:** ${new Date().toISOString().split('T')[0]}

## Decision
Describe the decision that was made clearly and concisely.

## Rationale
Why was this decision made? Reference project scale, concurrency (10K), or CQRS constraints.

## Invariant Guarantees
- **CQRS:** MySQL writes only; Elasticsearch reads only.
- **Multi-Tenancy:** Scoped by \`company_id\`.
- **Octane / Runtime:** Zero state pollution in FrankenPHP workers.

## Alternatives Considered
- **Alternative A:** Why was this rejected?
- **Alternative B:** Why was this rejected?

## Tradeoffs
- ➕ Advantage 1
- ➕ Advantage 2
- ➖ Tradeoff / Operational cost

## Consequences for Contributors
Instructions and rules every agent and developer must follow when modifying related code.

## EGM Goldfish Sign-off
- **Review Loop ID:** 
- **Participating Personas:** egm-design, egm-codebase-details, egm-implementation-readiness
`;

        fs.writeFileSync(filePath, template, 'utf8');
        console.log(`\x1b[32m✓ Created new ADR: docs/adr/${filename}\x1b[0m`);

        // Update README.md
        if (fs.existsSync(readmePath)) {
            let readme = fs.readFileSync(readmePath, 'utf8');
            const newRow = `| [${nextNum}](${filename}) | ${title} | ${status} |`;
            if (readme.includes('|---|---|---|')) {
                readme = readme.replace(/(|---|---|---|\r?\n)/, `$1\r\n${newRow}`);
            } else {
                readme += `\n${newRow}\n`;
            }
            fs.writeFileSync(readmePath, readme, 'utf8');
            console.log(`\x1b[32m✓ Updated docs/adr/README.md table with ADR ${nextNum}\x1b[0m`);
        }
        break;
    }

    default:
        console.log(`Unknown command: ${command}. Available commands: list, verify, new`);
}
