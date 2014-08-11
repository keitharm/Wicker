<?php
function command_exist($cmd) {
    $returnVal = shell_exec("which $cmd");
    return (empty($returnVal) ? false : true);
}
var_dump(command_exist($_GET['cmd']));
?>