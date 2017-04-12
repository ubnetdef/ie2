<ol class="breadcrumb">
	<li><a href="<?= $this->Html->url(['plugin' => 'admin', 'controller' => 'hints', 'action' => 'index']); ?>">Hint Manager</a></li>
	<li class="active">Delete Hint</li>
</ol>

<h2>Backend Panel - Hint Manager</h2>

<div class="row">
	<div class="col-md-10 col-md-offset-1">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Confirm Deletion</h3>
			</div>
			<div class="panel-body">
				<form class="form-horizontal" method="post">
					<p>Are you sure you wish to <strong>delete</strong> hint "<?= $hint['Hint']['title']; ?>" (associated with inject "<?= $hint['Inject']['title']; ?>").</p>

					<div class="text-center">
						<button type="submit" class="btn btn-danger">Delete Hint</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>