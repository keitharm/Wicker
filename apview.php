<?php
require_once("Wicker.php");
require_once("Scan.class.php");
require_once("AP.class.php");

$parent_ap   = AP::fromDB($_GET['parent_scan'], $_GET['bssid']);
$ind_ap      = AP::fromDB($_GET['scanid'], $_GET['bssid']);
$parent_scan = Scan::fromDB($_GET['parent_scan']);
$ind_scan    = Scan::fromDB($_GET['scanid']);

if ($_GET['do'] == "terminate") {
    $scan = Scan::fromDB($_GET['scanid']);
    if ($scan->getPID() != 0 && $scan->getStatus() == 1) {
        $scan->setStatus(2);
        system("sudo kill " . $scan->getPID());
        header('Location: apview.php?parent_scan=' . $_GET['parent_scan'] . '&scanid=' . $_GET['scanid'] . '&bssid=' . $_GET['bssid']);
        die;
    }
}

if ($_GET['do'] == "terminatenstart") {
    // Terminate parent scan
    $previous = Scan::fromDB($_GET['parent_scan']);
    if ($previous->getPID() != 0 && $previous->getStatus() == 1) {
        $previous->setStatus(2);
        system("sudo kill " . $previous->getPID());
    }

    // Start new individual scan
    $scan = Scan::newScan();
    $scan->setStatus(1);
    $scan->startIndScan($parent_ap->getBSSID(), $parent_ap->getChannel());
    $parent_ap->setIndScanID($scan->getID());
    header('Location: apview.php?parent_scan=' . $_GET['parent_scan'] . '&scanid=' . $scan->getID() . '&bssid=' . $_GET['bssid']);
    die;
}

if ($_GET['scanid'] == 0) {
    $data = $parent_ap;
} else {
    $data = $ind_ap;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?=$wicker->head("Viewing " . $data->getESSID())?>
        <link href="css/view.css" rel="stylesheet">
        <id hidden><?=$_GET['scanid']?></id>
        <running hidden><?=(($ind_scan->getStatus() == 1) ? "yes" : "no")?></running>
        <bssid hidden><?=$_GET['bssid']?></bssid>
    </head>
    <body>
        <?=$wicker->heading()?>
        <div class="container-fluid">
            <div class="row">
                <?=$wicker->menu("null")?>
                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                    <div id="apinfo">
                        <!-- apview info here -->
                    </div>
                    <h1 class="sub-header"><small>Actions</small></h1>
<?php
if ($parent_scan->getStatus() != 2 && $parent_scan->getStatus() != 3) {
?>
                    <input type="button" class="btn-success" value="Terminate parent scan and start individual scan" onClick="window.location='apview.php?do=terminatenstart&parent_scan=<?=$parent_scan->getID()?>&bssid=<?=$_GET['bssid']?>'">
<?php
} else {
    if ($_GET['scanid'] == 0 || ($_GET['scanid'] != 0 && $ind_scan->getStatus() >= 2)) {
?>
                    <input type="button" class="btn-success" value="Start individual scan" onClick="window.location='apview.php?do=terminatenstart&parent_scan=<?=$parent_scan->getID()?>&bssid=<?=$_GET['bssid']?>'">
<?php
    } else if ($_GET['scanid'] != 0 && $ind_scan->getStatus() == 1) {
?>
                    <input type="button" class="btn-danger" value="Terminate individual scan" onClick="window.location='apview.php?do=terminate&parent_scan=<?=$_GET['parent_scan']?>&scanid=<?=$_GET['scanid']?>&bssid=<?=$_GET['bssid']?>'">
<?php
    }
}
?>
                    <h1 class="sub-header"><small>Clients for current scan</small></h1>
                    <div class="table-responsive" id="currentclients">
                        <!--Clients for current scan here-->
                    </div>
                    <h1 class="sub-header"><small>Previous Scans</small></h1>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Scan #</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
<?php
unset($a);
$statement = $wicker->db->con()->prepare("SELECT * FROM `aps` WHERE `bssid` = ? AND `scan_id` <> ? ORDER BY `scan_id` DESC");
$statement->execute(array($parent_ap->getBSSID(), $_GET['scanid']));
while ($info = $statement->FetchObject()) {
    $a++;
?>
                                <tr>
                                    <td><a href="scanview.php?id=<?=$info->scan_id?>" target="_blank"><?=$info->scan_id?></a></td>
                                    <td><?=$wicker->timeconv($info->first_seen)?></td>
                                </tr>
<?php
}
?>
                            </tbody>
                        </table>
                    </div>
                    <h1 class="sub-header"><small>All known clients</small></h1>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Scan #</th>
                                    <th>BSSID</th>
                                    <th>First Seen</th>
                                </tr>
                            </thead>
                            <tbody>
<?php
unset($a);
$statement = $wicker->db->con()->prepare("SELECT * FROM `clients` WHERE `bssid` = ? GROUP BY `mac` ORDER BY `scan_id` DESC");
$statement->execute(array($parent_ap->getBSSID()));
while ($info = $statement->FetchObject()) {
    $a++;
?>
                                <tr>
                                    <td><?=$a?></td>
                                    <td><a href="scanview.php?id=<?=$info->scan_id?>" target="_blank"><?=$info->scan_id?></a></td>
                                    <td><span title="<?=$wicker->getMan($info->mac)?>"><?=$info->mac?></span></td>
                                    <td><?=$wicker->timeconv($info->first_seen)?></td>
                                </tr>
<?php
}
?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?=$wicker->footer()?>
        <script src="js/updateAP.js"></script>
    </body>
</html>
