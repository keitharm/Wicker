<?php
require_once("Wicker.php");
// Create a stream
$options  = array('http' => array('user_agent' => 'RandomAPI'));
$context  = stream_context_create($options);

// Open the file using the HTTP headers set above
$json = file_get_contents("https://randomapi.com/api/?key=NKOB-8C2V-SX9C-RA05&id=6byfjx&results=10", false, $context);
$stats = file_get_contents("https://randomapi.com/api/?key=NKOB-8C2V-SX9C-RA05&id=5gb2vm", false, $context);
$json = json_decode($json);
$stats = json_decode($stats);
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
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            <li class="active"><a href="index.php">Dashboard</a></li>
            <li><a href="stats.php">Statistics</a></li>
          </ul>
          <ul class="nav nav-sidebar">
            <li><a href="upload.php">Uploader</a></li>
            <li><a href="crack.php">Cracker</a></li>
            <li><a href="">One more nav</a></li>
            <li><a href="">Another nav item</a></li>
            <li><a href="">More navigation</a></li>
          </ul>
          <ul class="nav nav-sidebar">
            <li><a href="">Nav item again</a></li>
            <li><a href="">One more nav</a></li>
            <li><a href="">Another nav item</a></li>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h1 class="page-header">Dashboard</h1>

          <div class="row placeholders">
            <div class="col-xs-6 col-sm-3 placeholder">
              <h2><?=$stats->results[0]->{'Wicker Stats'}->cap?></h2>
              <span class="text-muted">.Cap Files</span>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
              <h2><?=$stats->results[0]->{'Wicker Stats'}->success?></h2>
              <span class="text-muted">Success Rate</span>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
              <h2><?=$stats->results[0]->{'Wicker Stats'}->passwords?></h2>
              <span class="text-muted">Passwords</span>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
              <h2><?=$stats->results[0]->{'Wicker Stats'}->active?></h2>
              <span class="text-muted">Active Operations</span>
            </div>
          </div>

          <h2 class="sub-header">Current Operations</h2>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>User</th>
                  <th>ESSID</th>
                  <th>BSSID</th>
                  <th>Pre-Computed?</th>
                  <th>% Complete</th>
                  <th>Run Time</th>
                </tr>
              </thead>
              <tbody>
<?php
for ($a = 0; $a < count($json->results); $a++) {
?>
                <tr>
                  <td><?=$json->results[$a]->{'Wicker Operations'}->User?></td>
                  <td><?=$json->results[$a]->{'Wicker Operations'}->ESSID?></td>
                  <td><?=$json->results[$a]->{'Wicker Operations'}->BSSID?></td>
                  <td><?=$json->results[$a]->{'Wicker Operations'}->PreComputed?></td>
                  <td><?=$json->results[$a]->{'Wicker Operations'}->Percent?></td>
                  <td><?=$json->results[$a]->{'Wicker Operations'}->Runtime?></td>
                </tr>
<?php
}
?> 
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/docs.min.js"></script>
    <script src="js/holder.js"></script>
  </body>
</html>
