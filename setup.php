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
echo "Version 1.2.0\n\n";

if ($argv[1] == "--view-config") {
    if (file_exists("wicker.conf.php")) {
        require_once("Config.class.php");
        $config->viewConfig();
        die;
    } else {
        echo R . "Wicker configuration file not found.\n" . W;
        die;
    }
} else if ($argv[1] == "--view-config-serialized") {
    if (file_exists("wicker.conf.php")) {
        require_once("Config.class.php");
        echo $config->viewConfigSerialized() . "\n";
        die;
    } else {
        echo R . "Wicker configuration file not found.\n" . W;
        die;
    }
} else if ($argv[1] == "--empty-dirs") {
    emptydirs();
    echo G . "logs, uploads, and scans have been deleted successfully!\n" . W;
    die;
} else if ($argv[1] == "--reset-db") {
    resetdb();
    die;
} else {
    if (isset($argv[1])) {
        echo R . "Invalid argument\n" . W . "Available options are:\n" . G . "--view-config" . W . "\n" . G . "--empty-dirs" . W . "\n" . G . "--reset-db" . W . "\n" . G . "--view-config-serialized" . W . "\n";
        die;
    }
}

// Check for configuration file.
if (file_exists("wicker.conf.php")) {
    echo O . "Warning" . W . ": There already appears to be a Wicker configuration file.\nIf you continue with the setup, the configuration file will be overwritten.\nYou may view your current configuration by typing " . G . "php setup.php --view-config" . W . ".\n\n" . W;
}

// Check if Wicker configuration file is corrupt.
$config_raw = @file_get_contents("wicker.conf.php");
$lines = explode("\n", $config_raw);
if (count($lines) != 5 && file_exists("wicker.conf.php")) {
    echo R . "Error" . W . ": Wicker configuration file appears to be corrupt!\n\n";
}

// Perform dependencies check
checkForDependencies();

echo "\nWelcome to the Wicker Setup script.\n\n";
echo "This script will help you configure all of Wicker's settings.\n";
echo "If you wish to keep the default settings (the value between [ ]),\nkeep your answer blank.\n\n";

// Database
echo "=====" . C . " Database Connection " . W . "=====\n";
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

    echo W . "Attempting to connect to MySQL Server...\n";
    $database = connectToDatabase();
    if ($database == true) {
        echo "Are these settings ok?\t\t";
        if (!in_array(strtolower(input()), array("yes", "y"))) {
            $database = false;
        }
    }
} while ($database == false);

echo W . "=====" . C . " Database Setup " . W . "=====\n";
// if true, ask if you want to import tables else ask if you want to create database and import tables.
setupDatabase();

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
$rfkill = false;
do {
    do {
        echo W . "Does rfkill block your wifi card? [true]:\t";
        $data["wireless"]["rfkill"] = strtolower(input("true"));

        if (in_array($data["wireless"]["rfkill"], array("true", "false", "yes", "no", "y", "n"))) {
            $rfkill = true;
            if (in_array($data["wireless"]["rfkill"], array("true", "yes", "y"))) {
                $data["wireless"]["rfkill"] = "true";
            } else {
                $data["wireless"]["rfkill"] = "false";
            }
        }
    } while ($rfkill == false);

    echo W . "Wireless interface to use [wlan0]:\t\t";
    $data["wireless"]["interface"] = input("wlan0");

    echo W . "Are these settings ok?\t\t\t\t";
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
    echo W . "airodump-ng location? [" . exec("which airodump-ng") . "]:\t";
    $data["tools"]["airodump-ng"] = input(exec("which airodump-ng"));

    echo W . "pyrit location? [" . exec("which pyrit") . "]:\t";
    $data["tools"]["pyrit"] = input(exec("which pyrit"));

    echo W . "pcapfix location? [" . exec("which pcapfix") . "]:\t";
    $data["tools"]["pcapfix"] = input(exec("which pcapfix"));

    echo W . "mysql location? [" . exec("which mysql") . "]:\t";
    $data["tools"]["mysql"] = input(exec("which mysql"));

    echo W . "lm-sensors location? [" . exec("which sensors") . "]:\t";
    $data["tools"]["lm-sensors"] = input(exec("which sensors"));

    echo W . "Are these settings ok?\t\t";
    if (!in_array(strtolower(input()), array("yes", "y"))) {
        $tools = false;
    } else {
        $tools = true;
    }
} while ($tools == false);

