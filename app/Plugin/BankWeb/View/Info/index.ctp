<div class="container">
	<div class="jumbotron">
		<h2>Bank Information</h2>
		<p>The BankAPI Server provides a RESTful interface for interacting with the bank.  Should you choose not to use the BankWEB Client provided, you can also interact directly with the API.</p>
	</div>

	<div class="well">
		<h3>BankWEB Configuration</h3>
		<ul class="list-unstyled">
			<li><strong>Server</strong>: <?= $api['host']; ?></li>
			<li><strong>Port</strong>: <?= $api['port']; ?></li>
			<li><strong>HTTPS</strong>: <?= $api['scheme'] == 'https' ? 'Yes' : 'No'; ?></li>
			<li><hr /></li>
			<li><strong>Username</strong>: <?= $username; ?></li>
			<li><strong>Password</strong>: <?= $password; ?></li>
		</ul>
	</div>
</div>