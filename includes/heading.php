<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php"><img src='img/logo.png' width="90" style="margin-top: -20px; margin-right: -5px"> - The stupid wifi pen tester</a>
        </div>
        <div class="navbar-center">
            <div class="line1">
                System Status
            </div>
            <div class="line2">
                <table class="tablestatus">
                    <thead>
<?php
                        $name = array("CPU 1", "CPU 2", "CPU 3", "CPU 4", "GPU", "Uptime", "1m", "5m", "15m", "Uploads", "Logs", "Scans");
                        for ($a = 0; $a < count($name); $a++) {
?>
                            <th><?=$name[$a]?></th>
                            <th><?=$this->space(5)?></th>
<?php
                        }
?>
                    </thead>
                    <tbody>
<?php
                        $status = $this->status();
                        for ($a = 0; $a < count($name); $a++) {
?>
                            <td id="<?=str_replace(' ','',$name[$a])?>"><?=$status[$a]?></td>
                            <td><?=$this->space(5)?></td>
<?php
                        }
?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
