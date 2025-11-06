<?php
session_start();
include_once 'config/database.php';
include_once 'models/Product.php';

$database = new Database();
$db = $database->getConnection();

// Handle Add to Cart
if(isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    
    if(!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    
    if(isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
    
    header("Location: cart.php");
    exit();
}

// Handle Remove from Cart
if(isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    unset($_SESSION['cart'][$product_id]);
    header("Location: cart.php");
    exit();
}

// Handle Update Quantity
if(isset($_POST['update_cart'])) {
    foreach($_POST['quantity'] as $product_id => $quantity) {
        if($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    header("Location: cart.php");
    exit();
}

// Calculate Cart Total
$cart_items = array();
$total = 0;

if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $product_id => $quantity) {
        $query = "SELECT * FROM products WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($product) {
            $product['cart_quantity'] = $quantity;
            $product['subtotal'] = $product['price'] * $quantity;
            $cart_items[] = $product;
            $total += $product['subtotal'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Lamba Fruit Bar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 20px 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .cart-wrapper {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
        }
        
        .cart-item {
            border-bottom: 1px solid #e9ecef;
            padding: 20px 0;
        }
        
        .cart-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
        }
        
        .total-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="header">
            <div>
                <h2 style="margin: 0; color: #667eea;">🛒 Shopping Cart</h2>
            </div>
            <div>
                <a href="index.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
            </div>
        </div>

        <div class="cart-wrapper">
            <?php if(empty($cart_items)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart" style="font-size: 5rem; color: #ccc;"></i>
                    <h3 class="mt-3">Your cart is empty</h3>
                    <a href="index.php" class="btn btn-primary mt-3">Start Shopping</a>
                </div>
            <?php else: ?>
                <form method="POST" action="">
                    <?php foreach($cart_items as $item): ?>
                        <div class="cart-item row align-items-center">
                            <div class="col-md-2">
                                <?php if(!empty($item['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                <?php else: ?>
                                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 2rem;">🍹</div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <h5><?php echo htmlspecialchars($item['product_name']); ?></h5>
                                <small class="text-muted"><?php echo htmlspecialchars($item['category']); ?></small>
                            </div>
                            <div class="col-md-2">
                                <strong style="color: #667eea;">₹<?php echo number_format($item['price'], 2); ?></strong>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="quantity[<?php echo $item['id']; ?>]" value="<?php echo $item['cart_quantity']; ?>" min="1" class="form-control" style="width: 80px;">
                            </div>
                            <div class="col-md-2 text-end">
                                <strong>₹<?php echo number_format($item['subtotal'], 2); ?></strong><br>
                                <a href="?remove=<?php echo $item['id']; ?>" class="text-danger small"><i class="fas fa-trash"></i> Remove</a>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="mt-3">
                        <button type="submit" name="update_cart" class="btn btn-secondary"><i class="fas fa-sync"></i> Update Cart</button>
                    </div>
                </form>

                <div class="total-section">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3>Total Amount</h3>
                            <p class="mb-0">Including all taxes</p>
                        </div>
                        <div class="text-end">
                            <h2 class="mb-0">₹<?php echo number_format($total, 2); ?></h2>
                        </div>
                    </div>
                    <hr style="border-color: rgba(255,255,255,0.3);">
                    <a href="checkout.php" class="btn btn-light w-100 py-3"><i class="fas fa-lock"></i> Proceed to Checkout</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
