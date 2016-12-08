<ol class="breadcrumb">
	<li><a href="<?= $this->Html->url('/schedule'); ?>">Scheduler</a></li>
	<li><a href="<?= $this->Html->url('/schedule/manager'); ?>">Schedule Manager</a></li>
	<li class="active">Delete Schedule</li>
</ol>

<?= $this->element('navbar/schedule', ['at_manager' => true]); ?>

<div class="row">
	<div class="col-md-12">
		<div class="well">
			<h2><?= $schedule['Inject']['title']; ?></h2>

			<p class="injectinfo">
				<strong>Submission Type</strong>: <?= $this->InjectStyler->getName($schedule['Inject']['type']); ?><br />
				<strong>Max Submissions</strong>: <?= $schedule['Inject']['max_submissions']; ?>
			</p>

			<hr />

			<?= $this->InjectStyler->contentOutput($schedule['Inject']['content'], $this->Auth->item()); ?>
		</div>
	</div>
</div>
