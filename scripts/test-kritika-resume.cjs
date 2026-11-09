const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

async function testKritikaResume() {
    console.log('🚀 Initializing Chromium to generate and parse Kritika Srivastava resume...');
    const browser = await chromium.launch({ headless: true });

    // Step 1: Render the resume as HTML and print to high-fidelity PDF
    const renderPage = await browser.newPage();
    const resumeHtml = `
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; font-size: 11pt; line-height: 1.5; color: #222; margin: 40px; }
            h1 { font-size: 22pt; margin-bottom: 4px; text-align: center; }
            .contact { text-align: center; font-size: 10pt; margin-bottom: 20px; color: #444; }
            h2 { font-size: 13pt; border-bottom: 1px solid #ccc; padding-bottom: 3px; margin-top: 18px; color: #0a3d62; }
            ul { margin-top: 5px; padding-left: 20px; }
            li { margin-bottom: 4px; }
            .exp-item { margin-bottom: 12px; }
            .job-title { font-weight: bold; }
            table { width: 100%; border-collapse: collapse; margin-top: 8px; }
            th, td { border: 1px solid #ddd; padding: 6px 10px; text-align: left; }
            th { background-color: #f2f2f2; }
        </style>
    </head>
    <body>
        <h1>Kritika Srivastava</h1>
        <div class="contact">
            Location: Noida - 201301 | Contact: +91 7992163061 | Email: srivastavakritika544@gmail.com
        </div>

        <h2>Career Objective</h2>
        <p>To establish myself as a skilled IT professional in a growth-oriented organization where I can utilize my technical support, development, coordination, and communication skills to contribute effectively towards organizational success and project execution.</p>

        <h2>Professional Summary</h2>
        <ul>
            <li>2.6 years of experience in Web Application Development and Technical Support using Microsoft .NET technologies.</li>
            <li>Hands-on experience in C#.NET, ASP.NET MVC Core, Web API, SQL Server, ADO.NET, and Entity Framework.</li>
            <li>Experience in Technical Support, issue resolution, client handling, and application troubleshooting.</li>
            <li>Worked on client coordination, task follow-ups, requirement understanding, and team coordination activities.</li>
            <li>Knowledge of SQL Server 2019 and database management.</li>
            <li>Strong communication, interpersonal, problem-solving, and multitasking skills.</li>
            <li>Quick learner, adaptable to challenges, and capable of working in team-oriented environments.</li>
        </ul>

        <h2>Professional Experience</h2>
        <div class="exp-item">
            <div class="job-title">Technical Support Engineer – Triotree Pvt. Ltd., Noida (8 Months)</div>
            <p>Provided technical support for web applications, resolved client issues, coordinated with internal teams for issue tracking and resolution, and assisted in client communication and follow-ups.</p>
        </div>
        <div class="exp-item">
            <div class="job-title">Software Developer & Technical Support – ITDOSE Info System Pvt. Ltd. (1 Year)</div>
            <p>Worked on application development and technical support activities including bug fixing, SQL handling, client coordination, troubleshooting, and team coordination for project-related tasks.</p>
        </div>
        <div class="exp-item">
            <div class="job-title">Software Developer – Atlantis Intelligence, Noida (1 Year)</div>
            <p>Worked on web application development using Microsoft .NET technologies, API integration, and database-related tasks.</p>
        </div>

        <h2>Technical Skills</h2>
        <table>
            <tr><th>Category</th><th>Skills</th></tr>
            <tr><td>Web Technologies</td><td>ASP.NET MVC Core</td></tr>
            <tr><td>Server Technologies</td><td>ADO.NET, Entity Framework, Web API</td></tr>
            <tr><td>Programming Languages</td><td>C#.NET, C++</td></tr>
            <tr><td>Database</td><td>SQL Server 2019</td></tr>
            <tr><td>Tools</td><td>Visual Studio, SQL Server Management Studio</td></tr>
            <tr><td>Scripting</td><td>jQuery</td></tr>
            <tr><td>Framework</td><td>Microsoft .NET 4.5</td></tr>
        </table>

        <h2>Education</h2>
        <ul>
            <li>BCA (2021) – ITM GIDA, Gorakhpur</li>
            <li>12th (2018) – Saraswati Vidya Mandir, Gorakhpur</li>
            <li>10th (2016) – Saraswati Vidya Mandir</li>
        </ul>

        <h2>Strengths</h2>
        <ul>
            <li>Strong communication and coordination skills</li>
            <li>Problem-solving and analytical mindset</li>
            <li>Quick learner and adaptable</li>
        </ul>

        <h2>Declaration</h2>
        <p>I hereby declare that the information provided above is true to the best of my knowledge and belief.</p>
        <p><strong>Kritika Srivastava</strong></p>
    </body>
    </html>
    `;

    await renderPage.setContent(resumeHtml, { waitUntil: 'networkidle' });
    const pdfPath = path.join(__dirname, 'kritika_srivastava_resume.pdf');
    await renderPage.pdf({ path: pdfPath, format: 'A4' });
    await renderPage.close();
    console.log(`📄 Generated test PDF: ${pdfPath} (${fs.statSync(pdfPath).size} bytes)`);

    // Step 2: Open JERO Candidate Form DOM page and parse with live Cloudflare WASM module
    const page = await browser.newPage();
    page.on('console', msg => {
        const text = msg.text();
        if (text.includes('[PARSER') || text.includes('[SFHP') || text.includes('Error')) {
            console.log(`[Browser Console]:`, text);
        }
    });

    const parserBridgeCode = fs.readFileSync(path.join(__dirname, '../public/js/jero-client-parser.js'), 'utf8');

    const formHtml = `
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Kritika Srivastava Parsing Test</title>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>
    <body>
        <form id="createForm">
            <!-- Basic Details -->
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
            </select>
            <select id="gender" name="gender">
                <option value="Rather Not Say">Rather Not Say</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>

            <!-- Qualification -->
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

            <!-- Employment Details -->
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
            </select>
            <select id="total_exp_month" name="total_exp_month">
                <option value="">Months</option>
                <option value="0">0 Months</option>
                <option value="1">1 Month</option>
                <option value="2">2 Months</option>
                <option value="3">3 Months</option>
                <option value="4">4 Months</option>
                <option value="5">5 Months</option>
                <option value="6">6 Months</option>
                <option value="7">7 Months</option>
                <option value="8">8 Months</option>
            </select>

            <!-- Relevant Experience -->
            <select id="rel_exp_year" name="rel_exp_year">
                <option value="">Years</option>
                <option value="0">Fresher</option>
                <option value="1">1 Year</option>
                <option value="2">2 Years</option>
                <option value="3">3 Years</option>
            </select>
            <select id="rel_exp_month" name="rel_exp_month">
                <option value="">Months</option>
                <option value="0">0 Months</option>
                <option value="6">6 Months</option>
            </select>

            <!-- Skills (Tagify Mock) -->
            <input type="text" id="kt_tagify_2" name="skills" value="" />
            
            <!-- Summary & Social Links -->
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

    await page.setContent(formHtml, { waitUntil: 'networkidle' });

    console.log('⚡ Loading Cloudflare WASM Parser module...');
    const pdfBytes = fs.readFileSync(pdfPath);

    const parseResult = await page.evaluate(async (base64) => {
        const binaryString = atob(base64);
        const bytes = new Uint8Array(binaryString.length);
        for (let i = 0; i < binaryString.length; i++) {
            bytes[i] = binaryString.charCodeAt(i);
        }
        const file = new File([bytes], 'kritika_srivastava_resume.pdf', { type: 'application/pdf' });

        const mod = await window.JeroClientParser.load();
        const parseFn = mod.parseResume || (mod.default && mod.default.parseResume);

        const startTime = performance.now();
        const parsed = await parseFn(file);
        const durationMs = Math.round(performance.now() - startTime);

        // Populate Form with SFHP
        window.JeroCandidateFormBridge.populate(parsed, false);

        return {
            rawParsed: parsed,
            durationMs: durationMs,
            hydratedForm: {
                full_name: $('#full_name').val(),
                name: $('#name').val(),
                email: $('#email').val(),
                phone: $('#phone').val(),
                primary_country_code: $('#primary_country_code').val(),
                dob: $('#dob').val(),
                nationality: $('#nationality').val(),
                gender: $('#gender').val(),
                qualification: $('#qualification').val(),
                qualification_text: $('#qualification option:selected').text(),
                current_company: $('#current_company').val(),
                current_designation: $('#current_designation').val(),
                total_exp_year: $('#total_exp_year').val(),
                total_exp_month: $('#total_exp_month').val(),
                careersummary: $('#careersummary').val(),
                linkedin: $('#linkedin').val(),
                github: $('#github').val(),
                tagifySkills: window.tagifyskill.value.map(t => t.value)
            }
        };
    }, pdfBytes.toString('base64'));

    await browser.close();

    console.log('\n============================================================');
    console.log('📊 KRITIKA SRIVASTAVA RESUME PARSER AUDIT OUTPUT:');
    console.log('============================================================');
    console.log(`⏱ Total Processing Time: ${parseResult.durationMs}ms`);
    console.log(`\n1. RAW EXTRACTED JSON KEYS:`, Object.keys(parseResult.rawParsed));
    console.log(`2. RAW CANDIDATE SUB-OBJECT:`, JSON.stringify(parseResult.rawParsed.candidate || {}, null, 2));
    console.log(`3. RAW EXPERIENCE ENTRIES:`, JSON.stringify(parseResult.rawParsed.experience || [], null, 2));
    console.log(`4. RAW EDUCATION ENTRIES:`, JSON.stringify(parseResult.rawParsed.education || [], null, 2));
    console.log(`5. RAW SKILLS EXTRACTED:`, JSON.stringify(parseResult.rawParsed.skills || [], null, 2));
    console.log(`\n============================================================`);
    console.log('📝 HYDRATED CANDIDATE FORM FIELDS IN DOM:');
    console.log('============================================================');
    console.log(JSON.stringify(parseResult.hydratedForm, null, 2));

    // Save test output to JSON
    fs.writeFileSync(
        path.join(__dirname, 'kritika_parse_result.json'),
        JSON.stringify(parseResult, null, 2)
    );
}

testKritikaResume().catch(err => {
    console.error('Test error:', err);
    process.exit(1);
});
