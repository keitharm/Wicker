<?php
require_once("Wicker.php");
require_once("Scan.class.php");

$do = $_GET['do'];
$id = $_GET['id'];

if ($do == "newscan") {
    $wep = $_POST['wep'];
    $wpa = $_POST['wpa'];
    if (!$wicker->mon0Enabled()) {
        $wicker->error("No wireless devices in monitor mode detected.");
        die;
    }
    $scan = Scan::newScan();
    $scan->setStatus(1);
    $scan->startScan($wep, $wpa);
    // Give airodump-ng a chance to create files
    sleep(1);
    header('Location: scanview.php?id=' . $scan->getID());
    die;
} else if ($do == "terminate") {
    $scan = Scan::fromDB($id);
    if ($scan->getPID() != 0 && $scan->getStatus() == 1) {
        $scan->setStatus(2);
        system("sudo kill " . $scan->getPID());
        header('Location: scanview.php?id=' . $scan->getID());
    } else {
        if ($scan->getPID() == 0) {
            $wicker->error("PID of scan was 0.");
        } else if ($scan->getStatus() == 2) {
            $wicker->error("This scan has already been terminated");
        } else {
            $wicker->error("An unknown error has occured");
        }
    }
    die;
} else if ($do == "update") {
    $scan    = Scan::fromDB($id);
    $data    = $scan->parseCSV();

    $aps     = $data["aps"];
    $clients = $data["clients"];

    // Update scan counts for APs and Clients
    $scan->setAPCount(count($aps));
    $scan->setClientCount(count($clients));

    // Add APs to DB if they aren't already there
    foreach ($aps as $ap) {
        $check = AP::fromDB($scan->getID(), $ap["bssid"]);

        // Add AP if not found
        if ($check->getID() == null) {
            AP::newAP($scan->getID(), $ap["bssid"], strtotime($ap["first_seen"]), strtotime($ap["last_seen"]), $ap["channel"], $ap["privacy"], $ap["cipher"], $ap["authentication"], $ap["power"], $ap["beacons"], $ap["ivs"], $ap["essid"], round($_POST['lat'], 7), round($_POST['long'], 7));
        // Update AP in DB
        } else {
            // Update Coordinates if seen within last 10 seconds
            if ($wicker->timeconv(strtotime($ap["last_seen"])) == "Just now") {
                $check->setLatitude(round($_POST['lat'], 7));
                $check->setLongitude(round($_POST['long'], 7));
            }
            $check->setLastSeen(strtotime($ap["last_seen"]));
            $check->setBeacons($ap["beacons"]);
            $check->setIVs($ap["ivs"]);
            $check->setPower($ap["power"]);

            // If this is an individual scan and not OPN or WEP, perform handshake check
            if ($scan->getIndividual() == 1 && $check->getHandshake() != 1 && !in_array($check->getPrivacy(), array("WEP", "OPN"))) {
                if ($scan->capturedHandshake()) {
                    $check->setHandshake(1);
                }
            } else {
                // If wep scan hasn't started yet, attempt to start if enough IVs
                if ($scan->getWEP() == 0 && $check->getIVs() >= 100) {
                    $scan->setWEP(1);
                    $scan->startAircrackWEP();
                }

                // If scan has started, than check to see if key has been recovered yet.
                if ($scan->getWEP() == 1) {
                    // Key recovered
                    $key = $scan->checkKey();
                    if ($key !== false) {
                        $check->setKey($key);
                    }
                }
            }
        }
    }

    // Add Clients to DB if they aren't already there
    foreach ($clients as $client) {
        $ap = AP::fromDB($scan->getID(), $client["bssid"]);
        $check = Client::fromDB($scan->getID(), $ap->getID(), $client["mac"]);

        // Add Client if not found
        if ($check->getID() == null) {
            Client::newClient($scan->getID(), $ap->getID(), $client["mac"], strtotime($client["first_seen"]), strtotime($client["last_seen"]), $client["power"], $client["packets"], $client["bssid"], $client["probed"]);
        // Update Client in DB
        } else {
            $check->setLastSeen(strtotime($client["last_seen"]));
            $check->setPackets($client["packets"]);
            $check->setProbed($client["probed"]);
        }
    }
}
?>
