<h2>Backend Panel - Hint Manager</h2>

<table class="table">
	<thead>
		<tr>
			<td width="5%">ID</td>
			<td width="5%">Parent</td>
			<td width="10%">Time Delay</td>
			<td width="5%">Cost</td>
			<td width="30%">Inject</td>
			<td width="25%">Name</td>
			<td width="20%">Actions</td>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $hints AS $hint ): ?>
		<tr>
			<td><?= $hint['Hint']['id']; ?></td>
			<td><?= empty($hint['Hint']['parent_id']) ? 'N/A' : $hint['Hint']['parent_id']; ?></td>
			<td><?= $hint['Hint']['time_wait']; ?> seconds</td>
			<td><?= $hint['Hint']['cost']; ?></td>
			<td><?= $hint['Inject']['title']; ?></td>
			<td><?= $hint['Hint']['title']; ?></td>
			<td>
				<?= $this->Html->link('Edit', '/admin/hints/edit/'.$hint['Hint']['id']); ?>
				| <?= $this->Html->link('Delete', '/admin/hints/delete/'.$hint['Hint']['id']); ?>
			</td>
		</tr>
		<?php endforeach; ?>
		<tr>
		<td colspan="8">
			<a href="<?= $this->Html->url('/admin/hints/create'); ?>" class="btn btn-primary pull-right">New Hint</a>
		</td>
	</tr>
	</tbody>
</table>
