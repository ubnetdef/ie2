<ol class="breadcrumb">
	<li><a href="<?= $this->Html->url('/schedule/manager'); ?>">Schedule Manager</a></li>
	<?php if ( isset($schedule) ): ?>
	<li class="active">Extend Schedule</li>
	<?php else: ?>
	<li class="active">Create Schedule</li>
	<?php endif; ?>
</ol>

<?php if ( isset($schedule) ): ?>
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
<?php endif; ?>

<div class="row">
	<div class="col-md-12">
		<div class="well">
			<?php if ( isset($schedule) ): ?>
			<h2>Schedule Information</h2>
			<?php else: ?>
			<h2>New Schedule Information</h2>
			<?php endif; ?>

			<hr />

			<?= $this->element('forms/schedule', [
				'injects' => $injects,
				'groups'  => $groups,
				'sid'     => 0,

				'fuzzy'   => isset($schedule) ? $schedule['Schedule']['fuzzy'] : true,
				'start'   => isset($schedule) ? $schedule['Schedule']['start'] : 0,
				'end'     => isset($schedule) ? $schedule['Schedule']['end'] : 0,
				'inject'  => isset($schedule) ? $schedule['Schedule']['inject_id'] : null,
				'group'   => isset($schedule) ? $schedule['Schedule']['group_id'] : null,
				'dep'     => isset($schedule) ? $schedule['Schedule']['dependency_id'] : 0,
				'active'  => isset($schedule) ? $schedule['Schedule']['active'] : false,
				'order'   => isset($schedule) ? $schedule['Schedule']['order'] : 0,
			]); ?>
		</div>
	</div>
</div>