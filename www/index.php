<?php
require_once("Wicker.php");

$mode = 0;

if ($mode == 1) {
    // Create a stream
    $options  = array('http' => array('user_agent' => 'RandomAPI'));
    $context  = stream_context_create($options);

    // Open the file using the HTTP headers set above
    $json = file_get_contents("https://randomapi.com/api/?key=NKOB-8C2V-SX9C-RA05&id=6byfjx&results=25", false, $context);
    $stats = file_get_contents("https://randomapi.com/api/?key=NKOB-8C2V-SX9C-RA05&id=5gb2vm", false, $context);
} else {
    $json = file_get_contents("json/aps/" . mt_rand(0, 10) . ".json");
    $stats = file_get_contents("json/stats/" . mt_rand(0, 10) . ".json");
}

$json = json_decode($json);
$stats = json_decode($stats);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?=$wicker->heading("Dashboard")?>
    </head>

    <body>

        <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.php">Wicker - The stupid wifi cracker</a>
                </div>
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <!--
                            <form class="navbar-form navbar-right" action="login" method="POST">
                                <div class="form-group">
                                    <input type="text" name="username" placeholder="Username" class="form-control" required <?=$auto?>>
                                </div>
                                <div class="form-group">
                                    <input type="password" name="password" placeholder="Password" class="form-control" required>
                                </div>
                                &nbsp;
                                <input type="submit" class="btn btn-success" value="Login">&nbsp;<input type="button" onClick="window.location='signup'" class="btn btn-primary" value="Sign up">
                            </form>
                            -->
                        </li>
                        <li><a href="https://github.com/solewolf/wicker" target="_blank">Github</a></li>
                        <!--
                        <li><a href="#">Settings</a></li>
                        <li><a href="#">Profile</a></li>
                        <li><a href="#">Help</a></li>
                        -->
                    </ul>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <?=$wicker->menu("index")?>
                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                    <h1 class="page-header">Dashboard</h1>

                    <div class="row placeholders">
                        <div class="col-xs-6 col-sm-3 placeholder">
                            <h2><?=$stats->results[0]->{'Wicker Stats'}->cap?></h2>
                            <span class="text-muted">.Cap Files</span>
                        </div>
                        <div class="col-xs-6 col-sm-3 placeholder">
                            <h2><?=$stats->results[0]->{'Wicker Stats'}->success?></h2>
                            <span class="text-muted">Success Rate</span>
                        </div>
                        <div class="col-xs-6 col-sm-3 placeholder">
                            <h2>1,178,735,606</h2>
                            <span class="text-muted">Passwords</span>
                        </div>
                        <div class="col-xs-6 col-sm-3 placeholder">
                            <h2><?=$stats->results[0]->{'Wicker Stats'}->active?></h2>
                            <span class="text-muted">Active Operations</span>
                        </div>
                    </div>

                    <h2 class="sub-header">Uploaded .caps</h2>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>View</th>
                                    <th>ESSID</th>
                                    <th>BSSID</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
<?php
$statement = $wicker->db->con()->prepare("SELECT * FROM cap ORDER BY ? DESC");
$statement->execute(array("timestamp"));
for ($a = 0; $a < $statement->rowCount(); $a++) {
    $info = $statement->fetchObject();
    $cap = CapFile::FromDB($info->id);
?>
                                <tr>
                                    <td><a href="view.php?id=<?=$cap->getID()?>">View</a></td>
                                    <td><?=$cap->getESSID()?></td>
                                    <td><?=$cap->getBSSID()?></td>
                                    <td><?=$cap->getStatus()?></td>
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
