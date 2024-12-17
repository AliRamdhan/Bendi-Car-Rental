<?php
class Returns {
    private $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    // Create a new return record
    public function createReturn(
        $rentalId,
        $returnDate,
        $late_days,
        $isDamage,
        $damage_description = null,
        $damageFee = null,
        $totalDamageFee
    ) {
        $query = "INSERT INTO returns 
                  (rental_id, return_date, late_days, isDamage, damage_description, damage_fee, total_damage_fee)
                  VALUES 
                  (:rental_id, :return_date, :late_days, :isDamage, :damage_description, :damage_fee, :total_damage_fee)";
        $stmt = $this->db->prepare($query);
    
        // Bind parameters
        $stmt->bindParam(':rental_id', $rentalId);
        $stmt->bindParam(':return_date', $returnDate);
        $stmt->bindParam(':late_days', $late_days);
        $stmt->bindParam(':isDamage', $isDamage);
        $stmt->bindParam(':damage_description', $damage_description);
        $stmt->bindParam(':damage_fee', $damageFee);
        $stmt->bindParam(':total_damage_fee', $totalDamageFee);
    
        // Execute and return the result
        return $stmt->execute();
    }
    

    // Read all returns
    public function getAllReturns() {
        $query = "SELECT * FROM returns";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update return record
    public function updateReturn($id, $rentalId, $tanggalPengembalian, $keterlambatanHari, $kerusakan, $deskripsiKerusakan = null, $biayaKerusakan = null) {
        $query = "UPDATE returns SET rental_id = :rental_id, tanggal_pengembalian = :tanggal_pengembalian, keterlambatan_hari = :keterlambatan_hari, 
                  kerusakan = :kerusakan, deskripsi_kerusakan = :deskripsi_kerusakan, biaya_kerusakan = :biaya_kerusakan WHERE id = :id";
        $stmt = $this->db->prepare($query);

        // Bind values
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':rental_id', $rentalId);
        $stmt->bindParam(':tanggal_pengembalian', $tanggalPengembalian);
        $stmt->bindParam(':keterlambatan_hari', $keterlambatanHari);
        $stmt->bindParam(':kerusakan', $kerusakan);
        $stmt->bindParam(':deskripsi_kerusakan', $deskripsiKerusakan);
        $stmt->bindParam(':biaya_kerusakan', $biayaKerusakan);

        // Execute
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete return record
    public function deleteReturn($id) {
        $query = "DELETE FROM returns WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);

        // Execute
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Get a specific return by ID
    public function getReturnById($id) {
        $query = "SELECT * FROM returns WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>