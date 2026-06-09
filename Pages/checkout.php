<?php

require_once(__DIR__.'/../vendor/autoload.php');
require_once(__DIR__.'/../Models/Database.php');
require_once(__DIR__.'/../Models/Cart.php');
require_once(__DIR__.'/../Models/CartItem.php');

$database = new Database();
$cart = new Cart($database, session_id());
$cartItems = $cart->getItems();


\Stripe\Stripe::setApiKey($_ENV['STRIPE_PRIVATE_KEY']);


//SKapa en array med line items som Stripe API:et kräver
$lineitems = [];
foreach($cart->getItems() as $cartitem ){
    array_push($lineitems, [
        "quantity" => $cartitem->quantity,
        "price_data" => [
            "currency" => "sek",
            "unit_amount" => $cartitem->productPrice*100,
            "product_data" => [
                "name" => $cartitem->productName
            ]
        ]

    ]);
}
// stoppa in en till line item för fraktkostnaden
// array_push($lineitems, [
//     "quantity" => 1,
//     "price_data" => [
//         "currency" => "sek",
//         "unit_amount" => 500,
//         "product_data" => [
//             "name" => "Fraktkostnad"
//         ]
//     ]

// ]);

// 
// // Nu är lineitems arrayen klar att skickas till Stripe API:et
$checkout_session = \Stripe\Checkout\Session::create([
    "mode" => "payment",
    "success_url" => "http://localhost:8000/checkoutsuccess",
    "cancel_url" => "http://localhost:8000",
    "locale" => "auto",
    "line_items" => $lineitems
]);

http_response_code(303);
header("Location: " . $checkout_session->url);
?>
