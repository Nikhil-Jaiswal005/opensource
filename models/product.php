<?php
class Product {
    private $conn;
    private $table_name = "products";

    public $id;
    public $product_name;
    public $category;
    public $price;
    public $quantity;
    public $description;
    public $image_url;

    public function __construct($db) {
        $this->conn = $db;
    }

    // CREATE
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET product_name=:product_name, category=:category, 
                      price=:price, quantity=:quantity, description=:description, 
                      image_url=:image_url";
        
        $stmt = $this->conn->prepare($query);
        
        $this->product_name = htmlspecialchars(strip_tags($this->product_name));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));
        
        $stmt->bindParam(":product_name", $this->product_name);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":image_url", $this->image_url);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // READ ALL
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // READ SINGLE
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->product_name = $row['product_name'];
            $this->category = $row['category'];
            $this->price = $row['price'];
            $this->quantity = $row['quantity'];
            $this->description = $row['description'];
            $this->image_url = $row['image_url'];
            return true;
        }
        return false;
    }

    // UPDATE
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET product_name=:product_name, category=:category, 
                      price=:price, quantity=:quantity, 
                      description=:description, image_url=:image_url 
                  WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->product_name = htmlspecialchars(strip_tags($this->product_name));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        $stmt->bindParam(":product_name", $this->product_name);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":image_url", $this->image_url);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // DELETE
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // SEARCH
    public function search($keyword) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE product_name LIKE ? OR category LIKE ? OR description LIKE ?
                  ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $keyword = "%{$keyword}%";
        $stmt->bindParam(1, $keyword);
        $stmt->bindParam(2, $keyword);
        $stmt->bindParam(3, $keyword);
        $stmt->execute();
        return $stmt;
    }
}
?>
