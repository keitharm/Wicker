<?php
require_once("Wicker.php");

class AP
{
    private $id;
    private $scan_id;
    private $ind_scan_id;
    private $bssid;
    private $first_seen;
    private $last_seen;
    private $channel;
    private $privacy;
    private $cipher;
    private $authentication;
    private $power;
    private $beacons;
    private $ivs;
    private $essid;
    private $latitude;
    private $longitude;
    private $key;
    private $handshake;

    public $db;

    public static function newAP($scan, $bssid, $first, $last, $channel, $privacy, $cipher, $authentication, $power, $beacons, $ivs, $essid, $latitude, $longitude) {
        global $wicker;
        $instance = new self();
        $instance->connectToDatabase();

        $statement = $instance->db->con()->prepare("INSERT INTO `aps` (`scan_id`, `bssid`, `first_seen`, `last_seen`, `channel`, `privacy`, `cipher`, `authentication`, `power`, `beacons`, `ivs`, `essid`, `latitude`, `longitude`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $statement->execute(array($scan, $bssid, $first, $last, $channel, $privacy, $cipher, $authentication, $power, $beacons, $ivs, $essid, $latitude, $longitude));

        return AP::fromDB($scan, $bssid);
    }

    public static function fromDB($scan, $bssid) {
        global $wicker;
        $instance = new self();
        $instance->connectToDatabase();

        $statement = $instance->db->con()->prepare("SELECT * FROM `aps` WHERE `scan_id` = ? AND `bssid` = ?");
        $statement->execute(array($scan, $bssid));

        $info = $statement->fetchObject();

        $instance->id             = $info->id;
        $instance->scan_id        = $info->scan_id;
        $instance->ind_scan_id    = $info->ind_scan_id;
        $instance->bssid          = $info->bssid;
        $instance->first_seen     = $info->first_seen;
        $instance->last_seen      = $info->last_seen;
        $instance->channel        = $info->channel;
        $instance->privacy        = $info->privacy;
        $instance->cipher         = $info->cipher;
        $instance->authentication = $info->authentication;
        $instance->power          = $info->power;
        $instance->beacons        = $info->beacons;
        $instance->ivs            = $info->ivs;
        $instance->essid          = $info->essid;
        $instance->latitude       = $info->latitude;
        $instance->longitude      = $info->longitude;
        $instance->key            = $info->key;
        $instance->handshake      = $info->handshake;

        return $instance;
    }

    private function connectToDatabase() {
        global $wicker;
        $this->db = $wicker->db;
    }

    private function setVal($field, $val) {
        $statement = $this->db->con()->prepare("UPDATE `aps` SET `$field` = ? WHERE `id` = ?");
        $statement->execute(array($val, $this->getID()));
    }

    public function getID() { return $this->id; }
    public function getScanID() { return $this->scan_id; }
    public function getIndScanID() { return $this->ind_scan_id; }
    public function getBSSID() { return $this->bssid; }
    public function getFirstSeen() { return $this->first_seen; }
    public function getLastSeen() { return $this->last_seen; }
    public function getChannel() { return $this->channel; }
    public function getPrivacy() { return $this->privacy; }
    public function getCipher() { return $this->cipher; }
    public function getAuthentication() { return $this->authentication; }
    public function getPower() { return $this->power; }
    public function getBeacons() { return $this->beacons; }
    public function getIVs() { return $this->ivs; }
    public function getESSID() { return $this->essid; }
    public function getLatitude() { return $this->latitude; }
    public function getLongitude() { return $this->longitude; }
    public function getKey() { return $this->key; }
    public function getHandshake() { return $this->handshake; }

    public function setIndScanID($val) { $this->setVal("ind_scan_id", $val); $this->ind_scan_id = $val; }
    public function setLastSeen($val) { $this->setVal("last_seen", $val); $this->last_seen = $val; }
    public function setBeacons($val) { $this->setVal("beacons", $val); $this->beacons = $val; }
    public function setIVs($val) { $this->setVal("ivs", $val); $this->ivs = $val; }
    public function setLatitude($val) { $this->setVal("latitude", $val); $this->latitude = $val; }
    public function setLongitude($val) { $this->setVal("longitude", $val); $this->longitude = $val; }
    public function setPower($val) { $this->setVal("power", $val); $this->power = $val; }
    public function setKey($val) { $this->setVal("key", $val); $this->key = $val; }
    public function setHandshake($val) { $this->setVal("handshake", $val); $this->handshake = $val; }
}

?>
