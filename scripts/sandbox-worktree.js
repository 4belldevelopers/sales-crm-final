#!/usr/bin/env node
/**
 * scripts/sandbox-worktree.js
 *
 * Ephemeral Git Worktree Isolation Sandbox Harness
 * Enables subagents and automated verification loops to run risky refactors,
 * tests, and asset builds in zero-copy isolated filesystem branches.
 *
 * Usage:
 *   node scripts/sandbox-worktree.js list
 *   node scripts/sandbox-worktree.js create <task-name>
 *   node scripts/sandbox-worktree.js destroy <task-name>
 */

import { execSync } from 'child_process';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const rootDir = path.resolve(__dirname, '..');
const worktreeBaseDir = path.join(rootDir, '.agents', 'worktrees');

const args = process.argv.slice(2);
const command = args[0] || 'list';
const taskName = args[1];

function sanitizeName(name) {
    return name ? name.toLowerCase().replace(/[^a-z0-9_-]/g, '-') : '';
}

switch (command) {
    case 'list': {
        console.log('\x1b[36m%s\x1b[0m', '════════════════════════════════════════════════════════════════════');
        console.log('\x1b[36m%s\x1b[0m', ' 🌿  JERO ISOLATED SUBAGENT WORKTREES');
        console.log('\x1b[36m%s\x1b[0m', '════════════════════════════════════════════════════════════════════');
        try {
            const output = execSync('git worktree list', { cwd: rootDir, encoding: 'utf8' });
            const lines = output.trim().split('\n');
            lines.forEach(line => {
                if (line.includes('.agents/worktrees') || line.includes('.claude/worktrees')) {
                    console.log(` \x1b[33m⚡ SANDBOX:\x1b[0m ${line}`);
                } else {
                    console.log(`    MAIN:    ${line}`);
                }
            });
        } catch (e) {
            console.error('Error listing worktrees:', e.message);
        }
        console.log('\x1b[36m%s\x1b[0m', '────────────────────────────────────────────────────────────────────');
        break;
    }

    case 'create': {
        if (!taskName) {
            console.error('\x1b[31mUsage: node scripts/sandbox-worktree.js create <task-name>\x1b[0m');
            process.exit(1);
        }
        const cleanName = sanitizeName(taskName);
        const targetDir = path.join(worktreeBaseDir, `sandbox-${cleanName}`);
        const branchName = `sandbox/${cleanName}`;

        if (!fs.existsSync(worktreeBaseDir)) {
            fs.mkdirSync(worktreeBaseDir, { recursive: true });
        }

        console.log(`\x1b[36mCreating isolated sandbox at:\x1b[0m ${targetDir}`);
        try {
            execSync(`git worktree add -b "${branchName}" "${targetDir}" HEAD`, {
                cwd: rootDir,
                stdio: 'inherit'
            });

            // Copy essential runtime config files if present
            const envPath = path.join(rootDir, '.env');
            if (fs.existsSync(envPath)) {
                fs.copyFileSync(envPath, path.join(targetDir, '.env'));
                console.log('\x1b[32m✓ Synchronized .env into sandbox workspace\x1b[0m');
            }

            console.log('\x1b[32m%s\x1b[0m', `\n🟢 Sandbox "${cleanName}" ready for isolated subagent execution!`);
            console.log(`Directory: ${targetDir}`);
            console.log(`Branch:    ${branchName}`);
        } catch (e) {
            console.error('\x1b[31mFailed to create worktree:\x1b[0m', e.message);
            process.exit(1);
        }
        break;
    }

    case 'destroy': {
        if (!taskName) {
            console.error('\x1b[31mUsage: node scripts/sandbox-worktree.js destroy <task-name>\x1b[0m');
            process.exit(1);
        }
        const cleanName = sanitizeName(taskName);
        const targetDir = path.join(worktreeBaseDir, `sandbox-${cleanName}`);
        const branchName = `sandbox/${cleanName}`;

        console.log(`\x1b[33mDestroying sandbox worktree:\x1b[0m ${targetDir}`);
        try {
            execSync(`git worktree remove --force "${targetDir}"`, {
                cwd: rootDir,
                stdio: 'inherit'
            });
            try {
                execSync(`git branch -D "${branchName}"`, {
                    cwd: rootDir,
                    stdio: 'inherit'
                });
            } catch (_) {}
            console.log('\x1b[32m%s\x1b[0m', `\n✓ Sandbox "${cleanName}" completely deleted.`);
        } catch (e) {
            console.error('\x1b[31mFailed to remove worktree:\x1b[0m', e.message);
            process.exit(1);
        }
        break;
    }

    default:
        console.log(`Unknown command: ${command}. Use list, create <name>, or destroy <name>.`);
}
