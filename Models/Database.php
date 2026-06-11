<?php
require_once("Models/Product.php");
require_once("Models/Category.php");
require_once("Models/FreightRule.php");
require_once("vendor/autoload.php");

class Database {
     public $pdo; // php data object - används för att ansluta till databas och göra queries

    function __construct(){
        // Ladda .env-filen§
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        // I know!!! Stupid! We will use .env soon!!!
            $host = $_ENV['DATABASE_HOST'];
            $db   = $_ENV['DATABASE_NAME'];
            $user = $_ENV['DATABASE_USER'];
            $pass = $_ENV['DATABASE_PASSWORD'];
            $port = $_ENV['DATABASE_PORT'];

            $dsn = "mysql:host=$host;port=$port;dbname=$db";
            $this->pdo = new PDO($dsn, $user, $pass);   
            // NU HAR VI EN KOPPLING (CONNECTION) TILL VÅR DATABAS OCH KAN GÖRA QUERIES
    }


    function getPopularProducts(){
        $query = $this->pdo->query("SELECT id,category_id,description,name as title,price,stock_level as stockLevel FROM product ORDER BY popularityFactor DESC LIMIT 0,10");
        $products = $query->fetchAll(PDO::FETCH_CLASS, "Product"); // KLASSNAMNET!!!
        return $products;
    }

    function getProductsForCategory($categoryId,$sort,$order){
        // sql injection - vid sort/order by
        if (!in_array($sort, ['title',  'price'])) {
            $sort = 'title';
        }
        if (!in_array($order, ['asc', 'desc'])) {
            $order = 'asc';
        }


        $query = $this->pdo->prepare("SELECT id,category_id,description,name as title,price,stock_level as stockLevel FROM product WHERE category_id=:categoryId ORDER BY $sort $order");
        $query->execute(['categoryId' => $categoryId]);
        $products = $query->fetchAll(PDO::FETCH_CLASS, "Product"); // KLASSNAMNET!!!
        return $products;
    }

    function searchProducts($searchWord){
        $query = $this->pdo->prepare("SELECT id,category_id,description,name as title,price,stock_level as stockLevel FROM product WHERE name like :searchWord");
        $searchWordWithProcent = '%' . $searchWord . '%';
        $query->execute(['searchWord' => $searchWordWithProcent]);


        //$query->execute(['searchWord' => '%' . $searchWord . '%']);
        $products = $query->fetchAll(PDO::FETCH_CLASS, "Product"); // KLASSNAMNET!!!
        return $products;
    }



    function getProduct($id){
        $query = $this->pdo->prepare("SELECT id,name as title, category_id, description, price, stock_level as stockLevel FROM product WHERE id = :id");
        $query->execute(['id' => $id]);
        $query->setFetchMode(PDO::FETCH_CLASS, 'Product');
        // TA SQL FRÅGA OCH KÖR I MYSQL
        // OCH OMVANDLA SVARET TILL EN PRODUCT-OBJEKT
        return $query->fetch();
    }


    function getCategory($id){
        $query = $this->pdo->prepare("SELECT * FROM category WHERE id = :id");
        $query->execute(['id' => $id]);
        $query->setFetchMode(PDO::FETCH_CLASS, 'Category');
        // TA SQL FRÅGA OCH KÖR I MYSQL
        // OCH OMVANDLA SVARET TILL EN PRODUCT-OBJEKT
        return $query->fetch();
    }

    function getFreightRule($id){
        $query = $this->pdo->prepare("SELECT id, zone_code as zoneCode, zone_name as zoneName, base_fee as baseFee, weight_modifier as weightMultiplier, free_shipping_threshold as freeShippingThreshold FROM freight_rules WHERE id = :id");
        $query->execute(['id' => $id]);
        $query->setFetchMode(PDO::FETCH_CLASS, 'FreightRule');
        // TA SQL FRÅGA OCH KÖR I MYSQL
        // OCH OMVANDLA SVARET TILL EN PRODUCT-OBJEKT
        return $query->fetch();
    }

    function getAllFreightRules(){
        $query = $this->pdo->query("SELECT id, zone_code as zoneCode, zone_name as zoneName, base_fee as baseFee, weight_modifier as weightMultiplier, free_shipping_threshold as freeShippingThreshold FROM freight_rules");
        $freightRules = $query->fetchAll(PDO::FETCH_CLASS, "FreightRule"); // KLASSNAMNET!!!
        return $freightRules;
    }

    function updateFreightRule($zoneCode, $zoneName, $baseFee, $weightMultiplier, $freeShippingLimit){
        //    
        $query = $this->pdo->prepare("INSERT INTO freight_rules (zone_code, zone_name, base_fee, weight_modifier," .
            " free_shipping_threshold) VALUES (:zoneCode, :zoneName, :baseFee, :weight_modifier, :free_shipping_threshold)" . 
            " ON DUPLICATE KEY UPDATE zone_name=:zoneName, base_fee=:baseFee, weight_modifier=:weight_modifier, free_shipping_threshold=:free_shipping_threshold");
        $query->execute([
            'zoneCode' => $zoneCode,
            'zoneName' => $zoneName,
            'baseFee' => $baseFee,
            'weight_modifier' => $weightMultiplier,
            'free_shipping_threshold' => $freeShippingLimit
        ]);
    }


