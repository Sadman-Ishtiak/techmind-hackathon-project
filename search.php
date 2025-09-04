<?php
session_start(); // needed for auth check
$title = "Search - JKKNIU Marketplace";

ob_start();
?>
    <h2 class="text-xl font-bold mb-4">Search Results</h2>
    <p>Here are the results for your search query.</p>
<?php
$content = ob_get_clean();

include './lib/layout.php';
