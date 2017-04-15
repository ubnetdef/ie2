<h2>Scoreboard</h2>
<h4>Round #<?= $round; ?></h4>

<div id="scoreboard"></div>

<script type="text/javascript" src="//www.google.com/jsapi"></script>
<script>
setTimeout(window.location.reload.bind(window.location), 30 * 1000);

google.load('visualization', '1.0', {'packages':['corechart', 'bar']});
google.setOnLoadCallback(drawCharts);
function drawCharts() {
	drawScoreboard();
}
function drawScoreboard() {
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Team');
	data.addColumn('number', 'Successful Checks');
	data.addRows([
		<?php foreach ( $overview AS $o ): ?>
		['<?= $o['Team']['name']; ?>', <?= $o['Check']['total_passed']; ?>],
		<?php endforeach; ?>
	]);
	var options = {
		width: 1000,
		height: 500,
		bars: 'horizontal',
		series: {
			0: {axis: 'Uptime'},
		},
		axes: {
			x: {
				Uptime: {side: 'top', label: 'Successful Checks'},
			}
		}
	};
	var chart = new google.charts.Bar(document.getElementById('scoreboard'));
	chart.draw(data, options);
}
</script>