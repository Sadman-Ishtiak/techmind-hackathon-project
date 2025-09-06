<?php
session_start();
require_once './config.php'; // DB pdoection
$title = "Categories - JKKNIU Marketplace";

ob_start();

echo '<h2 class="text-xl font-bold mb-4">Item Categories</h2>';
echo '<p>Here are the various categories of items available in our marketplace.</p>';

$stmt = $pdo->prepare("SELECT * FROM categories");
$stmt->execute();

echo "<ul>";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<li>" . htmlspecialchars($row['name']);

    $stmt2 = $pdo->prepare("SELECT * FROM subcategories WHERE category_name = ?");
    $stmt2->execute([$row['name']]);

    if ($stmt2->rowCount() > 0) {
        echo "<ul>";
        while ($sub = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            echo "<li>" . htmlspecialchars($sub['name']);

            $stmt3 = $pdo->prepare("SELECT * FROM subsubcategories WHERE subcategory_name = ?");
            $stmt3->execute([$sub["name"]]);

            if ($stmt3->rowCount() > 0) {
                echo "<ul>";
                while ($subsub = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                    echo "<li>" . htmlspecialchars($subsub['name']) . "</li>";
                }
                echo "</ul>";
            }

            echo "</li>";
        }
        echo "</ul>";
    }

    echo "</li>";
}
echo "</ul>";

$content = ob_get_clean();
include './lib/layout.php';
?>
