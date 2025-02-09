<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenAI Speech Streaming</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center h-screen">
    <div class="bg-white shadow-md rounded-lg p-6 w-96">
        <h2 class="text-lg font-semibold mb-4">Text-to-Speech</h2>
        
        <label class="block mb-2 font-medium">Enter Text:</label>
        <textarea id="textInput" class="w-full border p-2 rounded-lg" rows="3" placeholder="Type your text..."></textarea>

        <label class="block mt-4 font-medium">Select Voice:</label>
        <select id="voiceSelect" class="w-full border p-2 rounded-lg">
            <option value="alloy">Alloy</option>
            <option value="echo">Echo</option>
            <option value="fable">Fable</option>
            <option value="onyx">Onyx</option>
            <option value="nova">Nova</option>
            <option value="shimmer">Shimmer</option>
        </select>

        <button id="playBtn" class="bg-blue-600 text-white px-4 py-2 rounded-lg mt-4 w-full hover:bg-blue-700">
            Play Speech
        </button>

        <audio id="audioPlayer" class="mt-4 w-full" controls></audio>
    </div>

    <script>
        document.getElementById('playBtn').addEventListener('click', function() {
            const text = document.getElementById('textInput').value.trim();
            const voice = document.getElementById('voiceSelect').value;

            if (!text) {
                alert("Please enter text to convert to speech.");
                return;
            }

            fetch('/api/speech/stream', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ text, voice })
            })
            .then(response => response.blob())
            .then(blob => {
                const audioURL = URL.createObjectURL(blob);
                const audioPlayer = document.getElementById('audioPlayer');
                audioPlayer.src = audioURL;
                audioPlayer.play();
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>
