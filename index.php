<?php
session_start();
include_once 'config/database.php';
include_once 'models/Product.php';

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

$message = "";
$message_type = "";

// Handle CRUD Operations
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['action'])) {
        switch($_POST['action']) {
            case 'create':
                $product->product_name = $_POST['product_name'];
                $product->category = $_POST['category'];
                $product->price = $_POST['price'];
                $product->quantity = $_POST['quantity'];
                $product->description = $_POST['description'];
                $product->image_url = $_POST['image_url'];
                
                if($product->create()) {
                    $message = "Product added successfully!";
                    $message_type = "success";
                } else {
                    $message = "Unable to add product.";
                    $message_type = "danger";
                }
                break;
                
            case 'update':
                $product->id = $_POST['id'];
                $product->product_name = $_POST['product_name'];
                $product->category = $_POST['category'];
                $product->price = $_POST['price'];
                $product->quantity = $_POST['quantity'];
                $product->description = $_POST['description'];
                $product->image_url = $_POST['image_url'];
                
                if($product->update()) {
                    $message = "Product updated successfully!";
                    $message_type = "success";
                } else {
                    $message = "Unable to update product.";
                    $message_type = "danger";
                }
                break;
                
            case 'delete':
                $product->id = $_POST['id'];
                if($product->delete()) {
                    $message = "Product deleted successfully!";
                    $message_type = "success";
                } else {
                    $message = "Unable to delete product.";
                    $message_type = "danger";
                }
                break;
        }
    }
}

