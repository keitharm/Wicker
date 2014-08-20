$.ajaxSetup ({
    cache: false
});

function getStatsData(){
  $.ajax({
    url: '../fetch.php?type=',
    dataType: 'json',
    async: false,
    success: function(data){
      // results = data['Total Users'];
      if(!data['error']){
        results = data;
      }
    }
  });
  return results;
}

setInterval(function(){
  var data = getStatsData();

  for(int i=1; i<7; i++) {
    $('#'+i+' > #current').html(data[i]['current']);
    $('#'+i+' > #complete').html(data[i]['complete']);
    $('#'+i+' > #rate').html(data[i]['rate']);
    $('#'+i+' > #runtime').html(data[i]['runtime']);
    $('#'+i+' > #eta').html(data[i]['eta']);
  }

 

  $('#stat_total').html(data['Total Users']);
  $('#stat_today').html(data['Today']);
  $('#stat_thirty_avg').html(data['Avg Users 30']);
  $('#stat_ps_cs6').html(data['CS6']);
  $('#stat_ps_cc').html(data['CC']);
}, 1000);