// Set up directories for Wicker
echo W . "\n=====" . C . " Initial Directory Setup " . W . "=====\n";

echo "Creating directories\n";
echo "logs";
pause(false);
@mkdir("logs");
echo G . "done\n" . W;

echo "scans";
pause(false);
@mkdir("scans");
echo G . "done\n" . W;

echo "uploads";
pause(false);
@mkdir("uploads");
echo G . "done\n\n" . W;

echo "Setting up permissions for web user\n";
echo "Setting " . $data["webserver"]["user"] . " as group for directories";
pause(false);
exec("sudo chgrp " . $data["webserver"]["user"] . " logs scans uploads");
echo G . "done\n" . W;

echo "Setting permissions to 775 for directories";
pause(false);
exec("sudo chmod 775 logs scans uploads");
echo G . "done\n" . W;

echo "Setting group bit for directories";
pause(false);
exec("sudo chmod g+s logs scans uploads");
echo G . "done\n" . W;

// Generate conf file
echo W . "\nGenerating Wicker Configuration file";
pause();
file_put_contents("wicker.conf.php", "<?php\ndie;\n#" . serialize($data) . "\n?>\n");
if (!file_exists("wicker.conf.php")) {
    file_put_contents("/tmp/wicker.conf.php", "<?php\ndie;\n#" . serialize($data) . "\n?>\n");
    echo R . "Error" . W . ": Unable to save configuration file.\nPlease copy file /tmp/wicker.conf.php to this directory manually to complete setup.\n";
} else {
    echo G . "\nWicker Configuration file saved successfully!\n" . W;
}
echo "Setting group to " . $data["webserver"]["user"] . " for Wicker Configuration File";
pause(false);
exec("sudo chgrp " . $data["webserver"]["user"] . " wicker.conf.php");
echo G . "done\n" . W;

echo "Setting permissions to 775 for Wicker Configuration File";
pause(false);
exec("sudo chmod 775 wicker.conf.php");
echo G . "done\n\n" . W;
echo G . "Wicker Setup Complete!\n" . W;

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
        $dbh = new PDO('mysql:host=' . $data["database"]["url"], $data["database"]["username"], $data["database"]["password"], array(PDO::ATTR_TIMEOUT => "3"));
        echo G . "Connection successful!" . W . "\n\n";
        return true;
    } catch (PDOException $e) {
        echo R . "Unable to connect to server!" . W . "\n\n";
        return false;
    }
}

function setupDatabase() {
    global $data;
    echo "Checking if database exists";
    pause();

    $dbh = new PDO('mysql:host=' . $data["database"]["url"] . ';dbname=INFORMATION_SCHEMA', $data["database"]["username"], $data["database"]["password"], array(PDO::ATTR_TIMEOUT => "3"));
    // Check if database exists
    $query = $dbh->query("SELECT * FROM `SCHEMATA` WHERE `SCHEMA_NAME` = '" . $data["database"]["name"] . "'");
    if ($query->rowCount() == 0) {
        echo R . "Database was not found (or your credentials don't have valid permissions)\n\n" . W;
        $found = false;
    } else {
        echo G . "Database was found\n\n" . W;
        $found = true;
    }
    if ($found) {
        // Check for tables
        echo "Checking tables";
        pause();
        $dbh = new PDO('mysql:host=' . $data["database"]["url"] . ';dbname=' . $data["database"]["name"], $data["database"]["username"], $data["database"]["password"], array(PDO::ATTR_TIMEOUT => "3"));
        $query = $dbh->query("show tables");
        while ($results = $query->fetch()) {
            $tables[] = $results[0];
        }
        if ($tables == null) {
            echo G . "Database is empty" . W . "\n";
            importWickerTables(0, 0);
        } else {
            echo O . "Database is occupied" . W . "\n\n";
            echo "Listing tables in database.\n" . G . "Green" . W . " = Wicker tables\n" . R . "Red  " . W . " = Other tables\n----------\n";
            $good = 0; $bad = 0;
            foreach ($tables as $table) {
                if (in_array($table, array("aps", "attacks", "caps", "clients", "scans"))) {
                    echo G . $table . W . "\n";
                    $good++;
                } else {
                    echo R . $table . W . "\n";
                    $bad++;
                }
            }
            echo "----------\n";
            importWickerTables($good, $bad);
        }
        print_r($results);
    } else {
        $dbh = new PDO('mysql:host=' . $data["database"]["url"], $data["database"]["username"], $data["database"]["password"], array(PDO::ATTR_TIMEOUT => "3"));
        echo "Attemping to create database";
        pause();
        $dbh->query("CREATE DATABASE IF NOT EXISTS " . $data["database"]["name"]);
        if ($dbh->errorCode() != "00000") {
            echo R . "An error has occured while attempting to create the database.\nPlease verify that the credentials supplied have valid database creation permissions." . W . "\n";
            die;
        } else {
            echo G . "Database " . O . $data["database"]["name"] . G . " was created successfully!" . W . "\n";
        }

        // Import tables
        importWickerTables(0, 0);
    }
}

