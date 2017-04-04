<ol class="breadcrumb">
	<li><a href="<?= $this->Html->url('/schedule/manager'); ?>">Schedule Manager</a></li>
	<li class="active">Edit Schedule</li>
</ol>

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
<div class="row">
	<div class="col-md-12">
		<div class="well">
			<h2>Schedule Information</h2>

			<hr />

			<?= $this->element('forms/schedule', [
				'injects' => $injects,
				'groups'  => $groups,
				'sid'     => $schedule['Schedule']['id'],

				'fuzzy'   => $schedule['Schedule']['fuzzy'],
				'start'   => $schedule['Schedule']['start'],
				'end'     => $schedule['Schedule']['end'],
				'inject'  => $schedule['Schedule']['inject_id'],
				'group'   => $schedule['Schedule']['group_id'],
				'dep'     => $schedule['Schedule']['dependency_id'],
				'active'  => $schedule['Schedule']['active'],
				'order'   => $schedule['Schedule']['order'],
			]); ?>
		</div>
	</div>
</div>