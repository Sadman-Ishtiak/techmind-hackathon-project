<?php
session_start(); // needed for auth check
$title = "About Developers - JKKNIU Marketplace";

ob_start();
?>
    <div class="max-w-4xl mx-auto py-8">
        <h2 class="text-3xl font-extrabold text-center text-gray-800 mb-8">About the Developers</h2>
        <div class="flex flex-col md:flex-row gap-8 mb-8">
            <div class="bg-gray-50 shadow-xl rounded-lg p-8 flex-1 flex flex-col items-center text-center transition-all duration-300 hover:scale-105">
                <img src="https://via.placeholder.com/120" alt="MD Khairul Islam Tushar" class="rounded-full mb-6 w-32 h-32 object-cover border-4 border-white shadow-md">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">MD Khairul Islam Tushar</h3>
                <p class="text-lg text-blue-600 font-medium mb-4">Full Stack Developer</p>
                <p class="text-gray-700 text-base mb-4 leading-relaxed">Khairul is a dedicated full stack developer with expertise in PHP, JavaScript, and UI/UX design. His commitment to creating user-friendly and efficient solutions drives the core functionality of our platform.</p>
                <p class="text-gray-500 text-sm italic">Credentials: BSc in Computer Science, TechMind Hackathon Finalist</p>
            </div>
            <div class="bg-gray-50 shadow-xl rounded-lg p-8 flex-1 flex flex-col items-center text-center transition-all duration-300 hover:scale-105">
                <img src="https://via.placeholder.com/120" alt="Sadman Ishtiak" class="rounded-full mb-6 w-32 h-32 object-cover border-4 border-white shadow-md">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Sadman Ishtiak</h3>
                <p class="text-lg text-blue-600 font-medium mb-4">Full Stack Developer</p>
                <p class="text-gray-700 text-base mb-4 leading-relaxed">Sadman contributed significantly to both frontend and backend development. He excels in database design and is passionate about building scalable web applications that enhance the user experience.</p>
                <p class="text-gray-500 text-sm italic">Credentials: BSc in Software Engineering, TechMind Hackathon Finalist</p>
            </div>
        </div>
        <div class="bg-white rounded-lg p-8 shadow-md">
            <p class="text-gray-800 text-center text-lg leading-relaxed">
                Our mission is to foster a vibrant community where technology connects buyers and sellers in a seamless, user-friendly environment. We are committed to continuous improvement and innovation to make this marketplace a dynamic and valuable platform for everyone. Thank you for being a part of our journey!
            </p>
        </div>
    </div>
<?php
$content = ob_get_clean();

include './lib/layout.php';
?>
