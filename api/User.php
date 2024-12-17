<?php
class User {
    private $db;
    private $table = 'users';

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    public function createUser($username, $email, $password, $createdBy) {
        try {
            $query = "INSERT INTO users (username, email, password, created_by) 
                      VALUES (:username, :email, :password, :createdBy)";
            $stmt = $this->db->prepare($query);

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':createdBy', $createdBy);

            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Error creating user: " . $e->getMessage());
        }
    }

     
     public function login($loginIdentifier, $password) {
        try {
            
            $query = "SELECT * FROM users WHERE email = :identifier OR username = :identifier";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':identifier', $loginIdentifier);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                
                unset($user['password']); 
                return $user;
            } else {
                throw new Exception("Invalid email/username or password.");
            }
        } catch (PDOException $e) {
            throw new Exception("Error logging in: " . $e->getMessage());
        }
    }

    
    public function getUserById($id) {
        try {
            $query = "SELECT * FROM users WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching user: " . $e->getMessage());
        }
    }

    
    public function updateUser($id, $username, $email, $password, $updatedBy) {
        try {
            $query = "UPDATE users SET 
                        username = :username, 
                        email = :email, 
                        password = :password, 
                        updated_by = :updatedBy 
                      WHERE id = :id";
            $stmt = $this->db->prepare($query);

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':updatedBy', $updatedBy);
            $stmt->bindParam(':id', $id);

            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new Exception("Error updating user: " . $e->getMessage());
        }
    }

    
    public function deleteUser($id) {
        try {
            $query = "DELETE FROM users WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);

            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new Exception("Error deleting user: " . $e->getMessage());
        }
    }

    
    public function getAllUsers() {
        try {
            $query = "SELECT * FROM users ORDER BY created_at DESC";
            $stmt = $this->db->query($query);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching users: " . $e->getMessage());
        }
    }

    
    public function logout() {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            session_unset(); 
            session_destroy(); 
            
            return true; 
        } catch (Exception $e) {
            throw new Exception("Error logging out: " . $e->getMessage());
        }
    }

}