<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

</head>

<body class="font-sans antialiased dark:bg-black dark:text-white/50">
    <input type="file" id="fileInput">
    <button onclick="uploadFile()">Upload</button>
    <div id="progress"></div>

    <script>
        async function uploadFile() {
            const fileInput = document.getElementById('fileInput');
            const file = fileInput.files[0];
            const chunkSize = 1024 * 1024; // 1 MB chunk size
            const totalChunks = Math.ceil(file.size / chunkSize);
            const uploadIdentifier = Date.now().toString(); // Unique identifier for the upload session

            // Initialize progress
            let uploadedChunks = 0;
            updateProgress(0);

            for (let i = 0; i < totalChunks; i++) {
                const start = i * chunkSize;
                const end = Math.min(file.size, start + chunkSize);
                const chunk = file.slice(start, end);

                const formData = new FormData();
                formData.append('file', chunk);
                formData.append('chunk', i + 1);
                formData.append('chunks', totalChunks);
                formData.append('upload_identifier', uploadIdentifier); // Include upload identifier

                await axios.post('/upload', formData);

                uploadedChunks++;
                const percentage = Math.floor((uploadedChunks / totalChunks) * 100);
                updateProgress(percentage);
            }

            // Notify server that all chunks have been uploaded
            await axios.post('/upload/complete', {
                filename: file.name,
                upload_identifier: uploadIdentifier
            });

            alert('File uploaded successfully!');
        }

        function updateProgress(percentage) {
            const progressDiv = document.getElementById('progress');
            progressDiv.innerText = `Upload Progress: ${percentage}%`;
        }
    </script>
</body>

</html>