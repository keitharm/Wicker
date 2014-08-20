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
        <id hidden><?=$_GET['id']?></id>
    </head>
    <body>
        <?=$wicker->navbar()?>
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
                                    <!--<th>PID</th>-->
                                    <!--<th>Running</th>-->
                                    <th>Status</th>
                                    <th>Password</th>
                                    <th>Dictionary size</th>
                                    <th>Current</th>
                                    <th>% Complete</th>
                                    <th>Rate (w/s)</th>
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
                                <tr id="<?= $a ?>">
<?php
                                    if ($attack->getPID() == null || $attack->getStatus() == 2 || $attack->getStatus() == 3 || $attack->getStatus() == 4) {
?>
                                    <td><a href="ctl.php?cmd=execute&id=<?=$cap->getID()?>&attack=<?=$a?>">Execute</a> | Pause | Terminate</td>
<?php
                                    } else {
?>
                                    <td>Execute | 
<?php
                                    if ($attack->getStatus() == 5) {
?>
                                    <a href="ctl.php?cmd=resume&id=<?=$cap->getID()?>&attack=<?=$a?>">Resume</a> | 
<?php
                                    } else {
?>
                                    <a href="ctl.php?cmd=pause&id=<?=$cap->getID()?>&attack=<?=$a?>">Pause</a> | 
<?php
                                    }
                                    if ($attack->getStatus() == 1 || $attack->getStatus() == 5) {
?>
                                    <a href="ctl.php?cmd=terminate&id=<?=$cap->getID()?>&attack=<?=$a?>">Terminate</a>

<?php
} else {
?>                                  Terminate
<?php
}
?>                                  </td>
<?php
                                    }
?>
                                    <td id="attackStrength"><?=$attack->getAttackName()?></td>
                                    <!--<td><?=$attack->getPID()?></td>-->
                                    <!--<td><?=$attack->getStatusText()?></td>-->
                                    <td id="status"><?=$attack->getStatusName()?></td>
                                    <td id="password"><?=$attack->getPassword()?></td>
                                    <td id="dictSize"><?=number_format($attack->getDictionarySize())?></td>
                                    <td id="current"><?=number_format($attack->getCurrent())?></td>
                                    <td id="complete"><?=round($attack->getCurrent()/$attack->getDictionarySize()*100, 2)?>%</td>
                                    <td id="rate"><?=number_format($attack->getRate())?></td>
                                    <td id="runtime"><?=$attack->getRuntime()?></td>
<?php
                                    if ($attack->getRate() == 0) {
?>
                                    <td id="eta">--:--</td>
<?php
                                    } else {
?>
                                    <td id="eta"><?=(int)(gmdate("d", round(($attack->getDictionarySize()-$attack->getCurrent())/$attack->getRate()))-1) . gmdate(":H:i:s", round(($attack->getDictionarySize()-$attack->getCurrent())/$attack->getRate()))?></td>
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
        <script src="js/ajax.js"></script>
    </body>
</html>
