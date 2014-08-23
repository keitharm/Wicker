$.ajaxSetup ({
    cache: false
});

function getStatsData() {
    var id = $('id').html();

    $.ajax({
        url: 'fetch.php?type=cap&id='+id,
        dataType: 'json',
        async: false,
        success: function(data) {
            for(var i=1; i<=6; i++) {
                switch(data[i]['status']) {
                    case 1:     // Cracking
                        if(!$('#'+i+' > #status .progress-bar').hasClass('progress-bar-striped')) {
                            $('#'+i+' > #status .progress-bar').addClass('progress-bar-striped')
                                .removeClass('noStatus progress-bar-success progress-bar-warning progress-bar-danger');
                        }

                        $('#'+i+' > #status .progress-bar').attr('aria-valuenow', data[i]['complete']).html(data[i]['complete']+'%');

                        if(data[i]['complete'] > 20)
                            $('#'+i+' > #status .progress-bar').width(data[i]['complete']+'%');
                        break;
                    case 2:     // Failed
                        $('#'+i+' > #status .progress-bar').addClass('progress-bar-danger')
                            .removeClass('noStatus progress-bar-striped progress-bar-warning')
                            .attr('aria-valuenow', 50).removeAttr('style').html('Failed');
                        break;
                    case 3:     // Success
                        $('#'+i+' > #status .progress-bar').addClass('progress-bar-success')
                            .removeClass('noStatus progress-bar-striped progress-bar-warning progress-bar-danger')
                            .attr('aria-valuenow', 50).removeAttr('style').html('Success');
                        break;
                    case 4:     // Terminated
                        $('#'+i+' > #status .progress-bar').addClass('progress-bar-danger')
                            .removeClass('noStatus progress-bar-striped progress-bar-warning')
                            .attr('aria-valuenow', 50).removeAttr('style').html('Terminated');
                        break;
                    case 5:     // Paused
                        $('#'+i+' > #status .progress-bar').addClass('progress-bar-warning')
                            .removeClass('noStatus progress-bar-striped')
                            .attr('aria-valuenow', 50).removeAttr('style').html('Paused: '+data[i]['complete']+'%');
                        break;
                    default:     // No status
                        $('#'+i+' > #status .progress-bar').addClass('noStatus').html('----');
                        break;
                }

                $('#'+i+' > #rate').html(data[i]['rate']);
                //$('#'+i+' > #runtime').html(data[i]['runtime']);
                $('#'+i+' > #etc').html(data[i]['etc']);
            }
        }
    });
}

getStatsData();
setInterval(getStatsData, 1000);
