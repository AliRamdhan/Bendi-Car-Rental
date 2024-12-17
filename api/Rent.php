<?php
    class Rent {
        private $db;
        private $table = 'rents';  // Table name changed to 'rents' to match the DB schema

        public function __construct(Database $database) {
            $this->db = $database->getConnection();
        }

        // Create a new rental, including creating a new customer
        public function createRental($fullname, $kartu_type, $identity_number, $address, $phone_number, $car_id, $start_date, $end_date, $use_driver, $created_by, $total_payment) {
            try {
                // Start a transaction to ensure atomicity
                $this->db->beginTransaction();
                
                // Step 1: Insert new customer into the 'customers' table
                $query = "INSERT INTO customers (fullname, kartu_type, identity_number, address, phone_number) 
                        VALUES (:fullname, :kartu_type, :identity_number, :address, :phone_number)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':fullname', $fullname);
                $stmt->bindParam(':kartu_type', $kartu_type);
                $stmt->bindParam(':identity_number', $identity_number);
                $stmt->bindParam(':address', $address);
                $stmt->bindParam(':phone_number', $phone_number);

                if (!$stmt->execute()) {
                    // Rollback if customer creation fails
                    $this->db->rollBack();
                    return false;
                }

                // Get the generated customer ID
                $customer_id = $this->db->lastInsertId();

                // Step 2: Insert new rental into the 'rents' table using the customer ID
                $query = "INSERT INTO " . $this->table . " (car_id, customer_id, start_date, end_date, use_driver, created_by, total_payment) 
                VALUES (:car_id, :customer_id, :start_date, :end_date, :use_driver, :created_by, :total_payment)";
                            
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':car_id', $car_id);
                $stmt->bindParam(':customer_id', $customer_id);
                $stmt->bindParam(':start_date', $start_date);
                $stmt->bindParam(':end_date', $end_date);
                $stmt->bindParam(':use_driver', $use_driver);
                $stmt->bindParam(':created_by', $created_by);
                $stmt->bindParam(':total_payment', $total_payment);  // Fix here (replace total-payment with total_payment)
      
        
                if ($stmt->execute()) {
                    // Commit the transaction if both operations succeed
                    $this->db->commit();
                    return true;
                } else {
                    // Rollback if rental creation fails
                    $this->db->rollBack();
                    return false;
                }
            } catch (PDOException $e) {
                // Rollback on any error and log or return error message
                $this->db->rollBack();
                return "Database Error: " . $e->getMessage();
            }
        }

       
        public function getAllData() {
            $query = "SELECT r.*, 
                             c.fullname, c.phone_number, 
                             car.name AS car_model, car.description AS car_license_plate 
                      FROM " . $this->table . " r
                      INNER JOIN customers c ON r.customer_id = c.id
                      INNER JOIN cars car ON r.car_id = car.id";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getAllDataReport() {
            $query = "SELECT 
                         r.id AS rent_id,
                         c.fullname AS customer_name,
                         car.name AS car_model,
                         car.description AS car_license_plate,
                         r.start_date,
                         r.end_date,
                         COALESCE(ret.return_date, 'Belum Dikembalikan') AS return_date,
                         r.total_payment + COALESCE(ret.total_damage_fee, 0) AS total_payment
                      FROM rents r
                      INNER JOIN customers c ON r.customer_id = c.id
                      INNER JOIN cars car ON r.car_id = car.id
                      LEFT JOIN returns ret ON ret.rental_id = r.id";
        
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        

        public function updateRental($id, $fullname, $kartu_type, $identity_number, $car_id, $start_date, $end_date, $use_driver, $updated_by) {
            $query = "UPDATE " . $this->table . " SET 
                    fullname = :fullname,
                    kartu_type = :kartu_type,
                    identity_number = :identity_number,
                    car_id = :car_id,
                    start_date = :start_date,
                    end_date = :end_date,
                    use_driver = :use_driver,
                    updated_by = :updated_by,
                    updated_time = CURRENT_TIMESTAMP
                    WHERE id = :id";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':fullname', $fullname);
            $stmt->bindParam(':kartu_type', $kartu_type);
            $stmt->bindParam(':identity_number', $identity_number);
            $stmt->bindParam(':car_id', $car_id);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
            $stmt->bindParam(':use_driver', $use_driver);
            $stmt->bindParam(':updated_by', $updated_by);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        }

        public function deleteRental($id) {
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        }
    }
?>