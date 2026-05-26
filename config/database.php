<?php
// config/database.php
class Database {
    private $host = "localhost";
    private $db_name = "devmanager_db";
    private $username = "root"; // Usuário padrão do XAMPP
    private $password = "";     // Senha padrão do XAMPP (vazia)
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4", $this->username, $this->password);
            // Configura o PDO para lançar exceções em caso de erro (Premium feature para debug)
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            echo "Erro de conexão: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>