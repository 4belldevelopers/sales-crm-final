{{-- Dedicated Isolated Engine for Test Forward Resume (WASM + Direct Cloudflare R2 Pipeline) --}}
<script>
/**
 * AgyTestForwardResumeEngine
 *
 * Isolated Test Engine for Next-Gen 0%-Server-Load Forward Resume.
 * Features:
 *   - Client-side Rust WASM & vector PDF compilation in browser memory
 *   - Direct-to-R2 Presigned PUT Uploads (Zero server disk writes, Zero PHP streaming)
 *   - Multi-candidate async queue barrier & UUID tracking
 *   - Completely isolated namespace: window.AgyTestForwardResumeEngine
 */
(function (root) {
    'use strict';

    var TestEngine = {
        name: 'AgyTestForwardResumeEngine',
        version: '2.0-R2-Direct',

        /**
         * Compile candidate attachment using client-side WASM & vector libraries.
         */
        compileCandidate: async function (candidate, optId, companyData, jobSkills, onProgress) {
            var coreEngine = window.AgyForwardResumeEngine || window.AgyForwardResumeHelper;
            if (!coreEngine || typeof coreEngine.generateAttachmentForCandidate !== 'function') {
                throw new Error('Client WASM document engine core is not loaded.');
            }
            return await coreEngine.generateAttachmentForCandidate(candidate, optId, companyData, jobSkills, onProgress);
        },

        /**
         * Upload compiled PDF blob directly from browser memory to Cloudflare R2 / S3.
         * Server disk is never touched (0 bytes written to web server).
         */
        uploadToR2: async function (candidateId, pdfBlob, filename, intentUrl, csrfToken, onProgress) {
            if (typeof onProgress === 'function') {
                onProgress('Requesting Cloudflare R2 presigned upload authorization...');
            }

            var filePayload = [{
                candidate_id: candidateId,
                filename: filename,
                size: pdfBlob.size,
                mime: 'application/pdf'
            }];

            // Step 1: Request presigned PUT URL from lightweight intent endpoint
            var intentResponse = await $.ajax({
                type: 'POST',
                url: intentUrl,
                data: {
                    _token: csrfToken,
                    files: filePayload
                }
            });

            if (!intentResponse || intentResponse.status !== 'success' || !intentResponse.uploads || !intentResponse.uploads[candidateId]) {
                var errDetail = (intentResponse && intentResponse.message) ? intentResponse.message : 'Server returned empty upload intent';
                throw new Error('R2 Upload Intent failed: ' + errDetail);
            }

            var uploadDetails = intentResponse.uploads[candidateId];
            var putUrl = uploadDetails.upload_url;
            var attUuid = uploadDetails.attachment_id;

            if (typeof onProgress === 'function') {
                onProgress('Streaming vector PDF directly to Cloudflare R2...');
            }

            // Step 2: Directly upload PDF bytes to Cloudflare R2 via HTTP PUT (fetch)
            var putResult = await fetch(putUrl, {
                method: 'PUT',
                body: pdfBlob,
                headers: {
                    'Content-Type': 'application/pdf'
                }
            });

            if (!putResult.ok) {
                throw new Error('Direct-to-R2 upload failed with HTTP ' + putResult.status + ' (' + putResult.statusText + ')');
            }

            if (typeof onProgress === 'function') {
                onProgress('✓ Verified Cloudflare R2 object storage confirmation');
            }

            return {
                attachment_uuid: attUuid,
                candidate_id: candidateId,
                filename: filename,
                size: pdfBlob.size,
                uploaded_at: new Date().toISOString()
            };
        }
    };

    if (typeof window !== 'undefined') {
        window.AgyTestForwardResumeEngine = TestEngine;
    }
    if (typeof root !== 'undefined') {
        root.AgyTestForwardResumeEngine = TestEngine;
    }
})(typeof self !== 'undefined' ? self : this);
</script>
