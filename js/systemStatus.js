function getSystemStats() {
    $.ajax({
        url: "fetch.php?type=system",
        dataType: "json",
        success: function(data) {
            for(var i in data) {
                $('#'+i).html(data[i]);
            }
        }
    });
}

getSystemStats();
setInterval(getSystemStats, 5000);