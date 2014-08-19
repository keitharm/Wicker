<?php
require_once("Wicker.php");
require_once("Attack.class.php");

$cap = CapFile::FromDB($_GET['id']);
if ($cap->getID() == 0) {
    header('Location: index.php');
    die;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?=$wicker->heading("Viewing " . $cap->getESSID())?>
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
                <?=$wicker->menu("null")?>
                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                    <h1 class="page-header"><?=$cap->getESSID()?></h1>

                    <div class="row placeholders">
                        <div class="col-xs-6 col-sm-3 placeholder">
                            <h2><?=$cap->getESSID()?></h2>
                            <span class="text-muted">ESSID</span>
                        </div>
                        <div class="col-xs-6 col-sm-3 placeholder">
                            <h2><?=strtoupper($cap->getBSSID())?></h2>
                            <span class="text-muted">BSSID/AP MAC</span>
                        </div>
                        <div class="col-xs-6 col-sm-3 placeholder">
                            <h2><?=strtoupper($cap->getPackets())?></h2>
                            <span class="text-muted">Packets</span>
                        </div>
                        <div class="col-xs-6 col-sm-3 placeholder">
                            <h2><?=round($cap->getSize()/1024, 2)?> KB</h2>
                            <span class="text-muted">Size</span>
                        </div>

                        <div class="col-xs-6 col-sm-4 placeholder">
                            <h2><span title="<?=$cap->getChecksum()?>"><?=substr($cap->getChecksum(), 0, 16)?>...</span></h2>
                            <span class="text-muted">Checksum</span>
                        </div>
                        <div class="col-xs-6 col-sm-5 placeholder">
                            <h2><?=$cap->getLocation()?></h2>
                            <span class="text-muted">Location</span>
                        </div>
                        <div class="col-xs-6 col-sm-3 placeholder">
                            <h2><?=$wicker->timeconv($cap->getTimestamp())?></h2>
                            <span class="text-muted">Imported</span>
                        </div>
                    </div>

                    <h2 class="sub-header">Attacks</h2>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>Attack Type</th>
                                    <th>PID</th>
                                    <th>Running</th>
                                    <th>Status</th>
                                    <th>Password</th>
                                    <th>Dictionary size</th>
                                    <th>% Complete</th>
                                    <th>Run Time</th>
                                    <th>ETA</th>
                                </tr>
                            </thead>
                            <tbody>
<?php
for ($a = 1; $a <= 6; $a++) {
    unset($status);
    unset($runtime);
    $attack = Attack::fromDB($cap->getID(), $a);
    $attack->updateData();
?>
                                <tr>
<?php
                                    if ($attack->getPID() == null) {
?>
                                    <td><a href="execute.php?id=<?=$cap->getID()?>&attack=<?=$a?>">Execute</a> | <a href="terminate.php?id=<?=$cap->getID()?>&attack=<?=$a?>">Terminate</a></td>
<?php
                                    } else {
?>
                                    <td>Execute | 
<?php
                                    if ($attack->getStatus() == 1) {
?>
                                    <a href="terminate.php?id=<?=$cap->getID()?>&attack=<?=$a?>">Terminate</a>
<?php
} else {
?>                                  Terminate
<?php
}
?>                                  </td>
<?php
                                    }
?>
                                    <td><?=$attack->getAttackName()?></td>
                                    <td><?=$attack->getPID()?></td>
                                    <td><?=$attack->getStatusText()?></td>
                                    <td><?=$attack->getStatusName()?></td>
                                    <td><?=$attack->getPassword()?></td>
                                    <td><?=number_format($attack->getDictionarySize())?></td>
                                    <td><?=round($attack->getCurrent()/$attack->getDictionarySize()*100, 2)?>%</td>
                                    <td><?=$attack->getRuntime()?></td>
<?php
                                    if ($attack->getRate() == 0) {
?>
                                    <td>--:--</td>
<?php
                                    } else {
?>
                                    <td><?=gmdate("H:i:s", round(($attack->getDictionarySize()-$attack->getCurrent())/$attack->getRate()))?></td>
<?php
                                    }
?>  
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
