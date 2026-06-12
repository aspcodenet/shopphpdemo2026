<?php

require_once(__DIR__.'/../Models/Database.php');
require_once(__DIR__.'/../Models/Cart.php');
require_once(__DIR__.'/../Models/CartItem.php');



$database = new Database();
$cart = new Cart($database, session_id());


$freightRuleId = $_GET['freightRuleId'] ?? null; // Om freightRuleId inte skickas med i URL:en så sätt den till null
if($freightRuleId && $freightRuleId !== "null"){
    $freightRule = $database->getFreightRule($freightRuleId);
    $freightCost = $cart->calculateFreightCost($freightRule);
} else {
    $freightCost = 0;
}   


echo json_encode([
    "cartItems" => $cart->getItems(),
    "cartTotalPrice" => $cart->getTotalPrice() + $freightCost,
    "cartTotalWeight" => $cart->getTotalWeight(),
    "freightCost" => $freightCost
]);


?>