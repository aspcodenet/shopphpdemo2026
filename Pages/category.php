<?php
require_once("Models/Product.php");
require_once("components/ProductComponent.php");
require_once("components/HeaderComponent.php");
require_once("Models/Database.php");

// urlen ser ju yut som
// http://localhost:8000/category?id=2&sort=price&order=asc
$sort = $_GET['sort'] ?? "title";
$order = $_GET['order'] ?? "asc";
$selectedOption = $sort . '-' . $order;
$database = new Database();
//$categoryid kommer ju från URL  category.php?id=1
$categoryid = $_GET['id'];
$theCategory = $database->getCategory($categoryid);
// select * from category where id=$categoryid
$products = $database->getProductsForCategory($categoryid, $sort, $order);
// select * from products where category_id=$categoryId
$allCategories = $database->getAllCategories();


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
                                <li><a class="dropdown-item" href="#!">All Products</a></li>
                                <li><hr class="dropdown-divider" /></li>
                                <?php
                                foreach($allCategories as $category){
                                    ?>
                                    <li><a class="dropdown-item" 
                                        href="category.php?id=<?php echo $category->id; ?>">
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
                    <form method="get" action="search.php">
                        <div class="input-group">
                            <input name="q" class="form-control" type="search" placeholder="Search for..." aria-label="Search for..." />
                            <button type="submit" class="btn btn-outline-secondary" id="button-search" type="button">Go!</button>
                        </div>
                    </form>
                    <form class="d-flex">
                        <button class="btn btn-outline-dark" type="submit">
                            <i class="bi-cart-fill me-1"></i>
                            Cart
                            <span class="badge bg-dark text-white ms-1 rounded-pill">0</span>
                        </button>
                    </form>
                </div>
            </div>
        </nav>
        <!-- Header-->

        <!-- Section-->
        <section class="py-5">
        <h1 class="text-center"><?php echo $theCategory->name ;?></h1>
        <select id="sortselect">
            <option value="title-asc" <?php echo $selectedOption === 'title-asc' ? 'selected' : ''; ?>>Title A-Z</option>
            <option value="title-desc" <?php echo $selectedOption === 'title-desc' ? 'selected' : ''; ?>>Title Z-A</option>
            <option value="price-asc" <?php echo $selectedOption === 'price-asc' ? 'selected' : ''; ?>>Sort by price: low to high</option>
            <option value="price-desc" <?php echo $selectedOption === 'price-desc' ? 'selected' : ''; ?>>Sort by price: high to low</option>
        </select>
            <div class="container px-4 px-lg-5 mt-5">
                <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                    <?php 
                    foreach($products as $product){
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
