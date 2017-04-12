<ol class="breadcrumb">
	<li><a href="<?= $this->Html->url(['plugin' => 'BankWeb', 'controller' => 'account', 'action' => 'index']); ?>">Bank Account</a></li>
	<li class="active">Transactions</li>
</ol>

<div class="container">
	<h2>Transaction Logs - Account #<?= $account; ?></h2>

	<table class="table table-striped">
		<thead>
			<tr>
				<td>Type</td>
				<td>Source Account</td>
				<td>Destination Account</td>
				<td>Amount</td>
				<td>Time</td>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $logs AS $trans ): ?>
			<tr>
				<td><?= $trans['type']; ?></td>
				<td>#<?= $trans['src']; ?></td>
				<td>#<?= $trans['dst']; ?></td>
				<td><?= money_format('%.2n', $trans['amount']); ?></td>
				<td><?= $trans['time']; ?>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>