<h2>Backend Panel - User Manager</h2>

<table class="table">
	<thead>
		<tr>
			<td>User ID</td>
			<td>Username</td>
			<td>Team</td>
			<td>Status</td>
			<td>Expires</td>
			<td>Actions</td>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $users AS $user ): ?>
		<tr>
			<td><?= $user['User']['id']; ?></td>
			<td><?= $user['User']['username']; ?></td>
			<td><?= $user['Group']['name']; ?></td>
			<td><?= $user['User']['active'] == 1 ? 'Enabled' : 'Disabled'; ?></td>
			<td><?= ($user['User']['expiration'] == 0) ? 'Never' : date('m/d/Y \a\t g:iA', $user['User']['expiration']); ?></td>
			<td>
				<?= $this->Html->link('Edit', '/admin/users/edit/'.$user['User']['id']); ?>
				
				<?php if ( $this->Auth->user('id') != $user['User']['id'] ): ?>

				| <?= $this->Html->link('Toggle Status', '/admin/users/flip/'.$user['User']['id']); ?> 
				| <?= $this->Html->link('Emulate User', '/admin/users/emulate/'.$user['User']['id']); ?>
				| <?= $this->Html->link('Delete User', '/admin/users/delete/'.$user['User']['id']); ?>

				<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; ?>
		<tr>
		<td colspan="8">
			<a href="<?= $this->Html->url('/admin/users/create'); ?>" class="btn btn-primary pull-right">New User</a>
		</td>
	</tr>
	</tbody>
</table>