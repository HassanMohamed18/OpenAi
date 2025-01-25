<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stream Form Input</title>
</head>
<body>
    <h1>Stream Your Input</h1>

    <!-- Form to send input -->
    <form id="messageForm" method="GET" >
        
        <label for="message">Enter a message:</label>
        <input type="text" name="message" id="message" required>
        <button type="submit">Submit</button>
    </form>

    <div id="messageContainer">
        <!-- Streamed messages will appear here -->
    </div>

    <script>
        const form = document.getElementById('messageForm');
        const messageContainer = document.getElementById('messageContainer');

        // Handle form submission using AJAX (without page refresh)
        form.addEventListener('submit', function(event) {
            // event.preventDefault(); // Prevent the default form submission

            // const formData = new FormData(form);

            // Open a stream connection to receive data
            const eventSource = new EventSource("{{ route('chat.streamForm') }}?" + 'hassan');

            eventSource.onmessage = function(event) {
                const data = JSON.parse(event.data);

                // Display the streamed message in the container
                if (data.message) {
                    messageContainer.innerHTML = `<p>${data.message}</p>`;
                }
            };

            // Handle stream errors
            eventSource.onerror = function() {
                console.error("An error occurred while receiving the stream.");
                eventSource.close();
            };
        });
    </script>
</body>
</html>
