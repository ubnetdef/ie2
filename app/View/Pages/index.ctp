<h2>
	<?= $title; ?>

	<?php if ( $this->Auth->isAdmin() ): ?>
	<a href="<?= $this->Html->url('/admin/site'); ?>" class="btn btn-primary pull-right">Edit Page</a>
	<?php endif; ?>
</h2>

<?= $body; ?>
