/**
 * JERO Talent Resume Parser Web Worker (JeroResumeParser)
 * Offloads client-side regex extraction and NLP entity parsing off the main UI thread.
 * Strict Page Isolation (Rule 17) - Quarantined to Service Company Add Talent Flow.
 * 100% Browser Execution - Zero backend parsing (Rules 3 & 4).
 */
const JeroResumeParser = {
    parseText: function(text) {
        const cleanText = (text || '')
            .replace(/[^\x00-\x7F]/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();

        // Client NLP entity extraction
        const emailMatch = cleanText.match(/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/);
        const phoneMatch = cleanText.match(/(?:\+?\d{1,3}[-.\s]?)?\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}|\b\d{10}\b/);
        const linkedinMatch = cleanText.match(/https?:\/\/(?:www\.)?linkedin\.com\/in\/[A-Za-z0-9_-]+/i);
        const githubMatch = cleanText.match(/https?:\/\/(?:www\.)?github\.com\/[A-Za-z0-9_-]+/i);
        const summaryMatch = cleanText.match(/(?:Summary|Professional Summary|Career Summary|Profile|Objective)\s*[:\-]?\s*(.{30,500}?)(?=[A-Z][A-Za-z\s]{2,20}:|Experience|Skills|Education|Projects|$)/i);

        // Name detection
        let name = '';
        const namePattern = cleanText.match(/^([A-Z][a-z]+(?:\s+[A-Z][a-z]+)+)/m) || cleanText.match(/Name\s*[:\-]?\s*([A-Za-z\s]{3,40})/i);
        if (namePattern) name = namePattern[1].trim();

        // Skill matching against common stack keywords
        const popularSkills = ['React', 'Node.js', 'Python', 'Java', 'PHP', 'Laravel', 'AWS', 'Docker', 'Kubernetes', 'SQL', 'TypeScript', 'JavaScript', 'C#', '.NET', 'Angular', 'Vue'];
        const detectedSkills = [];
        popularSkills.forEach(function(s) {
            const re = new RegExp('\\b' + s.replace('.', '\\.') + '\\b', 'i');
            if (re.test(cleanText)) detectedSkills.push(s);
        });

        return {
            name: name || '',
            email: emailMatch ? emailMatch[0].trim() : '',
            phone: phoneMatch ? phoneMatch[0].replace(/[^\d+]/g, '') : '',
            linkedin: linkedinMatch ? linkedinMatch[0] : '',
            github: githubMatch ? githubMatch[0] : '',
            summary: summaryMatch ? summaryMatch[1].trim() : '',
            skills: detectedSkills
        };
    }
};

self.onmessage = async function(event) {
    const { action, fileData, fileName, mimeType, runId, text } = event.data || {};

    if (action === 'PARSE_RESUME') {
        try {
            self.postMessage({ type: 'PARSER_PROGRESS', runId, progress: 30 });

            let resumeText = text || '';
            if (!resumeText && fileData) {
                try {
                    const decoder = new TextDecoder('utf-8', { fatal: false });
                    resumeText = decoder.decode(new Uint8Array(fileData));
                } catch (_) {}
            }

            const parsed = JeroResumeParser.parseText(resumeText);

            self.postMessage({
                type: 'PARSER_SUCCESS',
                runId,
                data: parsed
            });
        } catch (error) {
            self.postMessage({
                type: 'PARSER_ERROR',
                runId,
                error: error.message || 'Worker resume parsing failed'
            });
        }
    }
};
