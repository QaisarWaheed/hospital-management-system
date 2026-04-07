<?php
require_once 'config.php';
// Select item names and trade names
$result = $conn->query("SELECT items.id ,items.name, categories.name AS category_title FROM items INNER JOIN categories ON items.category_id = categories.id WHERE items.category_id IN (1,3,4,5,6) AND items.status = 1 ");

$items = [];
while($row = $result->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode($items);
?>