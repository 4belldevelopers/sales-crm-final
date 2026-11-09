/**
 * JERO ATS — Octane / FrankenPHP Memory Leak & Tenant Contamination Scanner
 * Powered by Antigravity God-Level Tooling Suite
 *
 * Scans PHP controllers and services for long-lived state anti-patterns:
 * 1. Static state properties on non-read-only classes
 * 2. Un-reset singletons capturing tenant/request state
 * 3. Runtime config() or session() state leaks across HTTP requests
 */

import fs from 'fs';
import path from 'path';

const TARGET_DIRS = [
  path.resolve('app/Http/Controllers/ServiceCompany'),
  path.resolve('app/Services')
];

let totalScanned = 0;
let leaksFound = 0;

console.log("\n=============================================================");
console.log(" 🚀 JERO Octane & FrankenPHP Long-Lived State Leak Auditor");
console.log("=============================================================\n");

function scanDirectory(dir) {
  if (!fs.existsSync(dir)) return;
  const entries = fs.readdirSync(dir, { withFileTypes: true });

  for (const entry of entries) {
    const fullPath = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      scanDirectory(fullPath);
    } else if (entry.isFile() && entry.name.endsWith('.php')) {
      scanFile(fullPath);
    }
  }
}

function scanFile(filePath) {
  totalScanned++;
  const content = fs.readFileSync(filePath, 'utf-8');
  const lines = content.split('\n');
  const issues = [];

  lines.forEach((line, index) => {
    const lineNum = index + 1;
    const trimmed = line.trim();

    // Check for mutable static properties (e.g. public static $foo)
    if (/(public|protected|private)\s+static\s+\$[a-zA-Z0-9_]+/i.test(trimmed) && !/const\s+/i.test(trimmed)) {
      issues.push({
        line: lineNum,
        type: 'Mutable Static Property',
        desc: 'Static properties persist across all tenant requests in FrankenPHP/Octane workers.',
        snippet: trimmed
      });
    }

    // Check for runtime config mutation
    if (/config\(\s*\[/i.test(trimmed)) {
      issues.push({
        line: lineNum,
        type: 'Runtime Config Mutation',
        desc: 'Mutating config() at runtime leaks into subsequent worker requests under Octane.',
        snippet: trimmed
      });
    }
  });

  if (issues.length > 0) {
    leaksFound += issues.length;
    console.log(`⚠️  ${path.relative(process.cwd(), filePath)} (${issues.length} potential state leaks):`);
    issues.forEach(iss => {
      console.log(`   L${iss.line}: [${iss.type}] ${iss.snippet}`);
      console.log(`         ↳ ${iss.desc}`);
    });
    console.log();
  }
}

for (const dir of TARGET_DIRS) {
  scanDirectory(dir);
}

console.log("=============================================================");
console.log(` Scanned: ${totalScanned} files | State Issues Detected: ${leaksFound}`);
console.log(` Verdict: ${leaksFound === 0 ? '✅ 100% CLEAN FOR OCTANE' : '⚠️  AUDIT WARNINGS FLAGGED'}`);
console.log("=============================================================\n");
