<?php
// layout.php
// Define defaults
$title = $title ?? "Nazrul Bazar";
$year = date('Y');
// session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
        <style>
        body {
            background-color: #1e293b; /* Equivalent to bg-slate-800 */
        }
        /* Custom styles for the scrollbar to keep a minimalist design */
        .chat-body::-webkit-scrollbar {
            width: 6px;
        }
        .chat-body::-webkit-scrollbar-thumb {
            background-color: rgba(255,255,255,0.2);
            border-radius: 3px;
        }
        /* Custom styles for the chatbot icon */
        .chat-icon {
            position: fixed;
            bottom: 20px;
            right: 20px;
            cursor: pointer;
            z-index: 1000;
        }
        
        /* Animation Classes */
        .chat-container {
            position: fixed;
            bottom: 20px;
            right: 20px; /* UNCHANGED - Base position for open state */
            transition: all 0.4s ease-in-out;
            transform-origin: bottom right;
        }

        /* Initial state: closed and hidden */
        .chat-container.closed {
            width: 0;
            height: 0;
            opacity: 0;
            pointer-events: none; /* Disables interaction when closed */
            right: 100px; 
        }
        
        /* Final state: open and visible */
        .chat-container.open {
            width: 400px;
            height: 600px;
            opacity: 1;
            pointer-events: auto;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen bg-slate-800 text-slate-200">
    <header class="bg-slate-900 shadow-md py-4 sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <a href="./" class="text-3xl font-bold text-blue-400">Nazrul Bazar</a>
            
            <div class="flex items-center space-x-6">
                <nav class="hidden md:flex items-center space-x-6">
                    <a href="./auctions.php" class="text-slate-200 hover:text-blue-400 transition-colors duration-200">Auctions</a>
                    <a href="./buynow.php" class="text-slate-200 hover:text-blue-400 transition-colors duration-200">Buy Now</a>
                    <a href="./stores.php" class="text-slate-200 hover:text-blue-400 transition-colors duration-200">Stores</a>
                    <a href="./categories.php" class="text-slate-200 hover:text-blue-400 transition-colors duration-200">Categories</a>
                </nav>

                <form action="./search.php" method="GET" class="hidden md:flex">
                    <input 
                        type="text" 
                        name="q" 
                        placeholder="Search items..." 
                        class="border border-blue-600 bg-slate-700 text-slate-200 rounded-l-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 w-48"
                    >
                    <button type="submit" 
                        class="bg-blue-600 text-white px-3 py-2 rounded-r-md hover:bg-blue-700 transition-colors duration-200">
                        Search
                    </button>
                </form>

                <div id="auth-panel" class="relative flex items-center">
                    <?php if (!empty($_SESSION['user_id'])): ?>
                        <div class="flex items-center">
                            <span class="text-slate-200 font-medium mr-2 hidden md:block">
                                Hi, <?= htmlspecialchars($_SESSION['user_name']) ?>
                            </span>
                            <button id="user-menu-button" class="flex items-center space-x-1 focus:outline-none">
                                <svg class="w-5 h-5 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                                <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>

                        <div id="user-menu" class="absolute right-0 top-full mt-2 w-48 bg-slate-700 rounded-md shadow-lg py-1 hidden z-20">
                            <?php
                                // Role-based dashboard links
                                if ($_SESSION['user_role'] === 'admin') {
                                    echo '<a href="./admin_dashboard.php" class="block px-4 py-2 text-sm text-slate-200 hover:bg-slate-600">Admin Dashboard</a>';
                                } elseif ($_SESSION['user_role'] === 'store_owner') {
                                    echo '<a href="./owner_dashboard.php" class="block px-4 py-2 text-sm text-slate-200 hover:bg-slate-600">My Store</a>';
                                } else {
                                    echo '<a href="./dashboard.php" class="block px-4 py-2 text-sm text-slate-200 hover:bg-slate-600">Dashboard</a>';
                                }
                            ?>
                            <a href="./settings.php" class="block px-4 py-2 text-sm text-slate-200 hover:bg-slate-600">Settings</a>
                            <a href="./logout.php" class="block px-4 py-2 text-sm text-slate-200 hover:bg-slate-600">Log Out</a>
                        </div>
                    <?php else: ?>
                        <a href="./login.php" 
                           class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                            Log In
                        </a>
                    <?php endif; ?>
                </div>

                <button id="mobile-menu-btn" class="md:hidden text-slate-400 hover:text-blue-400 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <div id="mobile-menu" class="hidden md:hidden bg-slate-900 border-t border-slate-700 mt-2">
            <nav class="flex flex-col space-y-2 p-4">
                <a href="./auctions.php" class="text-slate-200 hover:text-blue-400 transition-colors duration-200">Auctions</a>
                <a href="./buynow.php" class="text-slate-200 hover:text-blue-400 transition-colors duration-200">Buy Now</a>
                <a href="./stores.php" class="text-slate-200 hover:text-blue-400 transition-colors duration-200">Stores</a>
                <a href="./categories.php" class="text-slate-200 hover:text-blue-400 transition-colors duration-200">Categories</a>

                <?php if (!empty($_SESSION['user_id'])): ?>
                    <?php
                        if ($_SESSION['user_role'] === 'admin') {
                            echo '<a href="./admin_dashboard.php" class="text-slate-200 hover:text-blue-400 transition-colors duration-200">Admin Dashboard</a>';
                        } elseif ($_SESSION['user_role'] === 'store_owner') {
                            echo '<a href="./owner_dashboard.php" class="text-slate-200 hover:text-blue-400 transition-colors duration-200">My Store</a>';
                        } else {
                            echo '<a href="./dashboard.php" class="text-slate-200 hover:text-blue-400 transition-colors duration-200">Dashboard</a>';
                        }
                    ?>
                    <a href="./settings.php" class="text-slate-200 hover:text-blue-400 transition-colors duration-200">Settings</a>
                    <a href="./logout.php" class="text-slate-200 hover:text-blue-400 transition-colors duration-200">Log Out</a>
                <?php else: ?>
                    <a href="./login.php" class="text-slate-200 hover:text-blue-400 transition-colors duration-200">Log In</a>
                    <a href="./register.php" class="text-slate-200 hover:text-blue-400 transition-colors duration-200">Register</a>
                <?php endif; ?>

                <form action="./search.php" method="GET" class="flex mt-3">
                    <input 
                        type="text" 
                        name="q" 
                        placeholder="Search..." 
                        class="border border-blue-600 bg-slate-700 text-slate-200 rounded-l-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 w-full"
                    >
                    <button type="submit" 
                        class="bg-blue-600 text-white px-3 py-2 rounded-r-md hover:bg-blue-700 transition-colors duration-200">
                        Go
                    </button>
                </form>
            </nav>
        </div>
    </header>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', () => {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        // Dropdown toggle
        document.addEventListener('DOMContentLoaded', function() {
            const userMenuBtn = document.getElementById('user-menu-button');
            const userMenu = document.getElementById('user-menu');

            if (userMenuBtn) {
                userMenuBtn.addEventListener('click', (event) => {
                    userMenu.classList.toggle('hidden');
                    event.stopPropagation();
                });
            }

            window.addEventListener('click', (event) => {
                if (userMenu && !userMenu.contains(event.target) && !userMenuBtn.contains(event.target)) {
                    userMenu.classList.add('hidden');
                }
            });
        });
    </script>

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 flex-grow">
        <?= $content ?? '' ?>
    </main>

    <footer class="bg-slate-900 border-t border-slate-700 py-4 mt-auto">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center text-sm text-slate-400">
            <p>&copy; <?= $year ?> Nazrul Bazar. All rights reserved.</p>
            <div class="space-x-4 mt-2 sm:mt-0">
                <a href="./about.php" class="hover:text-blue-400">About</a>
                <a href="./contact.php" class="hover:text-blue-400">Contact</a>
                <a href="./privacy.php" class="hover:text-blue-400">Privacy Policy</a>
            </div>
        </div>
    </footer>
    <div id="chatIcon" class="chat-icon bg-blue-600 text-white p-4 rounded-full shadow-lg hover:bg-blue-700 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
    </div>

    <div id="chatContainer" class="chat-container bg-slate-800 rounded-xl shadow-lg flex flex-col overflow-hidden closed mb-16">
        <div class="chat-header bg-blue-600 text-white p-4 text-center text-lg font-bold">
            AI Assistant Chatbot
        </div>
        <div id="chatBody" class="chat-body flex-1 p-4 overflow-y-auto flex flex-col gap-3 bg-slate-200">
            </div>
        <div class="chat-input flex p-2 border-t border-slate-700 bg-slate-800">
            <input 
                id="questionInput"
                type="text" 
                placeholder="Type your message..." 
                class="flex-1 p-2 border-0 rounded-lg m-1 focus:outline-none placeholder-slate-400 bg-slate-700 text-slate-200"
            >
            <button 
                id="sendButton"
                class="bg-blue-600 text-white px-6 py-2 rounded-full font-semibold hover:bg-blue-700 transition-colors"
            >
                Send
            </button>
        </div>
    </div>

    <script>
        const chatIcon = document.getElementById('chatIcon');
        const chatContainer = document.getElementById('chatContainer');
        const chatBody = document.getElementById('chatBody');
        const questionInput = document.getElementById('questionInput');
        const sendButton = document.getElementById('sendButton');
        const chatHistoryKey = 'chatHistory';

        // Function to toggle chat window visibility with animation
        chatIcon.addEventListener('click', () => {
            if (chatContainer.classList.contains('closed')) {
                chatContainer.classList.remove('closed');
                chatContainer.classList.add('open');
                scrollToBottom();
            } else {
                chatContainer.classList.remove('open');
                chatContainer.classList.add('closed');
            }
        });

        // Function to load messages from sessionStorage
        function loadChatHistory() {
            const history = JSON.parse(sessionStorage.getItem(chatHistoryKey)) || [];
            history.forEach(message => displayMessage(message.text, message.sender));
            scrollToBottom();
        }

        // Function to save a message to sessionStorage
        function saveMessage(text, sender) {
            const history = JSON.parse(sessionStorage.getItem(chatHistoryKey)) || [];
            history.push({ text, sender });
            sessionStorage.setItem(chatHistoryKey, JSON.stringify(history));
        }

        // Function to display a message in the chat body
        function displayMessage(text, sender) {
            const messageDiv = document.createElement('div');
            messageDiv.innerHTML = text;
            messageDiv.className = `message max-w-[85%] p-3 rounded-2xl shadow-md ${sender === 'user' ? 'user-message bg-blue-500 text-white self-end' : 'bot-message bg-slate-300 text-slate-800 self-start'}`;
            chatBody.appendChild(messageDiv);
        }

        // Function to scroll to the bottom of the chat
        function scrollToBottom() {
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        // Event listener for sending a message
        function sendMessage() {
            const userQuestion = questionInput.value.trim();
            if (userQuestion === '') return;

            displayMessage(userQuestion, 'user');
            saveMessage(userQuestion, 'user');

            questionInput.value = '';

            fetch('/Techminds/lib/chatbot.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `question=${encodeURIComponent(userQuestion)}`
            })
            .then(response => response.text())
            .then(data => {
                displayMessage(data, 'bot');
                saveMessage(data, 'bot');
                scrollToBottom();
            })
            .catch(error => {
                console.error('Error:', error);
                displayMessage('Network error. Please try again.', 'bot');
                saveMessage('Network error. Please try again.', 'bot');
                scrollToBottom();
            });
        }

        // Attach event listeners
        sendButton.addEventListener('click', sendMessage);
        questionInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        // Load chat history when the page loads
        document.addEventListener('DOMContentLoaded', loadChatHistory);
    </script>
</body>
</html>