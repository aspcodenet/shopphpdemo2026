<?php

require_once(__DIR__.'/../Models/Database.php');
require_once(__DIR__.'/../Models/Cart.php');
require_once(__DIR__.'/../Models/CartItem.php');

$productIdToAddToCart = $_GET['id'];

$database = new Database();
$cart = new Cart($database, session_id());

echo json_encode([
    "cartItems" => $cart->getItems(),
    "cartTotalPrice" => $cart->getTotalPrice()
]);


?>