$.ajaxSetup ({
    cache: false
});

var id = $('id').html();
$('#actions .btn-group > button').map(function() {
    $(this).width($(this).outerWidth());
});

function getStatsData() {
    $.ajax({
        url: 'fetch.php?type=cap&id='+id,
        dataType: 'json',
        async: true,
        success: function(data) {
            for(var i=1; i<=6; i++) {
                // Action Button updates
                var $actionButtons = $('#'+i+' > #actions .btn-group > button');
                if(data[i]['status'] == 0 || data[i]['status'] == 2 || data[i]['status'] == 3 || data[i]['status'] == 4) {
                    // Not executing
                    $actionButtons[0].disabled = false;
                    $actionButtons[1].disabled = true;
                    $actionButtons[2].disabled = true;
                    $actionButtons[1].innerHTML = 'Pause';
                } else {
                    // Executing
                    $actionButtons[0].disabled = true;
                    $actionButtons[1].disabled = false;
                    $actionButtons[2].disabled = false;

                    if(data[i]['status'] == 5)
                        $actionButtons[1].innerHTML = 'Resume';
                    else
                        $actionButtons[1].innerHTML = 'Pause';
                }

                // Progress bar updates
                switch(data[i]['status']) {
                    case 1:     // Cracking
                        $('#'+i+' > #status .progress-bar').addClass('progress-bar-striped')
                            .removeClass('noStatus progress-bar-success progress-bar-warning progress-bar-danger')
                            .attr('aria-valuenow', data[i]['complete']).html(data[i]['complete']+'%');

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

                $('#'+i+' > #rate').html(data[i]['rate']);              // Update rate
                //$('#'+i+' > #runtime').html(data[i]['runtime']);      // Update runtime
                $('#'+i+' > #etc').html(data[i]['etc']);                // Update ETC
            }
        }
    });
}

getStatsData();
setInterval(getStatsData, 1000);


function execute(attack) {
    $.ajax({
        url: 'ctl.php?cmd=execute&id='+id+'&attack='+attack,
        dataType: 'json',
        async: false
    }); 
    getStatsData();
}

function terminate(attack) {
    $.ajax({
        url: 'ctl.php?cmd=terminate&id='+id+'&attack='+attack,
        dataType: 'json',
        async: false
    }); 
    getStatsData();
}

function pauseToggle(attack) {
    var state = ($('tr#'+attack+' #actions > .btn-group button')[1].innerHTML == 'Pause') ? 'pause':'resume';
    $.ajax({
        url: 'ctl.php?cmd='+state+'&id='+id+'&attack='+attack,
        dataType: 'json',
        async: false
    }); 
    getStatsData();
}
