<?php
// You can define defaults for variables
$title = $title ?? "JKKNIU Marketplace";
$year = date('Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title) ?></title>
    <!-- <link rel="stylesheet" href="styles.css"> -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col min-h-screen">
    <header class="bg-white shadow-sm py-4 sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <a href="/" class="text-3xl font-bold text-gray-800">JKKNIU Marketplace</a>
            
            <div class="flex items-center space-x-6">
                <!-- Desktop Nav -->
                <nav class="hidden md:flex items-center space-x-6">
                    <a href="./auctions.php" class="text-gray-600 hover:text-gray-900 transition-colors duration-200">Auctions</a>
                    <a href="./buynow.php" class="text-gray-600 hover:text-gray-900 transition-colors duration-200">Buy Now</a>
                    <a href="./stores.php" class="text-gray-600 hover:text-gray-900 transition-colors duration-200">Stores</a>
                    <a href="./categories.php" class="text-gray-600 hover:text-gray-900 transition-colors duration-200">Categories</a>
                </nav>

                <!-- Desktop Search -->
                <form action="./search.php" method="GET" class="hidden md:flex">
                    <input 
                        type="text" 
                        name="q" 
                        placeholder="Search items..." 
                        class="border border-gray-300 rounded-l-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 w-48"
                    >
                    <button type="submit" 
                        class="bg-indigo-600 text-white px-3 py-2 rounded-r-md hover:bg-indigo-700 transition-colors duration-200">
                        Search
                    </button>
                </form>

                <!-- Auth Panel -->
                <div id="auth-panel" class="relative flex items-center">
                    <?php if (!empty($_SESSION['user'])): ?>
                        <div class="flex items-center">
                            <span class="text-gray-800 font-medium mr-2 hidden md:block">
                                Hi, <?= htmlspecialchars($_SESSION['user']['name']) ?>
                            </span>
                            <button id="user-menu-button" class="flex items-center space-x-1 focus:outline-none">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                                <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>

                        <div id="user-menu" class="absolute right-0 top-full mt-2 w-48 bg-white rounded-md shadow-lg py-1 hidden z-20">
                            <a href="./dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                            <a href="./settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                            <a href="./logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Log Out</a>
                        </div>
                    <?php else: ?>
                        <a href="./login.php" id="login-btn" 
                           class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors duration-200">
                            Log In
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden text-gray-600 hover:text-gray-900 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t mt-2">
            <nav class="flex flex-col space-y-2 p-4">
                <a href="./auctions.php" class="text-gray-600 hover:text-gray-900 transition-colors duration-200">Auctions</a>
                <a href="./buynow.php" class="text-gray-600 hover:text-gray-900 transition-colors duration-200">Buy Now</a>
                <a href="./stores.php" class="text-gray-600 hover:text-gray-900 transition-colors duration-200">Stores</a>
                <a href="./categories.php" class="text-gray-600 hover:text-gray-900 transition-colors duration-200">Categories</a>

                <?php if (!empty($_SESSION['user'])): ?>
                    <a href="./dashboard.php" class="text-gray-600 hover:text-gray-900 transition-colors duration-200">Dashboard</a>
                    <a href="./settings.php" class="text-gray-600 hover:text-gray-900 transition-colors duration-200">Settings</a>
                    <a href="./logout.php" class="text-gray-600 hover:text-gray-900 transition-colors duration-200">Log Out</a>
                <?php else: ?>
                    <a href="./login.php" class="text-gray-600 hover:text-gray-900 transition-colors duration-200">Log In</a>
                    <a href="./register.php" class="text-gray-600 hover:text-gray-900 transition-colors duration-200">Register</a>
                <?php endif; ?>

                <form action="./search.php" method="GET" class="flex mt-3">
                    <input 
                        type="text" 
                        name="q" 
                        placeholder="Search..." 
                        class="border border-gray-300 rounded-l-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 w-full"
                    >
                    <button type="submit" 
                        class="bg-indigo-600 text-white px-3 py-2 rounded-r-md hover:bg-indigo-700 transition-colors duration-200">
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

    <footer class="bg-gray-100 border-t py-4 mt-auto">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center text-sm text-gray-600">
            <p>&copy; <?= $year ?> JKKNIU Marketplace. All rights reserved.</p>
            <div class="space-x-4 mt-2 sm:mt-0">
                <a href="./about.php" class="hover:text-gray-900">About</a>
                <a href="./contact.php" class="hover:text-gray-900">Contact</a>
                <a href="./privacy.php" class="hover:text-gray-900">Privacy Policy</a>
            </div>
        </div>
    </footer>
</body>
</html>
