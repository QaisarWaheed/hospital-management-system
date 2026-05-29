<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<div style = "min-width: 450px;max-width: 450px;" id="chart_div"></div>
<script>
google.charts.load('current', {packages: ['corechart', 'bar']});
google.charts.setOnLoadCallback(drawMultSeries);
function drawMultSeries() {
  var data = google.visualization.arrayToDataTable([
      
        ['', '', { role: 'style' }, { role: 'annotation' }],
        <?php 
        $result = $lab_test_report_result;
        $report_time_for_graph = function_exists('format_lab_datetime')
            ? format_lab_datetime($row_test_report['reporting_date_time'], 'd-M-Y')
            : date('d-M-Y', strtotime($row_test_report['reporting_date_time']));
        if($result > $lab_reporting_test_normal_value_high)
        {
            echo "['".$report_time_for_graph."', ".$result.", 'red', ".$result."],";
        }
        elseif($result >= 40 && $result <= $lab_reporting_test_normal_value_low)
        {
            echo "['".$report_time_for_graph."', ".$result.", 'yellow', ".$result."],";
        } 
        elseif($result < 40)
        {
            echo "['".$report_time_for_graph."', ".$result.", 'yellow', ".$result."],";
        } 
        else
        {
            echo "['".$report_time_for_graph."', ".$result.", 'green', ".$result."],";
        } 
        ?>
        
      ]);
      var options = {
          legend: { position: 'none' }
      };
      var chart = new google.visualization.ColumnChart(
        document.getElementById('chart_div'));

      chart.draw(data, options);
    }    
</script>