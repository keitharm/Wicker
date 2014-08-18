<?php
require_once("CapFile.class.php");
require_once("Attack.class.php");
require_once("Wicker.php");

$id = $_GET['id'];
$attack_type = $_GET['attack'];
$dictionaries = array("10k most common.txt", "small", "medium", "rockyou.txt", "Custom-WPA", "Super-WPA");
$cap = CapFile::fromDB($id);
$attack = Attack::fromDB($id, $attack_type);
$attack->setTmpfile($wicker->random(2, 10));
$attack->setPwFile($wicker->random(2, 10));

system("aircrack-ng -w \"dictionaries/" . $dictionaries[$attack_type-1] . "\" -l \"passwords/" . $attack->getPwFile() . "\" \"uploads/" . $cap->getLocation() . "\" > \"logs/" . $attack->getTmpFile() . "\" &");
exec("ps aux | grep '" . $cap->getLocation() . "' | grep -v grep | awk '{ print $2 }' | tail -1", $out);
$attack->setPID($out[0]);
$attack->setStatus(1);

header('Location: view.php?id=' . $cap->getID());
?>