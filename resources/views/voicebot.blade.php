{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <title>Real-Time Voice Bot</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.1/echo.iife.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/7.0.3/pusher.min.js"></script>
</head>
<body>
    <h1>Laravel Real-Time Voice Bot</h1>
    <input type="file" id="audioFile">
    <button onclick="sendAudio()">Send</button>
    <p id="responseText"></p>
    <audio id="responseAudio" controls></audio>

    <script>
        async function sendAudio() {
            let fileInput = document.getElementById('audioFile');
            if (!fileInput.files.length) {
                alert("Please select an audio file.");
                return;
            }

            let formData = new FormData();
            formData.append('audio', fileInput.files[0]);

            let response = await fetch('/api/voice-chat', {
                method: 'POST',
                body: formData
            });

            let data = await response.json();
            document.getElementById('responseText').innerText = data.text;
            document.getElementById('responseAudio').src = data.audio;
        }

        // WebSocket Listening
        Pusher.logToConsole = true;
        const pusher = new Pusher("your_pusher_key", { cluster: "mt1" });
        const channel = pusher.subscribe("voice-bot-channel");
        
        channel.bind("VoiceBotResponse", (data) => {
            document.getElementById("responseText").innerText = data.responseText;
            document.getElementById("responseAudio").src = data.audioUrl;
        });
    </script>
</body>
</html> --}}


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
                let audioBlob = new Blob(audioChunks, { type: "audio/mp3" });
                let formData = new FormData();
                formData.append("audio", audioBlob, "voice.mp3");
                console.log(audioBlob)
                let response = await fetch("/api/voicebot", {
                    method: "POST",
                    body: formData
                });

                let data = await response.json();
                console.log(data.audio_url)
                document.getElementById("transcription").textContent = data.transcribed_text;
                document.getElementById("aiResponse").textContent = data.ai_response;
                document.getElementById("audioPlayer").src = data.audio_url;
                document.getElementById("audioPlayer").play();
                
                // Reset audioChunks
                audioChunks = [];
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
