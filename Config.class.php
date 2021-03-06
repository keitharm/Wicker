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
    // airodump-ng, pyrit
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
        echo "Airodump-ng:\t" . G . $this->getAirodumpng() . W . "\n";
        echo "Pyrit:\t\t" . G . $this->getPyrit() . W . "\n";
        echo "PCAPfix:\t" . G . $this->getPCAPFix() . W . "\n";
        echo "MySQL:\t\t" . G . $this->getMySQL() . W . "\n";
        echo "lm-sensors:\t" . G . $this->getSensors() . W . "\n";
    }

    public function viewConfigSerialized() {
        $lines = explode("\n", $this->config_raw);
        if (count($lines) != 5 || !file_exists("wicker.conf.php")) {
            die("Wicker configuration file appears to be corrupt! Please run setup.php (via CLI) to configure Wicker.\n");
        } else {
            return substr($lines[2], 1);
        }
    }

    private function loadConfig() {
        if (!file_exists("wicker.conf.php")) {
            echo "Wicker configuration file not found. Please run setup.php (via CLI) to configure Wicker.\n";
            die;
        }
        $this->config_raw = @file_get_contents("wicker.conf.php");
        $lines = explode("\n", $this->config_raw);
        if (count($lines) != 5 || !file_exists("wicker.conf.php")) {
            die("Wicker configuration file appears to be corrupt! Please run setup.php (via CLI) to configure Wicker.\n");
        } else {
            $this->config = unserialize(substr($lines[2], 1));
        }
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
        $this->tools["airodump-ng"]  = $this->config["tools"]["airodump-ng"];
        $this->tools["pyrit"]        = $this->config["tools"]["pyrit"];
        $this->tools["pcapfix"]      = $this->config["tools"]["pcapfix"];
        $this->tools["mysql"]        = $this->config["tools"]["mysql"];
        $this->tools["lm-sensors"]   = $this->config["tools"]["lm-sensors"];
    }

    public function updateConfig() {
        file_put_contents("wicker.conf.php", "<?php\ndie;\n#" . serialize($this->config) . "\n?>\n");
    }

    /* Getters */
    public function getDBURL() { return $this->database["url"]; }
    public function getDBName() { return $this->database["name"]; }
    public function getDBUser() { return $this->database["username"]; }
    public function getDBPass() { return $this->database["password"]; }

    public function getUser() { return $this->webserver["user"]; }

    public function getRFkill() { return $this->wireless["rfkill"]; }
    public function getInterface() { return $this->wireless["interface"]; }

    public function getAirodumpng() { return $this->tools["airodump-ng"]; }
    public function getPyrit() { return $this->tools["pyrit"]; }
    public function getPCAPFix() { return $this->tools["pcapfix"]; }
    public function getMySQL() { return $this->tools["mysql"]; }
    public function getSensors() { return $this->tools["lm-sensors"]; }

    /* Setters */
    public function setDBURL($val) { $this->config["database"]["url"]           = $val; $this->updateConfig(); }
    public function setDBName($val) { $this->config["database"]["name"]         = $val; $this->updateConfig(); }
    public function setDBUser($val) { $this->config["database"]["username"]     = $val; $this->updateConfig(); }
    public function setDBPass($val) { $this->config["database"]["password"]     = $val; $this->updateConfig(); }

    public function setUser($val) { $this->config["webserver"]["user"]          = $val; $this->updateConfig(); }

    public function setRFkill($val) { $this->config["wireless"]["rfkill"]       = $val; $this->updateConfig(); }
    public function setInterface($val) { $this->config["wireless"]["interface"] = $val; $this->updateConfig(); }

    public function setAirodumpng($val) { $this->config["tools"]["airodump-ng"]     = $val; $this->updateConfig(); }
    public function setPyrit($val) { $this->config["tools"]["pyrit"]                = $val; $this->updateConfig(); }
    public function setPCAPFix($val) { $this->config["tools"]["pcapfix"]            = $val; $this->updateConfig(); }
    public function setMySQL($val) { $this->config["tools"]["mysql"]                = $val; $this->updateConfig(); }
    public function setSensors($val) { $this->config["tools"]["lm-sensors"]         = $val; $this->updateConfig(); }
}

$config = new Config;
?>
