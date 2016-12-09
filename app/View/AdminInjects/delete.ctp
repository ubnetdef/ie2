<h2>Backend Panel - Delete Manager</h2>
<h4><?= $this->Auth->group('name'); ?></h4>

<div class="row">
	<div class="col-md-10 col-md-offset-1">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Confirm Deletion</h3>
			</div>
			<div class="panel-body">
				<form class="form-horizontal" method="post">
					<p>Are you sure you wish to <strong>delete</strong> "<?= $inject['Inject']['title']; ?>".</p>

					<div class="text-center">
						<button type="submit" class="btn btn-danger">Delete Inject</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>