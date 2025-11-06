CREATE DATABASE lamba_fruit_bar;
USE lamba_fruit_bar;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (product_id) REFERENCES products(id)
);

INSERT INTO products (product_name, category, price, quantity, description) VALUES
('Fresh Orange Juice', 'Juice', 50.00, 100, 'Freshly squeezed orange juice'),
('Mango Shake', 'Shake', 70.00, 80, 'Creamy mango milkshake'),
('Fruit Salad', 'Salad', 60.00, 50, 'Mixed seasonal fruits'),
('Watermelon Juice', 'Juice', 45.00, 90, 'Refreshing watermelon juice'),
('Banana Smoothie', 'Smoothie', 65.00, 75, 'Healthy banana smoothie');
