<?php
require_once("Wicker.php");

class Client
{
    private $id;
    private $scan_id;
    private $ap_id;
    private $mac;
    private $first_seen;
    private $last_seen;
    private $power;
    private $packets;
    private $bssid;
    private $probed;

    public $db;

    public static function newClient($scan, $ap, $mac, $first, $last, $power, $packets, $bssid, $probed) {
        global $wicker;
        $instance = new self();
        $instance->connectToDatabase();

        $statement = $instance->db->con()->prepare("INSERT INTO `clients` (`scan_id`, `ap_id`, `mac`, `first_seen`, `last_seen`, `power`, `packets`, `bssid`, `probed`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $statement->execute(array($scan, $ap, $mac, $first, $last, $power, $packets, $bssid, $probed));

        return Client::fromDB($scan, $ap, $mac);
    }

    public static function fromDB($scan, $ap, $mac) {
        global $wicker;
        $instance = new self();
        $instance->connectToDatabase();

        $statement = $instance->db->con()->prepare("SELECT * FROM `clients` WHERE `scan_id` = ? AND `ap_id` = ? AND `mac` = ?");
        $statement->execute(array($scan, $ap, $mac));

        $info = $statement->fetchObject();

        $instance->id         = $info->id;
        $instance->scan_id    = $info->scan_id;
        $instance->ap_id      = $info->ap_id;
        $instance->mac        = $info->mac;
        $instance->first_seen = $info->first_seen;
        $instance->last_seen  = $info->last_seen;
        $instance->power      = $info->power;
        $instance->packets    = $info->packets;
        $instance->bssid      = $info->bssid;
        $instance->probed     = $info->probed;

        return $instance;
    }

    private function connectToDatabase() {
        global $wicker;
        $this->db = $wicker->db;
    }

    private function setVal($field, $val) {
        $statement = $this->db->con()->prepare("UPDATE `clients` SET `$field` = ? WHERE `id` = ?");
        $statement->execute(array($val, $this->getID()));
    }

    public function getID() { return $this->id; }
    public function getScanID() { return $this->scan_id; }
    public function getAPID() { return $this->ap_id; }
    public function getMac() { return $this->mac; }
    public function getFirstSeen() { return $this->first_seen; }
    public function getLastSeen() { return $this->last_seen; }
    public function getPower() { return $this->power; }
    public function getPackets() { return $this->packets; }
    public function getBSSID() { return $this->bssid; }
    public function getProbed() { return $this->probed; }

    public function setLastSeen($val) { $this->setVal("last_seen", $val); $this->last_seen = $val; }
    public function setPackets($val) { $this->setVal("packets", $val); $this->packets = $val; }
    public function setProbed($val) { $this->setVal("probed", $val); $this->probed = $val; }
}

?>
