<ol class="breadcrumb">
	<li><a href="<?= $this->Html->url(['plugin' => 'BankWeb', 'controller' => 'products', 'action' => 'index']); ?>">Bank Products</a></li>
	<li class="active">Purchase Confirmation</li>
</ol>

<div class="container">
	<h2>Purchase Confirmation</h2>
	<h3><?= $item['Product']['name']; ?></h3>

	<form method="post">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="srcAcc" class="control-label">Source Account</label>
					<select class="form-control" name="srcAcc">
						<?php foreach ( $accounts AS $account ): ?>
							<option value="<?= $account['id']; ?>">
								#<?= $account['id']; ?> - BALANCE: $<?= money_format('%.2n', $account['balance']); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="pin" class="control-label">PIN</label>
					<input type="text" class="form-control" id="pin" name="pin" placeholder="PIN">
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="srcAcc" class="control-label">Item</label>
					<input type="text" class="form-control" value="<?= $item['Product']['name']; ?>" readonly="readonly">
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="amount" class="control-label">Amount</label>
					<div class="input-group">
						<div class="input-group-addon">$</div>
						<input type="text" class="form-control" id="amount" name="amount" value="<?= $item['Product']['cost']; ?>" readonly="readonly">
					</div>
				</div>
			</div>
		</div>

		<?php if ( !empty($item['Product']['user_input']) ) : ?>
		<hr />

		<div class="form-group">
			<label for="user_input" class="control-label"><?= $item['Product']['user_input']; ?></label>
			<textarea class="form-control" rows="3" id="user_input" name="user_input"></textarea>
		</div>
		<?php endif; ?>

		<div class="form-group">
			<button type="submit" class="btn btn-default">Purchase</button>
		</div>
	</form>
</div>