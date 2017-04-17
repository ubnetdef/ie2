<ol class="breadcrumb">
	<li><a href="<?= $this->Html->url(['plugin' => 'BankWeb', 'controller' => 'products', 'action' => 'index']); ?>">Bank Products</a></li>
	<li class="active">Purchase Confirmation</li>
</ol>

<div class="container">
	<h2>Purchase Confirmation</h2>
	<h3><?= $item['Product']['name']; ?></h3>

	<form class="form-horizontal" method="post">
		<div class="form-group">
			<label for="srcAcc" class="col-sm-2 control-label">Source Account</label>
			<div class="col-sm-10">
				<select class="form-control" name="srcAcc">
					<?php foreach ( $accounts AS $account ): ?>
						<option value="<?= $account['id']; ?>">
							#<?= $account['id']; ?> - BALANCE: $<?= money_format('%.2n', $account['balance']); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="form-group">
			<label for="srcAcc" class="col-sm-2 control-label">Item</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" value="<?= $item['Product']['name']; ?>" readonly="readonly">
			</div>
		</div>

		<div class="form-group">
			<label for="amount" class="col-sm-2 control-label">Amount</label>
			<div class="col-sm-10">
				<div class="input-group">
					<div class="input-group-addon">$</div>
					<input type="text" class="form-control" id="amount" name="amount" value="<?= $item['Product']['cost']; ?>" readonly="readonly">
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="pin" class="col-sm-2 control-label">PIN</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="pin" name="pin" placeholder="PIN">
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" class="btn btn-default">Send Money!</button>
			</div>
		</div>
	</form>
</div>