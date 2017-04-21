<?php
$extra_row = (benv('FEATURE_BANKWEB') && benv('FEATURE_SCOREENGINE'));
$col_amt = 6;

if (benv('FEATURE_SCOREENGINE')) {
	$col_amt = 2;
}
if (benv('FEATURE_BANKWEB') ) {
	$col_amt = (benv('FEATURE_SCOREENGINE') ? 6 : 4);
}
?>

<div class="row">
	<?php if ( benv('FEATURE_SCOREENGINE') ): ?>
	<div class="col-md-8">
		<h3>Uptime Overview (Round: <?= $round; ?>)</h3>
		<?= $this->EngineOutputter->generateScoreBoard(); ?>
	</div>
	<?php endif; ?>

	<?php if ($extra_row): ?>
	<div class="col-md-4">
	<div class="row">
	<?php endif; ?>

	<?php if (benv('FEATURE_BANKWEB')): ?>
	<div class="col-md-<?= $extra_row ? 12 : 4; ?>">
		<h3>Bank Overview</h3>
		<table class="table table-bordered">
			<thead>
				<tr>
					<td>Date</td>
					<td>Product</td>
					<td>Buyer</td>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $purchases AS $p ): ?>
				<tr class="warning">
					<td><?= $this->Time->timeAgoInWords($p['Purchase']['time']); ?></td>
					<td><?= $p['Product']['name']; ?></td>
					<td><?= $p['User']['username']; ?> (<?= $p['User']['Group']['name']; ?>)</td>
				</tr>
				<?php endforeach; ?>

				<?php if ( empty($purchases) ): ?>
				<tr>
					<td colspan="3">
						No pending purchases.
					</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php endif; ?>

	<?php if ($extra_row): ?>
	</div>
	<div class="row">
	<?php endif; ?>

	<div class="col-md-<?= $col_amt; ?>">
		<h3>Active</h3>
		<div class="list-group">
			<?php foreach ( $active_injects AS $i ): ?>
			<a href="<?= $this->Html->url('/staff/inject/'.$i->getInjectId()); ?>" class="list-group-item<?= $i->isRecent() ? ' list-group-item-info' : ''; ?>"><?= $i->getTitle(); ?></a>
			<?php endforeach; ?>

			<?php if ( empty($active_injects) ): ?>
			<a href="#" class="list-group-item">No injects.</a>
			<?php endif; ?>
		</div>
	</div>
	
	<div class="col-md-<?= $col_amt; ?>">
		<h3>Expired</h3>
		<div class="list-group">
			<?php foreach ( $recent_expired AS $i ): ?>
			<a href="<?= $this->Html->url('/staff/inject/'.$i->getInjectId()); ?>" class="list-group-item<?= $i->isRecent() ? ' list-group-item-warning' : ''; ?>"><?= $i->getTitle(); ?></a>
			<?php endforeach; ?>

			<?php if ( empty($recent_expired) ): ?>
			<a href="#" class="list-group-item">No injects.</a>
			<?php endif; ?>
		</div>
	</div>

	<?php if ($extra_row): ?>
	</div>
	</div>
	<?php endif; ?>
</div>

<h3>Recent Actions</h3>
<table class="table table-bordered">
	<tr>
		<td>Who?</td>
		<td>When?</td>
		<td>Type</td>
		<td>Message</td>
	</tr>
	<?php foreach ( $recent_logs AS $r ): ?>
	<tr>
		<td width="25%">
			<?= isset($r['User']['Group']['name']) ? $r['User']['Group']['name'].' - ' : ''; ?><strong><?= $r['User']['username']; ?></strong>
		</td>
		<td width="15%"><?= $this->Time->timeAgoInWords($r['Log']['time']); ?>
		<td width="10%"><?= $r['Log']['type']; ?></td>
		<td width="50%"><?= $r['Log']['message']; ?></td>
	</tr>
	<?php endforeach; ?>
</table>
