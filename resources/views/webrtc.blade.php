<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebRTC & OpenAI Realtime API</title>
</head>
<body>
    <h1>WebRTC & OpenAI Realtime API</h1>

    <script>
        async function init() {
            try {
                // Get an ephemeral key from the Laravel backend
                const tokenResponse = await fetch("/api/session");
                const data = await tokenResponse.json();
                const EPHEMERAL_KEY = data.client_secret.value;

                // Create a peer connection
                const pc = new RTCPeerConnection();

                // Set up to play remote audio from the model
                const audioEl = document.createElement("audio");
                audioEl.autoplay = true;
                pc.ontrack = e => audioEl.srcObject = e.streams[0];

                // Add local audio track for microphone input
                const ms = await navigator.mediaDevices.getUserMedia({
                    audio: true
                });
                pc.addTrack(ms.getTracks()[0]);

                // Set up data channel for sending and receiving events
                const dc = pc.createDataChannel("oai-events");

                // Wait until the data channel is open before sending messages
                dc.addEventListener("open", () => {
                    console.log("✅ Data channel is open!");

                    // Send the "conversation.item.create" event when the data channel opens
                    const event = {
                        type: "conversation.item.create",
                        item: {
                            type: "message",
                            role: "user",
                            content: [{
                                type: "input_text",
                                text: "What Prince album sold the most copies?"
                            }]
                        }
                    };

                    dc.send(JSON.stringify(event));
                    console.log("📤 Sent event:", event);
                });

                // Listen for incoming messages
                dc.addEventListener("message", (e) => {
                    const receivedEvent = JSON.parse(e.data);
                    console.log("📩 Received event:", receivedEvent);
                });

                // Start the session using SDP (Session Description Protocol)
                const offer = await pc.createOffer();
                await pc.setLocalDescription(offer);

                const baseUrl = "https://api.openai.com/v1/realtime";
                const model = "gpt-4o-realtime-preview-2024-12-17";
                const sdpResponse = await fetch(`${baseUrl}?model=${model}`, {
                    method: "POST",
                    body: offer.sdp,
                    headers: {
                        Authorization: `Bearer ${EPHEMERAL_KEY}`,
                        "Content-Type": "application/sdp"
                    },
                });

                const answer = {
                    type: "answer",
                    sdp: await sdpResponse.text()
                };
                await pc.setRemoteDescription(answer);

            } catch (error) {
                console.error("❌ Error initializing WebRTC session:", error);
            }
        }

        // Start WebRTC session
        init();
    </script>
</body>
</html> -->

