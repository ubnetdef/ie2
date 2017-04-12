<style>
.no-underline { text-decoration: none !important; }
</style>

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
				<a href="<?= $this->Html->url(['plugin' => 'admin', 'controller' => 'schedule', 'action' => 'flip', $i->getScheduleId()]); ?>">
					<i class="glyphicon glyphicon-<?= $i->getScheduleActive() ? 'ok' : 'remove'; ?>"></i>
				</a>
			</td>
			<td><?= $i->getTitle(); ?></td>
			<td><?= $i->getGroupName(); ?></td>
			<td><?= $i->getManagerStartString(); ?></td>
			<td><?= $i->getManagerEndString(); ?></td>
			<td>
				<a href="<?= $this->Html->url(['plugin' => 'admin', 'controller' => 'schedule', 'action' => 'edit', $i->getScheduleId()]); ?>" class="no-underline">
					<i class="glyphicon glyphicon-edit"></i>
				</a>
				<a href="<?= $this->Html->url(['plugin' => 'admin', 'controller' => 'schedule', 'action' => 'create', $i->getScheduleId()]); ?>" class="no-underline">
					<i class="glyphicon glyphicon-share-alt"></i>
				</a>
				<a href="<?= $this->Html->url(['plugin' => 'admin', 'controller' => 'schedule', 'action' => 'delete', $i->getScheduleId()]); ?>" class="no-underline">
					<i class="glyphicon glyphicon-remove"></i>
				</a>
			</td>
		</tr>
		<?php endforeach; ?>
		<tr>
			<td colspan="9">
				<a href="<?= $this->Html->url(['plugin' => 'admin', 'controller' => 'schedule', 'action' => 'create']); ?>" class="btn btn-info pull-right">Schedule an Inject</a>
			</td>
		</tr>
	</tbody>
</table>