<div class="container">
	<h2>myAccount - Account Information</h2>

	<table class="table table-striped">
		<thead>
			<tr>
				<td>Account Number</td>
				<td>Balance</td>
				<td>Actions</td>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $accounts AS $account ): ?>
			<tr>
				<td>#<?= $account['id']; ?></td>
				<td><?= money_format('%.2n', $account['balance']); ?></td>
				<td>
					<a href="<?= $this->Html->url(['plugin' => 'BankWeb', 'controller' => 'account', 'action' => 'transfer', $account['id']]); ?>" class="btn btn-xs btn-primary">Transfer Money</a>
					<a href="<?= $this->Html->url(['plugin' => 'BankWeb', 'controller' => 'account', 'action' => 'transactions', $account['id']]); ?>" class="btn btn-xs btn-info">View Transactions</a>
					<a href="<?= $this->Html->url(['plugin' => 'BankWeb', 'controller' => 'account', 'action' => 'pin', $account['id']]); ?>" class="btn btn-xs btn-danger">Change PIN</a>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<h3>Create a new account</h3>
	<p>This will create a new account under your username.</p>
	<form class="form-horizontal" method="post" action="<?= $this->Html->url(['plugin' => 'BankWeb', 'controller' => 'account', 'action' => 'create']); ?>">
		<div class="form-group">
			<label for="newpin" class="col-sm-2 control-label">New PIN</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="pin" placeholder="New Account PIN">
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" class="btn btn-default">Create New Account</button>
			</div>
		</div>
	</form>	
</div>