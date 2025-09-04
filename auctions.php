<?php
session_start(); // needed for auth check
$title = "Auctions - JKKNIU Marketplace";

ob_start();
?>
    <h2 class="text-xl font-bold mb-4">Latest Auctions</h2>
    <p>Here are the newest items for auction from students.</p>
<?php
$content = ob_get_clean();

include './lib/layout.php';
