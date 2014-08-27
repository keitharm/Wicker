<?php
require_once("Database.class.php");
require_once("CapFile.class.php");

class Wicker
{
    const VERSION = "Pre-Pre-Alpha";
    const NAME    = "Wicker";
    const SEP     = "{[^]}";
    const TAB     = "&nbsp;&nbsp;&nbsp;&nbsp;";

    public $db;
    public $user;

    public function __construct() {
        $this->connectToDatabase();
    }

    private function connectToDatabase() {
        $database = new Database;
        $this->db = $database;
    }

    public function random($mode, $length) {
        # RANDOM_DEFAULT
        if ($mode == 1) {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        }
        # RANDOM_NUMERIC
        if ($mode == 2) {
            $chars = "1234567890";
        }
        # RANDOM_ALPHA
        if ($mode == 3) {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        }
        # RANDOM_UPPER
        if ($mode == 4) {
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        }
        #RANDOM_LOWER
        if ($mode == 5) {
            $chars = "abcdefghijklmnopqrstuvwxyz";
        }
        # RANDOM_UPPERNUM
        if ($mode == 6) {
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        }
        # RANDOM_LOWERNUM
        if ($mode == 7) {
            $chars = "abcdefghijklmnopqrstuvwxyz1234567890";
        }
        # RANDOM_EVERYTHING
        if ($mode == 8) {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890~!@#$%^&*()_+-=[]\\{}|;':\"<>?,./";
        }
        # RANDOM_HEX
        if ($mode == 9) {
            $chars = "abcdef1234567890";
        }
        # RANDOM_UPPERHEX
        if ($mode == 10) {
            $chars = "ABCDEF1234567890";
        }

        for ($i = 0; $i < $length; $i++) {
            $result.= $chars[mt_rand(0, strlen($chars) - 1)];
        }

        return $result;
    }

    public function importcap($location) {
        $cap = CapFile::import($location);
        $new = CapFile::fromDB("[C]" . $cap->getChecksum());
        header('Location: view.php?id=' . $new->getID());
    }

    public function extractData($data, $search, $ending, $specific = -1) {
        $len = strlen($data);
        $matches = $this->findall($search, $data);
        $found = array();

        foreach ($matches as $val) {
            $bad = false;
            $offset = 0;
            $val += strlen($search);

            while (substr($data, $val+$offset, strlen($ending)) != $ending) {
                $offset++;

                // If we are outside of the range of the string, there is no ending match.
                if ($offset > $len) {
                    $bad = true;
                    break;
                }
            }

            if (!$bad) {
                $found[] = substr($data, $val, $offset);
            }
        }

        if ($found == false) {
            return false;
        }

        if ($specific == -1) {
            if (count($found) == 1) {
                return $found[0];
            }
            return $found;
        }
        return $found[$specific-1];
    }

    private function findall($needle, $haystack) {
        $pos       = 0;
        $len       = strlen($haystack);
        $searchlen = strlen($needle);
        $results   = array();
        $data      = $haystack;

        while (1) {
            $occurance = strpos($data, $needle);
            if ($occurance === false) {
                return $results;
            } else {
                $pos += $occurance+$searchlen;
                $results[] = $pos-$searchlen;
                $data = substr($haystack, ($pos));
            }
        }
    }

    public function doesExist($table, $fieldname, $value, $returnObject = false) {
        $statement = $this->db->con()->prepare("SELECT * FROM `$table` WHERE `$fieldname` = ?");
        $statement->execute(array($value));
        $info = $statement->FetchObject();
        
        // If return object
        if ($returnObject) {
            return $info;
        }

        // Return boolean
        if ($info != null) {
            return 1;
        }
        return 0;
    }

    public function error($msg, $misc = null) {
?>
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <?=$this->heading("Error")?>
            </head>
            <body>
                <?include('htmlBlocks/nav.php');?>
                <div class="container-fluid">
                    <div class="row">
                        <?=$this->menu(null)?>
                        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                            <h1 class="page-header">Error</h1>
                            <div class="row" align="center" style="margin-top: 100px">
                                <h2><?=$msg?></h2>
                                <p><?=$misc?></p>
                                <input type="button" class="btn btn-success" onClick="window.location='index.php'" value="Ok">
                            </div>
                        </div>
                    </div>
                </div>
                <?include('htmlBlocks/scriptIncludes.php')?>
            </body>
        </html>
<?php
    die;
    }

