const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

async function testUserPdfFormHydration() {
    const pdfPath = 'C:/Users/VIBHANSHU JAISWAL.000/.gemini/antigravity/brain/438b6f7f-08c9-4fa5-88dc-01a9f688dda0/.user_uploaded/media_1794224935258_82d81821.pdf';
    const pdfBytes = fs.readFileSync(pdfPath);
    console.log(`Loaded User PDF: ${pdfPath} (${pdfBytes.length} bytes)`);

    const parserBridgeCode = fs.readFileSync(path.join(__dirname, '../resources/views/service_company/job-applications/jero-client-parser.blade.php'), 'utf8')
        .replace(/<script>/gi, '')
        .replace(/<\/script>/gi, '');

    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();

    page.on('console', msg => console.log('[Browser Console]:', msg.text()));

    const formHtml = `
    <!DOCTYPE html>
    <html>
    <head>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>
    <body>
        <input type="text" id="full_name" name="full_name" value="">
        <input type="text" id="name" name="name" value="">
        <input type="email" id="email" name="email" value="">
        <input type="text" id="phone" name="phone" value="">
        <input type="text" id="primary_country_code" name="primary_country_code" value="">
        <input type="date" id="dob" name="dob" value="">
        
        <select id="nationality" name="nationality">
            <option value="">Select nationality</option>
            <option value="Indian">Indian (India)</option>
            <option value="American">American (United States)</option>
        </select>
        
        <input type="text" id="current_company" name="current_company" value="" placeholder="Enter current company">
        <input type="text" id="current_designation" name="current_designation" value="Lead Full Stack PHP Engineer (V7)" placeholder="e.g. Software Engineer">
        
        <select id="total_exp_year" name="total_exp_year">
            <option value="">Years</option>
            <option value="0" selected>Fresher</option>
            <option value="1">1 Year</option>
            <option value="2">2 Years</option>
            <option value="3">3 Years</option>
        </select>
        <select id="total_exp_month" name="total_exp_month">
            <option value="">Select Month</option>
            <option value="0">0 Month</option>
            <option value="6">6 Months</option>
            <option value="7">7 Months</option>
        </select>
        <select id="rel_exp_year" name="rel_exp_year">
            <option value="">Years</option>
            <option value="0" selected>Fresher</option>
            <option value="1">1 Year</option>
            <option value="2">2 Years</option>
        </select>
        <select id="rel_exp_month" name="rel_exp_month">
            <option value="">Select Month</option>
            <option value="0">0 Month</option>
            <option value="7">7 Months</option>
        </select>

        <select name="qualification" id="qualification">
            <option value="">Select qualification</option>
            <option value="13" data-aliases="btech,b.tech,be,computer science engineering">B.Tech / B.E (Engineering & Technology)</option>
            <option value="12" data-aliases="bs,b.s.,bsc,b.sc,bachelor of science">B.S. / B.Sc (Science, IT & Computing)</option>
            <option value="16" data-aliases="bca,b.c.a.,bachelor of computer applications">BCA (Computer Applications / Software)</option>
            <option value="2" data-aliases="12th,12th standard,hsc,intermediate,high school">12th Standard / High School Diploma</option>
            <option value="1" data-aliases="10th,10th standard,ssc,matric">10th Standard / Matriculation</option>
        </select>
        <div id="jero-cs-qualification" class="jero-custom-select">
            <button type="button" class="jero-cs-trigger">
                <span class="jero-cs-value jero-cs-placeholder">Select qualification</span>
                <span class="jero-cs-badge" style="display:none;"></span>
            </button>
            <div class="jero-cs-dropdown">
                <div class="jero-cs-item" data-val="13"><span class="jero-cs-item-text">B.Tech / B.E (Engineering & Technology)</span></div>
                <div class="jero-cs-item" data-val="16"><span class="jero-cs-item-text">BCA (Computer Applications / Software)</span></div>
                <div class="jero-cs-item" data-val="2"><span class="jero-cs-item-text">12th Standard / High School Diploma</span></div>
            </div>
        </div>

        <div class="textarea-wrapper">
            <textarea id="careersummary" name="careersummary" rows="10"></textarea>
            <span class="cc">0 / 100</span>
        </div>

        <input id="kt_tagify_2" name="kt_tagify_2" value="">
        <div id="candidatePrimarySkillChips"></div>
    </body>
    </html>
    `;

    await page.setContent(formHtml);

    // Mock window.addCandidatePrimarySkillsBatch
    await page.evaluate(() => {
        window.addedBatchSkills = [];
        window.addCandidatePrimarySkillsBatch = function(skills) {
            window.addedBatchSkills = skills;
            const container = document.getElementById('candidatePrimarySkillChips');
            container.innerHTML = '';
            skills.forEach(s => {
                const chip = document.createElement('span');
                chip.className = 'candidate-primary-skill-chip';
                chip.textContent = s;
                container.appendChild(chip);
            });
            $('#kt_tagify_2').val(skills.join(', '));
        };
    });

    await page.evaluate(parserBridgeCode);

    const testOutput = await page.evaluate(async (base64) => {
        const bin = atob(base64);
        const len = bin.length;
        const bytes = new Uint8Array(len);
        for (let i = 0; i < len; i++) {
            bytes[i] = bin.charCodeAt(i);
        }
        const file = new File([bytes], 'JEROWORKS_Kritika_Srivastava.pdf', { type: 'application/pdf' });

        const startTime = performance.now();
        await window.JeroClientParser.parseAndPopulate(file, { isEditMode: false });
        const duration = Math.round(performance.now() - startTime);

        return {
            duration,
            full_name: $('#full_name').val(),
            email: $('#email').val(),
            phone: $('#phone').val(),
            current_company: $('#current_company').val(),
            current_designation: $('#current_designation').val(),
            total_exp_year: $('#total_exp_year').val(),
            total_exp_month: $('#total_exp_month').val(),
            qualification: $('#qualification').val(),
            qualification_combobox_text: $('#jero-cs-qualification .jero-cs-value').text(),
            careersummary_text: $('#careersummary').val(),
            careersummary_counter: $('.cc').text(),
            addedBatchSkills: window.addedBatchSkills,
            chipCount: $('#candidatePrimarySkillChips .candidate-primary-skill-chip').length
        };
    }, pdfBytes.toString('base64'));

    await browser.close();

    console.log('=== TEST RESULT FOR USER EXACT PDF ===');
    console.log(JSON.stringify(testOutput, null, 2));
}

testUserPdfFormHydration().catch(err => {
    console.error('Error:', err);
    process.exit(1);
});
