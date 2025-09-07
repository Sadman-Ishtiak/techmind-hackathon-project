<?php
// chatbot.php
function getNaturalAnswer($question){
    // Connect to database
    $conn = new mysqli('localhost', 'root', '', 'Hackathon');
    if ($conn->connect_error) {
        return "Database connection failed: " . $conn->connect_error;
    }

    $question = trim($question);
    $question = strtolower($question);
    
    $keywords = preg_split('/\s+/', $question, -1, PREG_SPLIT_NO_EMPTY);

    if (empty($keywords)) {
        return "Please provide a valid search query.";
    }

    $escaped_keywords = [];
    foreach ($keywords as $word) {
        $escaped_keywords[] = $conn->real_escape_string($word);
    }
    
    $tables_to_search = [
        'products' => ['name', 'price', 'subcategory_name'],
        'stores' => ['name'],
        'auctions' => ['product_name', 'category_name', 'subcategory_name', 'description']
    ];

    $response = [];

    // START OF CHANGE: Define a CSS style for highlighting
    $highlight_style = 'style="color: #FF5733; font-weight: bold;"'; // A vibrant orange color
    // END OF CHANGE

    foreach($tables_to_search as $table => $columns){
        $like_clauses = [];
        foreach($columns as $col){
            foreach ($escaped_keywords as $keyword) {
                $like_clauses[] = "$col LIKE '%$keyword%'";
            }
        }
        $sql = "SELECT * FROM $table WHERE " . implode(" OR ", $like_clauses) . " LIMIT 5";
        $result = $conn->query($sql);

        if($result && $result->num_rows > 0){
            switch($table) {
                case 'products':
                    $response[] = "✨ I found some products that match your search:";
                    while($row = $result->fetch_assoc()){
                        // START OF CHANGE: Apply highlighting
                        $product_name = highlightKeywords($row['name'], $keywords, $highlight_style);
                        $subcategory_name = highlightKeywords($row['subcategory_name'], $keywords, $highlight_style);
                        // END OF CHANGE
                        $response[] = "• <b>{$product_name}</b>: You can find this in the <b>{$subcategory_name}</b> subcategory, priced at <b>\${$row['price']}</b>.";
                    }
                    break;
                case 'stores':
                    $response[] = "🏬 There are a few stores that might have what you're looking for:";
                    while($row = $result->fetch_assoc()){
                        // START OF CHANGE: Apply highlighting
                        $store_name = highlightKeywords($row['name'], $keywords, $highlight_style);
                        // END OF CHANGE
                        $response[] = "• We found a store named <b>{$store_name}</b>.";
                    }
                    break;
                case 'auctions':
                    $response[] = "⏳ Check out these auctions that are currently running:";
                    while($row = $result->fetch_assoc()){
                        // START OF CHANGE: Apply highlighting
                        $product_name = highlightKeywords($row['product_name'], $keywords, $highlight_style);
                        $category_name = highlightKeywords($row['category_name'], $keywords, $highlight_style);
                        // END OF CHANGE
                        $response[] = "• An auction for a <b>{$product_name}</b> in the <b>{$category_name}</b> category.";
                    }
                    break;
            }
        }
    }

    $conn->close();

    if(empty($response)){
        return "Sorry, I couldn't find anything for '$question'. Perhaps try a different keyword?";
    }

    return implode("<br>", $response);
}

// START OF CHANGE: New helper function to highlight keywords
function highlightKeywords($text, $keywords, $style) {
    foreach ($keywords as $keyword) {
        $pattern = '/(' . preg_quote($keyword, '/') . ')/i';
        $text = preg_replace($pattern, "<span $style>$1</span>", $text);
    }
    return $text;
}
// END OF CHANGE

// Check if a POST request was made and return the response
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['question'])) {
    echo getNaturalAnswer($_POST['question']);
}
?>