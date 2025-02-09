<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-Time Voice Bot</title>
</head>
<body>
    <h1>Laravel Real-Time Voice Bot</h1>

    <button id="recordButton">🎤 Start Recording</button>
    <button id="stopButton" disabled>⏹ Stop Recording</button>

    <p><strong>Transcribed Text:</strong> <span id="transcription"></span></p>
    <p><strong>AI Response:</strong> <span id="aiResponse"></span></p>
    
    <audio id="audioPlayer" controls></audio>

    <script>
        let mediaRecorder;
        let audioChunks = [];

        document.getElementById("recordButton").addEventListener("click", async () => {
            let stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(stream);
            
            mediaRecorder.ondataavailable = event => {
                audioChunks.push(event.data);
            };

            mediaRecorder.onstop = async () => {
                let audioBlob = new Blob(audioChunks, { type: "audio/webm" });
                let formData = new FormData();
                formData.append("audio", audioBlob, "voice.webm");

                let response = await fetch("/api/voicebot", {
                    method: "POST",
                    body: formData
                });

                const reader = response.body.getReader();
                let aiResponse = "";
                while (true) {
                    const { done, value } = await reader.read();
                    if (done) break;
                    aiResponse += new TextDecoder().decode(value);
                    document.getElementById("aiResponse").textContent = aiResponse;
                }

                // Convert AI response to speech
                let ttsResponse = await fetch("/api/voicebot/speech", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ text: aiResponse })
                });

                // let ttsBlob = await ttsResponse.blob();
                // let audioUrl = URL.createObjectURL(ttsBlob);
                // console.log(audioUrl)
                let data = await ttsResponse.json();
                let audioUrl = data.audio_url
                console.log(data.audio_url)
                document.getElementById("audioPlayer").src = audioUrl;
                document.getElementById("audioPlayer").play()
                audioChunks = []; // Reset recording
            };

            mediaRecorder.start();
            document.getElementById("recordButton").disabled = true;
            document.getElementById("stopButton").disabled = false;
        });

        document.getElementById("stopButton").addEventListener("click", () => {
            mediaRecorder.stop();
            document.getElementById("recordButton").disabled = false;
            document.getElementById("stopButton").disabled = true;
        });
    </script>
</body>
</html>

{{-- 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-Time Voice Bot</title>
</head>
<body>
    <h1>Laravel Real-Time Voice Bot</h1>

    <button id="recordButton">🎤 Start Recording</button>
    <button id="stopButton" disabled>⏹ Stop Recording</button>

    <p><strong>Transcribed Text:</strong> <span id="transcription"></span></p>
    <p><strong>AI Response:</strong> <span id="aiResponse"></span></p>
    
    <audio id="audioPlayer" controls autoplay></audio> <!-- 🔹 AutoPlay Added -->

    <script>
        let mediaRecorder;
        let audioChunks = [];

        document.getElementById("recordButton").addEventListener("click", async () => {
            let stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(stream);

            mediaRecorder.ondataavailable = event => {
                audioChunks.push(event.data);
            };

            mediaRecorder.onstop = async () => {
                let audioBlob = new Blob(audioChunks, { type: "audio/webm" });
                let formData = new FormData();
                formData.append("audio", audioBlob, "voice.webm");

                let response = await fetch("/api/voicebot", {
                    method: "POST",
                    body: formData
                });

                let data = await response.json();

                document.getElementById("transcription").textContent = data.transcribed_text;
                document.getElementById("aiResponse").textContent = data.ai_response;

                // 🔊 Automatically Play AI-generated Speech
                playStreamedAudio(data.streaming_audio_url);

                audioChunks = []; // Reset recording
            };

            mediaRecorder.start();
            document.getElementById("recordButton").disabled = true;
            document.getElementById("stopButton").disabled = false;
        });

        document.getElementById("stopButton").addEventListener("click", () => {
            mediaRecorder.stop();
            document.getElementById("recordButton").disabled = false;
            document.getElementById("stopButton").disabled = true;
        });

        function playStreamedAudio(url) {
            let audioPlayer = document.getElementById("audioPlayer");
            audioPlayer.src = url;
            audioPlayer.play();
        }
    </script>
</body>
</html> --}}
