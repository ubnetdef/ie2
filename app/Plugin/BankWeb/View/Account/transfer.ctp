<ol class="breadcrumb">
	<li><a href="<?= $this->Html->url(['plugin' => 'BankWeb', 'controller' => 'account', 'action' => 'index']); ?>">Bank Account</a></li>
	<li class="active">Transfer</li>
</ol>

<div class="container">
	<h2>Account Balance Transfer</h2>

	<form class="form-horizontal" method="post">
		<div class="form-group">
			<label for="srcAcc" class="col-sm-2 control-label">Source Account</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="srcAcc" name="srcAcc" value="<?= $acc; ?>" placeholder="#0000000000">
			</div>
		</div>

		<div class="form-group">
			<label for="dstAcc" class="col-sm-2 control-label">Destination Account</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="dstAcc" name="dstAcc" value="" placeholder="#0000000000">
			</div>
		</div>

		<div class="form-group">
			<label for="amount" class="col-sm-2 control-label">Amount</label>
			<div class="col-sm-10">
				<div class="input-group">
					<div class="input-group-addon">$</div>
					<input type="text" class="form-control" id="amount" name="amount" value="" placeholder="1000">
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