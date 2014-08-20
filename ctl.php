<?php
require_once("CapFile.class.php");
require_once("Attack.class.php");
require_once("Wicker.php");

$cmd         = $_GET['cmd'];
$id          = $_GET['id'];
$attack_type = $_GET['attack'];
$attack = Attack::fromDB($_GET['id'], $_GET['attack']);
if ($cmd == "execute") {
	$dictionaries = array("10k most common.txt", "small", "medium", "rockyou.txt", "Custom-WPA", "Super-WPA");
	$cap = CapFile::fromDB($id);
	$attack->setTmpfile($wicker->random(2, 10));
	$attack->setPwFile($wicker->random(2, 10));

	//system("aircrack-ng -w \"dictionaries/" . $dictionaries[$attack_type-1] . "\" -l \"passwords/" . $attack->getPwFile() . "\" \"uploads/" . $cap->getLocation() . "\" > \"logs/" . $attack->getTmpFile() . "\" &");
	system("pyrit -i \"dictionaries/" . $dictionaries[$attack_type-1] . "\" -r \"uploads/" . $cap->getLocation() . "\" attack_passthrough > \"logs/" . $attack->getTmpFile() . "\" &");
	exec("ps aux | grep '" . $cap->getLocation() . "' | grep -v grep | awk '{ print $2 }' | tail -1", $out);
	$attack->setPID($out[0]);
	$attack->setStatus(1);
} else if ($cmd == "terminate") {
	$attack->terminate();
} else if ($cmd == "pause") {
	posix_kill($attack->getPID(), 19);
	//passthru("sudo kill -STOP " . $attack->getPID());
	$attack->setStatus(5);
} else if ($cmd == "resume") {
	posix_kill($attack->getPID(), 18);
	//passthru("sudo kill -CONT " . $attack->getPID());
	$attack->setStatus(1);
}
header('Location: view.php?id=' . $_GET['id']);
die;
?>