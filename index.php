<?php
ob_start(); // STARTAR OUTPUT BUFFERING - ALL OUTPUT KOMMER ATT SPARAS I EN BUFFER OCH SKICKAS TILL WEBBLÄSAREN NÄR VI KALLAR PÅ OB_END_FLUSH() ELLER NÄR SCRIPTET AVSLUTAS. DETTA ÄR ANVÄNDBART FÖR ATT KUNNA SÄNDRA HTTP-HEADER EFTER ATT HA GENERERAT INNEHÅLL, VILKET ANNARS INTE SKULLE VARA MÖJLIGT EFTERSOM HEADER MÅSTE SÄNDAS INNAN NÅGOT ANNAT INNEHÅLL.
session_start(); // STARTAR EN NY SESSION ELLER ÅTERUPPTAR EN EXISTERANDE SESSION. DETTA ÄR NÖDVÄNDIGT FÖR ATT KUNNA ANVÄNDA $_SESSION-ARRAYEN FÖR ATT LAGRA OCH HANTERA ANVÄNDARDATA ÖVER FLERA SIDOR.
// session_id() -> HÄMTAR DEN NUVARANDE SESSIONENS ID. 
// DETTA ÄR ANVÄNDBART FÖR ATT KUNNA SPÅRA OCH HANTERA SESSIONS PÅ 
// ETT UNIKT SÄTT, S



// alla besökare fåt ett unikt session id
// det fixar PHP åt

// Index kommer att bli en grundsida - så vi skapar globala variabler här
require_once("Utils/Router.php"); // LADDAR IN ROUTER KLASSEN


//MAPPA URL mot Kod

$router = new Router();
$router->addRoute('/', function () {
    require_once( __DIR__ .'/Pages/index.php');
});
$router->addRoute('/product', function () {
    require_once( __DIR__ .'/Pages/product.php');
});
$router->addRoute('/category', function () {
    require_once( __DIR__ .'/Pages/category.php');
});
$router->addRoute('/admin', function () {
    require_once( __DIR__ .'/Pages/admin.php');
});
$router->addRoute('/admin/edit', function () {
    require_once( __DIR__ .'/Pages/edit.php');
});
$router->addRoute('/admin/new', function () {
    require_once( __DIR__ .'/Pages/new.php');
});

$router->addRoute('/search', function () {
    require_once( __DIR__ .'/Pages/search.php');
});
$router->addRoute('/allproducts', function () {
   require_once(__DIR__.'/Pages/allproducts.php');
});

$router->addRoute('/javascriptAddToCart', function () {
   require_once(__DIR__.'/API/addToCart.php');
});


$router->addRoute('/addToCart', function () {
   require_once(__DIR__.'/Pages/addToCart.php');
});
$router->addRoute('/removeFromCart', function () {
   require_once(__DIR__.'/Pages/removeFromCart.php');
});
$router->addRoute('/viewCart', function () {
   require_once(__DIR__.'/Pages/viewCart.php');
});
$router->dispatch();



?>