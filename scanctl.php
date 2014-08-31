<?php
require_once("Wicker.php");
require_once("Scan.class.php");

$do = $_GET['do'];
$id = $_GET['id'];

if ($do == "newscan") {
    $scan = Scan::newScan();
    if (!$scan->mon0Enabled()) {
        $wicker->error("No wireless devices in monitor mode detected.");
        die;
    }
    $scan->startScan();
    header('Location: scanview.php?id=' . $scan->getID());
    die;
} else if ($do == "terminate") {
    $scan = Scan::fromDB($id);
    system("sudo kill " . $scan->getPID());
    header('Location: scanview.php?id=' . $scan->getID());
    die;
}
?>
