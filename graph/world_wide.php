<!DOCTYPE html>
<html>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<body>
<div
id="myChart" style="width:100%; max-width:600px; height:500px;">
</div>

<script>
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
const data = google.visualization.arrayToDataTable([
  ['Contry', 'Mhl'],
  ['2025-08-01 20.8',20.8],
//   ['2025-08-21 20.6',20.6],
//   ['2025-09-05 50.4',50.4],
//   ['2025-10-05 50.9',50.9],
  ['2025-11-01 70.5',70.5]
]);

const options = {
  title:'COMPLETE BLOOD COUNT',
  is3D:true
};

const chart = new google.visualization.PieChart(document.getElementById('myChart'));
  chart.draw(data, options);
}
</script>

</body>
</html>


