var id = $('id').html();
var running = $('running').html();
function update() {
    $.get("scanctl.php?do=update&id=" + id);
    sleep(100);
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
    setInterval(update, 3000);
}

function sleep(milliseconds) {
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
}