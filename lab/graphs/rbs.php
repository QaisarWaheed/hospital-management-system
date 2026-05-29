  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<?php 
echo '
<div style = "font-size: 9px;background-color: white;font-weight: bold;text-align: center;max-width: 400px;">
    <span class = "text-warning" style = "">&#9660; Low (< '.$lab_reporting_test_normal_value_low.')</span>
    <span class = "text-info" style = "padding: 0px 35px;">&#9635; Normal ('.$lab_reporting_test_normal_value_low.' - '.$lab_reporting_test_normal_value_high.')</span>
    <span class = "text-danger" style = "">&#9650; High (> '.$lab_reporting_test_normal_value_high.')</span>
</div>';
?>
  <div style = "min-width: 450px;max-width: 450px;" id="chart_div"></div>
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