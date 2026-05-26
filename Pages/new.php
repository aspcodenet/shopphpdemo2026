<?php
require_once("Models/Database.php");
require_once ("Utils/Validator.php"); // 1
require_once ("Models/Category.php");

// product.php?id=1

// SKILJA PÅ VISA OCH "SPARA"
$database = new Database();
$product = new Product();
$allCategories = $database->getAllCategories();
$v = new Validator($_POST); // 2

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // HAR MAN SUBMITTAT FORM?

    $product->title = $_POST['title'];
    $product->description = $_POST['description'];  
    $product->price = $_POST['price'];
    $product->stockLevel = $_POST['stockLevel'];
    $product->category_id = $_POST['category'];

// 3 regler
    $v->field('title')->required()->alpha_num([' '])->min_len(3)->max_len(50);
    $v->field('stockLevel')->required()->numeric()->min_val(0);
    $v->field('price')->required()->numeric()->min_val(0);
// 4 validera!
    if($v->is_valid()){
      $database->createProduct($product);
        header("Location: /admin"); // Hoppa till denna sida = redirect
        exit; // KLAR KÖR INTE MER I DENNA FIL
  } 
  // SPARA
    // ta data från form och spara i databasen
}



// Hämta en Product från databasen med id = $id
// SELECT * from product where id = $id


?>
<h1>New Product</h1>


<form method="POST">
    <div>
        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="<?php echo $product->title; ?>">
          <span class="invalid-feedback"><?php echo $v->get_error_message('title');  ?></span>   
    </div>
    <div>
        <label for="category">Category</label>
        <select name="category" id="category">
            <?php
            foreach($allCategories as $category){
                ?>
                <option value="<?php echo $category->id; ?>">
                    <?php echo $category->name;?>
                </option>
            <?php 
            }
            ?>
        </select>
    </div>
    <div>
        <label for="description">Description</label>
        <textarea id="description" name="description"><?php echo $product->description; ?></textarea>
    </div>
    <div>
        <label for="price">Price</label>
        <input type="number" id="price"  name="price" value="<?php echo $product->price; ?>">
        <span class="invalid-feedback"><?php echo $v->get_error_message('price');  ?></span> 
    </div>
    <div>
        <label for="stockLevel">Stock Level</label>
        <input type="text" id="stockLevel" name="stockLevel" value="<?php echo $product->stockLevel; ?>">
        <span class="invalid-feedback"><?php echo $v->get_error_message('stockLevel');  ?></span>
    </div>
    <div>
        <button type="submit">Save</button>
    </div>
</form>

