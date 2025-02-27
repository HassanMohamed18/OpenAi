<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Gemini Chat</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #252424;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            padding: 20px;
        }
        .chat-container {
            width: 100%;
            max-width: 500px;
            background: #1e1e1e;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .chat-box {
            height: 300px;
            overflow-y: auto;
            padding: 10px;
            border-bottom: 1px solid #444;
        }
        .message {
            margin-bottom: 10px;
            padding: 8px 12px;
            border-radius: 10px;
            max-width: 75%;
            word-wrap: break-word;
        }
        .user-message { background: #007bff; color: white; align-self: flex-end; }
        .ai-message { background: #444; color: white; align-self: flex-start; }
        .input-area {
            display: flex;
            margin-top: 10px;
        }
        input {
            flex: 1;
            padding: 10px;
            border-radius: 5px;
            border: none;
            outline: none;
        }
        button {
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
        }
    </style>
</head>
<body>

    <h1>Chat with Gemini AI</h1>

    <div class="chat-container">
        <div class="chat-box" id="chatBox"></div>
        <div class="input-area">
            <input type="text" id="userMessage" placeholder="Type a message..." />
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>

    <script>
        function sendMessage() {
            let userMessage = $('#userMessage').val().trim();
            if (!userMessage) return;

            $('#chatBox').append(`<div class="message user-message">${userMessage}</div>`);
            $('#userMessage').val('');

            $.ajax({
                url: "/gemini/chat",
                method: "POST",
                data: { message: userMessage, _token: "{{ csrf_token() }}" },
                success: function(response) {
                    $('#chatBox').append(`<div class="message ai-message">${response.reply}</div>`);
                    $('.chat-box').scrollTop($('.chat-box')[0].scrollHeight);
                },
                error: function() {
                    $('#chatBox').append(`<div class="message ai-message">Error communicating with AI.</div>`);
                }
            });
        }
    </script>

</body>
</html>
