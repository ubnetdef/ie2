<ol class="breadcrumb">
	<li><a href="<?= $this->Html->url('/staff'); ?>">Competition Central</a></li>
	<li class="active">View Inject</li>
</ol>

<div class="well">
	<div class="row">
		<div class="col-md-10">
			<h2><?= $inject->getTitle(); ?></h2>

			<p class="injectinfo">
				<strong>To</strong>: <?= $this->Auth->group('name'); ?> &lt;<?= $this->Auth->user('username'); ?>@<?= env('INJECT_COMPANY_DOMAIN'); ?>&gt;<br />
				<strong>From</strong>: <?= $inject->getFromName(); ?> &lt;<?= $inject->getFromEmail(); ?>&gt;
			</p>
		</div>
		<div class="col-md-2">
			<?php if ( (bool)env('FEATURE_HINT_SUBSYSTEM') && $hints > 0 ): ?>
			<p><a herf="#" class="btn btn-info btn-block">HAS HINTS</a></p>
			<?php endif; ?>
		</div>
	</div>

	<hr />

	<?= $this->InjectStyler->contentOutput($inject->getContent(), $this->Auth->item()); ?>

	<hr />

	<?php if ( $inject->hasAttachments() ): ?>
	<h4>Attachments</h4>

	<?php foreach ( $inject->getAttachments() AS $a ): ?>
	- <?= $this->Html->link($a['name'], '/attachment/view/'.$a['id'].'/'.md5($a['id'].env('SECURITY_CIPHER_SEED')) ?>
	<?php endforeach; ?>

	<?php endif ?>
</div>