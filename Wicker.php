<?php
require_once("User.class.php");
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
		$this->startSession();
		$this->connectToDatabase();

		/*if ($this->loggedIn()) {
			$this->initializeUser($_SESSION['username']);
		} else {
			$this->initializeUser(null);
		}

		$this->checkSumUserAgent();

		if ($this->loggedIn()) {
			$this->checkSessionCredentials();
			$this->checkTutorial();
		}

		$this->validPostRef();
		*/

	}

	public function startSession() {
		session_name("Wicker");
		session_start();
	}

	public function connectToDatabase() {
		$database = new Database;
		$this->db = $database;
	}

	public function heading($title = "NULL") {
?>
	<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<!--<link rel="icon" href="../../favicon.ico">-->

		<title><?=WICKER::NAME?> <?=WICKER::VERSION?> :: <?=$title?></title>

		<!-- Bootstrap core CSS -->
		<link href="css/bootstrap.min.css" rel="stylesheet">

		<!-- Custom styles for this template -->
		<link href="css/dashboard.css" rel="stylesheet">

		<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
		<script src="js/ie10-viewport-bug-workaround.js"></script>

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
<?php
	}

	public function menu($selected) {
		$active[$selected] = " class=\"active\"";
?>
		<div class="col-sm-3 col-md-2 sidebar">
			<ul class="nav nav-sidebar">
				<li<?=$active["index"]?>><a href="index.php">Dashboard</a></li>
				<!--<li<?=$active["stats"]?>><a href="stats.php">Statistics</a></li>-->
				<!--<li<?=$active["about"]?>><a href="stats.php">About</a></li>-->
			</ul>
			<ul class="nav nav-sidebar">
				<!--<li<?=$active["local"]?>><a href="upload.php">Local Scan/Crack</a></li>-->
				<li<?=$active["upload"]?>><a href="upload.php">Uploader</a></li>
				<!--<li<?=$active["crack"]?>><a href="crack.php">Cracker</a></li>-->
			</ul>
		</div>
<?php
	}

	public function random($mode, $length) {
        #mt_srand(hexdec(substr(md5(openssl_random_pseudo_bytes(4)),0 ,8)));
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

	    $data = $haystack;
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
        $statement = $this->db->con()->prepare("SELECT * FROM $table WHERE $fieldname = ?");
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

		        <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		            <div class="container-fluid">
		                <div class="navbar-header">
		                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
		                        <span class="sr-only">Toggle navigation</span>
		                        <span class="icon-bar"></span>
		                        <span class="icon-bar"></span>
		                        <span class="icon-bar"></span>
		                    </button>
		                    <a class="navbar-brand" href="index.php">Wicker - The stupid wifi cracker</a>
		                </div>
		                <div class="navbar-collapse collapse">
		                    <ul class="nav navbar-nav navbar-right">
		                        <li>
		                            <!--
		                            <form class="navbar-form navbar-right" action="login" method="POST">
		                                <div class="form-group">
		                                    <input type="text" name="username" placeholder="Username" class="form-control" required <?=$auto?>>
		                                </div>
		                                <div class="form-group">
		                                    <input type="password" name="password" placeholder="Password" class="form-control" required>
		                                </div>
		                                &nbsp;
		                                <input type="submit" class="btn btn-success" value="Login">&nbsp;<input type="button" onClick="window.location='signup'" class="btn btn-primary" value="Sign up">
		                            </form>
		                            -->
		                        </li>
		                        <li><a href="https://github.com/solewolf/wicker" target="_blank">Github</a></li>
		                        <!--
		                        <li><a href="#">Settings</a></li>
		                        <li><a href="#">Profile</a></li>
		                        <li><a href="#">Help</a></li>
		                        -->
		                    </ul>
		                </div>
		            </div>
		        </div>

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
		        <?=$this->footer()?>
		    </body>
		</html>
<?php
	die;
	}

	public function footer() {
?>
		<!-- Bootstrap core JavaScript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/docs.min.js"></script>
		<script src="js/holder.js"></script>
<?php
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
}


// Create instance of Wicker
$wicker = new Wicker;


if (isset($_GET['sessioninfo'])) {
	echo "<pre>";
	print_r($_SESSION);
	echo session_id();
	echo "</pre>";
}

?>
