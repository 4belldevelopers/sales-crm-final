/**
 * Step 8b: Build a single SVG sprite from the Keenicons npm package.
 *
 * Prerequisites:
 *   npm install --save-dev @keenthemes/keenicons
 *
 * Run:
 *   node scripts/build-icon-sprite.js
 *
 * Output:
 *   public/icons.svg  — referenced by resources/views/components/icon.blade.php
 *
 * After the sprite is deployed, every <x-icon name="search" /> in Blade renders as
 *   <svg><use href="/icons.svg#ki-search"></use></svg>
 * instead of the 250 KB Keenicons font, saving ~200 KB per page load (after Brotli).
 *
 * To replace existing <i class="ki ki-duotone ki-XXX"> usages:
 *   node scripts/build-icon-sprite.js --replace
 * This scans resources/views/**\/*.blade.php and rewrites ki-duotone/ki-outline/ki-bold
 * tags to <x-icon name="XXX" />.
 */

const fs   = require('fs');
const path = require('path');

const keeniconsSrcDir = path.resolve(__dirname, '../node_modules/@keenthemes/keenicons/dist/duotone');
const outputFile      = path.resolve(__dirname, '../public/icons.svg');
const viewsDir        = path.resolve(__dirname, '../resources/views');

function buildSprite() {
    if (!fs.existsSync(keeniconsSrcDir)) {
        console.error('Keenicons package not found. Run: npm install --save-dev @keenthemes/keenicons');
        process.exit(1);
    }

    const files  = fs.readdirSync(keeniconsSrcDir).filter(f => f.endsWith('.svg'));
    const chunks = ['<svg xmlns="http://www.w3.org/2000/svg" style="display:none">'];

    for (const file of files) {
        const name    = 'ki-' + path.basename(file, '.svg');
        const content = fs.readFileSync(path.join(keeniconsSrcDir, file), 'utf8');
        // Extract inner SVG content (strip outer <svg> wrapper)
        const inner   = content.replace(/<\/?svg[^>]*>/g, '').trim();
        chunks.push(`<symbol id="${name}" viewBox="0 0 24 24">${inner}</symbol>`);
    }

    chunks.push('</svg>');
    fs.writeFileSync(outputFile, chunks.join('\n'), 'utf8');
    console.log(`Wrote ${files.length} icons → ${outputFile}`);
}

function replaceInViews() {
    // Regex: <i class="ki ki-duotone ki-XXXX[ other-classes]...">...</i>
    // Replaces with <x-icon name="XXXX" class="other-classes" />
    const RE = /<i\s+class="[^"]*\bki-duotone\s+ki-([\w-]+)([^"]*)"\s*(?:aria-hidden="true")?><\/i>/g;

    function walk(dir) {
        for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
            const full = path.join(dir, entry.name);
            if (entry.isDirectory()) {
                walk(full);
            } else if (entry.name.endsWith('.blade.php')) {
                const src  = fs.readFileSync(full, 'utf8');
                const out  = src.replace(RE, (_, iconName, extraClasses) => {
                    const cls = extraClasses.trim();
                    return cls ? `<x-icon name="${iconName}" class="${cls}" />` : `<x-icon name="${iconName}" />`;
                });
                if (out !== src) {
                    fs.writeFileSync(full, out, 'utf8');
                    console.log('Patched:', full);
                }
            }
        }
    }
    walk(viewsDir);
}

buildSprite();
if (process.argv.includes('--replace')) {
    replaceInViews();
    console.log('Blade replacements done.');
}
