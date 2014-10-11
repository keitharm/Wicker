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

        if ($ap->getPrivacy() == "WEP") {
            $privacy = "<font color='green'>WEP</font>";
        } else if ($ap->getPrivacy() == "WEP") {
            $privacy = "<font color='#0F0'>OPN</font>";
        } else {
            $privacy = "<font color='red'>" . $ap->getPrivacy() . "</font>";
        }
?>
        <tr>
            <td bgcolor="#<?=$hex?>"><?=$a?></td>
            <td><span title="<?=$wicker->getMan($ap->getBSSID())?>"><?=$ap->getBSSID()?></span></td>
            <td><a href="apview.php?parent_scan=<?=$_GET['id']?>&scanid=<?=$ap->getIndScanID()?>&bssid=<?=$ap->getBSSID()?>" target="_blank"><?=$ap->getESSID()?></a></td>
            <td><?=$wicker->timeconv($ap->getFirstSeen())?></td>
            <td><?=$wicker->timeconv($ap->getLastSeen())?></td>
            <td><?=$ap->getChannel()?></td>
            <td><?=$privacy?></td>
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
        if ($client->getMac() == "FF:FF:FF:FF:FF:FF" || ((time() - $client->getLastSeen()) > 60 && $scan->getStatus() != 2 && $scan->getStatus() != 3)) {
            continue;
        }
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
    if ($_GET['id'] == 0 || $data->getESSID() == null) {
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
        <h2>---</h2>
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
<?php
if ($data->getPrivacy() == "WEP") {
?>
    <div class="col-xs-6 col-sm-4 placeholder">
        <h2><?=(($data->getKey() == null) ? "<i>null</i>" : $data->getKey())?></h2>
        <span class="text-muted">Passphrase</span>
    </div>
<?php
} else if ($data->getPrivacy() == "WPA2WPA" || $data->getPrivacy() == "WPA2" || $data->getPrivacy() == "WPA") {
?>
    <div class="col-xs-6 col-sm-4 placeholder">
        <h2>:|</h2>
        <span class="text-muted">Handshake</span>
    </div>
<?php
} else if ($data->getPrivacy() == "OPN") {
?>
<?php
}
?>
</div>
<?php
    } else {
        if ($data->getPrivacy() == "WEP") {
            $privacy = "<font color='green'>WEP</font>";
        } else if ($data->getPrivacy() == "WEP") {
            $privacy = "<font color='#0F0'>OPN</font>";
        } else {
            $privacy = "<font color='red'>" . $data->getPrivacy() . "</font>";
        }
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
        <h2><?=$privacy?></h2>
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
<?php
if ($data->getPrivacy() == "WEP") {
?>
    <div class="col-xs-6 col-sm-4 placeholder">
        <h2><?=(($data->getKey() == null) ? "<i>null</i>" : "<font color='#0F0'>" . $data->getKey() . "</font>")?></h2>
        <span class="text-muted">Passphrase</span>
    </div>
<?php
} else if ($data->getPrivacy() == "WPA2WPA" || $data->getPrivacy() == "WPA2" || $data->getPrivacy() == "WPA") {
?>
    <div class="col-xs-6 col-sm-4 placeholder">
        <h2><?=(($data->getHandshake() == 0) ? "<font color='red'>no :(</font>" : "<font color='green'>yep! :)</font>")?></h2>
        <span class="text-muted"><?=(($data->getHandshake() == 0) ? "Handshake" : "<a href='scans/" . $scan->getGUID() . "-01.cap'>Handshake</a>")?></span>
    </div>
<?php
} else if ($data->getPrivacy() == "OPN") {
?>
<?php
}
?>
    <div class="col-xs-6 col-sm-8 placeholder">
        <textarea rows="7" cols="100%" readonly><?=$wicker->deControllify($scan->getLog())?></textarea><br>
        <span class="text-muted">Output of last command</span>
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
                <th>Actions</th>
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
                <td><input type="button" class="btn-success" value="Deauth" onClick="window.location='apview.php?do=deauth&deauthmac=<?=$client->getMac()?>&parent_scan=<?=$_GET['parent_scan']?>&scanid=<?=$_GET['id']?>&bssid=<?=$_GET['bssid']?>'"></td>
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