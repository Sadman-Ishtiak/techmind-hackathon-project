<?php
session_start(); // needed for auth check
$title = "Stores - JKKNIU Marketplace";

ob_start();
?>
    <h2 class="text-xl font-bold mb-4">Popular Stores</h2>
    <p>Here are the recommended stores from our users.</p>
<?php
$content = ob_get_clean();

include './lib/layout.php';
