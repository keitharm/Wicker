<?php
    require_once('Wicker.php');
?>

<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php"><img src='img/logo.png' width="90" style="margin-top: -20px; margin-right: -5px"> - The stupid wifi cracker</a>
        </div>
        <div class="navbar-center">
            <div class="line1">
                System Status
            </div>
            <div class="line2">
                <table class="tablestatus">
                    <thead>
<?php
                        $name = array("CPU0", "CPU1", "CPU2", "CPU3", "Uptime", "1m", "5m", "15m", "Uploads", "Logs");
                        for ($a = 0; $a < count($name); $a++) {
?>
                            <th><?=$name[$a]?></th>
                            <th><?=$wicker->space(5)?></th>
<?php
                        }
?>
                    </thead>
                    <tbody>
<?php
                        $status = $wicker->status();
                        for ($a = 0; $a < count($name); $a++) {
?>
                            <td><?=$status[$a]?></td>
                            <td><?=$wicker->space(5)?></td>
<?php
                        }
?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>