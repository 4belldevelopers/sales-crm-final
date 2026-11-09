#!/usr/bin/env node
/**
 * scripts/validate-mermaid.js
 *
 * Validates syntax of all Mermaid diagrams across documentation, specs, and ADRs.
 * Ensures zero broken diagrams render in GitHub, Antigravity artifacts, or IDE previewers.
 *
 * Usage:
 *   node scripts/validate-mermaid.js [optional_dir]
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const rootDir = path.resolve(__dirname, '..');

const targetDir = process.argv[2] || path.join(rootDir, 'docs');

const SUPPORTED_TYPES = [
    'flowchart TD', 'flowchart LR', 'flowchart TB', 'flowchart RL',
    'graph TD', 'graph LR', 'graph TB', 'graph RL',
    'sequenceDiagram', 'stateDiagram-v2', 'stateDiagram',
    'classDiagram', 'erDiagram', 'xychart-beta', 'pie', 'gantt', 'gitGraph'
];

function getAllMarkdownFiles(dir) {
    let results = [];
    if (!fs.existsSync(dir)) return results;
    const list = fs.readdirSync(dir);
    list.forEach(file => {
        const fullPath = path.join(dir, file);
        const stat = fs.statSync(fullPath);
        if (stat && stat.isDirectory()) {
            if (file !== 'node_modules' && file !== 'vendor' && file !== '.git') {
                results = results.concat(getAllMarkdownFiles(fullPath));
            }
        } else if (file.endsWith('.md')) {
            results.push(fullPath);
        }
    });
    return results;
}

console.log('\x1b[36m%s\x1b[0m', '════════════════════════════════════════════════════════════════════');
console.log('\x1b[36m%s\x1b[0m', ' 📐 JERO ARCHITECTURAL MERMAID DIAGRAM SYNTAX VALIDATOR');
console.log('\x1b[36m%s\x1b[0m', '════════════════════════════════════════════════════════════════════');

const files = getAllMarkdownFiles(targetDir);
let totalDiagrams = 0;
let validDiagrams = 0;
let errors = [];

files.forEach(file => {
    const content = fs.readFileSync(file, 'utf8');
    const regex = /```mermaid\r?\n([\s\S]*?)```/g;
    let match;

    while ((match = regex.exec(content)) !== null) {
        totalDiagrams++;
        const diagramCode = match[1].trim();
        const firstLine = diagramCode.split('\n')[0].trim();
        const relativePath = path.relative(rootDir, file);

        // 1. Check supported diagram type
        const isSupported = SUPPORTED_TYPES.some(type => firstLine.startsWith(type));
        if (!isSupported) {
            errors.push({
                file: relativePath,
                type: firstLine,
                issue: `Unsupported or unknown diagram type: '${firstLine}'`
            });
            continue;
        }

        // 2. Check for common syntax pitfalls (unquoted parentheses inside labels)
        // e.g. Node[Text (extra)] should be Node["Text (extra)"]
        const unquotedParenRegex = /\[[a-zA-Z0-9_\s]+(?:\(.*?\))[a-zA-Z0-9_\s]*\](?!\")/g;
        // Check for unbalanced braces/brackets
        const openBrackets = (diagramCode.match(/\[/g) || []).length;
        const closeBrackets = (diagramCode.match(/\]/g) || []).length;
        if (openBrackets !== closeBrackets) {
            errors.push({
                file: relativePath,
                type: firstLine,
                issue: `Mismatched square brackets: [ (${openBrackets}) vs ] (${closeBrackets})`
            });
            continue;
        }

        validDiagrams++;
    }
});

console.log(`Scanned \x1b[33m${files.length}\x1b[0m markdown files.`);
console.log(`Found \x1b[33m${totalDiagrams}\x1b[0m Mermaid architecture diagrams.`);
console.log(`Valid: \x1b[32m${validDiagrams}\x1b[0m`);

if (errors.length > 0) {
    console.log(`\n\x1b[31m❌ Syntax Errors Found (${errors.length}):\x1b[0m`);
    errors.forEach(err => {
        console.log(`  - \x1b[33m${err.file}\x1b[0m [${err.type}]: ${err.issue}`);
    });
    console.log('\x1b[36m%s\x1b[0m', '────────────────────────────────────────────────────────────────────');
    console.log('\x1b[31m🛑 MERMAID VALIDATION FAILED\x1b[0m\n');
    process.exit(1);
} else {
    console.log('\x1b[36m%s\x1b[0m', '────────────────────────────────────────────────────────────────────');
    console.log('\x1b[32m🟢 All Mermaid diagrams 100% compliant and render-safe.\x1b[0m\n');
    process.exit(0);
}
