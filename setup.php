<?php
// Color Codes
define(W,  "\033[0m");
define(R,  "\033[31m");
define(G,  "\033[32m");
define(O,  "\033[33m");
define(B,  "\033[34m");
define(P,  "\033[35m");
define(C,  "\033[36m");

echo "__        ___      _\n";
echo "\ \      / (_) ___| | _____ _ __\n";
echo " \ \ /\ / /| |/ __| |/ / _ \ '__|\n";
echo "  \ V  V / | | (__|   <  __/ |\n";
echo "   \_/\_/  |_|\___|_|\_\___|_|\n";
echo "Version 1.0.0\n\n";

if ($argv[1] == "--view-config") {
    if (file_exists("wicker.conf")) {
        require_once("Config.class.php");
        $config->viewConfig();
        die;
    } else {
        echo R . "Wicker configuration file not found.\n" . W;
        die;
    }
} else {
    if (isset($argv[1])) {
        echo R . "Invalid argument\n" . W . "Available options are: " . G . "--view-config" . W . ".\n";
        die;
    }
}

// Check for configuration file.
if (file_exists("wicker.conf")) {
    echo O . "Warning" . W . ": There already appears to be a Wicker configuration file.\nIf you continue with the setup, the configuration file will be overwritten.\nYou may view your current configuration by typing " . G . "php setup.php --view-config" . W . ".\n\n" . W;
}

// Check if Wicker configuration file is corrupt.
$tmp = @unserialize(gzuncompress(file_get_contents("wicker.conf")));
if ($tmp == null) {
    echo R . "Error" . W . ": Wicker configuration file appears to be corrupt!\n\n";
}

// Perform dependencies check
checkForDependencies();

echo "\nWelcome to the Wicker Setup script.\n\n";
echo "This script will help you configure all of Wicker's settings.\n";
echo "If you wish to keep the default settings to a question (the value between [ ]),\nkeep your answer blank.\n\n";

// Database
echo "=====" . C . " Database " . W . "=====\n";
$database = false;
do {
    echo W . "Database URL [localhost]:\t";
    $data["database"]["url"] = input("localhost");
    echo W . "Database name:\t\t\t";
    $data["database"]["name"] = input();
    echo W . "Database username:\t\t";
    $data["database"]["username"] = input();
    echo W . "Database password:\t\t";
    $data["database"]["password"] = input();
    echo W . "Attempting to connect to Database...\n";
    $database = connectToDatabase();
    if ($database == true) {
        echo "Are these settings ok?\t\t";
        if (!in_array(strtolower(input()), array("yes", "y"))) {
            $database = false;
        }
    }
} while ($database == false);

// Webserver user
echo W . "\n=====" . C . " Webserver User " . W . "=====\n";
$webserver = false;
do {
    echo W . "Webserver User:\t\t\t";
    $data["webserver"]["user"] = input();
    echo W . "Are these settings ok?\t\t";
    if (!in_array(strtolower(input()), array("yes", "y"))) {
        $webserver = false;
    } else {
        $webserver = true;
    }
} while ($webserver == false);

// Wireless
echo W . "\n=====" . C . " Wireless " . W . "=====\n";
$wireless = false;
do {
    echo W . "Does rfkill block your wifi card? [true]:\t";
    $data["wireless"]["rfkill"] = input("true");
    echo W . "Wireless interface to use [wlan0]:\t\t";
    $data["wireless"]["interface"] = input("wlan0");
    echo W . "Are these settings ok?\t\t";
    if (!in_array(strtolower(input()), array("yes", "y"))) {
        $wireless = false;
    } else {
        $wireless = true;
    }
} while ($wireless == false);

// Tools
echo W . "\n=====" . C . " Tools " . W . "=====\n";
$tools = false;
do {
    echo W . "aircrack-ng location? [" . exec("which aircrack-ng") . "]:\t";
    $data["tools"]["aircrack-ng"] = input(exec("which aircrack-ng"));
    echo W . "pyrit location? [" . exec("which pyrit") . "]:\t";
    $data["tools"]["pyrit"] = input(exec("which pyrit"));
    echo W . "wpaclean location? [" . exec("which wpaclean") . "]:\t";
    $data["tools"]["wpaclean"] = input(exec("which wpaclean"));
    echo W . "pcapfix location? [" . exec("which pcapfix") . "]:\t";
    $data["tools"]["pcapfix"] = input(exec("which pcapfix"));
    echo W . "Are these settings ok?\t\t";
    if (!in_array(strtolower(input()), array("yes", "y"))) {
        $tools = false;
    } else {
        $tools = true;
    }
} while ($tools == false);

// Generate conf file
echo W . "\nGenerating Wicker Configuration file";
echo ".";
sleep(1);
echo ".";
sleep(1);
echo ".";
sleep(1);   
file_put_contents("wicker.conf", gzcompress(serialize($data)));
if (!file_exists("wicker.conf")) {
    file_put_contents("/tmp/wicker.conf", gzcompress(serialize($data)));
    echo R . "Error" . W . ": Unable to save configuration file.\nPlease copy file /tmp/wicker.conf to this directory manually to complete setup.\n";
} else {
    echo G . "\nWicker Configuration file saved successfully!\n";
}

function input($default = null) {
    echo O;
    $var = trim(fgets(STDIN));
    if ($var == null) {
        return $default;
    } else {
        return $var;
    }
}

function connectToDatabase() {
    global $data;
    sleep(1);
    try {
        $dbh = new PDO('mysql:host=' . $data["database"]["url"] . ';dbname=' . $data["database"]["name"], $data["database"]["username"], $data["database"]["password"], array(PDO::ATTR_TIMEOUT => "3"));
        echo G . "Connection successful!" . W . "\n\n";
        return true;
    } catch (PDOException $e) {
        echo R . "Unable to connect to database..." . W . "\n\n";
        return false;
    }
}

function checkForDependencies() {
    $bad = false;
    echo "Performing dependencies check...\n";
    sleep(1);
    // Check for PDO
    if (!defined('PDO::ATTR_DRIVER_NAME')) {
        echo R . "✗ PDO\n" . W;
        $bad = true;
    } else {
        echo G . "✓ PDO\n" . W;
    }
    usleep(250000);

    // Check for Pyrit
    if (!command_exist("pyrit")) {
        echo R . "✗ Pyrit\n" . W;
        $bad = true;
    } else {
        echo G . "✓ Pyrit\n" . W;
    }
    usleep(250000);

    // Check for Aircrack-ng
    if (!command_exist("aircrack-ng")) {
        echo R . "✗ Aircrack-ng\n" . W;
        $bad = true;
    } else {
        echo G . "✓ Aircrack-ng\n" . W;
    }
    usleep(250000);

    // Check for Pyrit
    if (!command_exist("pcapfix")) {
        echo R . "✗ Pcapfix\n" . W;
        $bad = true;
    } else {
        echo G . "✓ Pcapfix\n" . W;
    }
    usleep(250000);

    if ($bad) {
        echo R . "Error" . W . ": Your system is missing some dependencies that Wicker...depends on.\nPlease install the missing dependencies before attempting setup again.\n";
        die;
    } else {
        echo G . "Everything looks good :)\n" . W;
    }
}

function command_exist($cmd) {
    $returnVal = shell_exec("which $cmd");
    return (empty($returnVal) ? false : true);
}
?>
