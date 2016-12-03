<style>
.injectinfo {
	font-size: 16px;
}
</style>

<ol class="breadcrumb">
	<li><a href="<?= $this->Html->url('/schedule'); ?>">Scheduler</a></li>
	<li><a href="<?= $this->Html->url('/schedule/manager'); ?>">Schedule Manager</a></li>
	<li class="active">Edit Schedule</li>
</ol>

<div class="row">
	<div class="col-md-12">
		<div class="well">
			<h2><?= $schedule['Inject']['title']; ?></h2>

			<p class="injectinfo">
				<strong>Submission Type</strong>: <?= $this->InjectStyler->getName($schedule['Inject']['type']); ?><br />
				<strong>Max Submissions</strong>: <?= $schedule['Inject']['max_submissions']; ?>
			</p>

			<hr />

			<?= $this->InjectStyler->contentOutput($schedule['Inject']['content'], $this->Auth->item()); ?>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="well">
			<h2>Schedule Information</h2>

			<hr />

			<form method="post" class="form-horizontal">
				<div class="row">
					<div class="col-md-4">
						<div class="panel panel-default">
							<div class="panel-heading">Timing</div>
							<div class="panel-body">
								<div class="form-group">
									<label for="start" class="col-sm-4 control-label">Fuzzy Schedule</label>
									<div class="col-sm-8">
										<div class="radio">
											<label>
												<input type="radio" name="fuzzy" value="1" checked> Yes
											</label>
										</div>
										<div class="radio">
											<label>
												<input type="radio" name="fuzzy" value="0"> No
											</label>
										</div>
									</div>
								</div>

								<div class="form-group">
									<label for="start" class="col-sm-4 control-label">Start</label>
									<div class="col-sm-8">
										<input type="datetime-local" class="form-control" id="start" name="start">
									</div>
								</div>

								<div class="form-group">
									<label for="end" class="col-sm-4 control-label">End</label>
									<div class="col-sm-8">
										<input type="datetime-local" class="form-control" id="end" name="end">
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-4">
						<div class="panel panel-default">
							<div class="panel-heading">Assignment</div>
							<div class="panel-body">
								<div class="form-group">
									<label for="inject_id" class="col-sm-4 control-label">Mapped Inject</label>
									<div class="col-sm-8">
										<select class="form-control" id="inject_id" name="inject_id">
											<option>Inject #1</option>
											<option>Inject #2</option>
											<option>Inject #3</option>
											<option>Inject #4</option>
											<option>Inject #5</option>
										</select>
									</div>
								</div>

								<div class="form-group">
									<label for="group_id" class="col-sm-4 control-label">Assigned Group</label>
									<div class="col-sm-8">
										<select class="form-control" id="group_id" name="group_id">
											<option>Staff</option>
											<option>--Administrative Team</option>
											<option>--White Team</option>
											<option>Blue Teams</option>
											<option>--Team 0</option>
										</select>
									</div>
								</div>

								<div class="form-group">
									<label for="dependency_id" class="col-sm-4 control-label">Inject Dependency</label>
									<div class="col-sm-8">
										<select class="form-control" id="dependency_id" name="dependency_id">
											<option>None</option>
											<option>Inject #1</option>
											<option>Inject #2</option>
											<option>Inject #3</option>
											<option>Inject #4</option>
											<option>Inject #5</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-4">
						<div class="panel panel-default">
							<div class="panel-heading">UI Display</div>
							<div class="panel-body">
								<div class="form-group">
									<label for="active" class="col-sm-4 control-label">Active</label>
									<div class="col-sm-8">
										<div class="radio">
											<label>
												<input type="radio" name="active" value="1" checked> Yes
											</label>
										</div>
										<div class="radio">
											<label>
												<input type="radio" name="active" value="0"> No
											</label>
										</div>
									</div>
								</div>

								<div class="form-group">
									<label for="start" class="col-sm-4 control-label">UI Order</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" name="order">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<button type="submit" class="btn btn-default pull-right">Submit</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>