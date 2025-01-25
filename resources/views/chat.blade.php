

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stream Chat with GPT-4</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Chat with GPT-4 (Streaming)</h1>

    <div class="mb-4">
        <h5>Chat History</h5>
        <ul class="list-group" id="messageHistory"></ul>
    </div>

    <form id="chatForm">
        <div class="mb-3">
            <label for="message" class="form-label">Your Message:</label>
            <textarea id="message" name="message" class="form-control" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send</button>
    </form>

    <div class="mt-3">
        <strong>AI Response:</strong>
        <div id="streamingResponse" class="border p-2"></div>
    </div>
</div>

<script>
    document.getElementById('chatForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const message = document.getElementById('message').value;

        // Reset UI
        const streamingResponse = document.getElementById('streamingResponse');
        streamingResponse.innerHTML = '';
        const messageHistory = document.getElementById('messageHistory');

        // Open an EventSource for server-sent events
        const eventSource = new EventSource(`{{ route('chat.store') }}?message=${encodeURIComponent(message)}`);

        eventSource.addEventListener('message',function(event){
            const data = JSON.parse(event.data);
            
            if (data.done) {
                // Close the stream when done
                eventSource.close();
                messageHistory.innerHTML += `<li class="list-group-item"><strong>You:</strong> ${message}<br><strong>GPT-4:</strong> ${streamingResponse.innerHTML}</li>`;
                document.getElementById('message').value = '';
            } else if (data.message) {
                // Append streamed chunks to the UI
                console.log(data.message)
                streamingResponse.innerHTML += data.message;
            }
        })
        
            
       

        eventSource.onerror = function () {
            console.error('An error occurred with the streaming.');
            eventSource.close();
        };
    });
</script>
</body>
</html>





