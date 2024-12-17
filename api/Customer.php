<?php
class Customer {
    private $pdo;

    public function __construct($host, $dbname, $username, $password) {
        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    // Create a new customer
    public function create($fullname, $kartu_type, $identity_number, $address, $phone_number) {
        $sql = "INSERT INTO CUSTOMERS (fullname, kartu_type, identity_number, address, phone_number) VALUES (:fullname, :kartu_type, :identity_number, :address, :phone_number)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':fullname' => $fullname,
            ':kartu_type' => $kartu_type,
            ':identity_number' => $identity_number,
            ':address' => $address,
            ':phone_number' => $phone_number
        ]);
        return $this->pdo->lastInsertId();
    }

    // Read all customers
    public function readAll() {
        $sql = "SELECT * FROM CUSTOMERS";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Read a single customer by ID
    public function readById($id) {
        $sql = "SELECT * FROM CUSTOMERS WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update a customer by ID
    public function update($id, $fullname, $kartu_type, $identity_number, $address, $phone_number) {
        $sql = "UPDATE CUSTOMERS SET fullname = :fullname, kartu_type = :kartu_type, identity_number = :identity_number, address = :address, phone_number = :phone_number WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':fullname' => $fullname,
            ':kartu_type' => $kartu_type,
            ':identity_number' => $identity_number,
            ':address' => $address,
            ':phone_number' => $phone_number
        ]);
    }

    // Delete a customer by ID
    public function delete($id) {
        $sql = "DELETE FROM CUSTOMERS WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
