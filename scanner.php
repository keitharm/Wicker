<?php
require_once("Wicker.php");

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?=$wicker->heading("Scanner")?>
        <link href="css/bars.css" rel="stylesheet">
        <id hidden><?=$_GET['id']?></id>
    </head>
    <body>
        <?=$wicker->navbar()?>
        <div class="container-fluid">
            <div class="row">
                <?=$wicker->menu("scanner")?>
                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                    <h1 class="page-header">Scanner</h1>

                </div>
            </div>
        </div>
        <?=$wicker->footer()?>
        <script src="js/ajax.js"></script>
    </body>
</html>
