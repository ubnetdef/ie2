<h2>My Profile</h2>

<form method="post" class="form-horizontal">
	<div class="form-group">
		<label class="col-sm-3 control-label">Username</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" value="<?php echo $this->Auth->user('username'); ?>" readonly="readonly" />
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label">Group Membership</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" value="<?php echo $group_path; ?>" readonly="readonly" />
		</div>
	</div>

	<?php if ( $password_change_enabled ): ?>
	<div class="form-group">
		<label for="old_password" class="col-sm-3 control-label">Current Password</label>
		<div class="col-sm-9">
			<input type="password" class="form-control" id="old_password" name="old_password" value="" />
		</div>
	</div>

	<div class="form-group">
		<label for="new_password" class="col-sm-3 control-label">New Password</label>
		<div class="col-sm-9">
			<input type="password" class="form-control" id="new_password" name="new_password" value="" />
		</div>
	</div>

	<div class="form-group">
		<label for="new_password" class="col-sm-3 control-label">New Password (Confirm)</label>
		<div class="col-sm-9">
			<input type="password" class="form-control" id="new_password2" name="new_password2" value="" />
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button type="submit" class="btn btn-default">Update Profile</button>
		</div>
	</div>
	<?php endif; ?>
</form>