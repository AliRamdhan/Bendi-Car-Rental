<?php

require 'config/config.php';

class Database {
    private $connection;

    public function __construct() {
        $config = Config::getDatabaseConfig();

        try {
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset=utf8";
            $this->connection = new PDO($dsn, $config['username'], $config['password']);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->connection;
    }

    public function closeConnection() {
        $this->connection = null;
    }
}

try {
    $db = new Database();
    $connection = $db->getConnection();

} catch (Exception $e) {
    echo $e->getMessage();
}