function checkForDependencies() {
    $bad = false;
    echo "Performing dependencies check\n";
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

    // Check for Airodump-ng
    if (!command_exist("airodump-ng")) {
        echo R . "✗ Airodump-ng\n" . W;
        $bad = true;
    } else {
        echo G . "✓ Airodump-ng\n" . W;
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

    // Check for MySQL
    if (!command_exist("mysql")) {
        echo O . "✗ MySQL\n" . W;
        $bad = true;
    } else {
        echo G . "✓ MySQL\n" . W;
    }
    usleep(250000);

    // Check for lm-sensrs
    if (!command_exist("sensors")) {
        echo O . "- lm-sensors\n" . W;
        $sensors = false;
    } else {
        echo G . "✓ lm-sensors\n" . W;
        $sensors = true;
    }
    usleep(250000);

    if ($bad) {
        echo R . "Error" . W . ": Your system is missing some dependencies that Wicker...depends on.\nPlease install the missing dependencies before attempting setup again.\n";
        die;
    } else {
        if (!$sensors) {
            echo W . "Warning" . W . ": lm-sensors is not required but it provides temperature information for your CPU.\n";
        }
        echo "\n" . G . "Everything looks good :)\n" . W;
    }
}

function command_exist($cmd) {
    $returnVal = shell_exec("which $cmd");
    return (empty($returnVal) ? false : true);
}

function pause($return = true) {
    echo ".";
    usleep(250000);
    echo ".";
    usleep(250000);
    echo ".";
    usleep(250000);
    if ($return) {
        echo "\n";
    }
}

function importWickerTables($good, $bad) {
    global $data;
    echo W . "=====" . C . " Database Setup " . W . "=====\n";
    if ($good == 5) {
        $table = false;
        do {
            echo W . "There already appear to be Wicker tables in this database.\nWhat would you like to do?\n\n";
            echo "(" . O . "D" . W . ")rop tables and import latest Wicker table structure.\n";
            echo "(" . O . "C" . W . ")ontinue without modifying tables.\n";
            echo "Choice: ";
            $choice = input();
            if (in_array(strtolower($choice), array('c', 'd'))) {
                $table = true;
            }
        } while ($table == false);

        if (strtolower($choice) == "c") {
            echo W . "Continuing without modifying tables.\n";
            return;
        } else {
            echo W . "Dropping old tables";
            pause();
            $dbh = new PDO('mysql:host=' . $data["database"]["url"] . ';dbname=' . $data["database"]["name"], $data["database"]["username"], $data["database"]["password"], array(PDO::ATTR_TIMEOUT => "3"));
            //"aps", "attacks", "caps", "clients", "scans"
            $dbh->query("DROP TABLE `aps`, `attacks`, `caps`, `clients`, `scans`");
            if ($dbh->errorCode() != "00000") {
                echo R . "An error has occured while attempting to drop old Wicker tables.\nPlease verify that the credentials supplied have valid database permissions." . W . "\n";
                die;
            }
            echo W . "Importing new Wicker tables";
            pause();
            exec("mysql -u " . $data["database"]["username"] . " -p" . $data["database"]["password"] . " -D " . $data["database"]["name"] . " < sql/wicker.sql", $output, $code);
            if ($code != 0) {
                echo R . "An error has occured while attempting to import new Wicker tables.\nPlease verify that the credentials supplied have valid database permissions." . W . "\n";
                die;
            } else {
                echo G . "Wicker tables imported successfully!\n";
            }
            return;
        }
    //Database is empty
    } else if ($good == 0 && $bad == 0) {
        echo W . "Importing Wicker tables";
        pause();
        exec("mysql -u " . $data["database"]["username"] . " -p" . $data["database"]["password"] . " -D " . $data["database"]["name"] . " < sql/wicker.sql", $output, $code);
        if ($code != 0) {
            echo R . "An error has occured while attempting to import new Wicker tables.\nPlease verify that the credentials supplied have valid database permissions." . W . "\n";
            die;
        } else {
            echo G . "Wicker tables imported successfully!\n";
        }
        return;
    } else {
        echo W . "Something weird happened\n";
        die;
    }
}

function emptydirs() {
    exec("rm -rf logs/* 2>/dev/null");
    exec("rm -rf uploads/* 2>/dev/null");
    exec("rm -rf scans/* 2>/dev/null");
}

function resetdb() {
    global $data;
    require_once("Config.class.php");
    $data["database"]["url"] = $config->getDBURL();
    $data["database"]["username"] = $config->getDBUser();
    $data["database"]["password"] = $config->getDBPass();
    $data["database"]["name"] = $config->getDBName();
    echo "Checking if database exists";
    pause();

    $dbh = new PDO('mysql:host=' . $data["database"]["url"] . ';dbname=INFORMATION_SCHEMA', $data["database"]["username"], $data["database"]["password"], array(PDO::ATTR_TIMEOUT => "3"));
    // Check if database exists
    $query = $dbh->query("SELECT * FROM `SCHEMATA` WHERE `SCHEMA_NAME` = '" . $data["database"]["name"] . "'");
    if ($query->rowCount() == 0) {
        echo R . "Database was not found (or your credentials don't have valid permissions)\n\n" . W;
        $found = false;
    } else {
        echo G . "Database was found\n\n" . W;
        $found = true;
    }
    if ($found) {
        // Check for tables
        echo "Checking tables";
        pause();
        $dbh = new PDO('mysql:host=' . $data["database"]["url"] . ';dbname=' . $data["database"]["name"], $data["database"]["username"], $data["database"]["password"], array(PDO::ATTR_TIMEOUT => "3"));
        $query = $dbh->query("show tables");
        while ($results = $query->fetch()) {
            $tables[] = $results[0];
        }
        if ($tables == null) {
            echo G . "Database is empty" . W . "\n";
            importWickerTables(0, 0);
        } else {
            echo O . "Database is occupied" . W . "\n\n";
            echo "Listing tables in database.\n" . G . "Green" . W . " = Wicker tables\n" . R . "Red  " . W . " = Other tables\n----------\n";
            $good = 0; $bad = 0;
            foreach ($tables as $table) {
                if (in_array($table, array("aps", "attacks", "caps", "clients", "scans"))) {
                    echo G . $table . W . "\n";
                    $good++;
                } else {
                    echo R . $table . W . "\n";
                    $bad++;
                }
            }
            echo "----------\n";
            importWickerTables($good, $bad);
        }
        print_r($results);
    } else {
        $dbh = new PDO('mysql:host=' . $data["database"]["url"], $data["database"]["username"], $data["database"]["password"], array(PDO::ATTR_TIMEOUT => "3"));
        echo "Attemping to create database";
        pause();
        $dbh->query("CREATE DATABASE IF NOT EXISTS " . $data["database"]["name"]);
        if ($dbh->errorCode() != "00000") {
            echo R . "An error has occured while attempting to create the database.\nPlease verify that the credentials supplied have valid database creation permissions." . W . "\n";
            die;
        } else {
            echo G . "Database " . O . $data["database"]["name"] . G . " was created successfully!" . W . "\n";
        }

        // Import tables
        importWickerTables(0, 0);
    }
}
?>
