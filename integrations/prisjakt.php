<?php
require_once(__DIR__.'/../Models/Database.php');
$database = new Database();
// ALLA PRODUKTER
$allProducts = $database->listAllProducts();
?>
<rss xmlns:pj="https://schema.prisjakt.nu/ns/1.0" xmlns:g="http://base.google.com/ns/1.0" version="3.0">
  <channel>
    <title>Prisjakt Minimal Example Feed</title>
    <description>This is an example feed with the minimal values required</description>
    <link>https://schema.prisjakt.nu</link>
<?php
foreach ($allProducts as $product) {
    ?>
    <item>
      <g:id><?php echo $product->id; ?></g:id>
      <g:title><?php echo $product->title; ?></g:title>
      <g:price><?php echo $product->price; ?></g:price>
      <g:link>http://localhost:8000/product?id=<?php echo $product->id; ?></g:link>
    </item>
<?php } ?>
  </channel>
</rss>