/**
 * JERO ATS — Blade XSS & Raw Unescaped Output Auditor
 * Powered by Antigravity God-Level Tooling Suite
 *
 * Scans Blade views for {!! ... !!} unescaped outputs.
 * Flags potentially vulnerable user-supplied strings (resumes, notes, names, titles).
 */

import fs from 'fs';
import path from 'path';

const TARGET_DIR = path.resolve('resources/views/service_company');

let totalBladeFiles = 0;
let rawOutputsCount = 0;
const criticalPatterns = [
  { name: 'Direct Request Input', regex: /\{!!\s*(\$request->|\$_GET|\$_POST|request\()/i },
  { name: 'Candidate/User Text', regex: /\{!!\s*(\$candidate->|\$user->|\$application->|\$row->)(?!.*(clean|strip_tags|purify|e\())/i },
  { name: 'Raw Note/Comment', regex: /\{!!\s*(\$note|\$comment|\$remark)(?!.*(clean|strip_tags|purify|e\())/i }
];

console.log("\n=============================================================");
console.log(" 🛡️  JERO Blade XSS & Raw Unescaped Output Auditor");
console.log(` Target: ${path.relative(process.cwd(), TARGET_DIR)}`);
console.log("=============================================================\n");

function scanDirectory(dir) {
  if (!fs.existsSync(dir)) return;
  const entries = fs.readdirSync(dir, { withFileTypes: true });

  for (const entry of entries) {
    const fullPath = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      scanDirectory(fullPath);
    } else if (entry.isFile() && entry.name.endsWith('.blade.php')) {
      scanBladeFile(fullPath);
    }
  }
}

function scanBladeFile(filePath) {
  totalBladeFiles++;
  const content = fs.readFileSync(filePath, 'utf-8');
  const lines = content.split('\n');
  const issues = [];

  lines.forEach((line, index) => {
    const lineNum = index + 1;
    const trimmed = line.trim();

    if (trimmed.includes('{!!')) {
      rawOutputsCount++;
      criticalPatterns.forEach(pattern => {
        if (pattern.regex.test(trimmed)) {
          issues.push({
            line: lineNum,
            type: pattern.name,
            snippet: trimmed.slice(0, 100)
          });
        }
      });
    }
  });

  if (issues.length > 0) {
    console.log(`⚠️  ${path.relative(process.cwd(), filePath)} (${issues.length} flagged):`);
    issues.forEach(iss => {
      console.log(`   L${iss.line}: [${iss.type}] ${iss.snippet}`);
    });
    console.log();
  }
}

scanDirectory(TARGET_DIR);

console.log("=============================================================");
console.log(` Scanned: ${totalBladeFiles} Blade files | Raw {!! !!} detected: ${rawOutputsCount}`);
console.log("=============================================================\n");
