<?php
session_start(); // needed for auth check
$title = "Contact Developers - JKKNIU Marketplace";

ob_start();
?>
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-4xl sm:text-5xl font-extrabold text-center text-gray-900 mb-4">Meet Our Developers</h1>
    <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto">
        Learn more about the passionate team who built this marketplace. Our goal is to connect buyers and sellers in a user-friendly environment, fostering a vibrant community of commerce.
    </p>

    <div class="flex flex-col md:flex-row gap-8 justify-center items-stretch">
        <!-- Developer Card 1 -->
        <div class="bg-white rounded-2xl shadow-xl p-8 flex-1 flex flex-col items-center text-center transform transition-transform duration-300 hover:scale-105">
            <img src="https://via.placeholder.com/150" alt="MD Khairul Islam Tushar" class="rounded-full mb-6 w-32 h-32 object-cover border-4 border-indigo-200">
            <h3 class="text-2xl font-bold text-gray-900 mb-2">MD Khairul Islam Tushar</h3>
            <p class="text-indigo-600 font-semibold mb-2">Full Stack Developer</p>
            <p class="text-gray-700 mb-4 text-sm leading-relaxed">Khairul worked on both frontend and backend features. He is skilled in PHP, JavaScript, and UI/UX design. He is dedicated to creating user-friendly and efficient solutions for the marketplace.</p>
            <p class="text-gray-500 text-xs mb-4">Credentials: BSc in Computer Science, TechMind Hackathon Finalist</p>
            <a href="mailto:khairul.tushar@email.com" class="mt-auto inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg font-semibold transition-transform hover:scale-105 hover:bg-indigo-700 shadow-md">
                Email Khairul
            </a>
        </div>

        <!-- Developer Card 2 -->
        <div class="bg-white rounded-2xl shadow-xl p-8 flex-1 flex flex-col items-center text-center transform transition-transform duration-300 hover:scale-105">
            <img src="https://via.placeholder.com/150" alt="Sadman Ishtiak" class="rounded-full mb-6 w-32 h-32 object-cover border-4 border-indigo-200">
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Sadman Ishtiak</h3>
            <p class="text-indigo-600 font-semibold mb-2">Full Stack Developer</p>
            <p class="text-gray-700 mb-4 text-sm leading-relaxed">Sadman contributed to both frontend and backend development. He has experience in PHP, JavaScript, and database design. He is passionate about building scalable web applications and enjoys learning new technologies.</p>
            <p class="text-gray-500 text-xs mb-4">Credentials: BSc in Software Engineering, TechMind Hackathon Finalist</p>
            <a href="mailto:sadman.ishtiak@email.com" class="mt-auto inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg font-semibold transition-transform hover:scale-105 hover:bg-indigo-700 shadow-md">
                Email Sadman
            </a>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();

include './lib/layout.php';
?>
