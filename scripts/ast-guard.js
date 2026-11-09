#!/usr/bin/env node
/**
 * scripts/ast-guard.js
 *
 * Deterministic AST Invariant Guard for JERO Rules
 * Runs ast-grep rules against modified/targeted files to enforce:
 *   - Rule 1: No direct DB::select in web/API controllers
 *   - Rule 11: No env() outside config/
 *   - Octane Guard: No config([...]) runtime mutation in controllers
 *   - Rule 18: Persona Code Freeze (zero touches to product_company / staffing_company)
 */

import { execSync } from 'child_process';
import path from 'path';
import fs from 'fs';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const rootDir = path.resolve(__dirname, '..');

const args = process.argv.slice(2);
let targetPath = args[0];

if (!targetPath) {
    try {
        const staged = execSync('git diff --cached --name-only --diff-filter=ACM', { cwd: rootDir, encoding: 'utf8' })
            .trim()
            .split('\n')
            .filter(f => f.endsWith('.php') && fs.existsSync(path.join(rootDir, f)));
        if (staged.length > 0) {
            targetPath = staged.join(' ');
        }
    } catch (_) {}
}

if (!targetPath) {
    targetPath = 'app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php';
}

console.log('\x1b[36m%s\x1b[0m', '════════════════════════════════════════════════════════════════════');
console.log('\x1b[36m%s\x1b[0m', ' 🛡️  JERO DETERMINISTIC AST INVARIANT GUARD (ast-grep)');
console.log('\x1b[36m%s\x1b[0m', '════════════════════════════════════════════════════════════════════');
console.log(`Target: ${targetPath}`);

let hasViolations = false;

// 1. Check Rule 18: Persona Code Freeze via Git
try {
    const gitDiff = execSync('git diff --name-only HEAD', { cwd: rootDir, encoding: 'utf8' });
    const stagedDiff = execSync('git diff --cached --name-only', { cwd: rootDir, encoding: 'utf8' });
    const touchedFiles = Array.from(new Set([...gitDiff.trim().split('\n'), ...stagedDiff.trim().split('\n')])).filter(Boolean);

    const frozenViolations = touchedFiles.filter(f => 
        f.startsWith('resources/views/product_company/') ||
        f.startsWith('resources/views/staffing_company/') ||
        f.startsWith('app/Http/Controllers/ProductCompany/') ||
        f.startsWith('app/Http/Controllers/StaffingCompany/')
    );

    if (frozenViolations.length > 0) {
        hasViolations = true;
        console.log('\n\x1b[31m%s\x1b[0m', '❌ [RULE 18 VIOLATION] Frozen Persona Modified:');
        frozenViolations.forEach(f => console.log(`   - \x1b[33m${f}\x1b[0m (Product/Staffing personas are strictly frozen)`));
    } else {
        console.log('\x1b[32m%s\x1b[0m', '✓ Rule 18 Persona Code Freeze: PASSED (0 frozen files touched)');
    }
} catch (e) {
    // If git fails, continue to AST scan
}

// 2. Run AST Grep Scan
const astGrepCmd = process.platform === 'win32'
    ? path.join(rootDir, 'node_modules', '.bin', 'ast-grep.cmd')
    : path.join(rootDir, 'node_modules', '.bin', 'ast-grep');

let matches = [];
try {
    const result = execSync(`"${astGrepCmd}" scan --json "${targetPath}"`, {
        cwd: rootDir,
        encoding: 'utf8',
        stdio: ['ignore', 'pipe', 'pipe']
    });
    matches = JSON.parse(result || '[]');
} catch (err) {
    if (err.stdout) {
        try {
            matches = JSON.parse(err.stdout.toString());
        } catch (_) {
            console.error('\x1b[31mError parsing ast-grep output:\x1b[0m', err.message);
            hasViolations = true;
        }
    } else {
        console.error('\x1b[31mError running ast-grep:\x1b[0m', err.message);
        hasViolations = true;
    }
}

if (matches.length > 0) {
    hasViolations = true;
    console.log(`\n\x1b[31m❌ Found ${matches.length} AST Invariant Violation(s):\x1b[0m`);
    matches.forEach((m, idx) => {
        console.log(`\n[\x1b[33m${idx + 1}\x1b[0m] Rule: \x1b[35m${m.ruleId}\x1b[0m`);
        console.log(`    File: \x1b[36m${m.file}:${m.range ? m.range.start.line : '?'}\x1b[0m`);
        console.log(`    Note: ${m.message || m.ruleId}`);
        const snippet = m.text ? m.text.replace(/\r?\n|\r/g, ' ').substring(0, 90) : '';
        console.log(`    Snippet: \x1b[90m${snippet}\x1b[0m`);
    });
} else if (!hasViolations) {
    console.log('\x1b[32m%s\x1b[0m', '✓ Deterministic AST Invariants: PASSED (0 violations)');
}

console.log('\x1b[36m%s\x1b[0m', '────────────────────────────────────────────────────────────────────');
if (hasViolations) {
    console.log('\x1b[31m%s\x1b[0m', '🛑 AST GUARD FAILED: Invariants breached. Fix violations before proceeding.');
    process.exit(1);
} else {
    console.log('\x1b[32m%s\x1b[0m', '🟢 AST GUARD PASSED: All architectural invariants verified.');
    process.exit(0);
}
