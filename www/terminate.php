<?php
require_once("CapFile.class.php");
require_once("Attack.class.php");
require_once("Wicker.php");
$attack = Attack::fromDB($_GET['id'], $_GET['attack']);
$attack->terminate();
header('Location: view.php?id=' . $_GET['id']);
?>