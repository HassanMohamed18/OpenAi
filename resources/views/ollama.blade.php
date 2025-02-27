<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel + Ollama Chat</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <h2>Chat with Ollama</h2>

    <div id="chat-box">
        <p><strong>Bot:</strong> Hello! How can I assist you today?</p>
    </div>

    <form id="chat-form">
        <input type="text" id="message" placeholder="Type a message..." required>
        <button type="submit">Send</button>
    </form>

    <script>
        $(document).ready(function () {
            $("#chat-form").submit(function (e) {
                e.preventDefault();

                let userMessage = $("#message").val();
                $("#chat-box").append("<p><strong>You:</strong> " + userMessage + "</p>");
                $("#message").val("");

                $.ajax({
                    url: "/ollama/chat",
                    type: "POST",
                    data: { message: userMessage, _token: "{{ csrf_token() }}" },
                    success: function (response) {
                        $("#chat-box").append("<p><strong>Bot:</strong> " + response.response + "</p>");
                    },
                    error: function () {
                        $("#chat-box").append("<p><strong>Bot:</strong> Error connecting to Ollama.</p>");
                    }
                });
            });
        });
    </script>

</body>
</html>
