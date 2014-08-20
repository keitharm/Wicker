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
        <?=$wicker->navbar()?>
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