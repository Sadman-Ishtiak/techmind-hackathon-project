<?php
session_start(); // needed for auth check
$title = "About Developers - Nazrul Bazar";

ob_start();
?>
    <div class="bg-blue-50 py-10 px-4 md:px-8">
        <h2 class="text-3xl font-bold mb-6 text-center text-blue-900">About the Developers</h2>
        <div class="flex flex-col md:flex-row gap-6 mb-6">
            <div class="bg-white shadow-lg hover:shadow-2xl transition-shadow duration-300 rounded-lg p-6 flex-1 flex flex-col items-center transform hover:-translate-y-2 transition-transform duration-300 ease-in-out border-t-4 border-blue-600">
                <img src="https://via.placeholder.com/100" alt="MD Khairul Islam Tushar" class="rounded-full mb-4 w-24 h-24 object-cover ring-4 ring-blue-200">
                <h3 class="text-xl font-semibold mb-2 text-blue-800">MD Khairul Islam Tushar</h3>
                <p class="mb-2 text-blue-600 font-medium">Full Stack Developer</p>
                <p class="text-gray-700 text-center mb-2">Khairul worked on both frontend and backend features. He is skilled in PHP, JavaScript, and UI/UX design. He is dedicated to creating user-friendly and efficient solutions for the marketplace.</p>
                <p class="text-blue-500 text-sm mt-2">Credentials: BSc in Computer Science, TechMind Hackathon Finalist</p>
            </div>
            <div class="bg-white shadow-lg hover:shadow-2xl transition-shadow duration-300 rounded-lg p-6 flex-1 flex flex-col items-center transform hover:-translate-y-2 transition-transform duration-300 ease-in-out border-t-4 border-blue-600">
                <img src="https://via.placeholder.com/100" alt="Sadman Ishtiak" class="rounded-full mb-4 w-24 h-24 object-cover ring-4 ring-blue-200">
                <h3 class="text-xl font-semibold mb-2 text-blue-800">Sadman Ishtiak</h3>
                <p class="mb-2 text-blue-600 font-medium">Full Stack Developer</p>
                <p class="text-gray-700 text-center mb-2">Sadman contributed to both frontend and backend development. He has experience in PHP, JavaScript, and database design. He is passionate about building scalable web applications and enjoys learning new technologies.</p>
                <p class="text-blue-500 text-sm mt-2">Credentials: BSc in Software Engineering, TechMind Hackathon Finalist</p>
            </div>
        </div>
        <p class="text-center text-gray-700 mt-6 max-w-2xl mx-auto">Our goal is to connect buyers and sellers in a user-friendly environment, fostering a vibrant community of commerce. We believe in the power of technology to transform lives and are committed to continuous improvement and innovation. Thank you for being a part of our journey!</p>
    </div>
<?php
$content = ob_get_clean();

include './lib/layout.php';
?>