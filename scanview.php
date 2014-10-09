<?php
require_once("Wicker.php");
require_once("Scan.class.php");

$scan = Scan::FromDB($_GET['id']);
if ($scan->getID() == 0) {
    #header('Location: index.php');
    #die;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?=$wicker->head("Viewing Scan #" . $scan->getID())?>
        <link href="css/bars.css" rel="stylesheet">
        <id hidden><?=$_GET['id']?></id>
        <running hidden><?=(($scan->getStatus() == 1) ? "yes" : "no")?></running>
    </head>
    <body>
        <?=$wicker->heading()?>
        <div class="container-fluid">
            <div class="row">
                <?=$wicker->menu("null")?>
                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                    <h1 class="page-header"><small><?=$scan->getID()?></small></h1>
<?php
if ($scan->getStatus() == 1) {
?>
                    <input type="button" class="btn-danger" value="Terminate Scan" onClick="window.location='scanctl.php?do=terminate&id=<?=$scan->getID()?>'">
<?php
}
?>
                    <div class="table-responsive">
                        <div id="apupdate">
                            <!-- AP Update here-->
                        </div>
                        <div id="clientupdate">
                            <!-- Client Update here-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?=$wicker->footer()?>
        <script src="js/updateScan.js"></script>
    </body>
</html>
