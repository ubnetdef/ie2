<style>
.no-underline { text-decoration: none !important; }
</style>

<ul class="nav nav-pills">
	<li class=""><a href="<?= $this->Html->url('/schedule'); ?>">Overview</a></li>
	<li class="active"><a href="<?= $this->Html->url('/schedule/manager'); ?>">Manager</a></li>
</ul>

<hr />

<h2>Schedule Manager</h2>

<table class="table">
	<thead>
		<tr>
			<td><abbr title="Schedule ID">SID</abbr></td>
			<td><abbr title="Inject ID">IID</abbr></td>
			<td><abbr title="Sequence ID">SEQID</abbr></td>
			<td>Enabled</td>
			<td>Title</td>
			<td>Assigned Group</td>
			<td>Start</td>
			<td>End</td>
			<td></td>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $injects AS $i ): ?>
		<tr>
			<td><?= $i->getScheduleId(); ?></td>
			<td><?= $i->getInjectId(); ?></td>
			<td><?= $i->getSequence(); ?></td>
			<td>
				<a href="<?= $this->Html->url('/schedule/flip/'.$i->getScheduleId()); ?>">
					<i class="glyphicon glyphicon-<?= $i->getScheduleActive() ? 'ok' : 'remove'; ?>"></i>
				</a>
			</td>
			<td><?= $i->getTitle(); ?></td>
			<td><?= $i->getGroupName(); ?></td>
			<td><?= $i->getManagerStartString(); ?></td>
			<td><?= $i->getManagerEndString(); ?></td>
			<td>
				<a href="<?= $this->Html->url('/schedule/edit/'.$i->getScheduleId()); ?>" class="no-underline">
					<i class="glyphicon glyphicon-edit"></i>
				</a>
				<a href="<?= $this->Html->url('/schedule/extend/'.$i->getScheduleId()); ?>" class="no-underline">
					<i class="glyphicon glyphicon-share-alt"></i>
				</a>
				<a href="<?= $this->Html->url('/schedule/delete/'.$i->getScheduleId()); ?>" class="no-underline">
					<i class="glyphicon glyphicon-remove"></i>
				</a>
			</td>
		</tr>
		<?php endforeach; ?>
		<tr>
			<td colspan="8">
				<a href="<?= $this->Html->url('/schedule/create'); ?>" class="btn btn-info pull-right">Schedule an Inject</a>
			</td>
		</tr>
	</tbody>
</table>