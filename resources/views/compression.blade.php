
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <input type="file" id="resumeInput" />
        <button id="uploadBtn">Upload</button>


<script src="https://cdn.jsdelivr.net/npm/jszip/dist/jszip.min.js"></script>
<script>
    document.getElementById('uploadBtn').addEventListener('click', async () => {
        const input = document.getElementById('resumeInput');
        const file = input.files[0];
        if (!file) {
            alert('Please select a file to upload!');
            return;
        }

        // Compress the file
        const zip = new JSZip();
        zip.file(file.name, file);
        const compressedContent = await zip.generateAsync({ type: 'blob' });
        var token = "{{ csrf_token() }}";
        const formData = new FormData();
        formData.append('file', compressedContent, file.name + '.zip');
        
        formData.append('_token', token); // Add the CSRF token as a fielde server
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        await fetch('http://localhost/MEECRUITUAT/index.php/compression-upload', {
            method: 'POST',
            body: formData,
        });
        alert('File uploaded successfully!');
    });
</script>
