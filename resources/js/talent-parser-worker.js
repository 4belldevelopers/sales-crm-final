/**
 * JERO Talent Resume Parser Web Worker
 * Offloads WebAssembly, PDF.js, and text extraction off the main UI thread.
 * Strict Page Isolation (Rule 17) - Quarantined to Service Company Add Talent Flow.
 */
try {
    importScripts('https://parser.jero.works/jero-resume-parser-2tURGwDx.js');
} catch (e) {
    console.warn('JeroResumeParser worker failed to importScripts:', e);
}

self.onmessage = async function(event) {
    const { action, fileData, fileName, mimeType, runId } = event.data || {};

    if (action === 'PARSE_RESUME') {
        try {
            self.postMessage({ type: 'PARSER_PROGRESS', runId, progress: 20 });

            if (typeof self.JeroResumeParser !== 'undefined' && typeof self.JeroResumeParser.parse === 'function') {
                const parsedResult = await self.JeroResumeParser.parse(fileData, {
                    filename: fileName,
                    mimeType: mimeType
                });

                self.postMessage({
                    type: 'PARSER_SUCCESS',
                    runId,
                    data: parsedResult
                });
            } else {
                throw new Error('JeroResumeParser WASM bundle not loaded in Web Worker.');
            }
        } catch (error) {
            self.postMessage({
                type: 'PARSER_ERROR',
                runId,
                error: error.message || 'Worker resume parsing failed'
            });
        }
    }
};
