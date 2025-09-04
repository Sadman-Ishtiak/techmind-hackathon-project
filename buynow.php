<?php
session_start(); // needed for auth check
$title = "Products - JKKNIU Marketplace";

ob_start();
?>
    <h2 class="text-xl font-bold mb-4">The buy now page</h2>
    <p>Here are the newest and hot items from stores.</p>
<?php
$content = ob_get_clean();

include './lib/layout.php';
