<?php
class Config
{
    private $config_raw;
    private $config;

    // database, username, password
    private $database;
    // user
    private $webserver;
    // rfkill, interface
    private $wireless;
    // aircrack-ng, pyrit
    private $tools;

    public function __construct() {
        $this->loadConfig();
        $this->parseConfig();
    }

    public function viewConfig() {
        define(W,  "\033[0m");
        define(G,  "\033[32m");
        define(C,  "\033[36m");
        echo "Wicker Configuration File Settings\n";
        echo "=====" . C . " Database " . W . "=====\n";
        echo "URL:\t\t" . G . $this->getDBURL() . W . "\n";
        echo "Database:\t" . G . $this->getDBName() . W . "\n";
        echo "Username:\t" . G . $this->getDBUser() . W . "\n";
        echo "Password:\t" . G . $this->getDBPass() . W . "\n\n";

        echo "=====" . C . " Webserver " . W . "=====\n";
        echo "User:\t\t" . G . $this->getUser() . W . "\n\n";

        echo "=====" . C . " Wireless " . W . "=====\n";
        echo "RFkill:\t\t" . G . $this->getRFkill() . W . "\n";
        echo "Interface:\t" . G . $this->getInterface() . W . "\n\n";

        echo "=====" . C . " Tools " . W . "=====\n";
        echo "Aircrack-ng:\t" . G . $this->getAircrackng() . W . "\n";
        echo "Pyrit:\t\t" . G . $this->getPyrit() . W . "\n";
        echo "WPAclean:\t" . G . $this->getWPAClean() . W . "\n";
        echo "PCAPfix:\t" . G . $this->getPCAPFix() . W . "\n";
    }

    private function loadConfig() {
        if (!file_exists("wicker.conf")) {
            echo "Wicker configuration file not found. Please run setup.php (via CLI) to configure Wicker.\n";
            die;
        }
        $this->config_raw = @file_get_contents("wicker.conf");
        $this->config = @unserialize(gzuncompress($this->config_raw)) or die ("Wicker configuration file appears to be corrupt! Please run setup.php (via CLI) to configure Wicker.\n");
    }

    private function parseConfig() {
        // Database
        $this->database["url"]       = $this->config["database"]["url"];
        $this->database["name"]      = $this->config["database"]["name"];
        $this->database["username"]  = $this->config["database"]["username"];
        $this->database["password"]  = $this->config["database"]["password"];

        // Webserver
        $this->webserver["user"]     = $this->config["webserver"]["user"];

        // Wireless
        $this->wireless["rfkill"]    = $this->config["wireless"]["rfkill"];
        $this->wireless["interface"] = $this->config["wireless"]["interface"];

        // Tools
        $this->tools["aircrack-ng"]  = $this->config["tools"]["aircrack-ng"];
        $this->tools["pyrit"]        = $this->config["tools"]["pyrit"];
        $this->tools["wpaclean"]     = $this->config["tools"]["wpaclean"];
        $this->tools["pcapfix"]      = $this->config["tools"]["pcapfix"];
    }

    public function getDBURL() { return $this->database["url"]; }
    public function getDBName() { return $this->database["name"]; }
    public function getDBUser() { return $this->database["username"]; }
    public function getDBPass() { return $this->database["password"]; }

    public function getUser() { return $this->webserver["user"]; }

    public function getRFkill() { return $this->wireless["rfkill"]; }
    public function getInterface() { return $this->wireless["interface"]; }

    public function getAircrackng() { return $this->tools["aircrack-ng"]; }
    public function getPyrit() { return $this->tools["pyrit"]; }
    public function getWPAClean() { return $this->tools["wpaclean"]; }
    public function getPCAPFix() { return $this->tools["pcapfix"]; }
}

$config = new Config;
?>
