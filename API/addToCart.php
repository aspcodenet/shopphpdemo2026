<?php

require_once(__DIR__.'/../Models/Database.php');
require_once(__DIR__.'/../Models/Cart.php');
require_once(__DIR__.'/../Models/CartItem.php');

$productIdToAddToCart = $_GET['id'];

$database = new Database();
$cart = new Cart($database, session_id());

// in princip = insert or update cartitem set 
//  = quantity + 1 where sessionId = session_id and productId = $productIdToAddToCart
// inte bara i databasen utan även i cartItems arrayen i Cart klassen
$cart->addItem($productIdToAddToCart, 1);

// READ CART FROM DATABASE AGAIN TO GET UPDATED CART ITEMS, TOTAL PRICE AND TOTAL WEIGHT
$cart = new Cart($database, session_id());


echo json_encode([
    'success' => true,
    'message' => "Product $productIdToAddToCart added to cart",
    'cartItemCount' => $cart->getItemsCount(),
    'cartTotalPrice' => $cart->getTotalPrice(),
    "cartItems" => $cart->getItems(),
    "cartTotalWeight" => $cart->getTotalWeight()
]);


?>