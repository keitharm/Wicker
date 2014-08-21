<?php
require_once("Config.class.php");

class Database
{
    private $db;

    public function __construct() {
        $this->connect();
    }

    public function connect() {
        try {
            $this->db = new PDO("mysql:host=localhost;port=3306;dbname=" . Config::DB_NAME, Config::DB_USER, Config::DB_PASS);
            $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            echo "Uh oh, something bad has happened.";
            die;
        }
    }

    public function con() {
        return $this->db;
    }
}
?>
