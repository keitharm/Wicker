<?php
require_once("Wicker.php");
require_once("Scan.class.php");

$scan = Scan::FromDB($_GET['id']);
if ($scan->getID() == 0) {
    #header('Location: index.php');
    #die;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?=$wicker->heading("Viewing Scan #" . $scan->getID())?>
        <link href="css/bars.css" rel="stylesheet">
        <id hidden><?=$_GET['id']?></id>
    </head>
    <body>
        <?=$wicker->navbar()?>
        <div class="container-fluid">
            <div class="row">
                <?=$wicker->menu("null")?>
                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                    <h1 class="page-header"><?=$scan->getID()?></h1>
                    <div class="row placeholders">
                        <input type="button" class="btn-danger" value="Terminate Scan" onClick="window.location='scanctl.php?do=terminate&id=<?=$scan->getID()?>'">
                    </div>


                    <div class="table-responsive">
<?php
#$scan = file_get_contents("aircrack/scan1029481-01.csv");
$csv = array_map('str_getcsv', file('scans/' . $scan->getGUID() . '-01.csv'));
unset($csv[0]);
$csv = array_values($csv);
foreach ($csv as &$line) {
    foreach ($line as &$val) {
        $val = trim($val);
    }
}
unset($csv[count($csv)-1]);
unset($csv[0]);

$aps = array();
$clients = array();
$a = 0;
$mode = "ap";
foreach ($csv as $group) {
    $a++;

    // Reached client section
    if (count($group) == 1) {
        $mode = "client";
        $a = 0;
        continue;
    }

    // AP section
    if ($mode == "ap") {
        $aps[$a]["bssid"]          = $group[0];
        $aps[$a]["first_seen"]     = $group[1];
        $aps[$a]["last_seen"]      = $group[2];
        $aps[$a]["channel"]        = $group[3];
        $aps[$a]["privacy"]        = $group[5];
        $aps[$a]["cipher"]         = $group[6];
        $aps[$a]["authentication"] = $group[7];
        $aps[$a]["power"]          = $group[8];
        $aps[$a]["beacons"]        = $group[9];
        $aps[$a]["ivs"]            = $group[10];
        $aps[$a]["essid"]          = $group[13];
    } else {
        $clients[$a]["mac"]        = $group[0];
        $clients[$a]["first_seen"] = $group[1];
        $clients[$a]["last_seen"]  = $group[2];
        $clients[$a]["power"]      = $group[3];
        $clients[$a]["packets"]    = $group[4];
        $clients[$a]["bssid"]      = $group[5];
        $clients[$a]["probed"]     = $group[6];
    }
}
$clients = array_values($clients);
unset($clients[0]); 
?>
                    <table class="table table-striped">
                        <h3>APs</h3>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>BSSID</th>
                                <th>ESSID</th>
                                <th>First seen</th>
                                <th>Last seen</th>
                                <th>Channel</th>
                                <th>Privacy</th>
                                <th>Cipher</th>
                                <th>Auth.</th>
                                <th>Power</th>
                                <th>Beacons</th>
                                <th>IVs</th>
                            </tr>
                        </thead>
                        <tbody>
<?php
$a = 0;
foreach ($aps as $ap) {
    $a++;
?>                          <tr>
                                <td><?=$a?></td>
                                <td><?=$ap["bssid"]?></td>
                                <td><?=$ap["essid"]?></td>
                                <td><?=$wicker->timeconv(strtotime($ap["first_seen"]))?></td>
                                <td><?=$wicker->timeconv(strtotime($ap["last_seen"]))?></td>
                                <td><?=$ap["channel"]?></td>
                                <td><?=$ap["privacy"]?></td>
                                <td><?=$ap["cipher"]?></td>
                                <td><?=$ap["authentication"]?></td>
                                <td><?=$ap["power"]?></td>
                                <td><?=$ap["beacons"]?></td>
                                <td><?=$ap["ivs"]?></td>
                            </tr>
<?php
}
?>
                        </tbody>
                    </table>
                    <table class="table table-striped">
                        <h3>Clients</h3>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>MAC</th>
                                <th>First seen</th>
                                <th>Last seen</th>
                                <th>Power</th>
                                <th>Packets</th>
                                <th>BSSID</th>
                                <th>Probes</th>
                            </tr>
                        </thead>
<?php
$a = 0;
foreach ($clients as $client) {
    $a++;
?>                          <tr>
                                <td><?=$a?></td>
                                <td><?=$client["mac"]?></td>
                                <td><?=$wicker->timeconv(strtotime($client["first_seen"]))?></td>
                                <td><?=$wicker->timeconv(strtotime($client["last_seen"]))?></td>
                                <td><?=$client["power"]?></td>
                                <td><?=$client["packets"]?></td>
                                <td><?=$client["bssid"]?></td>
                                <td><?=$client["probed"]?></td>
                            </tr>
<?php
}
?>
                        <tbody>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
        <?=$wicker->footer()?>
        <script src="js/ajax.js"></script>
    </body>
</html>
