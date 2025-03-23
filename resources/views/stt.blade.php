<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Speech to Text</title>
</head>
<body>
    <h2>🎤 Record and Transcribe Audio</h2>
    
    <button id="startRecording">Start Recording</button>
    <button id="stopRecording" disabled>Stop Recording</button>

    <p><strong>Transcription:</strong></p>
    <p id="transcription"></p>

    <script>
        let mediaRecorder;
        let audioChunks = [];

        document.getElementById('startRecording').addEventListener('click', async () => {
            let stream = await navigator.mediaDevices.getUserMedia({ audio: true });

            mediaRecorder = new MediaRecorder(stream, { mimeType: 'audio/webm' });  // Use webm for better compatibility
            mediaRecorder.ondataavailable = (event) => audioChunks.push(event.data);

            mediaRecorder.onstop = async () => {
                let audioBlob = new Blob(audioChunks, { type: 'audio/webm' });

                let formData = new FormData();
                formData.append('audio', audioBlob, 'audio.webm');  // Set correct filename

                try {
                    let response = await fetch('/api/transcribe', {
                        method: 'POST',
                        body: formData
                    });

                    let result = await response.json();
                    document.getElementById('transcription').innerText = result.transcription || 'Error transcribing audio';
                } catch (error) {
                    console.error("Error sending file:", error);
                    document.getElementById('transcription').innerText = "Failed to transcribe audio.";
                }
            };

            mediaRecorder.start();
            document.getElementById('startRecording').disabled = true;
            document.getElementById('stopRecording').disabled = false;
        });

        document.getElementById('stopRecording').addEventListener('click', () => {
            mediaRecorder.stop();
            document.getElementById('startRecording').disabled = false;
            document.getElementById('stopRecording').disabled = true;
        });
    </script>
</body>
</html>
