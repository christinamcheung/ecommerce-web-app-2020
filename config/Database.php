<?php

class Database
{
    // Database credentials.
    private $dbname = 'csci3172g1';
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function connect()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';', $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return null;
        }
        // Return database connection.
        return $this->conn;
    }
}