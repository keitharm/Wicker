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
                    <h1 class="page-header"><a href="scanview.php?id=<?=$_GET['scanid']?>"><small>Back to scan</small></a> - <small><?=$ap->getESSID()?></small></h1>

                    <div class="row placeholders">
                        <div class="col-xs-6 col-sm-4 placeholder">
                            <h2><?=strtoupper($ap->getBSSID())?></h2>
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
                </div>
            </div>
        </div>
        <?=$wicker->footer()?>
        <script src="js/viewStatus.js"></script>
    </body>
</html>
