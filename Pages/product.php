<?php
require_once "Models/Database.php";
// product.php?id=1

$id = $_GET['id'];

echo "Du klickade på Product id: " . $id;

// Hämta en Product från databasen med id = $id
// SELECT * from product where id = $id

$database = new Database();
$product = $database->getProduct($id);

?>

<h1><?php echo $product->title; ?></h1>
<p><?php echo $product->description; ?></p>
<p>Price: $<?php echo $product->price; ?></p>
<p>Stock level: <?php echo $product->stockLevel; ?></p>

