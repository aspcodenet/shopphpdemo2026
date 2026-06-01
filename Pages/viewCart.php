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

// === AI PRODUCT RECOMMENDATIONS ===
$recommendedProducts = [];
$aiError = null;

if (count($cartItems) > 0) {
    $apiKey = "ak-fc-f010f909de17b55f39a817c6ca5526d7ff6b054d597c9dbae48a5486dd2ecc21";
    $apiUrl = "https://fakecloud.systementor.se/v1/chat/completions";

    $allProducts = $database->listAllProducts();
    $catalog = [];
    foreach ($allProducts as $p) {
        $catalog[] = [
            "id" => (int)$p->id,
            "name" => $p->title,
            "category" => $p->category_id,
            "price" => (float)$p->price
        ];
    }

    $currentCart = [];
    foreach ($cartItems as $item) {
        $currentCart[] = [
            "id" => (int)$item->productId,
            "name" => $item->productName,
            "quantity" => (int)$item->quantity
        ];
    }

    $prompt = "You are a product recommendation engine for an online store.\n\n"
        . "Product catalog:\n" . json_encode($catalog, JSON_PRETTY_PRINT) . "\n\n"
        . "Current shopping cart:\n" . json_encode($currentCart, JSON_PRETTY_PRINT) . "\n\n"
        . "Based on the items in the cart, recommend 3 products from the catalog "
        . "that the customer might also want. Consider complementary items "
        . "(e.g., a monitor goes with a monitor stand) and items often bought together. "
        . "Do NOT recommend items already in the cart.\n\n"
        . "Respond with ONLY a JSON array of product IDs, like this:\n[1, 2, 3]";

    $body = json_encode([
        "model" => "StefanGpt1.0",
        "messages" => [["role" => "user", "content" => $prompt]]
    ]);



    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $apiKey",
            "Content-Type: application/json"
        ],
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // var_dump($response);
    // var_dump($httpCode);

    if ($httpCode !== 200) {
        $error = curl_error($ch);
        echo "cURL Error: " . $error . "\n";
        $aiError = "AI service error ($httpCode)";
    } else {
        $result = json_decode($response, true);
        $reply = $result["choices"][0]["message"]["content"] ?? "";
        echo "AI Reply: " . $reply . "\n";
        // strip markdown code fences if present
        $reply = trim($reply);
        $reply = preg_replace('/^```(?:json)?\s*/i', '', $reply);
        $reply = preg_replace('/\s*```$/', '', $reply);

        $ids = json_decode($reply, true);
        if (is_array($ids)) {
            foreach ($ids as $pid) {
                $product = $database->getProduct($pid);
                if ($product) {
                    $recommendedProducts[] = $product;
                }
            }
        } else {
            $aiError = "AI response wasn't valid JSON";
        }
    }
}


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
                        <td colspan="3">
                            SUMMA
                        </td>
                        <td id="cartTotalPrice"><?php echo $cart->getTotalPrice(); ?></td>
                        <td><a href="/checkout" class="btn btn-success">Checkout</a></td>
                    </tr>
                </tfoot>
            </table>
          
        </div>
        </section>
              <?php if (count($recommendedProducts) > 0): ?>
        <section class="py-5 bg-light">
            <div class="container px-4 px-lg-5">
                <h2 class="fw-bolder mb-4">You might also like</h2>
                <p class="text-muted mb-4">AI-powered recommendations based on your cart</p>
                <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 justify-content-center">
                    <?php foreach ($recommendedProducts as $rp): ?>
                    <div class="col mb-5">
                        <div class="card h-100">
                            <div class="card-body p-4">
                                <div class="text-center">
                                    <h5 class="fw-bolder"><?php echo htmlspecialchars($rp->title); ?></h5>
                                    <p class="text-muted small"><?php echo htmlspecialchars(substr($rp->description ?? '', 0, 80)); ?></p>
                                    $<?php echo number_format($rp->price, 2); ?>
                                </div>
                            </div>
                            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                                <div class="text-center">
                                    <a class="btn btn-outline-dark mt-auto" href="/product?id=<?php echo $rp->id; ?>">View</a>
                                    <button class="btn btn-success mt-auto" onclick="addToCart(<?php echo $rp->id; ?>, 1)">Add to cart</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php elseif ($aiError !== null): ?>
        <section class="py-3">
            <div class="container px-4 px-lg-5">
                <div class="alert alert-warning small"><?php echo htmlspecialchars($aiError); ?></div>
            </div>
        </section>
        <?php endif; ?>

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
                drawCart(data.cartItems, data.cartTotalPrice);
            });
        </script>
    </body>
</html>
