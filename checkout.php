<?php
session_start();
include_once 'config/database.php';

if(empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Calculate total
$total = 0;
foreach($_SESSION['cart'] as $product_id => $quantity) {
    $query = "SELECT price FROM products WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    $total += $product['price'] * $quantity;
}

// YOUR UPI DETAILS - Change these to your actual UPI details
$upi_id = "yourupiid@paytm";  // Replace with your UPI ID
$merchant_name = "Lamba Fruit Bar";

// Process Checkout
if(isset($_POST['place_order'])) {
    $customer_name = $_POST['customer_name'];
    $customer_email = $_POST['customer_email'];
    $customer_phone = $_POST['customer_phone'];
    // Alternative QR Code API - More Reliable
$qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($upi_link);

    $transaction_id = isset($_POST['transaction_id']) ? $_POST['transaction_id'] : '';
    
    // Insert main order
    $query = "INSERT INTO orders_main (customer_name, customer_email, customer_phone, total_amount, payment_status, payment_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $payment_status = ($payment_method == 'cod') ? 'pending' : 'completed';
    $stmt->execute([$customer_name, $customer_email, $customer_phone, $total, $payment_status, $transaction_id]);
    $order_id = $db->lastInsertId();
    
    // Insert order items
    foreach($_SESSION['cart'] as $product_id => $quantity) {
        $query = "SELECT product_name, price FROM products WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $query = "INSERT INTO order_items (order_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([$order_id, $product_id, $product['product_name'], $product['price'], $quantity]);
    }
    
    // Clear cart
    unset($_SESSION['cart']);
    
    $success_message = "Order placed successfully! Order ID: #" . $order_id;
}

// Generate UPI Payment Link
$upi_link = "upi://pay?pa={$upi_id}&pn=" . urlencode($merchant_name) . "&am={$total}&cu=INR&tn=" . urlencode("Order Payment");

// Generate UPI QR Code URL (using Google Charts API - Free)
$qr_code_url = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" . urlencode($upi_link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Lamba Fruit Bar</title>
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
        
        .checkout-wrapper {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .payment-method {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .payment-method:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }
        
        .payment-method.active {
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        }
        
        .payment-method input[type="radio"] {
            width: 20px;
            height: 20px;
            accent-color: #667eea;
        }
        
        .qr-code-section {
            text-align: center;
            padding: 30px;
            background: #f8f9fa;
            border-radius: 15px;
            margin-top: 20px;
        }
        
        .qr-code-section img {
            max-width: 300px;
            border: 5px solid white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            font-weight: 600;
        }
        
        .upi-apps {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 20px;
        }
        
        .upi-app-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="checkout-wrapper">
        <h2 class="mb-4"><i class="fas fa-shopping-bag"></i> Checkout</h2>
        
        <?php if(isset($success_message)): ?>
            <div class="alert alert-success">
                <h4><i class="fas fa-check-circle"></i> <?php echo $success_message; ?></h4>
                <p>Thank you for your order! We'll process it shortly.</p>
                <a href="index.php" class="btn btn-primary mt-3">Continue Shopping</a>
            </div>
        <?php else: ?>
            <form method="POST" action="" id="checkoutForm">
                <div class="row">
                    <div class="col-md-7">
                        <h4 class="mb-3">Customer Details</h4>
                        
                        <div class="mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" class="form-control" name="customer_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="customer_email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Phone Number *</label>
                            <input type="tel" class="form-control" name="customer_phone" required pattern="[0-9]{10}">
                        </div>
                        
                        <h4 class="mt-4 mb-3">Payment Method</h4>
                        
                        <!-- UPI Payment Option -->
                        <div class="payment-method active" onclick="selectPayment('upi')">
                            <div class="d-flex align-items-center">
                                <input type="radio" name="payment_method" value="upi" id="upi" checked>
                                <label for="upi" class="ms-3 mb-0">
                                    <h5 class="mb-1"><i class="fas fa-qrcode text-success"></i> UPI Payment</h5>
                                    <small class="text-muted">Pay using Google Pay, PhonePe, Paytm, BHIM & more</small>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Razorpay Option (Display Only) -->
                        <div class="payment-method" onclick="selectPayment('razorpay')">
                            <div class="d-flex align-items-center">
                                <input type="radio" name="payment_method" value="razorpay" id="razorpay">
                                <label for="razorpay" class="ms-3 mb-0">
                                    <h5 class="mb-1"><i class="fas fa-credit-card text-primary"></i> Razorpay</h5>
                                    <small class="text-muted">Credit Card, Debit Card, Net Banking</small>
                                    <br><span class="badge bg-warning text-dark mt-1">Coming Soon</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Cash on Delivery -->
                        <div class="payment-method" onclick="selectPayment('cod')">
                            <div class="d-flex align-items-center">
                                <input type="radio" name="payment_method" value="cod" id="cod">
                                <label for="cod" class="ms-3 mb-0">
                                    <h5 class="mb-1"><i class="fas fa-money-bill-wave text-success"></i> Cash on Delivery</h5>
                                    <small class="text-muted">Pay when you receive</small>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-5">
                        <div class="alert alert-info">
                            <h4>Order Summary</h4>
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <strong>₹<?php echo number_format($total, 2); ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax (0%):</span>
                                <strong>₹0.00</strong>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <h5>Total:</h5>
                                <h4 class="text-primary">₹<?php echo number_format($total, 2); ?></h4>
                            </div>
                        </div>
                        
                        <!-- UPI Payment Section -->
                        <div id="upiSection" class="qr-code-section">
                            <h5 class="mb-3">Scan QR Code to Pay</h5>
                            <img src="<?php echo $qr_code_url; ?>" alt="UPI QR Code">
                            
                            <div class="upi-apps mt-3">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e1/Google_Pay_Logo_%282020%29.svg/120px-Google_Pay_Logo_%282020%29.svg.png" class="upi-app-icon" alt="Google Pay">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b1/PhonePe_Logo.svg/120px-PhonePe_Logo.svg.png" class="upi-app-icon" alt="PhonePe">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/24/Paytm_Logo_%28standalone%29.svg/120px-Paytm_Logo_%28standalone%29.svg.png" class="upi-app-icon" alt="Paytm">
                            </div>
                            
                            <p class="mt-3 mb-2"><strong>OR</strong></p>
                            
                            <!-- Mobile UPI Intent Button -->
                            <a href="<?php echo $upi_link; ?>" class="btn btn-success btn-lg w-100 mb-3">
                                <i class="fas fa-mobile-alt"></i> Pay with UPI App
                            </a>
                            
                            <div class="mb-3 text-start">
                                <label class="form-label">Enter Transaction ID after payment *</label>
                                <input type="text" class="form-control" name="transaction_id" id="transaction_id" placeholder="12-digit UTR/Transaction ID">
                                <small class="text-muted">Find this in your UPI app after successful payment</small>
                            </div>
                        </div>
                        
                        <!-- Razorpay Section (Display Only) -->
                        <div id="razorpaySection" style="display: none;" class="alert alert-warning">
                            <i class="fas fa-info-circle"></i> Razorpay integration is currently under development. Please use UPI or Cash on Delivery.
                        </div>
                        
                        <!-- COD Section -->
                        <div id="codSection" style="display: none;" class="alert alert-success">
                            <i class="fas fa-check-circle"></i> You can pay cash when your order is delivered to you.
                        </div>
                    </div>
                </div>
                
                <button type="submit" name="place_order" class="btn btn-primary w-100 py-3 mt-3">
                    <i class="fas fa-check-circle"></i> Place Order
                </button>
            </form>
        <?php endif; ?>
    </div>

    <script>
    function selectPayment(method) {
        // Remove active from all
        document.querySelectorAll('.payment-method').forEach(el => el.classList.remove('active'));
        
        // Add active to selected
        document.getElementById(method).closest('.payment-method').classList.add('active');
        document.getElementById(method).checked = true;
        
        // Show/hide sections
        document.getElementById('upiSection').style.display = 'none';
        document.getElementById('razorpaySection').style.display = 'none';
        document.getElementById('codSection').style.display = 'none';
        
        if(method === 'upi') {
            document.getElementById('upiSection').style.display = 'block';
            document.getElementById('transaction_id').required = true;
        } else if(method === 'razorpay') {
            document.getElementById('razorpaySection').style.display = 'block';
            document.getElementById('transaction_id').required = false;
        } else if(method === 'cod') {
            document.getElementById('codSection').style.display = 'block';
            document.getElementById('transaction_id').required = false;
        }
    }
    
    // Initialize
    selectPayment('upi');
    </script>
</body>
</html>
