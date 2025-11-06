<?php
header("Content-Type: application/json");
include_once '../config/database.php';
include_once '../models/Product.php';

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

if(isset($_GET['id'])) {
    $product->id = $_GET['id'];
    if($product->readOne()) {
        $product_arr = array(
            "id" => $product->id,
            "product_name" => $product->product_name,
            "category" => $product->category,
            "price" => $product->price,
            "quantity" => $product->quantity,
            "description" => $product->description,
            "image_url" => $product->image_url
        );
        echo json_encode($product_arr);
    } else {
        echo json_encode(array("message" => "Product not found"));
    }
} else {
    echo json_encode(array("message" => "ID parameter missing"));
}
?>
