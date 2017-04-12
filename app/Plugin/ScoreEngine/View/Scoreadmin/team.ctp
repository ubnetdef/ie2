<h2>Backend Panel - Team Panel</h2>
<h4><?= $team['Team']['name']; ?></h4>

<div class="row">
<?php foreach ( $data AS $i => $d ): ?>
	<?php if ( $i % 3 == 0 ): ?>
</div>
<div class="row">
	<?php endif; ?>

	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<?php echo $d['Service']['name'] . ($d['Service']['enabled'] ? '' : ' (Disabled)'); ?>

				<?php
				if ( isset($latest[$d['Service']['name']]) ) {
					if ( $latest[$d['Service']['name']]['passed'] == 1 ) {
						echo '<span class="label label-success pull-right">UP</span>';
					} else {
						echo '<span class="label label-danger pull-right">DOWN</span>';
					}
				}
				?>
			</div>
			<div class="panel-body text-center">
				<h1><?php echo round($d['Check']['total_passed'] / $d['Check']['total'], 3) * 100; ?>%</h1>
				<h3>(<?php echo $d['Check']['total_passed']; ?>/<?php echo $d['Check']['total']; ?>)</h3>
				<h4>Latest: <?php echo isset($latest[$d['Service']['name']]) ? $latest[$d['Service']['name']]['passed'] == 1 ? 'UP' : 'DOWN' : 'N/A'; ?></h4>
			</div>
			<div class="panel-footer text-right">
				<?php echo $this->Html->link('More Information', ['plugin' => 'ScoreEngine', 'controller' => 'scoreadmin', 'action' => 'service', $team['Team']['id'], $d['Service']['id']]); ?>
			</div>
		</div>
	</div>
<?php endforeach; ?>
</div>