<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebRTC & OpenAI Realtime API</title>
    <style>
        #response {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            min-height: 50px;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <h1>WebRTC & OpenAI Realtime API</h1>
    <h3>AI Response:</h3>
    <div id="response">Waiting for AI response...</div>

    <script>
        async function init() {
            try {
                // 🔹 Get an ephemeral key from Laravel backend
                const tokenResponse = await fetch("/api/session");
                const data = await tokenResponse.json();
                const EPHEMERAL_KEY = data.client_secret.value;

                // 🔹 Create a peer connection
                const pc = new RTCPeerConnection();

                // 🔹 Set up to play remote audio from AI
                const audioEl = document.createElement("audio");
                audioEl.autoplay = true;
                pc.ontrack = e => audioEl.srcObject = e.streams[0];

                // 🔹 Add microphone input
                const ms = await navigator.mediaDevices.getUserMedia({
                    audio: true
                });
                pc.addTrack(ms.getTracks()[0]);

                // 🔹 Set up data channel for sending & receiving events
                const dc = pc.createDataChannel("oai-events");

                // 🔹 Wait for data channel to open
                dc.addEventListener("open", () => {
                    console.log("✅ Data channel is open!");

                    // 📤 Send user message (conversation.item.create)
                    const inputEvent = {
                        type: "conversation.item.create",
                        item: {
                            type: "message",
                            role: "user",
                            content: [{
                                type: "input_text",
                                text: "What Prince album sold the most copies?"
                            }]
                        }
                    };

                    dc.send(JSON.stringify(inputEvent));
                    console.log("📤 Sent input event:", inputEvent);

                    // 📤 Send response creation request (response.create)
                    const responseCreateEvent = {
                        type: "response.create",
                        response: {
                            modalities: ["text"],
                            instructions: "Answer the user's question clearly."
                        }
                    };

                    dc.send(JSON.stringify(responseCreateEvent));
                    console.log("📤 Sent response.create event:", responseCreateEvent);
                });

                // 📩 Listen for AI response
                dc.addEventListener("message", (e) => {
                    try {
                        const receivedEvent = JSON.parse(e.data);
                        console.log("📩 Received event:", receivedEvent);

                        const responseContainer = document.getElementById("response");

                        // 🎯 Handle AI response (final output)
                        if (receivedEvent.type === "response.done") {
                            const aiMessage = receivedEvent.response.output[0].content[0].text;

                            // Append AI response dynamically
                            responseContainer.innerHTML += `<p><strong>AI:</strong> ${aiMessage}</p>`;
                        }

                    } catch (error) {
                        console.error("❌ Error processing AI response:", error);
                    }
                });

                // 🔹 Start the session using SDP
                const offer = await pc.createOffer();
                await pc.setLocalDescription(offer);

                const baseUrl = "https://api.openai.com/v1/realtime";
                const model = "gpt-4o-realtime-preview-2024-12-17";
                const sdpResponse = await fetch(`${baseUrl}?model=${model}`, {
                    method: "POST",
                    body: offer.sdp,
                    headers: {
                        Authorization: `Bearer ${EPHEMERAL_KEY}`,
                        "Content-Type": "application/sdp"
                    },
                });

                const answer = {
                    type: "answer",
                    sdp: await sdpResponse.text()
                };
                await pc.setRemoteDescription(answer);

            } catch (error) {
                console.error("❌ Error initializing WebRTC session:", error);
            }
        }

        // Start WebRTC session
        init();
    </script>


</body>
</html> -->

