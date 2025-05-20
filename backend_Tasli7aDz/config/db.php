<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'tasli7a';
    private $username = 'root';
    private $password = '';
    public $pdo;

    public function getConnection() {
        $this->pdo = null;
        try {
            $this->pdo = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $exception) {
            die("Erreur de connexion à la base de données: " . $exception->getMessage());
        }

        return $this->pdo;
    }
}