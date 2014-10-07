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
        <?=$wicker->head("Viewing Scan #" . $scan->getID())?>
        <link href="css/bars.css" rel="stylesheet">
        <id hidden><?=$_GET['id']?></id>
    </head>
    <body>
        <?=$wicker->heading()?>
        <div class="container-fluid">
            <div class="row">
                <?=$wicker->menu("null")?>
                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                    <h1 class="page-header"><?=$scan->getID()?> - <?=$wicker->timeconv($scan->getTime())?></h1>
<?php
if ($scan->getStatus() == 1) {
?>
                    <input type="button" class="btn-danger" value="Terminate Scan" onClick="window.location='scanctl.php?do=terminate&id=<?=$scan->getID()?>'">
<?php
}
?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <h3>APs - <?=$scan->getAPCount()?></h3>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>BSSID</th>
                                    <th>ESSID</th>
                                    <th>First seen</th>
                                    <th>Last seen</th>
                                    <th>Channel</th>
                                    <th>Privacy</th>
                                    <th>Power</th>
                                    <th>Beacons</th>
                                    <th>IVs</th>
                                </tr>
                            </thead>
                            <tbody>
<?php
$aps = $scan->getAPs();
$a = 0;
if (count($aps) != 0) {
    foreach ($aps as $ap) {
        if ($ap->getChannel() < 1 || $ap->getESSID() == null || (time() - $ap->getLastSeen()) > 60) {
            continue;
        }
        $a++;
?>
                            <tr>
                                <td><?=$a?></td>
                                <td><?=$ap->getBSSID()?></td>
                                <td><a href="apview.php?scanid=<?=$_GET['id']?>&bssid=<?=$ap->getBSSID()?>"><?=$ap->getESSID()?></a></td>
                                <td><?=$wicker->timeconv($ap->getFirstSeen())?></td>
                                <td><?=$wicker->timeconv($ap->getLastSeen())?></td>
                                <td><?=$ap->getChannel()?></td>
                                <td><?=$ap->getPrivacy()?></td>
                                <td><?=$ap->getPower()?></td>
                                <td><?=$ap->getBeacons()?></td>
                                <td><?=$ap->getIVs()?></td>
                            </tr>
<?php
    }
}
?>

                            </tbody>
                        </table>
                        <table class="table table-striped">
                            <h3>Clients - <?=$scan->getClientCount()?></h3>
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
                            <tbody>
<?php
$clients = $scan->getClients();
$a = 0;
if (count($clients) != 0) {
    foreach ($clients as $client) {
        $a++;
?>
                                <tr>
                                    <td><?=$a?></td>
                                    <td><a href="clientview.php?scanid=<?=$_GET['id']?>&bssid=<?=$client->getMac()?>"><?=$client->getMac()?></a></td>
                                    <td><?=$wicker->timeconv($client->getFirstSeen())?></td>
                                    <td><?=$wicker->timeconv($client->getLastSeen())?></td>
                                    <td><?=$client->getPower()?></td>
                                    <td><?=$client->getPackets()?></td>
                                    <td><?=$client->getBSSID()?></td>
                                    <td><?=$client->getProbed()?></td>
                                </tr>
<?php
    }
}
?>
                            </tbody>
                        </table>
                        <h3>Map</h3>
                        <iframe src="mapview.php?id=<?=$_GET['id']?>" width="100%" height="500px"></iframe>
                    </div>
                </div>
            </div>
        </div>
        <?=$wicker->footer()?>
        <script src="js/coordinates.js"></script>
    </body>
</html>
