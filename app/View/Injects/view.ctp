<ol class="breadcrumb">
	<li><a href="<?= $this->Html->url('/injects'); ?>">Inject Inbox</a></li>
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

			<p class="injectinfo">
				<?= $this->InjectStyler->timeOutput($inject); ?>
				<?php if ( $inject->getRemainingSubmissions() > 1 ): ?>
				<br /><strong>Remaining Submissions</strong>: <?= $inject->getRemainingSubmissions(); ?>
				<?php endif; ?>
			</p>
		</div>
		<div class="col-md-2">
			<?php if ( (bool)env('FEATURE_HINT_SUBSYSTEM') && $hints > 0 ): ?>
			<p><a herf="#" class="btn btn-info btn-block" data-toggle="modal" data-target=".hint_modal">HINTS</a></p>
			<?php endif; ?>

			<?php if ( (bool)env('FEATURE_HELP_SUBSYSTEM') ): ?>
			<p><a href="#" class="btn btn-info btn-block">REQUEST HELP</a></p>
			<?php endif; ?>

			<?php if ( $inject->getSubmissionCount() > 0 ): ?>
			<p><span class="btn btn-success btn-block disabled">SUBMITTED</span></p>
			<?php endif; ?>

			<?php if ( $inject->isExpired() ): ?>
			<p><span class="btn btn-danger btn-block disabled">EXPIRED</span></p>
			<?php endif; ?>
		</div>
	</div>

	<hr />

	<?= $this->InjectStyler->contentOutput($inject->getContent(), $this->Auth->item()); ?>

	<hr />

	<?php if ( $inject->hasAttachments() ): ?>
	<h4>Attachments</h4>

	<?php foreach ( $inject->getAttachments() AS $a ): ?>
	- <?= $this->Html->link($a['name'], '/injects/attachment/'.$inject->getScheduleId().'/'.$a['id']) ?>
	<?php endforeach; ?>

	<hr />
	<?php endif ?>

	<ul class="nav nav-tabs">
		<li class="<?= $inject->isAcceptingSubmissions() ? ' active in' : ''; ?>">
			<a href="#submit" role="tab" data-toggle="tab">Submit</a>
		</li>
		<li class="<?= !$inject->isAcceptingSubmissions() ? ' active in' : ''; ?>">
			<a href="#view" role="tab" data-toggle="tab">View Submission<?= $inject->getMaxSubmissions() > 1 ? 's' : ''; ?></a>
		</li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane fade<?= $inject->isAcceptingSubmissions() ? ' active in' : ''; ?>" id="submit">
			<?= $this->InjectStyler->typeOutput($inject->getType()); ?>
		</div>
		<div class="tab-pane fade<?= !$inject->isAcceptingSubmissions() ? ' active in' : ''; ?>" id="view">
			<?= $this->InjectStyler->submittedOutput($inject->getType(), $submissions); ?>
		</div>
	</div>
</div>

<?php
if ( (bool)env('FEATURE_HINT_SUBSYSTEM') && $hints > 0 ) {
	echo $this->element('hint_modal', ['inject_title' => $inject->getTitle()]);
	?>

<script>
$('.hint_modal').on('show.bs.modal', function (event) {
	modal = $(this)

	$.get('<?= $this->Html->url('/injects/hints/'.$inject->getId()); ?>').done(function(data) {
		modal.find('.modal-body').html(data);
	});
});

$(document).on('click', '.unlock_hint', function() {
	hint_id = $(this).data('hint');
	$.get('<?= $this->Html->url('/injects/unlock_hint/'.$inject->getId()); ?>/'+hint_id).done(function() {
		$('.hint_modal').modal('show');
	});
});
</script>

	<?php
}
?>