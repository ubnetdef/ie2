<?php
$col = (bool)env('SCOREENGINE_BOARD_SHOW_INJECTS') ? 9 : 12;
?>
<div class="row">
	<div class="col-md-<?= $col; ?>">
		<h4>Round #<?= $round; ?></h4>
		<?= $this->EngineOutputter->generateScoreBoard(); ?>
	</div>
	<?php if ( (bool)env('SCOREENGINE_BOARD_SHOW_INJECTS') ): ?>
	<div class="col-md-3">
		<h4>Active Injects</h4>
		<ul class="list-group">
			<?php foreach ( $active_injects AS $i ): ?>
			<li class="list-group-item<?= $i->isRecent() ? ' list-group-item-info' : ''; ?>"><?= $i->getTitle(); ?></li>
			<?php endforeach; ?>

			<?php if ( empty($active_injects) ): ?>
			<li class="list-group-item">No injects.</li>
			<?php endif; ?>
		</ul>
	</div>
	<?php endif; ?>
</div>