/**
 * JERO ATS / CRM — Local Development Server for Company Intelligence Worker
 * Runs the exact Cloudflare Worker code locally using Node 20+ native fetch and Request/Response.
 * Listens on http://127.0.0.1:8787 with global CORS enabled.
 */

import http from 'node:http';
import worker from './worker.js';

const PORT = process.env.PORT || 8787;
const HOST = '127.0.0.1';

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
  console.log(`Ready to enrich domains. Example: http://${HOST}:${PORT}/?domain=razorpay.com`);
});
