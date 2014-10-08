var id = $('id').html();

function postCoordinates(position) {
    $.post( "scanctl.php?do=coords&id=" + id, { lat: position.coords.latitude, long: position.coords.longitude } );
}
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(postCoordinates);
    } else {
        $.post( "scanctl.php?do=coords&id=" + id, { lat: 0, long: 0 } );
    }
}
getLocation();
setInterval(getLocation, 5000);