{{-- 
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chat - WebRTC Voice & Text</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #212121;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            padding: 20px;
        }

        .chat-container {
            width: 100%;
            max-width: 600px;
            background: #2c2c2c;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .chat-box {
            height: 400px;
            overflow-y: auto;
            padding: 15px;
            display: flex;
            flex-direction: column;
        }

        .message-container {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .message {
            max-width: 75%;
            padding: 10px 15px;
            border-radius: 10px;
            word-wrap: break-word;
            animation: fadeIn 0.3s ease-in-out;
        }

        .user-message {
            background-color: #007bff;
            color: white;
            text-align: right;
            align-self: flex-end;
        }

        .ai-message {
            background-color: #444;
            color: white;
            align-self: flex-start;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin: 5px;
        }

        .user-container {
            justify-content: flex-end;
        }

        .user-container .message {
            order: 1;
        }

        .user-container .avatar {
            order: 2;
            margin-left: 10px;
        }

        .ai-container {
            justify-content: flex-start;
        }

        .ai-container .avatar {
            margin-right: 10px;
        }

        .input-area {
            display: flex;
            align-items: center;
            padding: 10px;
            background: #1e1e1e;
            border-top: 1px solid #444;
        }

        input {
            flex: 1;
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            outline: none;
            background: #333;
            color: white;
        }

        button {
            margin-left: 10px;
            padding: 10px 12px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .send-btn {
            background: #007bff;
            color: white;
        }

        .mic-btn {
            background: #28a745;
            color: white;
        }

        button:hover {
            opacity: 0.8;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <h1>AI Chat Assistant</h1>

    <div class="chat-container">
        <div class="chat-box" id="chatBox">
            <div class="message-container ai-container">
                <img src="ai-avatar.png" alt="AI Avatar" class="avatar">
                <div class="message ai-message">Hello! How can I assist you today? 😊</div>
            </div>
        </div>

        <div class="input-area">
            <input type="text" id="userMessage" placeholder="Type a message..." />
            <button class="send-btn" onclick="sendMessage()">Send</button>
            <button class="mic-btn" onclick="startVoiceRecognition()">🎤</button>
        </div>
    </div>

    <script>
        let dc;
        let recognition;

        async function init() {
            try {
                const tokenResponse = await fetch("/api/session");
                const data = await tokenResponse.json();
                const EPHEMERAL_KEY = data.client_secret.value;

                const pc = new RTCPeerConnection();
                const audioEl = document.createElement("audio");
                audioEl.autoplay = true;
                pc.ontrack = e => audioEl.srcObject = e.streams[0];

                const ms = await navigator.mediaDevices.getUserMedia({
                    audio: true
                });
                pc.addTrack(ms.getTracks()[0]);

                dc = pc.createDataChannel("oai-events");

                dc.addEventListener("open", () => {
                    console.log("✅ Data channel is open!");
                });

                dc.addEventListener("message", (e) => {
                    try {
                        const receivedEvent = JSON.parse(e.data);
                        console.log("📩 Received event:", receivedEvent);

                        if (receivedEvent.type === "response.done") {
                            const responseOutput = receivedEvent.response?.output;

                            if (responseOutput && responseOutput.length > 0) {
                                const content = responseOutput[0]?.content?.[0];

                                if (content) {
                                    const aiMessage = content.type === "text" ? content.text : content
                                        .transcript;
                                    addMessage("AI", aiMessage);
                                } else {
                                    console.warn("⚠️ No content in response output:", responseOutput);
                                }
                            } else {
                                console.warn("⚠️ No valid response output:", receivedEvent.response);
                            }
                        }
                    } catch (error) {
                        console.error("❌ Error processing AI response:", error);
                    }
                });

                const offer = await pc.createOffer();
                await pc.setLocalDescription(offer);

                const baseUrl = "https://api.openai.com/v1/realtime";
                const model = "gpt-4o-realtime-preview-2024-12-17";
                const sdpResponse = await fetch(`${baseUrl}?model=${model}`, {
                    method: "POST",
                    body: offer.sdp,
                    headers: {
                        Authorization: `Bearer ${EPHEMERAL_KEY}`,
                        "Content-Type": "application/sdp"
                    },
                });

                const answer = {
                    type: "answer",
                    sdp: await sdpResponse.text()
                };
                await pc.setRemoteDescription(answer);

            } catch (error) {
                console.error("❌ Error initializing WebRTC session:", error);
            }
        }

        function sendMessage() {
            const userMessage = document.getElementById("userMessage").value.trim();
            if (!userMessage) return;

            addMessage("User", userMessage);

            if (!dc || dc.readyState !== "open") {
                alert("Connection not ready. Please wait...");
                return;
            }

            const inputEvent = {
                type: "conversation.item.create",
                item: {
                    type: "message",
                    role: "user",
                    content: [{
                        type: "input_text",
                        text: userMessage
                    }]
                }
            };

            dc.send(JSON.stringify(inputEvent));

            const responseCreateEvent = {
                type: "response.create",
                response: {
                    modalities: ["text"],
                    instructions: "Answer the user clearly."
                }
            };

            dc.send(JSON.stringify(responseCreateEvent));
            document.getElementById("userMessage").value = "";
        }

        function startVoiceRecognition() {
            if (!('webkitSpeechRecognition' in window)) {
                alert("Speech recognition is not supported in this browser.");
                return;
            }

            recognition = new webkitSpeechRecognition();
            recognition.continuous = false;
            recognition.interimResults = false;
            recognition.lang = "ar-SA";

            recognition.start();

            recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                document.getElementById("userMessage").value = transcript;
                sendMessage();
            };
        }

        function addMessage(sender, text) {
            const chatBox = document.getElementById("chatBox");
            const container = document.createElement("div");
            container.className = sender === "User" ? "message-container user-container" : "message-container ai-container";

            const avatar = document.createElement("img");
            avatar.src = sender === "User" ? "/images/hassan.jpg" : "/images/ai_avatar.png";
            avatar.className = "avatar";

            const messageDiv = document.createElement("div");
            messageDiv.className = sender === "User" ? "message user-message" : "message ai-message";
            messageDiv.innerHTML = text;

            container.appendChild(avatar);
            container.appendChild(messageDiv);
            chatBox.appendChild(container);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        init();
    </script>
</body>

</html> --}}


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chat - WebRTC Voice & Text</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #212121;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            padding: 20px;
        }

        .chat-container {
            width: 100%;
            max-width: 600px;
            background: #2c2c2c;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 85vh;
        }

        .chat-box {
            flex-grow: 1;
            overflow-y: auto;
            padding: 15px;
            display: flex;
            flex-direction: column;
            max-height: 70vh;
        }

        .message-container {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .message {
            max-width: 75%;
            padding: 10px 15px;
            border-radius: 10px;
            word-wrap: break-word;
            animation: fadeIn 0.3s ease-in-out;
            font-size: 0.9rem;
        }

        .user-message {
            background-color: #007bff;
            color: white;
            text-align: right;
            align-self: flex-end;
        }

        .ai-message {
            background-color: #444;
            color: white;
            align-self: flex-start;
        }

        .avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            margin: 5px;
        }

        .user-container {
            justify-content: flex-end;
        }

        .user-container .message {
            order: 1;
        }

        .user-container .avatar {
            order: 2;
            margin-left: 10px;
        }

        .ai-container {
            justify-content: flex-start;
        }

        .ai-container .avatar {
            margin-right: 10px;
        }

        .input-area {
            display: flex;
            align-items: center;
            padding: 10px;
            background: #1e1e1e;
            border-top: 1px solid #444;
        }

        input {
            flex: 1;
            padding: 10px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            outline: none;
            background: #333;
            color: white;
        }

        button {
            margin-left: 8px;
            padding: 8px 10px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .send-btn {
            background: #007bff;
            color: white;
        }

        .mic-btn {
            background: #28a745;
            color: white;
        }

        button:hover {
            opacity: 0.8;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            body {
                padding: 10px;
                height: 100vh;
            }

            .chat-container {
                width: 100%;
                height: 90vh;
            }

            .chat-box {
                max-height: 75vh;
            }

            input {
                font-size: 12px;
                padding: 8px;
            }

            button {
                font-size: 12px;
                padding: 6px 8px;
            }

            .avatar {
                width: 30px;
                height: 30px;
            }

            .message {
                font-size: 0.8rem;
            }
        }
    </style>
</head>

<body>

    <h1>AI Chat Assistant</h1>

    <div class="chat-container">
        <div class="chat-box" id="chatBox">
            <div class="message-container ai-container">
                <img src="ai-avatar.png" alt="AI Avatar" class="avatar">
                <div class="message ai-message">Hello! How can I assist you today? 😊</div>
            </div>
        </div>

        <div class="input-area">
            <input type="text" id="userMessage" placeholder="Type a message..." />
            <button class="send-btn" onclick="sendMessage()">Send</button>
            {{-- <button class="mic-btn" onclick="startVoiceRecognition()">🎤</button> --}}
        </div>
    </div>

    <script>
        let dc;
        let recognition;

        async function init() {
            try {
                const tokenResponse = await fetch("/api/session");
                const data = await tokenResponse.json();
                const EPHEMERAL_KEY = data.client_secret.value;

                const pc = new RTCPeerConnection();
                const audioEl = document.createElement("audio");
                audioEl.autoplay = true;
                pc.ontrack = e => audioEl.srcObject = e.streams[0];

                const ms = await navigator.mediaDevices.getUserMedia({
                    audio: true
                });
                pc.addTrack(ms.getTracks()[0]);

                dc = pc.createDataChannel("oai-events");

                dc.addEventListener("open", () => {
                    console.log("✅ Data channel is open!");
                });

                dc.addEventListener("message", (e) => {
                    try {
                        const receivedEvent = JSON.parse(e.data);
                        console.log("📩 Received event:", receivedEvent);

                        if (receivedEvent.type === "response.done") {
                            const responseOutput = receivedEvent.response?.output;

                            if (responseOutput && responseOutput.length > 0) {
                                const content = responseOutput[0]?.content?.[0];

                                if (content) {
                                    const aiMessage = content.type === "text" ? content.text : content
                                        .transcript;
                                    addMessage("AI", aiMessage);
                                } else {
                                    console.warn("⚠️ No content in response output:", responseOutput);
                                }
                            } else {
                                console.warn("⚠️ No valid response output:", receivedEvent.response);
                            }
                        }
                    } catch (error) {
                        console.error("❌ Error processing AI response:", error);
                    }
                });

                const offer = await pc.createOffer();
                await pc.setLocalDescription(offer);

                const baseUrl = "https://api.openai.com/v1/realtime";
                const model = "gpt-4o-mini-realtime-preview-2024-12-17";
                const sdpResponse = await fetch(`${baseUrl}?model=${model}`, {
                    method: "POST",
                    body: offer.sdp,
                    headers: {
                        Authorization: `Bearer ${EPHEMERAL_KEY}`,
                        "Content-Type": "application/sdp"
                    },
                });

                const answer = {
                    type: "answer",
                    sdp: await sdpResponse.text()
                };
                await pc.setRemoteDescription(answer);

            } catch (error) {
                console.error("❌ Error initializing WebRTC session:", error);
            }
        }

        function sendMessage() {
            const userMessage = document.getElementById("userMessage").value.trim();
            if (!userMessage) return;

            addMessage("User", userMessage);

            if (!dc || dc.readyState !== "open") {
                alert("Connection not ready. Please wait...");
                return;
            }

            const inputEvent = {
                type: "conversation.item.create",
                item: {
                    type: "message",
                    role: "user",
                    content: [{
                        type: "input_text",
                        text: userMessage
                    }]
                }
            };

            dc.send(JSON.stringify(inputEvent));

            const responseCreateEvent = {
                type: "response.create",
                response: {
                    modalities: ["text"],
                    instructions: "Answer the user clearly."
                }
            };

            dc.send(JSON.stringify(responseCreateEvent));
            document.getElementById("userMessage").value = "";
        }

        // function startVoiceRecognition() {
        //     if (!('webkitSpeechRecognition' in window)) {
        //         alert("Speech recognition is not supported in this browser.");
        //         return;
        //     }

        //     recognition = new webkitSpeechRecognition();
        //     recognition.continuous = false;
        //     recognition.interimResults = false;
        //     recognition.lang = "ar-SA";

        //     recognition.start();

        //     recognition.onresult = (event) => {
        //         const transcript = event.results[0][0].transcript;
        //         document.getElementById("userMessage").value = transcript;
        //         sendMessage();
        //     };
        // }

        function addMessage(sender, text) {
            const chatBox = document.getElementById("chatBox");
            const container = document.createElement("div");
            container.className = sender === "User" ? "message-container user-container" : "message-container ai-container";

            const avatar = document.createElement("img");
            avatar.src = sender === "User" ? "/images/hassan.jpg" : "/images/ai_avatar.png";
            avatar.className = "avatar";

            const messageDiv = document.createElement("div");
            messageDiv.className = sender === "User" ? "message user-message" : "message ai-message";
            messageDiv.innerHTML = text;

            container.appendChild(avatar);
            container.appendChild(messageDiv);
            chatBox.appendChild(container);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        init();
    </script>
</body>

</html>
