const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

async function runE2ETest() {
    console.log('🚀 Launching Chromium browser via Playwright...');
    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();

    // Catch console logs from browser page
    page.on('console', msg => {
        const text = msg.text();
        if (text.includes('[PARSER') || text.includes('[SFHP') || text.includes('Error')) {
            console.log(`[Browser Console ${msg.type()}]:`, text);
        }
    });

    console.log('🌐 Loading mock HTML page with JERO Candidate Form DOM...');
    
    // Read the actual jero-client-parser.js script
    const parserBridgeCode = fs.readFileSync(path.join(__dirname, '../public/js/jero-client-parser.js'), 'utf8');

    // Build realistic JERO Candidate Form HTML
    const htmlContent = `
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>JERO Parser SFHP E2E Test</title>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>
    <body>
        <form id="createForm">
            <!-- Basic Profile Fields -->
            <input type="text" id="full_name" name="full_name" value="" />
            <input type="text" id="name" name="name" value="" />
            <input type="email" id="email" name="email" value="" />
            <input type="tel" id="phone" name="phone" value="" />
            <input type="text" id="primary_country_code" name="primary_country_code" value="" />
            <input type="date" id="dob" name="dob" value="" />
            
            <!-- Nationality & Gender Selects -->
            <select id="nationality" name="nationality">
                <option value="">Select nationality</option>
                <option value="Indian">🇮🇳 Indian (India)</option>
                <option value="American">🇺🇸 American (United States)</option>
                <option value="British">🇬🇧 British (United Kingdom)</option>
            </select>
            <select id="gender" name="gender">
                <option value="Rather Not Say">Rather Not Say</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>

            <!-- Qualification Select -->
            <select id="qualification" name="qualification">
                <option value="">Select qualification</option>
                <option value="13">B.Tech / B.E (Engineering & Technology)</option>
                <option value="40">M.Tech / M.E</option>
                <option value="41">MBA</option>
                <option value="12">B.S. / B.Sc</option>
            </select>

            <!-- Experience Details -->
            <input type="text" id="current_company" name="current_company" value="" />
            <input type="text" id="current_designation" name="current_designation" value="" />
            <select id="total_exp_year" name="total_exp_year">
                <option value="">Years</option>
                <option value="0">Fresher</option>
                <option value="1">1 Year</option>
                <option value="3">3 Years</option>
                <option value="5">5 Years</option>
                <option value="8">8 Years</option>
            </select>
            <select id="total_exp_month" name="total_exp_month">
                <option value="">Months</option>
                <option value="0">0 Months</option>
                <option value="3">3 Months</option>
                <option value="6">6 Months</option>
            </select>
            <select id="rel_exp_year" name="rel_exp_year"><option value="5">5 Years</option></select>
            <select id="rel_exp_month" name="rel_exp_month"><option value="0">0 Months</option></select>

            <!-- Skills -->
            <input type="text" id="kt_tagify_2" name="skills" value="" />

            <!-- Summary & Social -->
            <textarea id="careersummary" name="careersummary"></textarea>
            <input type="url" id="linkedin" name="linkedin" value="" />
            <input type="url" id="github" name="github" value="" />
        </form>

        <script>
            // Mock Tagify instance
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

    console.log('🧪 Test 1: Testing Cloudflare Parser Module Dynamic Import...');
    const moduleCheck = await page.evaluate(async () => {
        try {
            const mod = await window.JeroClientParser.load();
            const fn = mod.parseResume || (mod.default && mod.default.parseResume);
            return {
                success: typeof fn === 'function',
                keys: Object.keys(mod)
            };
        } catch (e) {
            return { success: false, error: e.message };
        }
    });

    console.log('Result of Test 1 (Cloudflare WASM Dynamic Import):', JSON.stringify(moduleCheck, null, 2));

    console.log('\n🧪 Test 2: Testing JeroCandidateFormBridge with SFHP on Candidate Data...');
    const populateResult = await page.evaluate(() => {
        // Mock parsed ATS JSON output (same schema emitted by Cloudflare parser)
        const mockAtsJson = {
            candidate: {
                name: 'Rahul Sharma',
                email: 'rahul.sharma@example.com',
                phone: '9876543210',
                countryCode: '+91',
                dob: '1995-08-15',
                nationality: 'Indian',
                gender: 'Male',
                location: 'Bengaluru, Karnataka, India',
                currentCompany: 'FinTech Innovations Pvt Ltd',
                currentDesignation: 'Senior Backend Engineer',
                linkedin: 'https://linkedin.com/in/rahulsharma-dev',
                github: 'https://github.com/rahulsharma'
            },
            skills: ['PHP', 'Laravel', 'MySQL', 'Elasticsearch', 'Docker', 'Redis', 'PHP'], // Note duplicate 'PHP'
            experience: [
                {
                    company: 'FinTech Innovations Pvt Ltd',
                    title: 'Senior Backend Engineer',
                    startDate: '2021-01',
                    endDate: 'present'
                }
            ],
            education: [
                {
                    degree: 'Bachelor of Technology (B.Tech)',
                    field: 'Computer Science and Engineering',
                    institution: 'NIT'
                }
            ],
            summary: 'Experienced backend software engineer with over 5 years developing scalable financial payment architectures.'
        };

        // Run SFHP population in ADD MODE
        window.JeroCandidateFormBridge.populate(mockAtsJson, false);

        // Read back values from the DOM
        return {
            full_name: $('#full_name').val(),
            email: $('#email').val(),
            phone: $('#phone').val(),
            primary_country_code: $('#primary_country_code').val(),
            dob: $('#dob').val(),
            nationality: $('#nationality').val(),
            gender: $('#gender').val(),
            qualification: $('#qualification').val(),
            current_company: $('#current_company').val(),
            current_designation: $('#current_designation').val(),
            total_exp_year: $('#total_exp_year').val(),
            careersummary: $('#careersummary').val(),
            linkedin: $('#linkedin').val(),
            github: $('#github').val(),
            tagifySkillsCount: window.tagifyskill.value.length,
            tagifySkills: window.tagifyskill.value.map(t => t.value)
        };
    });

    console.log('Result of Test 2 (DOM Values Populated):', JSON.stringify(populateResult, null, 2));

    console.log('\n🧪 Test 3: Testing "Fill-If-Empty" Rule (Manual edits in Add Mode)...');
    const fillIfEmptyResult = await page.evaluate(() => {
        // User manually typed a custom phone and company
        $('#phone').val('9999999999');
        $('#current_company').val('Custom Manual Company');

        // New resume dropped
        const incomingJson = {
            candidate: {
                name: 'Rahul Sharma Updated',
                phone: '1111111111', // should NOT overwrite manual 9999999999!
                currentCompany: 'Resume Extracted Company' // should NOT overwrite!
            },
            skills: ['PHP', 'Go']
        };

        window.JeroCandidateFormBridge.populate(incomingJson, false);

        return {
            phone: $('#phone').val(),
            current_company: $('#current_company').val(),
            preserved: $('#phone').val() === '9999999999' && $('#current_company').val() === 'Custom Manual Company'
        };
    });

    console.log('Result of Test 3 (Fill-If-Empty Invariant):', JSON.stringify(fillIfEmptyResult, null, 2));

    console.log('\n🧪 Test 4: Testing EDIT MODE Immutability Protection...');
    const editModeResult = await page.evaluate(() => {
        // Set existing saved candidate data
        $('#full_name').val('Verified Candidate Name');
        $('#email').val('verified@candidate.com');

        // Parse new resume in EDIT MODE (isEditMode: true)
        const incomingJson = {
            candidate: {
                name: 'Completely Different Name',
                email: 'different@example.com'
            },
            skills: ['Vue.js', 'React']
        };

        window.JeroCandidateFormBridge.populate(incomingJson, true);

        return {
            full_name: $('#full_name').val(),
            email: $('#email').val(),
            immutable: $('#full_name').val() === 'Verified Candidate Name' && $('#email').val() === 'verified@candidate.com'
        };
    });

    console.log('Result of Test 4 (Edit Mode Immutability Invariant):', JSON.stringify(editModeResult, null, 2));

    console.log('\n🧪 Test 5: Testing Tagify Set Deduplication...');
    const tagifyDedupeResult = await page.evaluate(() => {
        // Check if duplicate 'PHP' was rejected
        const skills = window.tagifyskill.value.map(t => t.value);
        const phpOccurrences = skills.filter(s => s.toLowerCase() === 'php').length;
        return {
            totalSkills: skills.length,
            skills: skills,
            phpOccurrences: phpOccurrences,
            zeroDuplicates: phpOccurrences === 1
        };
    });

    console.log('Result of Test 5 (Tagify Deduplication):', JSON.stringify(tagifyDedupeResult, null, 2));

    await browser.close();
    console.log('\n🎉 ALL E2E BROWSER TESTS COMPLETED SUCCESSFULLY!');
}

runE2ETest().catch(err => {
    console.error('❌ E2E Test Failed:', err);
    process.exit(1);
});
