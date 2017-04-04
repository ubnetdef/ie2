<h2>Backend Panel - ScoreEngine Manager</h2>

<table class="table">
	<thead>
		<tr>
			<td>Team ID</td>
			<td>Team Name</td>
			<td>Status</td>
			<td>Actions</td>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $teams AS $team ): ?>
		<tr>
			<td><?= $team['Team']['id']; ?></td>
			<td><?= $team['Team']['name']; ?></td>
			<td><?= $team['Team']['enabled'] == 1 ? 'Enabled' : 'Disabled'; ?></td>
			<td>
				<?= $this->Html->link('Team Overview', ['plugin' => 'ScoreEngine', 'controller' => 'scoreadmin', 'action' => 'team', $team['Team']['id']]); ?>
				
				| <?= $this->Html->link('Service Config', ['plugin' => 'ScoreEngine', 'controller' => 'scoreadmin', 'action' => 'config', $team['Team']['id']]); ?> 
			</td>
		</tr>
		<?php endforeach; ?>
	</tr>
	</tbody>
</table>
	
