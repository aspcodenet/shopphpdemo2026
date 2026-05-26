<?php
require_once("Models/Product.php");
require_once("components/HeaderComponent.php");
require_once("Models/Database.php");


$database = new Database();
// sort=name&order=asc
$sort = $_GET['sort'] ?? "title"; // Om sort inte finns i url så sortera på name
$order = $_GET['order'] ?? "asc"; // Om order inte finns i url så sortera i stigande ordning
$page = $_GET['page'] ?? 1; // Om page inte finns i url så visa första sidan
$result = $database->getAllProducts($sort, $order,$page );
$allProducts = $result['products'];
$totalPages = $result['totalPages'];


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
                            <span class="badge bg-dark text-white ms-1 rounded-pill">0</span>
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
                        <th>
                        <a href="admin.php?sort=title&order=asc">
                            <i class="bi bi-sort-up"></i>    
                        </a>
                        Name
                        <a href="admin.php?sort=title&order=desc">
                        <i class="bi bi-sort-down"></i> 
                        </a>
                        </th>
                        <th>
                        <a href="admin.php?sort=price&order=asc">
                            <i class="bi bi-sort-up"></i>    
                        </a>
                        Price
                        <a href="admin.php?sort=price&order=desc"></a>
                        <i class="bi bi-sort-down"></i> 
                        </th>
                        <th>
                        <i class="bi bi-sort-up"></i>    
                        Stock level
                        <i class="bi bi-sort-down"></i> 
                        </th>
                        <th>action</th>
                </thead>

                <tbody>
                    <?php
                    foreach($allProducts as $product){
                        ?>
                    <tr>
                        <td><?php echo $product->title; ?></td>
                       
                        <td><?php echo $product->price; ?></td>
                        <td><?php echo $product->stockLevel; ?></td>
                        <td><a  href="/admin/edit?id=<?php echo $product->id; ?>"   class="btn btn-primary">Edit</a></td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
                <tfoot>

                    <tr>
                        <td colspan="5">
                            <a href="/admin/new" class="btn btn-success">Create new product</a>
                        </td>
                </tfoot>
            </table>
            <nav>
                <ul class="pagination">
                    <?php
                    for($i = 1; $i <= $totalPages; $i++){
                        ?>
                        <li class="page-item <?php if($i == $page) echo "active"; ?>">
                            <a class="page-link" href="admin.php?sort=<?php echo $sort; ?>&order=<?php echo $order; ?>&page=<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </nav>
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
