<?php
require_once("Wicker.php");

$conf = unserialize($config->viewConfigSerialized());

if ($_GET['do'] == "enablemonitor") {
    $wicker->enableMon0();
} else if ($_GET['do'] == "disablemonitor") {
    $wicker->disableMon0();
} else if ($_GET['do'] == "save" && $_POST != null) {
    foreach ($_POST as &$val) {
        $val = trim($val);
    }
    $config->setDBURL($_POST['url']);
    $config->setDBName($_POST['name']);
    $config->setDBUser($_POST['username']);
    $config->setDBPass($_POST['password']);

    $config->setUser($_POST['user']);

    $config->setRFkill($_POST['rfkill']);
    $config->setInterface($_POST['interface']);

    $config->setAirodumpng($_POST['airodump-ng']);
    $config->setPyrit($_POST['pyrit']);
    $config->setPCAPFix($_POST['pcapfix']);
    $config->setMySQL($_POST['mysql']);
    $config->setSensors($_POST['lm-sensors']);

    header('Location: settings.php?do=success');
    die;
}

if ($_GET['do'] == "success") {
    $msg = "<font color='green'>Settings saved successfully!</font>";
}
/*
// Generate conf file
echo W . "\nGenerating Wicker Configuration file";
pause();
file_put_contents("wicker.conf.php", "<?php\ndie;\n#" . serialize($data) . "\n?>\n");
if (!file_exists("wicker.conf.php")) {
    file_put_contents("/tmp/wicker.conf.php", "<?php\ndie;\n#" . serialize($data) . "\n?>\n");
    echo R . "Error" . W . ": Unable to save configuration file.\nPlease copy file /tmp/wicker.conf.php to this directory manually to complete setup.\n";
} else {
    echo G . "\nWicker Configuration file saved successfully!\n";
}
*/
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?=$wicker->head("Settings")?>
    </head>
    <body>
        <?=$wicker->heading()?>
        <div class="container-fluid">
            <div class="row">
                <?=$wicker->menu("settings")?>
                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                    <h1 class="page-header">Settings</h1>
                    <h2 class="sub-header">Configuration file</h2>
                    <div class="table-responsive">
                        <?=$msg?>
                        <table class="table-condensed table-striped">
                            <tbody>
                                <form action="settings.php?do=save" method="POST">
                                    <tr>
                                        <td><b>Tools</b></td>
                                        <td></td>
                                        <td><b>Database</b></td>
                                        <td></td>
                                        <td><b>Webserver</b></td>
                                    </tr>
                                    <tr>
                                        <td>Airodump-ng</td>
                                        <td><input type="text" name="airodump-ng" value="<?=$conf['tools']['airodump-ng']?>"></td>
                                        <td>URL</td>
                                        <td><input type="text" name="url" value="<?=$conf['database']['url']?>"></td>
                                        <td>User</td>
                                        <td><input type="text" name="user" value="<?=$conf['webserver']['user']?>"></td>
                                    </tr>
                                    <tr>
                                        <td>Pyrit</td>
                                        <td><input type="text" name="pyrit" value="<?=$conf['tools']['pyrit']?>"></td>
                                        <td>Name</td>
                                        <td><input type="text" name="name" value="<?=$conf['database']['name']?>"></td>
                                        <td><b>Wireless</b></td>
                                        <td></td>
                                    <tr>
                                        <td>Pcapfix</td>
                                        <td><input type="text" name="pcapfix" value="<?=$conf['tools']['pcapfix']?>"></td>
                                        <td>Username</td>
                                        <td><input type="text" name="username" value="<?=$conf['database']['username']?>"></td>
                                        <td>RFkill</td>
                                        <td><input type="text" name="rfkill" value="<?=$conf['wireless']['rfkill']?>"></td> 
                                    </tr>
                                    <tr>
                                        <td>MySQL</td>
                                        <td><input type="text" name="mysql" value="<?=$conf['tools']['mysql']?>"></td>
                                        <td>Password</td>
                                        <td><input type="text" name="password" value="<?=$conf['database']['password']?>"></td>
                                        <td>Interface</td>
                                        <td><input type="text" name="interface" value="<?=$conf['wireless']['interface']?>"></td>
                                        
                                    </tr>
                                    <tr>
                                        <td>lm-sensors</td>
                                        <td><input type="text" name="lm-sensors" value="<?=$conf['tools']['lm-sensors']?>"></td>
                                        <td><input type="submit" class="btn btn-success" value="Save settings"></td>
                                    </tr>
                                </form>
                            </tbody>
                        </table>
                    </div>
                    <h2 class="sub-header">Actions</h2>
                        <?=$config->getInterface()?> Monitor Mode: <?=((!$wicker->mon0Enabled()) ? "<font color='red'>False</font>" : "<font color='green'>True</font>")?><br><br>
                        <?=((!$wicker->mon0Enabled()) ? "<button class=\"btn btn-success\" onclick=\"window.location='settings.php?do=enablemonitor'\">Enable monitor mode</button>" : "<button class=\"btn btn-danger\" onclick=\"window.location='settings.php?do=disablemonitor'\">Disable monitor mode</button>")?>

                    <h2 class="sub-header">Running programs</h2>
                    <h3 class="sub-header">Airodump</h3>
<?php
                    exec("ps -eo user,pid,command | grep airodump | awk '{print $1 \" \" $2 \" \" $3}'", $airodump);
                    foreach ($airodump as $entry) {
                        $ex = explode(" ", $entry);
                        if ($ex[0] == "root") {
                            echo "<button class=\"btn btn-danger\" onclick=\"window.location='settings.php?do=kill&pid=" . $ex[1] . "'\">Kill " . $ex[1] . "</button>&nbsp;";
                        }
                    }
                    if (count($airodump) == 2) {
                        echo "No airodump-ng sessions running.";
                    }
?>
                    <h3 class="sub-header">Pyrit</h3>
<?php
                    exec("ps -eo user,pid,command | grep pyrit | awk '{print $1 \" \" $2 \" \" $3}'", $pyrit);
                    foreach ($pyrit as $entry) {
                        $ex = explode(" ", $entry);
                        if ($ex[0] == "root") {
                            echo "<button class=\"btn btn-danger\" onclick=\"window.location='settings.php?do=kill&pid=" . $ex[1] . "'\">Kill " . $ex[1] . "</button>&nbsp;";
                        }
                    }
                    if (count($pyrit) == 2) {
                        echo "No pyrit sessions running.";
                    }
?>
                </div>
            </div>
        </div>
        <?=$wicker->footer()?>
    </body>
</html>
