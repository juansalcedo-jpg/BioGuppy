<?php

class Database {
    private $host     = "localhost";
    private $db_name  = "brangus_inventario";
    private $username = "root";
    private $password = "";
    public  $conn;

    public function __construct() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("SET NAMES 'utf8mb4'");
        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
        }
    }
}