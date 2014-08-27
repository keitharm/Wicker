<?php
require_once("Wicker.php");

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?include('htmlBlocks/head.php');heading('Dashboard')?>
    </head>
    <body>
        <?include('htmlBlocks/header.php');?>
        <div class="container-fluid">
            <div class="row">
                <?include('htmlBlocks/nav.php');menu("index")?>
                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                    <h1 class="page-header">Dashboard</h1>

                    <div class="row placeholders">
                        <div class="col-xs-6 col-sm-3 placeholder">
                            <h2><?=$wicker->capfiles()?></h2>
                            <span class="text-muted">.Cap Files</span>
                        </div>
                        <div class="col-xs-6 col-sm-3 placeholder">
                            <h2>0%</h2>
                            <span class="text-muted">Success Rate</span>
                        </div>
                        <div class="col-xs-6 col-sm-3 placeholder">
                            <h2>9,611,286</h2>
                            <span class="text-muted">Passwords</span>
                        </div>
                        <div class="col-xs-6 col-sm-3 placeholder">
                            <h2>0</h2>
                            <span class="text-muted">Active Operations</span>
                        </div>
                    </div>

                    <h2 class="sub-header">Uploaded .caps</h2>
                    <div class="table-responsive">
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
$statement = $wicker->db->con()->prepare("SELECT * FROM `caps` WHERE `status` = ? ORDER BY `id` DESC");
$statement->execute(array(0));
for ($a = 0; $a < $statement->rowCount(); $a++) {
    $info = $statement->fetchObject();
    $cap = CapFile::FromDB($info->id);
?>
                                <tr>
                                    <td><?=$a+1?></td>
                                    <td><a href="view.php?id=<?=$cap->getID()?>">View</a> | <a href="ctl.php?cmd=hide&id=<?=$cap->getID()?>">Hide</a></td>
                                    <td><?=$cap->getESSID()?></td>
                                    <td><?=strtoupper($cap->getBSSID())?></td>
                                    <td><?=$wicker->timeconv($cap->getTimestamp())?>
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
        <?include('htmlBlocks/scriptIncludes.php')?>
    </body>
</html>
