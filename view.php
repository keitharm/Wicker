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
        <link href="css/bars.css" rel="stylesheet">
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
for ($a = 1; $a <= 6; $a++) {
    unset($status);
    unset($runtime);
    $attack = Attack::fromDB($cap->getID(), $a);
    $attack->updateData();
                echo "<tr id=\"$a\">";

                    if ($attack->getPID() == null || $attack->getStatus() == 2 || $attack->getStatus() == 3 || $attack->getStatus() == 4) {
                        echo "<td><a href=\"ctl.php?cmd=execute&id=" . $cap->getID() . "&attack=$a\">Execute</a> | Pause | Terminate</td>";
                    } else {
                        echo "<td>Execute | ";
                        if ($attack->getStatus() == 5) {
                            echo "<a href=\"ctl.php?cmd=resume&id=" . $cap->getID() . "&attack=$a\">Resume</a> | ";
                        } else {
                            echo "<a href=\"ctl.php?cmd=pause&id=" . $cap->getID() . "&attack=$a\">Pause</a> | ";
                        }
                        if ($attack->getStatus() == 1 || $attack->getStatus() == 5) {
                            echo "<a href=\"ctl.php?cmd=terminate&id=" . $cap->getID() . "&attack=$a\">Terminate</a>";
                        } else {
                            echo "Terminate";
                        }                                  
                        echo "</td>";
                    }
?>
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
                                        echo "<td id=\"etc\">" . (int)(gmdate("d", round(($attack->getDictionarySize()-$attack->getCurrent())/$attack->getRate()))-1) . gmdate(":H:i:s", round(($attack->getDictionarySize()-$attack->getCurrent())/$attack->getRate())) . "</td>";
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
        <script src="js/ajax.js"></script>
    </body>
</html>
