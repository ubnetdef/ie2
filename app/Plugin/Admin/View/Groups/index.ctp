<h2>Backend Panel - Group Manager</h2>

<table class="table">
	<thead>
		<tr>
			<td>ID</td>
			<td>Name</td>
			<td>Team Mapping</td>
			<td>Actions</td>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $groups AS $id => $name ): ?>
		<tr>
			<td><?= $id; ?></td>
			<td><?= $name; ?></td>
			<td><?= isset($mappings[$id]) ? $mappings[$id] : 'N/A'; ?></td>
			<td>
				<?= $this->Html->link('Edit', '/admin/groups/edit/'.$id); ?>
				| <?= $this->Html->link('Delete', '/admin/groups/delete/'.$id); ?>
			</td>
		</tr>
		<?php endforeach; ?>
		<tr>
		<td colspan="8">
			<a href="<?= $this->Html->url('/admin/groups/create'); ?>" class="btn btn-primary pull-right">New Group</a>
		</td>
	</tr>
	</tbody>
</table>
	
