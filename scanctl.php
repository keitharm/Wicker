<?php
require_once("Wicker.php");
require_once("Scan.class.php");

$do = $_GET['do'];
$id = $_GET['id'];

if ($do == "newscan") {
    $scan = Scan::newScan();
    if (!$scan->mon0Enabled()) {
        $wicker->error("No wireless devices in monitor mode detected.");
        die;
    }
    $scan->startScan();
    // Give airodump-ng a chance to create files
    sleep(1);
    header('Location: scanview.php?id=' . $scan->getID());
    die;
} else if ($do == "terminate") {
    $scan = Scan::fromDB($id);
    system("sudo kill " . $scan->getPID());
    header('Location: scanview.php?id=' . $scan->getID());
    die;
} else if ($do == "coords") {
    $scan    = Scan::fromDB($id);
    $data    = $scan->parseCSV();

    $aps     = $data["aps"];
    $clients = $data["clients"];

    // Add APs to DB if they aren't already there
    foreach ($aps as $ap) {
        $check = AP::fromDB($scan->getID(), $ap["bssid"]);

        // Add AP if not found
        if ($check->getID() == null) {
            AP::newAP($scan->getID(), $ap["bssid"], strtotime($ap["first_seen"]), strtotime($ap["last_seen"]), $ap["channel"], $ap["privacy"], $ap["cipher"], $ap["authentication"], $ap["power"], $ap["beacons"], $ap["ivs"], $ap["essid"], round($_POST['lat'], 7), round($_POST['long'], 7));
        // Update AP if in DB
        } else {
            $check->setLastSeen(strtotime($ap["last_seen"]));
            $check->setBeacons($ap["beacons"]);
            $check->setIVs($ap["ivs"]);
        }
    }

    // Add Clients to DB if they aren't already there
    foreach ($clients as $client) {
        $ap = AP::fromDB($scan->getID(), $client["bssid"]);
        $check = Client::fromDB($scan->getID(), $ap->getID(), $client["mac"]);

        // Add Client if not found
        if ($check->getID() == null) {
            Client::newClient($scan->getID(), $ap->getID(), $client["mac"], strtotime($client["first_seen"]), strtotime($client["last_seen"]), $client["power"], $client["packets"], $client["bssid"], $client["probed"]);
        // Update Client if in DB
        } else {
            $check->setLastSeen(strtotime($client["last_seen"]));
            $check->setPackets($client["packets"]);
            $check->setProbed($client["probed"]);
        }
    }
}
?>
