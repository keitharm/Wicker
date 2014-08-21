<?php
require_once("Wicker.php");

$mode = 0;

if ($mode == 1) {
    // Create a stream
    $options  = array('http' => array('user_agent' => 'RandomAPI'));
    $context  = stream_context_create($options);

    // Open the file using the HTTP headers set above
    $json  = file_get_contents("https://randomapi.com/api/?key=NKOB-8C2V-SX9C-RA05&id=6byfjx&results=25", false, $context);
    $stats = file_get_contents("https://randomapi.com/api/?key=NKOB-8C2V-SX9C-RA05&id=5gb2vm", false, $context);
} else {
    $json  = file_get_contents("json/aps/" . mt_rand(0, 10) . ".json");
    $stats = file_get_contents("json/stats/" . mt_rand(0, 10) . ".json");
}

$json  = json_decode($json);
$stats = json_decode($stats);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?=$wicker->heading("Dashboard")?>
    </head>
    <body>
        <?=$wicker->navbar()?>
        <div class="container-fluid">
            <div class="row">
                <?=$wicker->menu("index")?>
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
                            <h2>1,188,494,739</h2>
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
$statement = $wicker->db->con()->prepare("SELECT * FROM cap WHERE `status` = ? ORDER BY id DESC");
$statement->execute(array(0));
for ($a = 0; $a < $statement->rowCount(); $a++) {
    $info = $statement->fetchObject();
    $cap = CapFile::FromDB($info->id);
?>
                                <tr>
                                    <td><?=$a+1?></td>
                                    <td><a href="view.php?id=<?=$cap->getID()?>">View</a> | <a href="ctl.php?cmd=hide&id=<?=$cap->getID()?>">Hide</a></td>
                                    <td><?=$cap->getESSID()?></td>
                                    <td><?=$cap->getBSSID()?></td>
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
        <?=$wicker->footer()?>
    </body>
</html>
