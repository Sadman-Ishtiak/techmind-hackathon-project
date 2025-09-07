<?php
session_start(); // needed for auth check
$title = "About Developers - Nazrul Bazar";

ob_start();
?>
<<<<<<< HEAD
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
=======
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
>>>>>>> a8d1043f7a1709163c59375c9b5b325189b71467
        </div>
        <p class="text-center text-gray-700 mt-6 max-w-2xl mx-auto">Our goal is to connect buyers and sellers in a user-friendly environment, fostering a vibrant community of commerce. We believe in the power of technology to transform lives and are committed to continuous improvement and innovation. Thank you for being a part of our journey!</p>
    </div>
<?php
$content = ob_get_clean();

include './lib/layout.php';
<<<<<<< HEAD
?>
=======
?>
>>>>>>> a8d1043f7a1709163c59375c9b5b325189b71467
