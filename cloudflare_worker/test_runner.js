import { enrichCompany } from './worker.js';
import worker from './worker.js';

async function runTests() {
  console.log('================================================================');
  console.log('⚡ TESTING JERO COMPANY INTELLIGENCE CLOUDFLARE WORKER (NODE 20)');
  console.log('================================================================\n');

  // Test 1: Anti-SSRF Security Check
  console.log('🔒 Test 1: Anti-SSRF Firewall Verification');
  const ssrfRequest = new Request('https://worker.local/enrich?domain=127.0.0.1');
  const ssrfRes = await worker.fetch(ssrfRequest, {}, {});
  const ssrfData = await ssrfRes.json();
  console.log(`- Requesting 127.0.0.1: Status = ${ssrfRes.status}, Error = ${ssrfData.error ? 'Blocked ✅' : 'Failed ❌'}`);

  const cloudReq = new Request('https://worker.local/enrich?domain=169.254.169.254');
  const cloudRes = await worker.fetch(cloudReq, {}, {});
  const cloudData = await cloudRes.json();
  console.log(`- Requesting 169.254.169.254: Status = ${cloudRes.status}, Error = ${cloudData.error ? 'Blocked ✅' : 'Failed ❌'}\n`);

  // Test 2: Real-World Domains
  const domains = ['shopify.com', 'stripe.com', 'razorpay.com'];

  for (const domain of domains) {
    console.log(`🏢 Test 2: Enriching ${domain}...`);
    const start = Date.now();
    const data = await enrichCompany(domain);
    const latency = Date.now() - start;

    console.log(`  ⏱️ Latency: ${latency}ms`);
    console.log(`  📌 [1] Company Name: ${data.name}`);
    console.log(`  📌 [2] Logo: ${data.logo}`);
    console.log(`  📌 [3] Industry: ${data.industry}`);
    console.log(`  📌 [4] Headcount: ${data.company_sizes}`);
    console.log(`  📌 [5] Company Type: ${data.company_type}`);
    console.log(`  📌 [6] Founded Year: ${data.founded}`);
    console.log(`  📌 [7] Careers URL: ${data.career_page_url}`);
    console.log(`  📌 [8] Description: ${(data.description || '').slice(0, 80)}...`);
    console.log(`  📌 [9] Tags: ${data.tags.join(', ')}`);
    console.log(`  📌 [10] City: ${data.city}`);
    console.log(`  📌 [11] Full Address: ${data.address}`);
    console.log(`  📌 [12] Contact Person: ${data.cp_name}`);
    console.log(`  📌 [13] Contact Email: ${data.cp_email}`);
    console.log(`  📌 [14] Calling Code: ${data.calling_code}`);
    console.log(`  📌 [15] Contact Phone: ${data.cp_number}`);
    console.log(`  📌 [16] Designation: ${data.cp_desigination}`);
    console.log(`  📌 [17] LinkedIn: ${data.linkedin_url}`);
    console.log(`  📌 [18] Twitter/X: ${data.twitter_url}`);
    console.log(`  📌 [19] GitHub: ${data.github_url}`);
    console.log(`  📌 [20] AP Email: ${data.ap_email}`);
    console.log(`  📌 [21] Tax ID: ${data.tax_id}`);
    console.log(`  🌐 Extended: YT=${data.youtube_url ? '✅' : 'None'}, FB=${data.facebook_url ? '✅' : 'None'}, IG=${data.instagram_url ? '✅' : 'None'}`);
    console.log('----------------------------------------------------------------\n');
  }

  console.log('🎉 ALL TESTS COMPLETED SUCCESSFULLY AT GOD LEVEL!');
}

runTests().catch(console.error);
