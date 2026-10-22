/**
 * High-Speed Static Location & Taxonomy Bundling Script (ES Module / Bun / Node)
 * Produces maximum-density Brotli (.br, quality 11) archives and manifest hashes.
 */
import fs from 'node:fs';
import path from 'node:path';
import zlib from 'node:zlib';
import crypto from 'node:crypto';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const startTime = process.hrtime.bigint();

const targetDir = path.resolve(__dirname, '../public/assets/service_company/client');
const inputPath = path.join(targetDir, 'countries.json');

if (!fs.existsSync(inputPath)) {
    console.error(`Input file not found: ${inputPath}`);
    process.exit(1);
}

const rawData = fs.readFileSync(inputPath);
const countries = JSON.parse(rawData.toString('utf8'));

// Maximum density Brotli compression (Quality 11)
const brotliBuffer = zlib.brotliCompressSync(rawData, {
    params: {
        [zlib.constants.BROTLI_PARAM_QUALITY]: 11,
        [zlib.constants.BROTLI_PARAM_SIZE_HINT]: rawData.length
    }
});

const outputPathBr = path.join(targetDir, 'countries.json.br');
fs.writeFileSync(outputPathBr, brotliBuffer);

// Calculate SHA-256 integrity hash
const hash = crypto.createHash('sha256').update(rawData).digest('hex');
const manifest = {
    version: '1.0.0',
    hash: hash,
    count: countries.length,
    raw_size_bytes: rawData.length,
    brotli_size_bytes: brotliBuffer.length,
    compression_ratio: ((1 - (brotliBuffer.length / rawData.length)) * 100).toFixed(1) + '%',
    updated_at: new Date().toISOString()
};

const manifestPath = path.join(targetDir, 'countries-manifest.json');
fs.writeFileSync(manifestPath, JSON.stringify(manifest, null, 2));

const endTime = process.hrtime.bigint();
const elapsedMs = Number(endTime - startTime) / 1000000;

console.log(`[Brotli Bundler] Bundled ${countries.length} countries:`);
console.log(`  Raw JSON:    ${(rawData.length / 1024).toFixed(1)} KB`);
console.log(`  Brotli .br:  ${(brotliBuffer.length / 1024).toFixed(1)} KB (${manifest.compression_ratio} compression)`);
console.log(`  SHA-256:     ${hash.substring(0, 16)}...`);
console.log(`  Build Time:  ${elapsedMs.toFixed(2)} ms`);
