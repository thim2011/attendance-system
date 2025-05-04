<style>

.chatbot-button {
    position: fixed;
    bottom: 10px;
    right: 10px;
    padding: 8px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.chatbot {
    display: none;
    position: fixed;
    bottom: 5px;
    right: 5px;
    width: 400px;
    height: 470px;
    background-color: white;
    
    border-radius: 0 5px 0 0 ;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.4);
    overflow: hidden;
    z-index: 1000;
}

.chatbot-header {
    background-color: rgb(54, 88, 182);
    color: white;
    padding: 2px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chatbot-header span {
    flex: 1;
    text-align: center;
    font-size: 20px;
    font-weight: bold;
    color: white !important;
}

.close-button {
    background: none;
    border: none;
    color: white;
    font-size: 30px;
    cursor: pointer;
}

.chatbot-body {
    padding: 10px;
    height: calc(100% - 100px);
    overflow-y: auto;
}

.chatbot-body ul {
    padding: 0;
    list-style-type: none;
}

.chat {
    display: flex;
    align-items: flex-start;
    flex-wrap: wrap;
    margin-bottom: 5px;
}

.chat.incoming {
    justify-content: flex-start;
}

.chat.outgoing {
    justify-content: flex-end;
}

.chat p {
    padding: 10px;
    max-width: 270px;
    margin: 5px 5px 5px 0;
}

.chat.incoming p {
    border-radius: 10px 10px 10px 0;
}

.chat.incoming span{
    height: 32px;
    width: 32px;
    color: #fff !important;
    align-self: flex-end;
    background-color: rgb(54, 88, 182);
    text-align: center;
    line-height: 32px;
    border-radius: 4px;
    margin : 0 5px 7px 0;
}

.chat.outgoing p {
    background-color: rgb(54, 88, 182);
    border-radius: 10px 10px 0 10px;
    color: white !important;
}

.chatbot .chatbot-footer {
    position: absolute;
    bottom: 0;
    gap: 5px;
    width: 100%;
    padding: 5px 1px 5px 10px;

    display: flex;
}

.chatbot-footer textarea{
    padding: 12px 11px 12px 2px;
    font-size: 0.95rem;
    height: 50px;
    width: 100%;
    border: none;
    resize: none;
    outline: none;
}

.chatbot-footer button {
    padding: 8px 20px;
    background-color: rgb(54, 88, 182);
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
@media (max-width: 568px) {
    .chatbot {
        width: 100%;
        height: 100%;
        border-left: none;
        border-top: 1px solid #ccc;
        bottom: 0;
        right: 0;
    }
}
#notice{
    bottom: 50px;
    text-align: center;
    font-size: 13px !important;
    font-weight: bold;
    position: absolute;
}
    </style>

    <button id="chatbotButton" class="chatbot-button"><i class="fa-regular fa-message"></i> 小助理</button>
    <div id="chatbot" class="chatbot">
        <div class="chatbot-header">
            <span><i class="fa-solid fa-robot"></i> 小助理</span>
            <button id="closeChatbot" class="close-button">&times;</button>
        </div>
        <div class="chatbot-body">
            <ul >
                <li class="chat incoming">
                    <span><i class="fa-solid fa-robot"></i></span>
                    <p>你好！我是公司的小助理</p>
                </li>
  
            </ul>
            <p id="notice" class="text-body">*如果需要請假請加 <u>#我要請假</u> 在前頭</p>
        </div>
        <div class="chatbot-footer">
            <textarea id="chatInput" placeholder="輸入訊息..."></textarea>
            <button id="sendButton"><i class="fa-regular fa-paper-plane"></i></button>
        </div>
    </div>

    <script>
        function appendChatMessage(type, message, isRobot = false) {
            const chatBody = $('.chatbot-body ul');
            const newMessageItem = $('<li>').addClass('chat ' + type);
            const newMessage = $('<p>').html(message);

            if (isRobot) {
                const robotIcon = $('<span><i class="fa-solid fa-robot"></i></span>');
                newMessageItem.append(robotIcon);
            }

            newMessageItem.append(newMessage);
            chatBody.append(newMessageItem);
        }

        $(document).ready(function() {
            $('#chatbotButton').on('click', function() {
                $('#chatbot').show();
                $('body').toggleClass('chatbot-open');
                let mode = $('body').hasClass('chatbot-open') ? "chatbot-open" : "";
                localStorage.setItem('chatbot', mode);
            });

            $('#closeChatbot').on('click', function() {
                $('#chatbot').hide();
                localStorage.removeItem('chatbot');
            });

            $('#sendButton').on('click', function() {
                const userid = $('#userid').val();  
                const chatInput = $('#chatInput');
                const message = chatInput.val();
                if (message.trim() !== '') {
                    appendChatMessage('outgoing', message);
                    chatInput.val('');
                    const chatBody = $('.chatbot-body ul');
                    chatBody.scrollTop(chatBody[0].scrollHeight);
                }

                $.post("../../BLL/Chatbot/Chatbot.php", 
                    {message: message, userid: userid}, 
                    function(data, status) {
                        if (status == "success") {
                            if (data.err == 0) {
                                console.log(data.msg);
                                appendChatMessage('incoming', data.msg, true);
                            } else {
                                appendChatMessage('incoming', data.msg, true);
                            }
                        } else {
                            console.log("登入時，系統發生錯誤！");
                        }
                    },
                    "json"
                );
            });
       
        });
    </script>