    function listAllProducts(){
        $query = $this->pdo->query("SELECT id,category_id,description,name as title,price,stock_level as stockLevel, weight FROM product");
        $products = $query->fetchAll(PDO::FETCH_CLASS, "Product"); // KLASSNAMNET!!!
        return $products;
    }


    function getAllProducts($sort, $order, $page){
        // ALDRIG LITA PÅ INPUT FRÅN ANVÄNDAREN I EN SQL-QUERY
        if (!in_array($sort, ['title', 'categoryName', 'price', 'stockLevel'])) {
            $sort = 'title';
        }
        if (!in_array($order, ['asc', 'desc'])) {
            $order = 'asc';
        }

        // OCKSÅ ATT VARA I PAGING
        $limit = 40; // Antal produkter per sida
        $offset = ($page - 1) * $limit;

        $query = $this->pdo->query("SELECT id,description,name as title,price,stock_level as stockLevel FROM product ORDER BY $sort $order LIMIT $offset,$limit");
        $products = $query->fetchAll(PDO::FETCH_CLASS, "Product"); // KLASSNAMNET!!!
   
        $query = $this->pdo->query("SELECT  CEIL (COUNT(*)/$limit) FROM product");
        $totalPages = $query->fetchColumn();
   
        return ["products" => $products, "totalPages" => $totalPages];
        // en fiunktion som returnerar många saker
        // = associativ array
    }

    function createProduct($product){
        // INSERT INTO
        $query = $this->pdo->prepare("INSERT INTO product (name, description, price, stock_level, category_id,weight) VALUES (:name, :description, :price, :stockLevel, :category_id, :weight)");
        $query->execute([
            'name' => $product->title,
            'description' => $product->description,
            'price' => $product->price,
            'stockLevel' => $product->stockLevel,
            'category_id' => $product->category_id,
            'weight' => $product->weight
        ]);
    }

    function saveProduct($product){
        // UPDATE
        $query = $this->pdo->prepare("UPDATE product SET name=:name, description=:description, price=:price, stock_level=:stockLevel, category_id=:category_id, weight=:weight WHERE id=:id");
        $query->execute([
            'name' => $product->title,
            'description' => $product->description,
            'price' => $product->price,
            'stockLevel' => $product->stockLevel,
            'category_id' => $product->category_id,
            'weight' => $product->weight,
            'id' => $product->id
        ]);
    }

    function getAllCategories(){
        $query = $this->pdo->query("SELECT id,name FROM category");
        $categories = $query->fetchAll(PDO::FETCH_CLASS, "Category"); // KLASSNAMNET!!!
        return $categories;
    }



        function getCartItems($userId, $sessionId){
            if($userId != null ){
                $query = $this->pdo->prepare("UPDATE CartItem SET userId=:userId WHERE userId IS NULL AND  sessionId = :sessionId");
                $query->execute(['sessionId' => $sessionId, 'userId' => $userId]);
            }

            $query = $this->pdo->prepare("SELECT CartItem.Id as id, CartItem.productId, CartItem.quantity, product.name as productName, product.price as productPrice, product.price * CartItem.quantity as rowPrice  , product.weight as weight FROM CartItem JOIN product ON product.id=CartItem.productId  WHERE userId=:userId or sessionId = :sessionId");
            $query->execute(['sessionId' => $sessionId, 'userId' => $userId]);


            return $query->fetchAll(PDO::FETCH_CLASS, 'CartItem');
        }

        function convertSessionToUser($session_id, $userId, $newSessionId){
            $query = $this->pdo->prepare("UPDATE CartItem SET userId=:userId, sessionId=:newSessionId WHERE sessionId = :sessionId");
            $query->execute(['sessionId' => $session_id, 'userId' => $userId, 'newSessionId' => $newSessionId]);
        }

        function updateCartItem($userId, $sessionId,$productId, $quantity){
            if($quantity <= 0){
                $query = $this->pdo->prepare("DELETE FROM CartItem WHERE (userId=:userId or sessionId=:sessionId) AND productId = :productId");
                $query->execute([ 'userId' => $userId, 'sessionId' => $sessionId, 'productId' => $productId]);
                return;
            }
            $query = $this->pdo->prepare("SELECT * FROM CartItem  WHERE (userId=:userId or sessionId=:sessionId) AND productId = :productId");
            $query->execute([ 'userId' => $userId, 'sessionId' => $sessionId, 'productId' => $productId]);
            if($query->rowCount() == 0){
                $query = $this->pdo->prepare("INSERT INTO CartItem (productId, quantity, sessionId, userId) VALUES (:productId, :quantity, :sessionId, :userId)");
                $query->execute([ 'userId' => $userId, 'sessionId' => $sessionId, 'productId' => $productId, 'quantity' => $quantity]);
            }
            else{
                $query = $this->pdo->prepare("UPDATE CartItem SET quantity = :quantity WHERE (userId=:userId or sessionId=:sessionId) AND productId = :productId");
                $query->execute([ 'userId' => $userId, 'sessionId' => $sessionId, 'productId' => $productId, 'quantity' => $quantity]);
            }
        }


};

?>