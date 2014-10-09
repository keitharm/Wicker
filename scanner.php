<?php
require_once("Wicker.php");
require_once("Scan.class.php");

if ($_GET['do'] == "hide") {
    $id = $_GET['id'];
    $scan = Scan::fromDB($id);
    if ($scan->getStatus() != 3) {
        $scan->setStatus(3);
    }
    header('Location: scanner.php');
    die;
} else if ($_GET['do'] == "unhide") {
    $id = $_GET['id'];
    $scan = Scan::fromDB($id);
    if ($scan->getStatus() == 3) {
        $scan->setStatus(2);
    }
    header('Location: scanner.php');
    die;
} else if ($_GET['do'] == "showhidden") {
    $hidden = true;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?=$wicker->head("Scanner")?>
        <link href="css/bars.css" rel="stylesheet">
        <id hidden><?=$_GET['id']?></id>
    </head>
    <body>
        <?=$wicker->heading()?>
        <div class="container-fluid">
            <div class="row">
                <?=$wicker->menu("scanner")?>
                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
<?php
if (!$hidden) {
?>
                    <h1 class="page-header">Scanner - <a href="scanner.php?do=showhidden">Show hidden scans</a></h1>
<?php
} else {
?>
                    <h1 class="page-header">Scanner - <a href="scanner.php">Show normal scans</a></h1>
<?php
}
?>

                    <div class="row placeholders">
                        <div class="col-xs-6 col-sm-3 placeholder">
                            <h2><?=$wicker->countScans($hidden)?></h2>
                            <span class="text-muted">Scans</span>
                        </div>
                        <div class="col-xs-6 col-sm-3 placeholder">
                            <h2><?=$wicker->totalAPs()?></h2>
                            <span class="text-muted">APs</span>
                        </div>
                        <div class="col-xs-6 col-sm-3 placeholder">
                            <h2><?=$wicker->totalClients()?></h2>
                            <span class="text-muted">Clients</span>
                        </div>
                        <div class="col-xs-6 col-sm-3 placeholder">
                            <h2></h2>
                            <span class="text-muted"></span>
                        </div>
                    </div>
<?php
if (!$hidden) {
?>
                    <h2 class="sub-header">Previous Scans</h2>
<?php
} else {
?>
                    <h2 class="sub-header">Hidden Scans</h2>
<?php
}
?>
                    <div class="table-responsive">
                        <form action="scanctl.php?do=newscan" method="POST">
                            <input type="checkbox" name="wep" value="wep" checked="checked"> WEP <input type="checkbox" name="wpa" value="wpa" checked="checked"> WPA
                            <input type="submit" class="btn-success" value="New Scan">
                        </form>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Actions</th>
                                    <th>APs</th>
                                    <th>Clients</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
<?php
if (!$hidden) {
    $action = "hide";
    $statement = $wicker->db->con()->prepare("SELECT * FROM `scans` WHERE `status` <> 3 AND `individual` <> 1 ORDER BY `id` DESC");
} else {
    $action = "unhide";
    $statement = $wicker->db->con()->prepare("SELECT * FROM `scans` WHERE `status` = 3 AND `individual` <> 1 ORDER BY `id` DESC");
}
$statement->execute();
for ($a = 0; $a < $statement->rowCount(); $a++) {
    $info = $statement->fetchObject();
    $scan = Scan::FromDB($info->id);
?>
                                <tr>
                                    <td><?=$a+1?></td>
                                    <td><a href="scanview.php?id=<?=$scan->getID()?>">View</a> | <a href="scanner.php?do=<?=$action?>&id=<?=$scan->getID()?>"><?=ucfirst($action)?></a></td>
                                    <td><?=$scan->getAPCount()?></td>
                                    <td><?=$scan->getClientCount()?></td>
                                    <td><?=$wicker->timeconv($scan->getTime())?></td>
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
        <script src="js/ajax.js"></script>
    </body>
</html>
