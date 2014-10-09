<?php
require_once("Scan.class.php");

$type = $_GET['type'];
$scan = Scan::FromDB($_GET['id']);
if ($type == "ap") {
?>
    <table class="table">
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
$aps = $scan->getAPs("power", "DESC");
$a = 0;
if (count($aps) != 0) {
    foreach ($aps as $ap) {
        if ($ap->getChannel() < 1 || $ap->getESSID() == null || ((time() - $ap->getLastSeen()) > 60 && $scan->getStatus() != 2 && $scan->getStatus() != 3)) {
            continue;
        }
        $a++;

        // Color code the BSSIDs
        $hex = substr(md5($ap->getBSSID()),0 ,6);
?>
        <tr>
            <td bgcolor="#<?=$hex?>"><?=$a?></td>
            <td><span title="<?=$wicker->getMan($ap->getBSSID())?>"><?=$ap->getBSSID()?></span></td>
            <td><a href="apview.php?parent_scan=<?=$_GET['id']?>&scanid=<?=$ap->getIndScanID()?>&bssid=<?=$ap->getBSSID()?>" target="_blank"><?=$ap->getESSID()?></a></td>
            <td><?=$wicker->timeconv($ap->getFirstSeen())?></td>
            <td><?=$wicker->timeconv($ap->getLastSeen())?></td>
            <td><?=$ap->getChannel()?></td>
            <td><?=(($ap->getPrivacy() == "WEP") ? "<font color='green'>WEP</font>" : "<font color='red'>" . $ap->getPrivacy() . "</font>")?></td>
            <td><span style='color: <?=$wicker->color($ap->getPower(), -75, -60)?>'><?=$ap->getPower()?></span></td>
            <td><?=$ap->getBeacons()?></td>
            <td><?=number_format($ap->getIVs())?></td>
        </tr>
<?php
    }
}
?>

        </tbody>
    </table>
<?php
} else if ($type == "client") {
?>
    <table class="table">
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
        $hex = substr(md5($client->getBSSID()),0 ,6);
?>
            <tr>
                <td bgcolor="#<?=$hex?>"><?=$a?></td>
                <td><a href="clientview.php?scanid=<?=$_GET['id']?>&bssid=<?=$client->getMac()?>" target="_blank"><span title="<?=$wicker->getMan($client->getMac())?>"><?=$client->getMac()?></span></a></td>
                <td><?=$wicker->timeconv($client->getFirstSeen())?></td>
                <td><?=$wicker->timeconv($client->getLastSeen())?></td>
                <td><?=$client->getPower()?></td>
                <td><?=$client->getPackets()?></td>
                <td><span title="<?=$wicker->getMan($client->getBSSID())?>"><?=$client->getBSSID()?></span></td>
                <td><?=$client->getProbed()?></td>
            </tr>
<?php
    }
}
?>
        </tbody>
    </table>
<?php
} else if ($type == "apview") {
    $data = AP::fromDB($_GET['id'], $_GET['bssid']);
    if ($_GET['id'] == 0 || $data->getEssid() == null) {
?>
<h1 class="page-header"></h1>

<div class="row placeholders">
    <div class="col-xs-6 col-sm-4 placeholder">
        <h2>--:--:--:--:--:--</h2>
        <span class="text-muted">BSSID/AP MAC</span>
    </div>
    <div class="col-xs-6 col-sm-4 placeholder">
        <h2>---</h2>
        <span class="text-muted">First Seen</span>
    </div>
    <div class="col-xs-6 col-sm-3 placeholder">
        <h2>--</h2>
        <span class="text-muted">Last Seen</span>
    </div>
    <div class="col-xs-6 col-sm-4 placeholder">
        <h2>---</h2>
        <span class="text-muted">Privacy</span>
    </div>
    <div class="col-xs-6 col-sm-4 placeholder">
        <h2>--- DB</h2>
        <span class="text-muted">Power</span>
    </div>
    <div class="col-xs-6 col-sm-3 placeholder">
        <h2>0</h2>
        <span class="text-muted">IVs</span>
    </div>

</div>
<?php
    } else {
?>
<h1 class="page-header"><small><?=$data->getESSID()?> - channel: <?=$data->getChannel()?></small></h1>

<div class="row placeholders">
    <div class="col-xs-6 col-sm-4 placeholder">
        <h2><span title="<?=$wicker->getMan($data->getBSSID())?>"><?=$data->getBSSID()?></span></h2>
        <span class="text-muted">BSSID/AP MAC</span>
    </div>
    <div class="col-xs-6 col-sm-4 placeholder">
        <h2><?=$wicker->timeconv($data->getFirstSeen())?></h2>
        <span class="text-muted">First Seen</span>
    </div>
    <div class="col-xs-6 col-sm-3 placeholder">
        <h2><?=$wicker->timeconv($data->getLastSeen())?></h2>
        <span class="text-muted">Last Seen</span>
    </div>
    <div class="col-xs-6 col-sm-4 placeholder">
        <h2><?=(($data->getPrivacy() == "WEP") ? "<font color='green'>WEP</font>" : "<font color='red'>" . $data->getPrivacy() . "</font>")?></h2>
        <span class="text-muted">Privacy</span>
    </div>
    <div class="col-xs-6 col-sm-4 placeholder">
        <h2><span style='color: <?=$wicker->color($data->getPower(), -75, -60)?>'><?=$data->getPower()?></span> DB</h2>
        <span class="text-muted">Power</span>
    </div>
    <div class="col-xs-6 col-sm-3 placeholder">
        <h2><?=number_format($data->getIVs())?></h2>
        <span class="text-muted">IVs</span>
    </div>

</div>
<?php
    }
} else if ($type == "clientapview") {
?>
    <table class="table">
        <h3>Clients - <?=$scan->getClientCount()?></h3>
        <thead>
            <tr>
                <th>#</th>
                <th>MAC</th>
                <th>First seen</th>
                <th>Last seen</th>
                <th>Power</th>
                <th>Packets</th>
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
                <td><a href="clientview.php?scanid=<?=$_GET['id']?>&bssid=<?=$client->getMac()?>" target="_blank"><span title="<?=$wicker->getMan($client->getMac())?>"><?=$client->getMac()?></span></a></td>
                <td><?=$wicker->timeconv($client->getFirstSeen())?></td>
                <td><?=$wicker->timeconv($client->getLastSeen())?></td>
                <td><?=$client->getPower()?></td>
                <td><?=$client->getPackets()?></td>
                <td><?=$client->getProbed()?></td>
            </tr>
<?php
    }
}
?>
        </tbody>
    </table>
<?php
}
?>