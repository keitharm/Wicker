<?php
require_once("Wicker.php");
require_once("AP.class.php");
require_once("Client.class.php");

class Scan
{
    private $id;
    private $guid;
    private $time;
    private $status;
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
        $instance->status  = $info->status;
        $instance->aps     = $info->aps;
        $instance->clients = $info->clients;
        $instance->pid     = $info->pid;

        return $instance;
    }

    private function connectToDatabase() {
        global $wicker;
        $this->db = $wicker->db;
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
        system("sudo airodump-ng -w \"scans/" . $this->getGUID() . "\" --output-format csv --ignore-negative-one mon0 > /dev/null &");
        exec("ps aux | grep 'sudo airodump-ng -w scans/" . $this->getGUID() . "' | grep -v grep | awk '{ print $2 }' | tail -1", $out);
        $this->setPID($out[0]);
    }

    public function parseCSV() {
        // Fetch scan's CSV and remove whitespace and unset extraneous lines.
        $csv = array_map('str_getcsv', file('scans/' . $this->getGUID() . '-01.csv'));
        unset($csv[0]);
        $csv = array_values($csv);
        foreach ($csv as &$line) {
            foreach ($line as &$val) {
                $val = trim($val);
            }
        }
        unset($csv[count($csv)-1]);
        unset($csv[0]);

        $aps = array();
        $clients = array();
        $a = 0;
        $mode = "ap";
        foreach ($csv as $group) {
            $a++;

            // Reached client section
            if (count($group) == 1) {
                $mode = "client";
                $a = 0;
                continue;
            }

            // AP section
            if ($mode == "ap") {
                $aps[$a]["bssid"]          = $group[0];
                $aps[$a]["first_seen"]     = $group[1];
                $aps[$a]["last_seen"]      = $group[2];
                $aps[$a]["channel"]        = $group[3];
                $aps[$a]["privacy"]        = $group[5];
                $aps[$a]["cipher"]         = $group[6];
                $aps[$a]["authentication"] = $group[7];
                $aps[$a]["power"]          = $group[8];
                $aps[$a]["beacons"]        = $group[9];
                $aps[$a]["ivs"]            = $group[10];
                $aps[$a]["essid"]          = $group[13];
            } else {
                $clients[$a]["mac"]        = $group[0];
                $clients[$a]["first_seen"] = $group[1];
                $clients[$a]["last_seen"]  = $group[2];
                $clients[$a]["power"]      = $group[3];
                $clients[$a]["packets"]    = $group[4];
                $clients[$a]["bssid"]      = $group[5];
                $clients[$a]["probed"]     = $group[6];
            }
        }
        $clients = array_values($clients);
        unset($clients[0]);

        return array("aps" => $aps, "clients" => $clients);
    }

    public function getAPs($sort = "id", $order = "DESC") {
        $statement = $this->db->con()->prepare("SELECT * FROM `aps` WHERE `scan_id` = ? ORDER BY $sort $order");
        $statement->execute(array($this->getID()));

        while ($info = $statement->fetchObject()) {
            $aps[] = AP::fromDB($info->scan_id, $info->bssid);
        }
        return $aps;
    }

    public function getClients($sort = "id", $order = "DESC") {
        $statement = $this->db->con()->prepare("SELECT * FROM `clients` WHERE `scan_id` = ? ORDER BY $sort $order");
        $statement->execute(array($this->getID()));

        while ($info = $statement->fetchObject()) {
            $clients[] = Client::fromDB($info->scan_id, $info->ap_id, $info->mac);
        }
        return $clients;
    }

    public function getID() { return $this->id; }
    public function getGUID() { return $this->guid; }
    public function getTime() { return $this->time; }
    public function getStatus() { return $this->status; }
    public function getAPCount() { return $this->aps; }
    public function getClientCount() { return $this->clients; }
    public function getPID() { return $this->pid; }

    public function setGUID($val) { $this->setVal("guid", $val); $this->guid = $val; }
    public function setTime($val) { $this->setVal("time", $val); $this->time = $val; }
    public function setStatus($val) { $this->setVal("status", $val); $this->status = $val; }
    public function setAPCount($val) { $this->setVal("aps", $val); $this->aps = $val; }
    public function setClientCount($val) { $this->setVal("clients", $val); $this->clients = $val; }
    public function setPID($val) { $this->setVal("pid", $val); $this->pid = $val; }
}

?>
