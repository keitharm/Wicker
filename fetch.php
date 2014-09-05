<?php
require_once("Attack.class.php");

$type = $_GET['type'];
$data = array();

switch($type) {
    case "cap":
        $id = $_GET['id'];

        for ($a = 1; $a <= 8; $a++) {
            unset($attack);
            $attack = Attack::fromDB($id, $a);
            $attack->updateData();
            $data[$a]['status']   = $attack->getStatus();
            $data[$a]["complete"] = round($attack->getCurrent()/$attack->getDictionarySize()*100, 2);
            $data[$a]["rate"]     = number_format($attack->getRate());
            $data[$a]["runtime"]  = $attack->getRuntime();
            if ($attack->getRate() != 0) {
                $data[$a]["etc"]      = (int)(gmdate("d", round(($attack->getDictionarySize()-$attack->getCurrent())/$attack->getRate()))-1) . gmdate(":H:i:s", round(($attack->getDictionarySize()-$attack->getCurrent())/$attack->getRate()));
            } else {
                $data[$a]["etc"] = "--:--";
            }
        }
        break;
    case "system":
        $name = array("CPU0", "CPU1", "CPU2", "CPU3", "Uptime", "1m", "5m", "15m", "Uploads", "Logs");

        for($i = 0; $i < count($name); $i++)
            $data[$name[$i]] = $wicker->status()[$i];
        break;
}

echo json_encode($data, JSON_UNESCAPED_SLASHES);
?>
