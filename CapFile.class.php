<?php
require_once("Wicker.php");

Class CapFile
{
    private $id;
    private $location;
    private $repaired;
    private $raw;
    private $checksum;
    private $complete;
    private $runtime;
    private $status;
    private $password;

    private $bssid;
    private $essid;
    private $packets;
    private $size;
    private $timestamp;

    public $db;

    public static function import($location) {
        $instance = new self();
        $instance->connectToDatabase();
        $instance->location = $location;

        // Fix pcap file if it needs to be repaired
        $instance->fixCap();

        // Analyze pcap file and extract info from it
        $instance->analyzeCap();

        // Insert record into DB
        $instance->addToDB();

        // Generate attack records
        $instance->generateAttackRecords();
        
        return $instance;
    }

    public function __construct($id) {
        global $wicker;
        $this->connectToDatabase();

        if (substr($id, 0, 3) == "[C]") {
            $id = substr($id, 3);
            $statement = $wicker->db->con()->prepare("SELECT * FROM `caps` WHERE `checksum` = ?");
        } else {
            $statement = $wicker->db->con()->prepare("SELECT * FROM `caps` WHERE `id` = ?");
        }

        $statement->execute(array($id));
        $info = $statement->fetchObject();

        $this->id        = $info->id;
        $this->location  = $info->location;
        $this->repaired  = $info->repaired;
        $this->raw       = $info->raw;
        $this->checksum  = $info->checksum;
        $this->complete  = $info->complete;
        $this->runtime   = $info->runtime;
        $this->status    = $info->status;
        $this->password  = $info->password;
        $this->timestamp = $info->timestamp;
        $this->size      = $info->size;

        $this->bssid     = $info->bssid;
        $this->essid     = $info->essid;
        $this->packets   = $info->packets;
    }

    private function connectToDatabase() {
        $database = new Database;
        $this->db = $database;
    }

    private function fixCap() {
        $this->changeToUploads();
        shell_exec("pcapfix " . $this->location);
        if (file_exists("fixed_" . $this->location)) {
            $this->location = "fixed_" . $this->location;
            $this->repaired = true;
        } else {
            $this->repaired = false;
        }
    }

    private function analyzeCap() {
        global $wicker;
        $this->changeToUploads();
        $this->raw          = shell_exec("pyrit -r " . $this->location . " analyze");
        $this->bssid        = $wicker->extractData($this->raw, "AccessPoint ", " ('");
        $this->essid        = $wicker->extractData($this->raw, "('", "'):");
        $this->packets      = $wicker->extractData($this->raw, "Parsed ", " packets");
        $this->checksum     = md5(file_get_contents($this->location));
        $this->size         = filesize($this->location);
    }

    private function addToDB() {
        global $wicker;
        // Check if this is a duplicate
        if (!$wicker->doesExist("caps", "checksum", $this->checksum)) {
            $statement = $this->db->con()->prepare("INSERT INTO `caps` (`essid`, `bssid`, `checksum`, `location`, `raw`, `packets`, `size`, `timestamp`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $statement->execute(array($this->essid, $this->bssid, $this->checksum, $this->location, $this->raw, $this->packets, $this->size, time()));
        } else {
            $cap = CapFile::fromDB("[C]" . $this->checksum);
            $wicker->error("This Cap File was imported " . $wicker->timeconv($cap->getTimestamp()) . ".", "Cap file hash: " . $this->checksum);
        }
    }

    private function changeToUploads() {
        if (basename(getcwd()) != "uploads") {
            chdir("uploads");
        }
    }

    private function generateAttackRecords() {
        $cap = CapFile::fromDB("[C]" . $this->getChecksum());
        for ($a = 1; $a <= 8; $a++) {
            $statement = $this->db->con()->prepare("INSERT INTO `attacks` (`cap_id`, `attack`) VALUES (?, ?)");
            $statement->execute(array($cap->getID(), $a));
        }
    }

    private function setVal($field, $val) {
        $statement = $this->db->con()->prepare("UPDATE `caps` SET `$field` = ? WHERE `id` = ?");
        $statement->execute(array($val, $this->getID()));
    }

    public function getTimestamp() { return $this->timestamp; }
    public function getLocation() { return $this->location; }
    public function getID() { return $this->id; }
    public function getChecksum() { return $this->checksum; }
    public function getESSID() { return $this->essid; }
    public function getBSSID() { return $this->bssid; }
    public function getPackets() { return $this->packets; }
    public function getSize() { return $this->size; }
    public function getStatus() { return $this->status; }
    public function getPassword() { return $this->password; }

    public function setStatus($val) { $this->setVal("status", $val); $this->status = $val; }
    public function setPassword($val) { $this->setVal("password", $val); $this->password = $val; }
}
