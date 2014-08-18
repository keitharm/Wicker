<?php
require_once("Wicker.php");

if ($_FILES != null) {
    $tmpname = $_FILES["file"]["tmp_name"];
    $name = $_FILES["file"]["name"];
    $size = $_FILES["file"]["size"];
    $ext = pathinfo($name, PATHINFO_EXTENSION);
    $bad = false;
    if (!in_array($ext, array("cap"))) {
        $bad = true;
        $msg = "Only . files are accepted.";
    }

    if ($size > 1024*1024*10) {
        #$bad = true;
        #$msg = "Only files up to 20 mb are accepted.";
    }

    if (!$bad) {
        // Random Generated Location
        do {
            $location = $wicker->random(1, 10);
            $_SESSION['tmpdata'] = $location;
            $_SESSION['tmptitle'] = $ext;
        } while (file_exists("uploads/" . $location . ".cap"));

        $check = move_uploaded_file($tmpname, "uploads/" . $location . ".cap");
        $wicker->importcap($location . ".cap");
        die;

    }
}

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
                <?=$wicker->menu("upload")?>
                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                    <h1 class="page-header">Upload</h1>
                    <h3>Upload a .cap file for cracking</h3>
                    <form action="upload.php" method="POST" enctype="multipart/form-data">
                        <input type="file" name="file"><br>
                        <input type="submit" class="btn btn-success" value="Upload">
                    </form>
                </div>
            </div>
        </div>
        <?=$wicker->footer()?>
    </body>
</html>
