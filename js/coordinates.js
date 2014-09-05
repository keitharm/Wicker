var id = $('id').html();

function postCoordinates(position) {
    $.post( "scanctl.php?do=coords&id=" + id, { lat: position.coords.latitude, long: position.coords.longitude } );
}
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(postCoordinates);
    } else {
        x.innerHTML = "Geolocation is not supported by this browser.";
    }
}
getLocation();
setInterval(getLocation, 5000);