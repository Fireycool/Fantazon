<?php
class AccessDB {
    private $dbServername;
    private $dbUsername;
    private $dbPassword;
    private $dbName;
    private $conn;

    public function __construct($server, $username, $password, $database) {
        $this->dbServername = $server;
        $this->dbUsername = $username;
        $this->dbPassword = $password;
        $this->dbName = $database;
    }

    public function connectarse() {
        $this->conn = new mysqli($this->dbServername, $this->dbUsername, $this->dbPassword, $this->dbName);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function closeConnection() {
        $this->conn->close();
    }
}
?>
