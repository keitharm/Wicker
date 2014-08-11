<?php
/**
 *
 *  The new global.php
 *  Contains all general functions for RandomAPI
 *
 */

// temp
require_once("User.class.php");
require_once("Database.class.php");

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

        <title><?=WICKER::NAME?> <?=WICKER::VERSION?> <?=$title?></title>

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
}

// Create instance of RandomAPI
$wicker = new Wicker;


if (isset($_GET['sessioninfo'])) {
    echo "<pre>";
    print_r($_SESSION);
    print_r($api->user);
    echo session_id();
    echo "</pre>";
}

?>
