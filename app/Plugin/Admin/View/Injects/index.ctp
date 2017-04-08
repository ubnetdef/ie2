<h2>Backend Panel - Inject Manager</h2>

<table class="table">
	<thead>
		<tr>
			<td>ID</td>
			<td>Name</td>
			<td>Type</td>
			<td>Max Points</td>
			<td>Max Submissions</td>
			<td>Actions</td>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $injects AS $inject ): ?>
		<tr>
			<td><?= $inject['Inject']['id']; ?></td>
			<td><?= $inject['Inject']['title']; ?></td>
			<td><?= $this->InjectStyler->getName($inject['Inject']['type']); ?></td>
			<td><?= $inject['Inject']['max_points']; ?></td>
			<td><?= $inject['Inject']['max_submissions']; ?></td>
			<td>
				<?= $this->Html->link('Edit', '/admin/injects/edit/'.$inject['Inject']['id']); ?>
				| <?= $this->Html->link('Delete', '/admin/injects/delete/'.$inject['Inject']['id']); ?>
			</td>
		</tr>
		<?php endforeach; ?>
		<tr>
		<td colspan="8">
			<a href="<?= $this->Html->url('/admin/injects/create'); ?>" class="btn btn-primary pull-right">New Inject</a>
		</td>
	</tr>
	</tbody>
</table>
