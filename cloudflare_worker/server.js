/**
 * JERO ATS / CRM — Local Development Server for Company Intelligence Worker
 * Runs the exact Cloudflare Worker code locally using Node 20+ native fetch and Request/Response.
 * Listens on http://127.0.0.1:8787 with global CORS enabled.
 * Serves the Interactive Test Lab UI at http://127.0.0.1:8787.
 */

import http from 'node:http';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import worker from './worker.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const PORT = process.env.PORT || 8787;
const HOST = '127.0.0.1';

const htmlContent = fs.readFileSync(path.join(__dirname, 'index.html'), 'utf8');

const server = http.createServer(async (req, res) => {
  // Global CORS Headers
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', '*');

  if (req.method === 'OPTIONS') {
    res.writeHead(204);
    res.end();
    return;
  }

  const parsedUrl = new URL(`http://${HOST}:${PORT}${req.url}`);

  // Serve interactive test UI on / or /test when no domain param is present
  if ((parsedUrl.pathname === '/' || parsedUrl.pathname === '/test') && !parsedUrl.searchParams.get('domain') && !parsedUrl.searchParams.get('url')) {
    res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
    res.end(htmlContent);
    return;
  }

  try {
    const fullUrl = `http://${HOST}:${PORT}${req.url}`;
    const webReq = new Request(fullUrl, {
      method: req.method,
      headers: req.headers
    });

    const webRes = await worker.fetch(webReq, {}, {});
    const headers = {};
    for (const [key, value] of webRes.headers.entries()) {
      headers[key] = value;
    }
    headers['Access-Control-Allow-Origin'] = '*';

    res.writeHead(webRes.status, headers);
    const body = await webRes.text();
    res.end(body);
  } catch (err) {
    console.error('[Worker Server Error]:', err);
    res.writeHead(500, { 'Content-Type': 'application/json', 'Access-Control-Allow-Origin': '*' });
    res.end(JSON.stringify({ error: err.message || 'Internal error' }));
  }
});

server.listen(PORT, HOST, () => {
  console.log(`[JERO God-Level Company Intelligence] Local Worker running on http://${HOST}:${PORT}`);
  console.log(`Interactive Test UI: http://${HOST}:${PORT}`);
  console.log(`API Endpoint: http://${HOST}:${PORT}/?domain=razorpay.com`);
});
