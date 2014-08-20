<?php
require_once("Attack.class.php");

$type = $_GET['type'];
$data = array();
if ($type == "cap") {
	$id = $_GET['id'];
	for ($a = 1; $a <= 6; $a++) {
		unset($attack);
		$attack = Attack::fromDB($id, $a);
		$attack->updateData();
		$data[$a]["current"]  = $attack->getCurrent();
		$data[$a]["complete"] = round($attack->getCurrent()/$attack->getDictionarySize()*100, 2) . "%";
		$data[$a]["rate"]     = number_format($attack->getRate());
		$data[$a]["runtime"]  = $attack->getRuntime();
		if ($attack->getRate() != 0) {
			$data[$a]["eta"]      = (int)(gmdate("d", round(($attack->getDictionarySize()-$attack->getCurrent())/$attack->getRate()))-1) . gmdate(":H:i:s", round(($attack->getDictionarySize()-$attack->getCurrent())/$attack->getRate()));
		} else {
			$data[$a]["eta"] = "--:--";
		}
	}
}
echo json_encode($data, JSON_UNESCAPED_SLASHES);
?>