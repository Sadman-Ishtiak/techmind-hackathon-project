<?php
session_start(); // needed for auth check
$title = "Contact Developers - JKKNIU Marketplace";

ob_start();
?>
    <h2 class="text-xl font-bold mb-4">About the Developers</h2>
    <div class="flex flex-col md:flex-row gap-6 mb-6">
        <div class="bg-white shadow-md rounded-lg p-6 flex-1 flex flex-col items-center">
            <img src="https://via.placeholder.com/100" alt="MD Khairul Islam Tushar" class="rounded-full mb-4 w-24 h-24 object-cover">
            <h3 class="text-lg font-semibold mb-2">MD Khairul Islam Tushar</h3>
            <p class="mb-2">Full Stack Developer</p>
            <p class="text-gray-700 mb-2">Khairul worked on both frontend and backend features. He is skilled in PHP, JavaScript, and UI/UX design. He is dedicated to creating user-friendly and efficient solutions for the marketplace.</p>
            <p class="text-gray-600 text-sm">Credentials: BSc in Computer Science, TechMind Hackathon Finalist</p>
            <a href="mailto:khairul.tushar@email.com" class="mt-2 text-blue-600 hover:underline text-sm">Email Khairul</a>
        </div>
        <div class="bg-white shadow-md rounded-lg p-6 flex-1 flex flex-col items-center">
            <img src="https://via.placeholder.com/100" alt="Sadman Ishtiak" class="rounded-full mb-4 w-24 h-24 object-cover">
            <h3 class="text-lg font-semibold mb-2">Sadman Ishtiak</h3>
            <p class="mb-2">Full Stack Developer</p>
            <p class="text-gray-700 mb-2">Sadman contributed to both frontend and backend development. He has experience in PHP, JavaScript, and database design. He is passionate about building scalable web applications and enjoys learning new technologies.</p>
            <p class="text-gray-600 text-sm">Credentials: BSc in Software Engineering, TechMind Hackathon Finalist</p>
            <a href="mailto:sadman.ishtiak@email.com" class="mt-2 text-blue-600 hover:underline text-sm">Email Sadman</a>
        </div>
    </div>
    <p>Our goal is to connect buyers and sellers in a user-friendly environment, fostering a vibrant community of commerce. We believe in the power of technology to transform lives and are committed to continuous improvement and innovation. Thank you for being a part of our journey!</p>
<?php
$content = ob_get_clean();

include './lib/layout.php';