// Get all products
$stmt = $product->read();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lamba Fruit Bar - Inventory Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 30px;
            text-align: center;
        }
        
        .header h1 {
            color: #667eea;
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 2.5rem;
        }
        
        .header p {
            color: #666;
            font-size: 1.1rem;
        }
        
        .logo {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        
        .content-wrapper {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 30px;
        }
        
        /* Enhanced Statistics Cards */
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
        }
        
        .stat-info h3 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            color: #333;
        }
        
        .stat-info p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        /* Category Filters */
        .category-filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        
        .filter-btn {
            padding: 10px 20px;
            border: 2px solid #e9ecef;
            background: white;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .filter-btn:hover {
            border-color: #667eea;
            color: #667eea;
            transform: translateY(-2px);
        }
        
        .filter-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #667eea;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        /* Enhanced Card Animations */
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
            position: relative;
        }
        
        .card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: 0.5s;
            z-index: 1;
        }
        
        .card:hover::before {
            left: 100%;
        }
        
        .card-img-top {
            border-radius: 15px 15px 0 0;
            transition: transform 0.5s ease;
        }
        
        .card:hover .card-img-top {
            transform: scale(1.1);
        }
        
        .price-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.95);
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 700;
            color: #667eea;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
            z-index: 10;
        }
        
        .modal-content {
            border-radius: 20px;
            border: none;
        }
        
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px 20px 0 0;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        .btn-sm {
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 0.875rem;
        }
        
        .search-box {
            margin-bottom: 20px;
        }
        
        .search-box input {
            border-radius: 10px;
            padding: 12px 20px;
            border: 2px solid #e9ecef;
        }
        
        .product-image-placeholder {
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            border-radius: 15px 15px 0 0;
        }
        
        /* Loading Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .product-card {
            animation: fadeIn 0.6s ease-out;
        }
        
        .product-card:nth-child(1) { animation-delay: 0.1s; }
        .product-card:nth-child(2) { animation-delay: 0.2s; }
        .product-card:nth-child(3) { animation-delay: 0.3s; }
        .product-card:nth-child(4) { animation-delay: 0.4s; }
        .product-card:nth-child(5) { animation-delay: 0.5s; }
        .product-card:nth-child(6) { animation-delay: 0.6s; }
        
        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.8rem;
            }
            
            .stat-card {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Header -->
        <div class="header">
            <div class="logo">🍹🍊🥭</div>
            <h1>Lamba Fruit Bar</h1>
            <p>University Kiosk Inventory Management System</p>
            <a href="cart.php" class="btn btn-primary" style="position: absolute; top: 20px; right: 20px;">
                <i class="fas fa-shopping-cart"></i> Cart 
                <?php 
                $cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
                echo "({$cart_count})";
                ?>
            </a>
        </div>

        <!-- Alert Messages -->
        <?php if($message): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Statistics Dashboard -->
        <div class="row mb-4">
            <?php
            $stmt_stats = $product->read();
            $total_products = $stmt_stats->rowCount();
            
            $stmt_value = $db->query("SELECT SUM(price * quantity) as total_value FROM products");
            $total_value = $stmt_value->fetch(PDO::FETCH_ASSOC)['total_value'];
            
            $stmt_low = $db->query("SELECT COUNT(*) as low_stock FROM products WHERE quantity < 20");
            $low_stock = $stmt_low->fetch(PDO::FETCH_ASSOC)['low_stock'];
            
            $stmt = $product->read(); // Re-fetch for the products grid
            ?>
            
            <div class="col-md-4 mb-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_products; ?></h3>
                        <p>Total Products</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="stat-info">
                        <h3>₹<?php echo number_format($total_value, 2); ?></h3>
                        <p>Inventory Value</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $low_stock; ?></h3>
                        <p>Low Stock Items</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-wrapper">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-box"></i> Product Inventory</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="fas fa-plus"></i> Add New Product
                </button>
            </div>

            <!-- Category Filters -->
            <div class="category-filters">
                <button class="filter-btn active" data-category="all">
                    <i class="fas fa-th"></i> All
                </button>
                <button class="filter-btn" data-category="juice">
                    <i class="fas fa-glass-whiskey"></i> Juice
                </button>
                <button class="filter-btn" data-category="shake">
                    <i class="fas fa-blender"></i> Shake
                </button>
                <button class="filter-btn" data-category="smoothie">
                    <i class="fas fa-cocktail"></i> Smoothie
                </button>
                <button class="filter-btn" data-category="salad">
                    <i class="fas fa-leaf"></i> Salad
                </button>
                <button class="filter-btn" data-category="snacks">
                    <i class="fas fa-cookie"></i> Snacks
                </button>
            </div>

            <!-- Search Box -->
            <div class="search-box">
                <input type="text" class="form-control" id="searchInput" placeholder="🔍 Search products by name, category, or description...">
            </div>

            <!-- Products Grid -->
            <div class="row" id="productsGrid">
                <?php
                $num = $stmt->rowCount();
                if($num > 0) {
                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        extract($row);
                        ?>
                        <div class="col-md-6 col-lg-4 col-xl-3 mb-4 product-card" data-name="<?php echo strtolower($product_name); ?>" data-category="<?php echo strtolower($category); ?>" data-description="<?php echo strtolower($description); ?>">
                            <div class="card h-100">
                                <div class="price-badge">₹<?php echo number_format($price, 2); ?></div>
                                
                                <?php if(!empty($image_url)): ?>
                                    <img src="<?php echo htmlspecialchars($image_url); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product_name); ?>" style="height: 200px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="product-image-placeholder">🍹</div>
                                <?php endif; ?>
                                
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($product_name); ?></h5>
                                    <p class="card-text">
                                        <span class="badge bg-info mb-2"><?php echo htmlspecialchars($category); ?></span><br>
                                        <small>Stock: 
                                            <?php if($quantity < 20): ?>
                                                <span class="badge bg-danger"><?php echo $quantity; ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-success"><?php echo $quantity; ?></span>
                                            <?php endif; ?>
                                        </small><br>
                                        <small class="text-muted"><?php echo htmlspecialchars(substr($description, 0, 60)); ?>...</small>
                                    </p>
                                </div>
                                <div class="card-footer bg-white border-0">
                                    <form method="POST" action="cart.php" class="mb-2">
                                        <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" name="add_to_cart" class="btn btn-success btn-sm w-100 mb-2">
                                            <i class="fas fa-shopping-cart"></i> Add to Cart
                                        </button>
                                    </form>
                                    <button class="btn btn-warning btn-sm w-100 mb-2" onclick="editProduct(<?php echo $id; ?>)" data-bs-toggle="modal" data-bs-target="#editProductModal">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-danger btn-sm w-100" onclick="deleteProduct(<?php echo $id; ?>)">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<div class='col-12 text-center'><p>No products found. Add your first product!</p></div>";
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Add New Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="mb-3">
                            <label class="form-label">Product Name *</label>
                            <input type="text" class="form-control" name="product_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Category *</label>
                            <select class="form-select" name="category" required>
                                <option value="">Select Category</option>
                                <option value="Juice">Juice</option>
                                <option value="Shake">Shake</option>
                                <option value="Smoothie">Smoothie</option>
                                <option value="Salad">Salad</option>
                                <option value="Snacks">Snacks</option>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price (₹) *</label>
                                <input type="number" step="0.01" class="form-control" name="price" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quantity *</label>
                                <input type="number" class="form-control" name="quantity" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Image URL</label>
                            <input type="text" class="form-control" name="image_url" placeholder="https://example.com/image.jpg">
                            <small class="text-muted">Enter the full URL of your product image</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" id="edit_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Product Name *</label>
                            <input type="text" class="form-control" name="product_name" id="edit_product_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Category *</label>
                            <select class="form-select" name="category" id="edit_category" required>
                                <option value="Juice">Juice</option>
                                <option value="Shake">Shake</option>
                                <option value="Smoothie">Smoothie</option>
                                <option value="Salad">Salad</option>
                                <option value="Snacks">Snacks</option>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price (₹) *</label>
                                <input type="number" step="0.01" class="form-control" name="price" id="edit_price" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quantity *</label>
                                <input type="number" class="form-control" name="quantity" id="edit_quantity" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Image URL</label>
                            <input type="text" class="form-control" name="image_url" id="edit_image_url" placeholder="https://example.com/image.jpg">
                            <small class="text-muted">Enter the full URL of your product image</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let searchTerm = this.value.toLowerCase();
            let cards = document.querySelectorAll('.product-card');
            
            cards.forEach(card => {
                let name = card.getAttribute('data-name');
                let category = card.getAttribute('data-category');
                let description = card.getAttribute('data-description');
                
                if(name.includes(searchTerm) || category.includes(searchTerm) || description.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Category filter functionality
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                let category = this.getAttribute('data-category');
                let cards = document.querySelectorAll('.product-card');
                
                cards.forEach(card => {
                    if(category === 'all' || card.getAttribute('data-category') === category) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // Edit product function
        function editProduct(id) {
            fetch('api/get_product.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('edit_id').value = data.id;
                    document.getElementById('edit_product_name').value = data.product_name;
                    document.getElementById('edit_category').value = data.category;
                    document.getElementById('edit_price').value = data.price;
                    document.getElementById('edit_quantity').value = data.quantity;
                    document.getElementById('edit_description').value = data.description;
                    document.getElementById('edit_image_url').value = data.image_url || '';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading product data');
                });
        }

        // Delete product function
        function deleteProduct(id) {
            if(confirm('Are you sure you want to delete this product?')) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                
                let actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete';
                
                let idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                idInput.value = id;
                
                form.appendChild(actionInput);
                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
