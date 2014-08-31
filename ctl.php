<?php
require_once("Wicker.php");
require_once("CapFile.class.php");
require_once("Attack.class.php");

$cmd         = $_GET['cmd'];
$id          = $_GET['id'];
$attack_type = $_GET['attack'];
$attack      = Attack::fromDB($_GET['id'], $_GET['attack']);

if(is_null($cmd) || is_null($id) || is_null($attack_type)) {
    header('Location: view.php?id=' . $_GET['id']);
    die;
}

if ($cmd == "execute") {
    $dictionaries = array("10k most common.txt", "rockyou.txt", "small", "Custom-WPA", "Super-WPA", "big", "bigger", "combined");
    $cap = CapFile::fromDB($id);
    $attack->setTmpfile($wicker->newGUID());

    system("pyrit -i \"dictionaries/" . $dictionaries[$attack_type-1] . "\" -r \"uploads/" . $cap->getLocation() . "\" attack_passthrough > \"logs/" . $attack->getTmpFile() . "\" &");
    exec("ps aux | grep '" . $cap->getLocation() . "' | grep -v grep | awk '{ print $2 }' | tail -1", $out);

    $attack->setPID($out[0]);
    $attack->setStatus(1);
} else if ($cmd == "terminate") {
    $attack->terminate();
} else if ($cmd == "pause") {
    posix_kill($attack->getPID(), 19);

    $attack->setStatus(5);
} else if ($cmd == "resume") {
    posix_kill($attack->getPID(), 18);

    $attack->setStatus(1);
} else if ($cmd == "hide") {
    $cap = CapFile::fromDB($id);
    $cap->setStatus(1);

    header('Location: index.php');
    die;
} else {
    header('Location: index.php');
    die;
}
?>
