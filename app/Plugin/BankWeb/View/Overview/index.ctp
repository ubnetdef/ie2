<h2>BankWeb Overview</h2>

<table class="table table-bordered">
	<thead>
		<tr>
			<td width="5%">ID</td>
			<td width="15%">Date</td>
			<td width="20%">Product</td>
			<td width="20%">Purchased By</td>
			<td width="10%">Completed</td>
			<td width="20%">Time-To-Completion</td>
			<td width="10%">Actions</td>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $purchases AS $p ): ?>
		<tr class="<?= $p['Purchase']['completed'] ? '' : 'danger'; ?>">
			<td><?= $p['Purchase']['id']; ?></td>
			<td><?= tz_date('n/j/Y \a\t g:iA', $p['Purchase']['time']); ?></td>
			<td><?= $p['Product']['name']; ?></td>
			<td><?= $p['User']['username']; ?> (<?= $p['User']['Group']['name']; ?>)</td>
			<td><?= $p['Purchase']['completed'] ? $p['Purchase']['completed_by'] : 'N/A'; ?></td>
			<td><?= $p['Purchase']['completed'] ? fuzzy_duration($p['Purchase']['time'], $p['Purchase']['completed_time']) : 'N/A'; ?></td>
			<td>
			<?php if ( $p['Purchase']['completed'] ): ?>
				N/A
			<?php else: ?>
				<a
					href="<?= $this->Html->url(['plugin' => 'BankWeb', 'controller' => 'overview', 'action' => 'mark', $p['Purchase']['id']]); ?>"
					class="btn btn-primary btn-xs edit-btn"
				>
					Mark as Completed
				</a>
			<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>