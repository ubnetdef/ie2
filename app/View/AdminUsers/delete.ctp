<h2>Backend Panel - User Manager</h2>

<div class="row">
	<div class="col-md-10 col-md-offset-1">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Confirm Deletion</h3>
			</div>
			<div class="panel-body">
				<form class="form-horizontal" method="post">
					<p>Are you sure you wish to <strong>delete</strong> "<?= $user['User']['username']; ?>".</p>

					<div class="text-center">
						<button type="submit" class="btn btn-danger">Delete User</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>