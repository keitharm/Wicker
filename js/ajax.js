$.ajaxSetup ({
    cache: false
});

function getStatsData(){
    var id = $('id').html();
    var results;
    
    $.ajax({
        url: '/fetch.php?type=cap&id='+id,
        dataType: 'json',
        async: false,
        success: function(data){
            results = data;
        }
    });
    return results;
}

setInterval(function(){
    var data = getStatsData();

    for(var i=1; i<=6; i++) {

        switch(data[i]['status']) }
            case 0:     // No status
                $('#'+i+' > #status > .progress-bar').addClass('noStatus').html('----');
                break;
            case 1:     // Cracking
                $('#'+i+' > #status > .progress-bar').addClass('progress-bar-striped')
                    .removeClass('noStatus progress-bar-success progress-bar-warning progress-bar-danger')
                    .attr('aria-valuenow', data[i]['complete']).width(data[i]['complete']).html(data[i]['complete']+'%');
                break;
            case 2:     // Failed
                $('#'+i+' > #status > .progress-bar').addClass('progress-bar-danger')
                    .removeClass('noStatus progress-bar-striped progress-bar-warning')
                    .attr('aria-valuenow', 50).html('Failed');
                break;
            case 3:     // Success
                $('#'+i+' > #status > .progress-bar').addClass('progress-bar-success')
                    .removeClass('noStatus progress-bar-striped progress-bar-warning progress-bar-danger')
                    .attr('aria-valuenow', 50]).html('Success');
                break;
            case 4:     // Terminated
                $('#'+i+' > #status > .progress-bar').addClass('progress-bar-danger')
                    .removeClass('noStatus progress-bar-striped progress-bar-warning')
                    .attr('aria-valuenow', 50).html('Terminated');
                break;
            case 5:     // Paused
                $('#'+i+' > #status > .progress-bar').addClass('progress-bar-warning')
                    .removeClass('noStatus progress-bar-striped')
                    .attr('aria-valuenow', 50).html('Paused');
                break;
        }

        $('#'+i+' > #rate').html(data[i]['rate']);
        //$('#'+i+' > #runtime').html(data[i]['runtime']);
        $('#'+i+' > #etc').html(data[i]['etc']);
    }
}, 2000);
