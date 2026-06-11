<?php
require_once("Models/Product.php");
require_once("components/HeaderComponent.php");
require_once("Models/Database.php");
require_once("Models/Cart.php");
require_once("Models/CartItem.php");


$database = new Database();

$cart = new Cart($database, session_id());
$cartItems = $cart->getItems();
$antalICarten = $cart->getItemsCount();


?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php headerComponent("SuperShoppen - Admin"); ?>
    </head>
    <body>
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container px-4 px-lg-5">
                <a class="navbar-brand" href="/index.php">SuperShoppen</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Kategorier</a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="#!">All Products</a></li>
                                <li><hr class="dropdown-divider" /></li>
                                    <li><a class="dropdown-item" href="#!">En cat</a></li>
                            </ul> 
                        </li>
                        <li class="nav-item"><a class="nav-link" href="#!">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="#!">Create account</a></li>
                    </ul>
                    <form class="d-flex">
                        <button class="btn btn-outline-dark" type="submit">
                            <i class="bi-cart-fill me-1"></i>
                            Cart
                            <span class="badge bg-dark text-white ms-1 rounded-pill" id="cartItemCount"><?php echo $antalICarten; ?></span>
                        </button>
                    </form>
                </div>
            </div>
        </nav>
        <!-- Header-->
        <header class="bg-dark py-5">
            <div class="container px-4 px-lg-5 my-5">
                <div class="text-center text-white">
                    <h1 class="display-4 fw-bolder">Super shoppen</h1>
                    <p class="lead fw-normal text-white-50 mb-0">Handla massa onödigt hos oss!</p>
                </div>
            </div>
        </header>
        <!-- Section-->
        <section class="py-5">
        <div class="container px-4 px-lg-5 mt-5">
            <table class="table">
                <thead>
                        <th colspan="5">Cart</th>
                </thead>

                <tbody id="cartItem">
              
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5">
                        Kundkorgen väger:   
                        <span id="cartTotalWeight">

                        </span>  kg     
                    </td>
                    </tr>
                    <tr>
                        <td>
                            <select id="freightRulesSelect" class="form-select">
                                <option value="">Välj fraktalternativ</option>
                                <?php 
                                    $freightRules = $database->getAllFreightRules();
                                    foreach($freightRules as $rule){
                                        echo "<option value='$rule->id'>$rule->zoneName - $rule->baseFee kr + $rule->weightMultiplier kr/kg</option>";
                                    }
                                ?>

                            </select>
                        </td>
                        <td></td>
                        <td>    </td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td colspan="3">
                            SUMMA
                        </td>
                        <td id="cartTotalPrice"><?php echo $cart->getTotalPrice(); ?></td>
                        <td><a href="/checkout" class="btn btn-success">Betala</a></td>
                    </tr>
                </tfoot>
            </table>
          
        </div>
        </section>
        <!-- Footer-->
        <footer class="py-5 bg-dark">
        <div class="container"><p class="m-0 text-center text-white">Copyright &copy; Your Shop 2025</p></div>
        </footer>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
    <script>
            // när sidan laddas så rendera cart items i tabellen
            document.addEventListener("DOMContentLoaded", async function() {
                const data = await fetchCartItems();
                drawCart(data.cartItems, data.cartTotalPrice, data.cartTotalWeight);
            });
        </script>
    </body>
</html>
