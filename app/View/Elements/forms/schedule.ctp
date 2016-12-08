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
									<input type="radio" name="fuzzy" value="1"<?= $fuzzy ? ' checked' : ''; ?>> Yes
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" name="fuzzy" value="0"<?= !$fuzzy ? ' checked' : ''; ?>> No
								</label>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label for="start" class="col-sm-4 control-label">Start</label>
						<div class="col-sm-8">
							<input type="datetime-local" class="form-control" id="start" name="start" value="<?= $start; ?>">
						</div>
					</div>

					<div class="form-group">
						<label for="end" class="col-sm-4 control-label">End</label>
						<div class="col-sm-8">
							<input type="datetime-local" class="form-control" id="end" name="end" value="<?= $end; ?>">
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
								<?php foreach ( $injects AS $i ): ?>
								<option value="<?= $i['Inject']['id']; ?>"<?= $i['Inject']['id'] == $inject ? ' checked' : ''; ?>>
									<?= $i['Inject']['title']; ?>
								</option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label for="group_id" class="col-sm-4 control-label">Assigned Group</label>
						<div class="col-sm-8">
							<select class="form-control" id="group_id" name="group_id">
								<?php foreach ( $groups AS $id => $g ): ?>
								<option value="<?= $id; ?>"<?= $id == $group ? ' checked' : ''; ?>>
									<?= $g; ?>
								</option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label for="dependency_id" class="col-sm-4 control-label">Inject Dependency</label>
						<div class="col-sm-8">
							<select class="form-control" id="dependency_id" name="dependency_id">
								<option value="0"<?= 0 == $dep ? ' checked' : ''; ?>>None</option>
								<option disabled>──────────</option>
								<?php foreach ( $injects AS $i ): ?>
								<option value="<?= $i['Inject']['id']; ?>"<?= $i['Inject']['id'] == $dep ? ' checked' : ''; ?>>
									<?= $i['Inject']['title']; ?>
								</option>
								<?php endforeach; ?>
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
									<input type="radio" name="active" value="1"<?= $active ? ' checked' : ''; ?>> Yes
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" name="active" value="0"<?= !$active ? ' checked' : ''; ?>> No
								</label>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label for="start" class="col-sm-4 control-label">UI Order</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="order" value="<?= $order; ?>">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<button type="submit" class="btn btn-default pull-right">Submit</button>

			<?php if ( $sid > 0 ): ?>
			<a href="<?= $this->Html->url('/schedule/delete/'.$sid); ?>" class="btn btn-danger pull-right">Delete</a>
			<?php endif; ?>
		</div>
	</div>
</form>