<ol class="breadcrumb">
	<li><a href="<?= $this->Html->url('/schedule'); ?>">Scheduler</a></li>
	<li><a href="<?= $this->Html->url('/schedule/manager'); ?>">Schedule Manager</a></li>
	<li class="active">Create Schedule</li>
</ol>

<?= $this->element('navbar/schedule', ['at_manager' => true]); ?>

<div class="row">
	<div class="col-md-12">
		<div class="well">
			<h2>Schedule Information</h2>

			<hr />

			<?= $this->element('forms/schedule', [
				'injects' => $injects,
				'groups'  => $groups,
				'sid'     => 0,

				'fuzzy'   => true,
				'start'   => 0,
				'end'     => 0,
				'inject'  => null,
				'group'   => null,
				'dep'     => 0,
				'active'  => 0,
				'order'   => 0,
			]); ?>
		</div>
	</div>
</div>