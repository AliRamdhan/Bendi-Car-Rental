<?php
// config.php - Konfigurasi aplikasi untuk koneksi database

require 'vendor/autoload.php';

class Config {
    private static $envLoaded = false;

    public static function loadEnv() {
        if (!self::$envLoaded) {
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
            $dotenv->load();
            self::$envLoaded = true;
        }
    }

    public static function getDatabaseConfig() {
        self::loadEnv();

        return [
            'host' => $_ENV['DB_HOST'],
            'port' => $_ENV['DB_PORT'],
            'dbname' => $_ENV['DB_DATABASE'],
            'username' => $_ENV['DB_USERNAME'],
            'password' => $_ENV['DB_PASSWORD'],
        ];
    }
}