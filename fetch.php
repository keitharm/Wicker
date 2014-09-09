<?php
require_once("Attack.class.php");

$type = $_GET['type'];
$data = array();

switch($type) {
    case "cap":
        for ($a = 1; $a <= 8; $a++) {
            unset($attack);
            $attack = Attack::fromDB($_GET['id'], $a);
            $attack->updateData();
            $data[$a]['status']   = $attack->getStatus();
            $data[$a]['password'] = $attack->getPassword();
            $data[$a]["complete"] = sprintf("%.2f", round($attack->getCurrent()/$attack->getDictionarySize()*100, 2));
            $data[$a]["rate"]     = number_format($attack->getRate());
            $data[$a]["runtime"]  = $attack->getRuntime();
            if ($attack->getRate() != 0 && $attack->getStatus() == 1) {
                $days = (int)(gmdate("d", round(($attack->getDictionarySize()-$attack->getCurrent())/$attack->getRate()))-1);
                if ($days < 10) {
                    $days = "0" . $days;
                }
                $data[$a]["etc"] = $days . gmdate(":H:i:s", round(($attack->getDictionarySize()-$attack->getCurrent())/$attack->getRate()));
            } else {
                $data[$a]["etc"] = "00:00:00:00";
            }
        }
        break;
    case "system":
        $name = array("CPU1", "CPU2", "CPU3", "CPU4", "Uptime", "1m", "5m", "15m", "Uploads", "Logs");

        for($i = 0; $i < count($name); $i++)
            $data[$name[$i]] = $wicker->status()[$i];
        break;
}

echo json_encode($data, JSON_UNESCAPED_SLASHES);
?>
