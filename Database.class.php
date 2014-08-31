<?php
require_once("Config.class.php");

class Database
{
    private $db;

    public function __construct() {
        $this->connect();
    }

    public function connect() {
        global $config;
        try {
            $this->db = new PDO("mysql:host=" . $config->getDBURL() . ";port=3306;dbname=" . $config->getDBName(), $config->getDBUser(), $config->getDBPass());
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
