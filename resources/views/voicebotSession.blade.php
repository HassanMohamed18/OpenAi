<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-Time Voice Bot</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <h1>Real-Time Voice Bot</h1>

    <button id="startButton">Start Talking</button>
    <p><strong>User:</strong> <span id="userSpeech"></span></p>
    <p><strong>AI:</strong> <span id="aiResponse"></span></p>

    <script>
        let recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
        recognition.lang = 'en-US';
        recognition.interimResults = false;
        recognition.continuous = false;

        document.getElementById('startButton').addEventListener('click', function () {
            recognition.start();
        });

        recognition.onresult = function (event) {
            let userText = event.results[0][0].transcript;
            document.getElementById('userSpeech').innerText = userText;
            sendToAI(userText);
        };

        function sendToAI(text) {
            axios.post("{{ route('chat.voice') }}", { user_message: text })
                .then(response => {
                    console.log(response)
                    let aiText = response.data.response_text || "No response received.";
                    document.getElementById('aiResponse').innerText = aiText;
                    speakText(aiText);
                })
                .catch(error => {
                    console.error("Error:", error);
                });
        }

        function speakText(text) {
            let speech = new SpeechSynthesisUtterance(text);
            speech.lang = 'en-US';
            speech.volume = 1;
            speech.rate = 1;
            speech.pitch = 1;
            speech.onend = function () {
                recognition.start(); // Restart listening after AI finishes speaking
            };
            window.speechSynthesis.speak(speech);
        }
    </script>
</body>
</html>
