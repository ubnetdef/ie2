<form method="post" class="form-horizontal">
	<div class="form-group">
		<label for="name" class="col-sm-3 control-label">Name</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="name" name="name" value="<?= !empty($group) ? $group['Group']['name'] : ''; ?>" required="required" />
		</div>
	</div>

	<div class="form-group">
		<label for="team_number" class="col-sm-3 control-label">Team Mapping (optional)</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="team_number" name="team_number" value="<?= !empty($group) ? $group['Group']['team_number'] : ''; ?>" />
		</div>
	</div>

	<div class="form-group">
		<label for="parent_id" class="col-sm-3 control-label">Group Parent</label>
		<div class="col-sm-9">
			<select class="form-control" id="parent_id" name="parent_id" required="required">
				<option value=""<?= (!empty($group) && $group['Group']['parent_id'] === NULL) ? ' selected="selected"' : ''; ?>>
					None
				</option>
				<option disabled>──────────</option>
				<?php foreach($groups AS $id => $name): ?>
				<option value="<?php echo $id; ?>"<?= (!empty($group) && $group['Group']['parent_id'] == $id) ? ' selected="selected"' : ''; ?>>
					<?= $name; ?>
				</option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button type="submit" class="btn btn-default"><?= !empty($group) ? 'Edit' : 'Create'; ?> Group</button>
		</div>
	</div>
</form>