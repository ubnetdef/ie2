<style>
.injectinfo {
	font-size: 16px;
}
</style>

<ol class="breadcrumb">
	<li><a href="<?= $this->Html->url('/staff/graders'); ?>">Grader Island</a></li>
	<li class="active">Grade Inject</li>
</ol>

<?php if ( $submission['Submission']['deleted'] ): ?>
<div class="alert alert-danger">
	This submission has been deleted. It will not be factored into the score export.
</div>
<?php endif; ?>

<div class="row">
	<div class="col-md-6">
		<div class="well">
			<h2><?= $submission['Inject']['title']; ?></h2>

			<p class="injectinfo">
				<strong>To</strong>: <?= $submission['Group']['name']; ?> &lt;<?= $submission['User']['username']; ?>@<?= env('INJECT_COMPANY_DOMAIN'); ?>&gt;<br />
				<strong>From</strong>: <?= $submission['Inject']['from_name']; ?> &lt;<?= $submission['Inject']['from_email']; ?>&gt;
			</p>

			<hr />

			<?= $this->InjectStyler->contentOutput($submission['Inject']['content'], $this->Auth->item()); ?>
		</div>
	</div>
	<div class="col-md-6">
		<div class="well">
			<h2>Submission by <?= $submission['User']['username']; ?></h2>

			<p class="injectinfo">
				<strong>Submitted</strong>: <?= $this->Time->timeAgoInWords($submission['Submission']['created']); ?><br />
				<strong>Team</strong>: <?= $submission['Group']['name']; ?>
			</p>

			<?php if ( !empty($submission['Grade']['grade']) ): ?>
			<p class="injectinfo">
				<strong>Grader</strong>: <?= $submission['Grader']['username']; ?><br />
				<strong>Graded</strong>: <?= $this->Time->timeAgoInWords($submission['Grade']['created']); ?>
			</p>
			<?php endif; ?>

			<hr />

			<p><?= $this->InjectStyler->graderOutput($submission['Inject']['type'], $submission); ?></p>
		</div>
	</div>
</div>

<form method="post">
	<div class="row">
		<div class="col-md-4">
			<div class="form-group">
				<div class="input-group">
					<div class="input-group-addon">Grade: </div>
					<input type="text" class="form-control" name="grade" placeholder="0" value="<?= $submission['Grade']['grade']; ?>" />
					<div class="input-group-addon">/<?= $submission['Inject']['max_points']; ?></div>
				</div>
			</div>
		</div>

		<div class="col-md-8">
			<p><textarea class="form-control" rows="5" name="comments" placeholder="Enter any comments here..."><?= $submission['Grade']['comments']; ?></textarea></p>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-6 col-sm-offset-3">
			<input type="submit" class="btn btn-success btn-block" value="Save!" />
		</div>
	</div>
</form>