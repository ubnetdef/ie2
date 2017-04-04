<h2>Backend Panel - Team Panel</h2>
<h4><?= $team['Team']['name']; ?></h4>

<?php foreach ( $data AS $d ): ?>
<div class="panel panel-<?= $d['Check']['passed'] == 1 ? 'success' : 'danger'; ?>">
	<div class="panel-heading">
		<h4 class="panel-title">
			Check - <?php echo date('h:i A', strtotime($d['Check']['time'])); ?>
		</h4>
	</div>
	<div class="panel-body">
		<pre>
		<?php echo $d['Check']['output']; ?>
		</pre>
	</div>
	<div class="panel-footer">
		Result: <?php echo $d['Check']['passed'] == 1 ? 'PASSED' : 'FAILED'; ?>
	</div>
</div>
<?php endforeach; ?>
