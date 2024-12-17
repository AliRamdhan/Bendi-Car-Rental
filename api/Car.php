<?php
class Car {
    private $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }


    public function create($name, $description, $price, $driver_price) {
        $query = "INSERT INTO cars (name, description, price, driver_price) 
                  VALUES (:name, :description, :price, :driver_price)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':driver_price', $driver_price);
        
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }


    public function getAllCars() {
        $query = "SELECT * FROM cars";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getCarById($id) {
        $query = "SELECT * FROM cars WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function update($id, $name, $description, $price, $driver_price) {
        $query = "UPDATE cars SET name = :name, description = :description, price = :price, driver_price = :driver_price WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':driver_price', $driver_price);
        
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }


    public function delete($id) {
        $query = "DELETE FROM cars WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>