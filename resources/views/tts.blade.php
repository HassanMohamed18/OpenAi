<form id="ttsForm">
    <input type="text" id="text" name="text" placeholder="Enter text" required>
    <button type="submit">Generate Speech</button>
</form>

<audio id="audioPlayer" controls></audio>

<script>
document.getElementById('ttsForm').addEventListener('submit', async function (event) {
    event.preventDefault();
    
    const text = document.getElementById('text').value;
    const audio = document.getElementById('audioPlayer');

    // Reset the audio element
    audio.src = '';
    
    // Create a Media Source to stream the audio
    const mediaSource = new MediaSource();
    audio.src = URL.createObjectURL(mediaSource);

    mediaSource.addEventListener('sourceopen', async () => {
        const sourceBuffer = mediaSource.addSourceBuffer('audio/mpeg');

        const response = await fetch('/api/tts-stream', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ text: text })
        });

        const reader = response.body.getReader();
        let isFirstChunk = true;

        async function processStream() {
            while (true) {
                const { done, value } = await reader.read();
                if (done) {
                    mediaSource.endOfStream();
                    break;
                }

                // Append data to SourceBuffer
                sourceBuffer.appendBuffer(value);

                if (isFirstChunk) {
                    isFirstChunk = false;
                    audio.play(); // Start playing as soon as we receive the first chunk
                }
            }
        }

        // Handle buffering updates
        sourceBuffer.addEventListener('updateend', processStream);

        // Start processing the audio stream
        processStream();
    });
});
</script>
