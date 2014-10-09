var id = $('id').html();
var running = $('running').html();
function update() {
    $.get("scanctl.php?do=update&id=" + id);

    $.ajax({
        url: "scanupdate.php?type=ap&id=" + id,
        success: function(data) {
            $('#apupdate').html(data);
        }
    });

    $.ajax({
        url: "scanupdate.php?type=client&id=" + id,
        success: function(data) {
            $('#clientupdate').html(data);
        }
    });
}
update();
if (running == "yes") {
    setInterval(update, 5000);
}