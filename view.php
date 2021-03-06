<?php
require_once("Wicker.php");
require_once("Attack.class.php");

$cap = CapFile::fromDB($_GET['id']);
if ($cap->getID() == 0) {
    header('Location: index.php');
    die;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?=$wicker->head("Viewing " . $cap->getESSID())?>
        <link href="css/view.css" rel="stylesheet">
        <id hidden><?=$_GET['id']?></id>
    </head>
    <body>
        <?=$wicker->heading()?>
        <div class="container-fluid">
            <div class="row">
                <?=$wicker->menu("null")?>
                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                    <h1 class="page-header"><small><?=$cap->getESSID()?></small></h1>

                    <div class="row placeholders">
                        <div class="col-xs-6 col-sm-4 placeholder">
                            <h2><?=strtoupper($cap->getBSSID())?></h2>
                            <span class="text-muted">BSSID/AP MAC</span>
                        </div>
                        <div class="col-xs-6 col-sm-4 placeholder">
                            <h2><?=strtoupper($cap->getPackets())?></h2>
                            <span class="text-muted">Packets</span>
                        </div>
                        <div class="col-xs-6 col-sm-3 placeholder">
                            <h2><?=round($cap->getSize()/1024, 2)?> KB</h2>
                            <span class="text-muted">Size</span>
                        </div>

                        <div class="col-xs-6 col-sm-4 placeholder">
                            <h2><span title="<?=$cap->getLocation()?>"><?=$cap->getLocation()?></span></h2>
                            <span class="text-muted">Location</span>
                        </div>
                        <div class="col-xs-6 col-sm-4 placeholder">
                            <h2><?=$cap->getPassword()?></h2>
                            <span class="text-muted">Password</span>
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
                                    <th>Dictionary</th>
                                    <th>Status</th>
                                    <th>Dictionary size</th>
                                    <th>Rate (w/s)</th>
                                    <th>Run Time</th>
                                    <th>ETC</th>
                                </tr>
                            </thead>
                            <tbody>

<?php
for ($a = 1; $a <= 9; $a++) {
    unset($status);
    unset($runtime);
    $attack = Attack::fromDB($cap->getID(), $a);
    $attack->updateData();
    echo "<tr id=\"$a\">";
?>
                                    <td id="actions">
                                        <div class="btn-group">
                                            <button type="button" onclick="execute(<?=$a?>)" class="btn btn-default">Execute</button>
                                            <button type="button" onclick="pauseToggle(<?=$a?>)" class="btn btn-default">Pause</button>
                                            <button type="button" onclick="terminate(<?=$a?>)" class="btn btn-default">Stop</button>
                                        </div>
                                    </td>
                                    <td id="dictionaryName"><?=$attack->getAttackName()?></td>
                                    <td id="status">
                                        <div class="progress">
                                            <div class="progress-bar noStatus active" 
                                            role="progressbar" aria-valuenow="0">
                                                ----
                                            </div>
                                        </div>
                                    </td>
                                    <td id="dictSize"><?=number_format($attack->getDictionarySize())?></td>
                                    <td id="rate"><?=number_format($attack->getRate())?></td>
                                    <td id="runtime"><?=$attack->getRuntime()?></td>
<?php
                                    if ($attack->getRate() == 0) {
                                        echo "<td id=\"etc\">--:--</td>";
                                    } else {
                                        $days = (int)(gmdate("d", round(($attack->getDictionarySize()-$attack->getCurrent())/$attack->getRate()))-1);
                                        if ($days < 10) {
                                            $days = "0" . $days;
                                        }
                                        echo "<td id=\"etc\">" . $days . gmdate(":H:i:s", round(($attack->getDictionarySize()-$attack->getCurrent())/$attack->getRate())) . "</td>";
                                    }
                                    echo "</tr>";
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
