<ol class="breadcrumb">
	<li><a href="<?= $this->Html->url(['plugin' => 'BankWeb', 'controller' => 'overview', 'action' => 'index']); ?>">BankWeb Overview</a></li>
	<li class="active">View Purchase</li>
</ol>
<h2>Purchase Information</h2>

<div class="well">
	<p>
		<strong>Who</strong>: <?= $purchase['User']['username']; ?> (<?= $purchase['User']['Group']['name']; ?>)<br />
		<strong>What</strong>: <?= $purchase['Product']['name']; ?> (#<?= $purchase['Product']['id']; ?>)<br />
		<strong>When</strong>: <?= $this->Time->timeAgoInWords($purchase['Purchase']['time']); ?>
	</p>

	<?php if ( $purchase['Purchase']['user_input'] ): ?>

	<hr />

	<p><strong>User Message</strong>: <?= $purchase['Purchase']['user_input']; ?></p>

	<?php endif; ?>

	<hr />

	<p>
		<strong>Completed</strong>: <?= $purchase['Purchase']['completed'] ? 'YES' : 'NO'; ?><br />
		<strong>Completed By</strong>: <?= $purchase['Purchase']['completed_by'] ?: 'N/A'; ?><br />
		<strong>Completed On</strong>: <?= $this->Time->timeAgoInWords($purchase['Purchase']['completed_time']); ?>
	</p>
</div>