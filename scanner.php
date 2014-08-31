<?php
require_once("Wicker.php");
require_once("Scan.class.php");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?=$wicker->heading("Scanner")?>
        <link href="css/bars.css" rel="stylesheet">
        <id hidden><?=$_GET['id']?></id>
    </head>
    <body>
        <?=$wicker->navbar()?>
        <div class="container-fluid">
            <div class="row">
                <?=$wicker->menu("scanner")?>
                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                    <h1 class="page-header">Scanner</h1>

                    <div class="row placeholders">
                        <div class="col-xs-6 col-sm-3 placeholder">
                            <h2><?=$wicker->scanfiles()?></h2>
                            <span class="text-muted">Scans</span>
                        </div>
                        <div class="col-xs-6 col-sm-3 placeholder">
                            <h2>0%</h2>
                            <span class="text-muted">Success Rate</span>
                        </div>
                        <div class="col-xs-6 col-sm-3 placeholder">
                            <h2>0</h2>
                            <span class="text-muted">APs</span>
                        </div>
                        <div class="col-xs-6 col-sm-3 placeholder">
                            <h2>0</h2>
                            <span class="text-muted">Clients</span>
                        </div>
                    </div>

                    <h2 class="sub-header">Previous Scans</h2>
                    <div class="table-responsive">
                        <input type="button" class="btn-success" value="New Scan" onClick="window.location='scanctl.php?do=newscan'">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Actions</th>
                                    <th>ESSID</th>
                                    <th>BSSID</th>
                                    <th>Uploaded</th>
                                </tr>
                            </thead>
                            <tbody>
<?php
$statement = $wicker->db->con()->prepare("SELECT * FROM `scans` ORDER BY `id` DESC");
$statement->execute();
for ($a = 0; $a < $statement->rowCount(); $a++) {
    $info = $statement->fetchObject();
    $scan = Scan::FromDB($info->id);
?>
                                <tr>
                                    <td><?=$a+1?></td>
                                    <td><a href="scanview.php?id=<?=$scan->getID()?>">View</a></td>
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
