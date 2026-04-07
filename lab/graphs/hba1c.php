  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <div style = "min-width: 550px;max-width: 550px;" id="chart_div"></div>
<script>
google.charts.load('current', {packages: ['corechart', 'bar']});
google.charts.setOnLoadCallback(drawMultSeries);

function drawMultSeries() {
  var data = google.visualization.arrayToDataTable([
      
        ['', '', { role: 'style' }, { role: 'annotation' }],
        <?php 
        $result = $lab_test_report_result;
        $report_time_for_graph = date_format(date_create($row['reporting_date_time']), "d-M-Y");
        if($result > $lab_reporting_test_normal_value_high)
        {
            echo "['".$report_time_for_graph."', ".$result.", 'red', ".$result."],";
        }
        elseif($result <= $lab_reporting_test_normal_value_low)
        {
            echo "['".$report_time_for_graph."', ".$result.", 'green', ".$result."],";
        }
        else
        {
            echo "['".$report_time_for_graph."', ".$result.", 'yellow', ".$result."],";
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