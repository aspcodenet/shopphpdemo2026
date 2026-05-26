<?php
require_once("Models/Product.php");
require_once("components/ProductComponent.php");
require_once("components/HeaderComponent.php");
require_once("Models/Database.php");
require_once("Models/Cart.php");
require_once("Models/CartItem.php");

$database = new Database();

$popularProducts = $database->getPopularProducts();
$allCategories = $database->getAllCategories();

$cart = new Cart($database, session_id());
$antalICarten = $cart->getItemsCount();

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php headerComponent("SuperShoppen - Startsidan"); ?>
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
                                <li><a class="dropdown-item" href="/allproducts">All Products</a></li>
                                <li><hr class="dropdown-divider" /></li>
                                <?php
                                foreach($allCategories as $category){
                                    ?>
                                    <li><a class="dropdown-item" 
                                        href="category?id=<?php echo $category->id; ?>">
                                        <?php echo $category->name;?>
                                        </a>
                                    </li>
                                <?php 
                                }
                                ?>
                            </ul> 
                        </li>
                        <li class="nav-item"><a class="nav-link" href="#!">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="#!">Create account</a></li>
                    </ul>
                    <form method="get" action="/search">
                        <div class="input-group">
                            <input name="q" class="form-control" type="search" placeholder="Search for..." aria-label="Search for..." />
                            <button type="submit" class="btn btn-outline-secondary" id="button-search" type="button">Go!</button>
                        </div>
                    </form>
                    
                        <a class="btn btn-outline-dark" href="/viewCart">
                            <i class="bi-cart-fill me-1"></i>
                            Cart
                            <span class="badge bg-dark text-white ms-1 rounded-pill" id="cartItemCount">
                                <?php echo $antalICarten; ?>
                            </span>
                        </a>
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
                <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                    <?php 
                    foreach($popularProducts as $product){
                        productComponent($product);
                    }
                    ?>
                </div>
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
    </body>
</html>
