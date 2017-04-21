<h2>Scoreboard</h2>
<h4>Round #<?= $round; ?></h4>

<div class="row">
	<div class="col-md-8">
		<div id="scoreboard"></div>
	</div>
	<div class="col-md-4">
		<h3>Top Uptime</h3>
		<ol>
			<?php for ( $i=0; $i < 5; $i++ ): ?>
			<li><?= $team_mappings[$overview[$i]['Team']['id']]; ?> - <?= $overview[$i]['Check']['total_passed']; ?></li>
			<?php endfor; ?>
		</ol>

		<h3>Top Inject Scores</h3>
		<ol>
			<?php for ( $i=0; $i < 5; $i++ ): if ( !isset($grades[$i]) ) continue; ?>
			<li><?= $grades[$i]['Group']['name']; ?> - <?= $grades[$i]['Submission']['total_grade']; ?></li>
			<?php endfor; ?>
		</ol>
	</div>
</div>

<script type="text/javascript" src="//www.gstatic.com/charts/loader.js"></script>
<script>
setTimeout(window.location.reload.bind(window.location), 30 * 1000);

google.charts.load('current', {'packages':['bar']});
google.charts.setOnLoadCallback(drawScoreboard);

function drawScoreboard() {
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Team');
	data.addColumn('number', 'Successful Checks');
	data.addColumn('number', 'Inject Score');
	data.addRows([
		[
			'Check Team', <?= $max_check; ?>, <?= $max_grade; ?>
		],
		<?php foreach ( $overview AS $o ): ?>
		[
			'<?= $team_mappings[$o['Team']['id']]; ?>',
			<?= $o['Check']['total_passed']; ?>,
			<?= isset($grade_team_mappings[$o['Team']['id']]) ? $grade_team_mappings[$o['Team']['id']] : 0; ?>
		],
		<?php endforeach; ?>
	]);
	var options = {
		height: 500,
		bars: 'horizontal',
		series: {
			0: {axis: 'Uptime'},
			1: {axis: 'Inject Score'},
		},
		axes: {
			x: {
				'Uptime': {side: 'top', label: 'Successful Checks'},
				'Inject Score': {label: 'Inject Score'},
			}
		}
	};
	var chart = new google.charts.Bar(document.getElementById('scoreboard'));
	chart.draw(data, options);
}
</script>