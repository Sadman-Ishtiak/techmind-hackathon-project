<?php
session_start(); // needed for auth check
$title = "Home - JKKNIU Marketplace";

ob_start();
?>
    <h2 class="text-xl font-bold mb-4">Latest Entries</h2>
    <p>Here are the newest items from students and stores.</p>
<?php
$content = ob_get_clean();

include './lib/layout.php';
