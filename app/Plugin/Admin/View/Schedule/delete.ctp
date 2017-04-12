<ol class="breadcrumb">
	<li><a href="<?= $this->Html->url(['plugin' => 'admin', 'controller' => 'schedule', 'action' => 'manager']); ?>">Schedule Manager</a></li>
	<li class="active">Delete Schedule</li>
</ol>

<div class="row">
	<div class="col-md-10 col-md-offset-1">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Confirm Deletion</h3>
			</div>
			<div class="panel-body">
				<form class="form-horizontal" method="post">
					<ul class="list-unstyled">
						<li><strong>Schedule ID</strong>: <?= $schedule->getScheduleId(); ?></li>
						<li><strong>Inject Title</strong>: <?= $schedule->getTitle(); ?></li>
						<li><strong>Submission Type</strong>: <?= $this->InjectStyler->getName($schedule->getType()); ?></li>
						<li><strong>Max Submissions</strong>: <?= $schedule->getMaxSubmissions(); ?></li>
						<li><strong>Start</strong>: <?= $schedule->getStartString(); ?></li>
						<li><strong>End</strong>: <?= $schedule->getEndString(); ?></li>
					</ul>

					<hr />

					<?= $this->InjectStyler->contentOutput($schedule->getContent(), $this->Auth->item()); ?>

					<hr />

					<p>Please confirm you wish to delete this schedule.</p>

					<div class="text-center">
						<button type="submit" class="btn btn-danger">Delete Schedule</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>