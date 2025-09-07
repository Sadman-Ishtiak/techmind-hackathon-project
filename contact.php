<?php
session_start(); // needed for auth check
$title = "Contact Developers - Nazrul Bazar";

ob_start();
?>
    <div class="max-w-7xl mx-auto py-8 px-4 text-slate-200">
        <h2 class="text-3xl font-extrabold mb-8 text-blue-400 animate-fade-in-down">About the Developers</h2>
        <div class="flex flex-col md:flex-row gap-6 mb-6">
            <div class="bg-slate-800 rounded-xl shadow-lg p-6 flex-1 flex flex-col items-center border border-slate-700 transition-transform duration-300 hover:scale-105">
                <img src="https://via.placeholder.com/100" alt="MD Khairul Islam Tushar" class="rounded-full mb-4 w-24 h-24 object-cover border-2 border-blue-400">
                <h3 class="text-xl font-semibold mb-2 text-blue-400">MD Khairul Islam Tushar</h3>
                <p class="mb-2 text-slate-400">Full Stack Developer</p>
                <p class="text-slate-300 text-center mb-2">Khairul worked on both frontend and backend features. He is skilled in PHP, JavaScript, and UI/UX design. He is dedicated to creating user-friendly and efficient solutions for the marketplace.</p>
                <p class="text-slate-500 text-sm text-center mb-2">Credentials: BSc in Computer Science, TechMind Hackathon Finalist</p>
                <a href="mailto:khairul.tushar@email.com" class="mt-4 inline-block text-blue-400 hover:underline font-semibold">Email Khairul</a>
            </div>
            <div class="bg-slate-800 rounded-xl shadow-lg p-6 flex-1 flex flex-col items-center border border-slate-700 transition-transform duration-300 hover:scale-105">
                <img src="https://via.placeholder.com/100" alt="Sadman Ishtiak" class="rounded-full mb-4 w-24 h-24 object-cover border-2 border-blue-400">
                <h3 class="text-xl font-semibold mb-2 text-blue-400">Sadman Ishtiak</h3>
                <p class="mb-2 text-slate-400">Full Stack Developer</p>
                <p class="text-slate-300 text-center mb-2">Sadman contributed to both frontend and backend development. He has experience in PHP, JavaScript, and database design. He is passionate about building scalable web applications and enjoys learning new technologies.</p>
                <p class="text-slate-500 text-sm text-center mb-2">Credentials: BSc in Software Engineering, TechMind Hackathon Finalist</p>
                <a href="mailto:sadman.ishtiak@email.com" class="mt-4 inline-block text-blue-400 hover:underline font-semibold">Email Sadman</a>
            </div>
        </div>
        <p class="text-slate-300 text-center max-w-2xl mx-auto">Our goal is to connect buyers and sellers in a user-friendly environment, fostering a vibrant community of commerce. We believe in the power of technology to transform lives and are committed to continuous improvement and innovation. Thank you for being a part of our journey!</p>
    </div>
<?php
$content = ob_get_clean();

include './lib/layout.php';