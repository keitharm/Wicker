<?php
    function menu($selected) {
        $active[$selected] = " class=\"active\"";
?>
        <div class="col-sm-3 col-md-2 sidebar">
            <ul class="nav nav-sidebar">
                <li<?=$active["index"]?>><a href="index.php">Dashboard</a></li>
                <li<?=$active["upload"]?>><a href="upload.php">Uploader</a></li>
                <!--<li<?=$active["stats"]?>><a href="stats.php">Statistics</a></li>-->
                <!--<li<?=$active["about"]?>><a href="stats.php">About</a></li>-->
                <!--<li<?=$active["local"]?>><a href="upload.php">Local Scan/Crack</a></li>-->
                <!--<li<?=$active["crack"]?>><a href="crack.php">Cracker</a></li>-->
            </ul>
        </div>
<?php
    }
?>
