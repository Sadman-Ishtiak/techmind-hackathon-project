<?php
session_start(); // needed for auth check
$title = "Categories - JKKNIU Marketplace";

ob_start();
?>
    <h2 class="text-xl font-bold mb-4">Item Categories</h2>
    <p>Here are the various categories of items available in our marketplace.</p>
<?php
$content = ob_get_clean();

include './lib/layout.php';