    public function timeconv($timestamp, $span = true) {
        $elapsed = time() - $timestamp;

        if ($about == true) {
            $about = "About ";
        }
        if ($elapsed < 10) {
            $data = "Just now";
        }

        // Seconds
        else if ($elapsed < 60) {
            if ($elapsed != 1) {
                $s = "s";
            }
            $data = $about . $elapsed . " second" . $s . " ago";
        }

        // Minutes
        else if ($elapsed < 60*60) {
            if ($elapsed >= 60*2) {
                $s = "s";
            }
            $data = $about . floor($elapsed/60) . " minute" . $s . " ago";
        }

        // Hours
        else if ($elapsed < 60*60*24) {
            if ($elapsed >= 60*60*2) {
                $s = "s";
            }
            $data = $about . floor($elapsed/(60*60)) . " hour" . $s . " ago";
        }
        
        // Days
        else if ($elapsed < 60*60*24*30) {
            if ($elapsed >= 60*60*24*2) {
                $s = "s";
            }
            $data = $about . floor($elapsed/(60*60*24)) . " day" . $s . " ago";
        }

        // Months
        else if ($elapsed < 60*60*24*30*12) {
            if ($elapsed >= 60*60*24*30*2) {
                $s = "s";
            }
            $data = "about " . floor($elapsed/(60*60*24*30)) . " month" . $s . " ago";
        } else {
            if ($elapsed >= 60*60*24*30*12*2) {
                $s = "s";
            }
            $data = "about " . floor($elapsed/(60*60*24*30*12)) . " year" . $s . " ago";
        }
        if ($span) {
            return "<span title='" . date("F j, Y, g:i a", $timestamp) . "'>" . $data . "</span>";
        }
        return $data;
    }

    public function status() {
        exec("sensors; uptime; du -hs uploads; du -hs logs;", $data);
        $cpus = array(substr($this->extractData($data[8], "+", " C")[0], 0, -2), substr($this->extractData($data[9], "+", " C")[0], 0, -2), substr($this->extractData($data[10], "+", " C")[0], 0, -2), substr($this->extractData($data[11], "+", " C")[0], 0, -2));
        $loadavgs = explode(", ", substr($data[13], strpos($data[13], "average: ")+9));
        $uptime = array($this->extractData($data[13], "up ", ",") . " - " . $this->extractData($data[13], ",", ",")[0], $loadavgs[0], $loadavgs[1], $loadavgs[2]);
        $uploads = trim($this->extractData(":" . $data[14], ":", "uploads"));
        $logs = trim($this->extractData(":" . $data[15], ":", "logs"));
        return array("<span style='color: " . $this->color($cpus[0], 100, 70) . "'>" . $cpus[0] . "°C</span>", "<span style='color: " . $this->color($cpus[1], 100, 70) . "'>" . $cpus[1] . "°C</span>", "<span style='color: " . $this->color($cpus[2], 100, 70) . "'>" . $cpus[2] . "°C</span>", "<span style='color: " . $this->color($cpus[3], 100, 70) . "'>" . $cpus[3] . "°C</span>", $uptime[0], "<span style='color: " . $this->color($uptime[1], 6.00, 1.00) . "'>" . $uptime[1] . "</span>", "<span style='color: " . $this->color($uptime[2], 6.00, 1.00) . "'>" . $uptime[2] . "</span>", "<span style='color: " . $this->color($uptime[3], 6.00, 1.00) . "'>" . $uptime[3] . "</span>", $uploads, $logs);
    }

    public function capfiles() {
        if ($handle = opendir("uploads")) {
            while (($file = readdir($handle)) !== false){
                if (!in_array($file, array('.', '..')) && !is_dir($dir.$file)) 
                    $i++;
            }
        }
        return $i;
    }

    public function scanfiles() {
        if ($handle = opendir("scans")) {
            while (($file = readdir($handle)) !== false){
                if (!in_array($file, array('.', '..')) && !is_dir($dir.$file))
                    $i++;
            }
        }
        return $i;
    }

    public function space($val) {
        return str_repeat("&nbsp;", $val);
    }

    public function color($current, $max, $min = 0) {
        $current -= $min;
        $max -= $min;
        $percent = round(($current/$max)*100);
        if ($percent < 0) {
            $percent = 0;
        }

        $red = round(($percent*255)/100);
        $green = 255-$red;
        if ($percent < 0) {
        $rgb = "rgb(255, 0, 0)";
        }
        return "rgb(" . $red . ", " . $green . ", 0)";
    }

    public function newGUID() {
        $guid = $this->random(10, 8) . "-" . $this->random(10, 4) . "-" . $this->random(10, 4) . "-" . $this->random(10, 4) . "-" . $this->random(10, 12);
        return $guid;
    }
}


// Create instance of Wicker
$wicker = new Wicker;

?>

