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
                <img src="/images/ai_avatar.png" alt="AI Avatar" class="avatar">
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
        let stage = -1;
        let recognition;
        let translationPrompt =
            "You are a language expert. Translate user queries into precise English questions.Translate the user query into a clear and precise English question without changing its meaning:Return only the translated question, nothing else.";
        const collectionName = 'realestate';
        const schema = '{available_units(numeric),project_name(string),price_per_sqm(numeric)}';
        let userMessage = '';

        function mongRetreival(translated_message) {
            //const system_message = 'You are an expert MongoDB query builder.'
            const mongoprompt = `You are an expert MongoDB query builder. Given the following MongoDB schema for the realestate collection: ${schema}, generate a MongoDB aggregation pipeline in JSON format.

                ### **Instructions (Strictly Follow)**
                ✅ **Sort and/or filter only if necessary** based on the given query : ${translated_message}.  
                ✅ **If no sorting is needed, exclude the sort stage**.  
                ✅ **If no filtering is needed, exclude the match stage**. 
                ✅ **Limit the results to 5**. 
                ✅ **You can use multiple filters**. 
                ✅ **Use only fields specified in the schema**.  
                ✅ **Respect data types** (e.g., numeric fields must have numeric values).  
                ✅ **Use regex for partial string matching**, but **do NOT** include ^ at the beginning or $ at the end of the pattern.  
                ✅ **Convert date-related values into Unix timestamps (seconds).**  
                ✅ **Ignore conditions that attempt to filter a numeric field using a string.**
                ✅ **Do not select only the required fields** 
                ✅ **Exclude \`null\` values from the sorting process**.
                ✅ **Ensure that fields used in any mathematical operations (such as "$multiply") are numeric**. Use "$toDouble" or "$toInt" for converting fields like \`price_per_sqm\` or \`available_units\` to numeric types before performing any mathematical operations.

                Return only a valid JSON array, nothing else.`;
            const mongoEvent = {
                type: "conversation.item.create",
                item: {
                    type: "message",
                    role: "user",
                    content: [{
                        type: "input_text",
                        text: translated_message
                    }]
                }
            };

            dc.send(JSON.stringify(mongoEvent));

            const mongoCreateEvent = {
                type: "response.create",
                response: {
                    modalities: ["text"],
                    instructions: mongoprompt,
                    //temperature: 0.7
                }
            };

            dc.send(JSON.stringify(mongoCreateEvent));

        }

        function chat(user_message, retreive_context) {

            const chatprompt = `You are a helpful assistant that uses stored relative context : ${retreive_context} 
            to answer questions and provides a relevant recommendation at the end of your response.
            ** the answer Must be in details **.
            **After answering the user’s question, provide a relevant recommendation based on the topic discussed**.
            `;
            const chatEvent = {
                type: "conversation.item.create",
                item: {
                    type: "message",
                    role: "user",
                    content: [{
                        type: "input_text",
                        text: user_message
                    }]
                }
            };

            dc.send(JSON.stringify(chatEvent));

            const chatCreateEvent = {
                type: "response.create",
                response: {
                    modalities: ["text", "audio"],
                    instructions: chatprompt,
                    max_output_tokens: 1000,
                    // temperature: 0.7
                }
            };

            dc.send(JSON.stringify(chatCreateEvent));
        }


        async function fetchAggregatedData(userMessage, pipeline) {
            try {
                const response = await fetch('/api/get-aggregated-data', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        match: pipeline
                    })
                });

                if (!response.ok) {
                    throw new Error('Failed to fetch data');
                }

                const data = await response.json();

                console.log('Matches:', data.pipeline);
                console.log('Relative Context:', data.results);

                if (data.results) {
                    chat(userMessage, data.results)
                    dc.send(JSON.stringify(chatEvent));
                     addMessage("AI", `<pre>${JSON.stringify(data.results, null, 2)}</pre>`);
                } else {
                    addMessage("AI", "No relevant data found.");
                }

            } catch (error) {
                console.error('Error fetching aggregated data:', error);
            }
        }

        let stream = false;
        let messageDiv = ''
        const pc = new RTCPeerConnection();
        const audioEl = document.createElement("audio");
        audioEl.autoplay = true;
        //const dc = null;
        async function init() {
            try {
                const tokenResponse = await fetch("/api/session");
                const data = await tokenResponse.json();
                const EPHEMERAL_KEY = data.client_secret.value;

                // const pc = new RTCPeerConnection();
                // const audioEl = document.createElement("audio");
                // audioEl.autoplay = true;
                // pc.ontrack = e => audioEl.srcObject = e.streams[0];

                // const ms = await navigator.mediaDevices.getUserMedia({
                //     audio: true
                // });
                // pc.addTrack(ms.getTracks()[0]);

                pc.ontrack = e => audioEl.srcObject = e.streams[0];

                // ✅ Generate a silent audio track to satisfy OpenAI's SDP requirement
                // const ctx = new AudioContext();
                // const oscillator = ctx.createOscillator();
                // const dst = ctx.createMediaStreamDestination();
                // oscillator.connect(dst);
                // oscillator.start();

                // const silentStream = dst.stream;
                // const silentTrack = silentStream.getAudioTracks()[0]; // Silent track
                // pc.addTrack(silentTrack, silentStream);
                const ms = await navigator.mediaDevices.getUserMedia({
                    audio: true
                });
                pc.addTrack(ms.getTracks()[0]);



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
        init();

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
                            if (stage == 0) {
                                mongRetreival(aiMessage);
                                stage = 1
                            } else if (stage == 1) {
                                fetchAggregatedData(userMessage, aiMessage);
                                stage = 2

                            } else if (stage == 2) {
                                addMessage("AI", aiMessage);
                                stage = -1

                            } else {
                                stage = -1
                            }
                            //addMessage("AI", results);
                        } else {
                            console.warn("⚠️ No content in response output:", responseOutput);
                        }
                    } else {
                        console.warn("⚠️ No valid response output:", receivedEvent.response);
                    }
                } else if (receivedEvent.type === "response.text.delta" && stage == 2) {

                    if (!stream) {
                        let sender = "AI"
                        const chatBox = document.getElementById("chatBox");
                        const container = document.createElement("div");
                        container.className = sender === "User" ? "message-container user-container" :
                            "message-container ai-container";

                        const avatar = document.createElement("img");
                        avatar.src = sender === "User" ? "/images/hassan.jpg" : "/images/ai_avatar.png";
                        avatar.className = "avatar";

                        messageDiv = document.createElement("div");
                        messageDiv.className = sender === "User" ? "message user-message" : "message ai-message";

                        container.appendChild(avatar);
                        container.appendChild(messageDiv);
                        chatBox.appendChild(container);
                        chatBox.scrollTop = chatBox.scrollHeight;
                        stream = true;
                    }
                    // addMessage("AI", receivedEvent.delta);
                    messageDiv.innerHTML += receivedEvent.delta.replace(/\n/g, '<br>');
                    console.log(receivedEvent.delta)

                } else if (receivedEvent.type === "response.text.done" && stage == 2) {
                    stream = false
                    stage = -1
                }
            } catch (error) {
                console.error("❌ Error processing AI response:", error);
            }
        });

        function sendMessage() {
            userMessage = document.getElementById("userMessage").value.trim();
            if (!userMessage) return;

            addMessage("User", userMessage);

            if (!dc || dc.readyState !== "open") {
                alert("Connection not ready. Please wait...");
                return;
            }

            const translateEvent = {
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

            dc.send(JSON.stringify(translateEvent));

            const responseCreateEvent = {
                type: "response.create",
                response: {
                    modalities: ["text"],
                    instructions: translationPrompt
                }
            };

            dc.send(JSON.stringify(responseCreateEvent));

            stage = 0;

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
            container.className = sender === "User" ? "message-container user-container" :
                "message-container ai-container";

            const avatar = document.createElement("img");
            avatar.src = sender === "User" ? "/images/hassan.jpg" : "/images/ai_avatar.png";
            avatar.className = "avatar";

            const messageDiv = document.createElement("div");
            messageDiv.className = sender === "User" ? "message user-message" : "message ai-message";
            messageDiv.innerHTML += text;

            container.appendChild(avatar);
            container.appendChild(messageDiv);
            chatBox.appendChild(container);
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    </script>
</body>

</html>
