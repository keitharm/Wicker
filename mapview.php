<html>
    <head>
        <meta name="viewport" context="initial-scale=1.0, user-scalable=no">
        <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensors=false"></script>
        <script type="text/javascript">
            function initialize() {
                <?=require_once("map.php");?>
            }
        </script>
    </head>
<body onload="initialize()">
    <div id="map_canvas" style="width:1800px; height:900px;">
</body>
</html>
