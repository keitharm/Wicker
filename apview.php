<?php
require_once("Wicker.php");
require_once("Scan.class.php");
require_once("AP.class.php");

$scan = Scan::fromDB($_GET['scanid']);
$ap   = AP::fromDB($_GET['scanid'], $_GET['bssid']);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?=$wicker->head("Viewing " . $scan->getID())?>
        <link href="css/view.css" rel="stylesheet">
        <id hidden><?=$_GET['id']?></id>
    </head>
    <body>
        <?=$wicker->heading()?>
        <div class="container-fluid">
            <div class="row">
                <?=$wicker->menu("null")?>
                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                    <h1 class="page-header"><small><?=$ap->getESSID()?></small></h1>

                    <div class="row placeholders">
                        <div class="col-xs-6 col-sm-4 placeholder">
                            <h2><span title="<?=$wicker->getMan($ap->getBSSID())?>"><?=$ap->getBSSID()?></span></h2>
                            <span class="text-muted">BSSID/AP MAC</span>
                        </div>
                        <div class="col-xs-6 col-sm-4 placeholder">
                            <h2><?=$wicker->timeconv($ap->getFirstSeen())?></h2>
                            <span class="text-muted">First Seen</span>
                        </div>
                        <div class="col-xs-6 col-sm-3 placeholder">
                            <h2><?=$wicker->timeconv($ap->getLastSeen())?></h2>
                            <span class="text-muted">Last Seen</span>
                        </div>

                    </div>
                    <h1 class="sub-header"><small>Clients for current scan</small></h1>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>BSSID</th>
                                    <th>First Seen</th>
                                    <th>Last Seen</th>
                                </tr>
                            </thead>
                            <tbody>
<?php
unset($a);
$statement = $wicker->db->con()->prepare("SELECT * FROM `clients` WHERE `bssid` = ? AND `scan_id` = ? GROUP BY `mac`");
$statement->execute(array($ap->getBSSID(), $_GET['scanid']));
while ($info = $statement->FetchObject()) {
    $a++;
?>
                                <tr>
                                    <td><?=$a?></td>
                                    <td><span title="<?=$wicker->getMan($info->mac)?>"><?=$info->mac?></span></td>
                                    <td><?=$wicker->timeconv($info->first_seen)?></td>
                                    <td><?=$wicker->timeconv($info->last_seen)?></td>
                                </tr>
<?php
}
?>
                            </tbody>
                        </table>
                    </div>
                    <h1 class="sub-header"><small>Previous Scans</small></h1>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
<?php
$statement = $wicker->db->con()->prepare("SELECT * FROM `aps` WHERE `bssid` = ? AND `scan_id` <> ?");
$statement->execute(array($ap->getBSSID(), $_GET['scanid']));
while ($info = $statement->FetchObject()) {
    $a++;
?>
                                <tr>
                                    <td><a href="scanview.php?id=<?=$info->scan_id?>"><?=$a?></a></td>
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
$statement->execute(array($ap->getBSSID()));
while ($info = $statement->FetchObject()) {
    $a++;
?>
                                <tr>
                                    <td><?=$a?></td>
                                    <td><?=$info->scan_id?></td>
                                    <td><span title="<?=$wicker->getMan($info->mac)?>"><?=$info->mac?></span></td>
                                    <td><?=$wicker->timeconv($info->first_seen)?></td>
                                </tr>
<?php
}
?>
                            </tbody>
                        </table>
                    </div>
                    <h3>Map</h3>
                    <iframe src="mapview.php?ap&scanid=<?=$_GET['scanid']?>&bssid=<?=$_GET['bssid']?>" width="100%" height="500px"></iframe>
                </div>
            </div>
        </div>
        <?=$wicker->footer()?>
        <script src="js/viewStatus.js"></script>
    </body>
</html>
