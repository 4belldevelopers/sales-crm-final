const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

async function testMultipleResumes() {
    console.log('🚀 [TEST] Initializing Playwright Headless Chromium...');
    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();

    // Capture browser console logs
    page.on('console', msg => {
        const text = msg.text();
        if (text.includes('[PARSER') || text.includes('[SFHP') || text.includes('Error')) {
            console.log(`[Browser Console]:`, text);
        }
    });

    const uploadsDir = path.join(__dirname, '../storage/app/public/uploads');
    const files = fs.readdirSync(uploadsDir).filter(f => f.endsWith('.pdf')).slice(0, 3);
    console.log(`📄 Testing ${files.length} real resumes from storage...`);

    const parserBridgeCode = fs.readFileSync(path.join(__dirname, '../public/js/jero-client-parser.js'), 'utf8');

    const htmlContent = `
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>JERO Real Resume Parser Multi-File Test</title>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>
    <body>
        <form id="createForm">
            <input type="text" id="full_name" name="full_name" value="" />
            <input type="text" id="name" name="name" value="" />
            <input type="email" id="email" name="email" value="" />
            <input type="tel" id="phone" name="phone" value="" />
            <input type="text" id="primary_country_code" name="primary_country_code" value="" />
            <input type="date" id="dob" name="dob" value="" />
            
            <select id="nationality" name="nationality">
                <option value="">Select nationality</option>
                <option value="Indian">🇮🇳 Indian (India)</option>
                <option value="American">🇺🇸 American (United States)</option>
                <option value="British">🇬🇧 British (United Kingdom)</option>
                <option value="Canadian">🇨🇦 Canadian (Canada)</option>
            </select>
            <select id="gender" name="gender">
                <option value="Rather Not Say">Rather Not Say</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>

            <select id="qualification" name="qualification">
                <option value="">Select qualification</option>
                <option value="13">B.Tech / B.E (Engineering & Technology)</option>
                <option value="40">M.Tech / M.E</option>
                <option value="41">MBA</option>
                <option value="12">B.S. / B.Sc</option>
                <option value="14">MCA</option>
                <option value="15">BCA</option>
                <option value="16">Diploma</option>
            </select>

            <input type="text" id="current_company" name="current_company" value="" />
            <input type="text" id="current_designation" name="current_designation" value="" />
            <select id="total_exp_year" name="total_exp_year">
                <option value="">Years</option>
                <option value="0">Fresher</option>
                <option value="1">1 Year</option>
                <option value="2">2 Years</option>
                <option value="3">3 Years</option>
                <option value="4">4 Years</option>
                <option value="5">5 Years</option>
                <option value="6">6 Years</option>
                <option value="7">7 Years</option>
                <option value="8">8 Years</option>
                <option value="9">9 Years</option>
                <option value="10">10 Years</option>
                <option value="11">11 Years</option>
                <option value="12">12 Years</option>
                <option value="13">13 Years</option>
                <option value="14">14 Years</option>
                <option value="15">15 Years</option>
            </select>
            <select id="total_exp_month" name="total_exp_month">
                <option value="">Months</option>
                <option value="0">0 Months</option>
                <option value="3">3 Months</option>
                <option value="6">6 Months</option>
                <option value="9">9 Months</option>
            </select>

            <input type="text" id="kt_tagify_2" name="skills" value="" />
            <textarea id="careersummary" name="careersummary"></textarea>
            <input type="url" id="linkedin" name="linkedin" value="" />
            <input type="url" id="github" name="github" value="" />
        </form>

        <script>
            window.tagifyskill = {
                value: [],
                removeAllTags: function() { this.value = []; },
                addTags: function(tags) {
                    tags.forEach(t => {
                        this.value.push({ value: t });
                    });
                }
            };
        </script>
        <script>
            ${parserBridgeCode}
        </script>
    </body>
    </html>
    `;

    await page.setContent(htmlContent, { waitUntil: 'networkidle' });

    // Pre-load the WASM module
    await page.evaluate(async () => {
        await window.JeroClientParser.load();
    });

    const testResults = [];

    for (let i = 0; i < files.length; i++) {
        const fileName = files[i];
        const filePath = path.join(uploadsDir, fileName);
        const buf = fs.readFileSync(filePath);
        console.log(`\n------------------------------------------------------------`);
        console.log(`[Resume ${i + 1}/${files.length}]: Parsing ${fileName} (${buf.length} bytes)...`);

        const res = await page.evaluate(async ({ base64, fname }) => {
            // Reset form for next test
            $('#createForm')[0].reset();
            window.tagifyskill.removeAllTags();

            const binaryString = atob(base64);
            const bytes = new Uint8Array(binaryString.length);
            for (let j = 0; j < binaryString.length; j++) {
                bytes[j] = binaryString.charCodeAt(j);
            }
            const file = new File([bytes], fname, { type: 'application/pdf' });

            const mod = await window.JeroClientParser.load();
            const parseFn = mod.parseResume || (mod.default && mod.default.parseResume);

            const start = performance.now();
            const parsed = await parseFn(file);
            const durationMs = Math.round(performance.now() - start);

            window.JeroCandidateFormBridge.populate(parsed, false);

            return {
                fileName: fname,
                durationMs,
                candidate: {
                    name: $('#full_name').val(),
                    email: $('#email').val(),
                    phone: $('#phone').val(),
                    countryCode: $('#primary_country_code').val(),
                    nationality: $('#nationality').val(),
                    company: $('#current_company').val(),
                    designation: $('#current_designation').val(),
                    qualification: $('#qualification').val(),
                    expYears: $('#total_exp_year').val(),
                    skillsCount: window.tagifyskill.value.length,
                    skillsSample: window.tagifyskill.value.slice(0, 8).map(s => s.value),
                    hasSummary: ($('#careersummary').val() || '').length > 0,
                    linkedin: $('#linkedin').val()
                }
            };
        }, { base64: buf.toString('base64'), fname: fileName });

        console.log(`⏱ Time: ${res.durationMs}ms`);
        console.log(`👤 Candidate: ${res.candidate.name || '(Not detected)'}`);
        console.log(`📧 Email: ${res.candidate.email || '(Not detected)'}`);
        console.log(`📞 Phone: +${res.candidate.countryCode || ''} ${res.candidate.phone || '(Not detected)'}`);
        console.log(`🏢 Company: ${res.candidate.company || '(Not detected)'}`);
        console.log(`💼 Designation: ${res.candidate.designation || '(Not detected)'}`);
        console.log(`🎓 Qualification: ID ${res.candidate.qualification || '(Not detected)'}`);
        console.log(`⌛ Experience: ${res.candidate.expYears ? res.candidate.expYears + ' yrs' : 'Fresher / Not detected'}`);
        console.log(`🛠 Skills (${res.candidate.skillsCount}): ${res.candidate.skillsSample.join(', ')}`);
        testResults.push(res);
    }

    await browser.close();

    console.log(`\n============================================================`);
    console.log(`🎉 ALL ${files.length} REAL RESUMES PARSED & HYDRATED SUCCESSFULLY IN CLIENT RAM!`);
}

testMultipleResumes().catch(err => {
    console.error('Test error:', err);
    process.exit(1);
});
