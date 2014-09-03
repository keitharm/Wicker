<?php
require_once("Wicker.php");

class Scan
{
    private $id;
    private $guid;
    private $time;
    private $aps;
    private $clients;
    private $pid;

    public $db;

    public static function newScan() {
        global $wicker;
        $instance = new self();
        $instance->connectToDatabase();

        $guid = $wicker->newGUID();

        $statement = $instance->db->con()->prepare("INSERT INTO `scans` (`time`, `guid`) VALUES (?, ?)");
        $statement->execute(array(time(), $guid));

        // Rest a second
        sleep(1);
        return Scan::fromDB("[G]" . $guid);
    }

    public static function fromDB($id) {
        global $wicker;
        $instance = new self();
        $instance->connectToDatabase();

        if (substr($id, 0, 3) == "[G]") {
            $id = substr($id, 3);
            $statement = $instance->db->con()->prepare("SELECT * FROM `scans` WHERE `guid` = ?");
        } else {
            $statement = $instance->db->con()->prepare("SELECT * FROM `scans` WHERE `id` = ?");
        }

        $statement->execute(array($id));
        $info = $statement->fetchObject();

        $instance->id      = $info->id;
        $instance->guid    = $info->guid;
        $instance->time    = $info->time;
        $instance->aps     = $info->aps;
        $instance->clients = $info->clients;
        $instance->pid     = $info->pid;

        return $instance;
    }

    private function connectToDatabase() {
        $database = new Database;
        $this->db = $database;
    }

    private function setVal($field, $val) {
        $statement = $this->db->con()->prepare("UPDATE `scans` SET `$field` = ? WHERE `id` = ?");
        $statement->execute(array($val, $this->getID()));
    }

    public function mon0Enabled() {
        exec("/sbin/iwconfig mon0", $out);
        if (count($out) == 0) {
            return 0;
        }
        return 1;
    }

    public function startScan() {
        system("sudo airodump-ng -t wpa -t wpa2 -w \"scans/" . $this->getGUID() . "\" --output-format csv --ignore-negative-one mon0 > /dev/null &");
        exec("ps aux | grep '" . $this->getGUID() . "' | grep -v grep | awk '{ print $2 }' | tail -1", $out);
        $this->setPID($out[0]);
    }

    public function getID() { return $this->id; }
    public function getGUID() { return $this->guid; }
    public function getTime() { return $this->time; }
    public function getAPs() { return $this->aps; }
    public function getClients() { return $this->clients; }
    public function getPID() { return $this->pid; }

    public function setGUID($val) { $this->setVal("guid", $val); $this->guid = $val; }
    public function setTime($val) { $this->setVal("time", $val); $this->time = $val; }
    public function setAPs($val) { $this->setVal("aps", $val); $this->aps = $val; }
    public function setClients($val) { $this->setVal("clients", $val); $this->clients = $val; }
    public function setPID($val) { $this->setVal("pid", $val); $this->pid = $val; }
}

?>
