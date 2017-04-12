<ol class="breadcrumb">
	<li><a href="<?= $this->Html->url(['plugin' => 'BankWeb', 'controller' => 'account', 'action' => 'index']); ?>">Bank Account</a></li>
	<li class="active">PIN Change</li>
</ol>

<div class="container">
	<h2>PIN Change - Account #<?= $account; ?></h2>

	<p>If you do not remember your PIN, you can purchase the PIN reset service from White Team.</p>

	<form class="form-horizontal" method="post">
		<div class="form-group">
			<label for="newpin" class="col-sm-2 control-label">Account Number</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="account" value="<?= $account; ?>" readonly="readonly">
			</div>
		</div>
		<div class="form-group">
			<label for="newpin" class="col-sm-2 control-label">Current PIN</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="pin" placeholder="Current Account PIN">
			</div>
		</div>

		<div class="form-group">
			<label for="newpin" class="col-sm-2 control-label">New PIN</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="newpin" placeholder="New Account PIN">
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" class="btn btn-default">Update Account</button>
			</div>
		</div>
	</form>	
</div>