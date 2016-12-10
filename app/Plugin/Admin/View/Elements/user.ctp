<?php
$this->Html->css('/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min', ['inline' => false]);

$this->Html->script('/vendor/moment.min', ['inline' => false]);
$this->Html->script('/vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min', ['inline' => false]);
?>

<form method="post" class="form-horizontal">
	<div class="form-group">
		<label for="username" class="col-sm-3 control-label">Username</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="username" name="username" value="<?= !empty($user) ? $user['User']['username'] : ''; ?>" required="required" />
		</div>
	</div>

	<div class="form-group">
		<label for="password" class="col-sm-3 control-label">Password</label>
		<div class="col-sm-9">
			<input type="password" class="form-control" id="password" name="password" value="" />
		</div>
	</div>

	<div class="form-group">
		<label for="group_id" class="col-sm-3 control-label">Group Membership</label>
		<div class="col-sm-9">
			<select class="form-control" id="group_id" name="group_id" required="required">
				<?php foreach($groups AS $id => $name): ?>
				<option value="<?php echo $id; ?>"<?= (!empty($user) && $user['User']['group_id'] == $id) ? ' selected="selected"' : ''; ?>>
					<?= $name; ?>
				</option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>

	<div class="form-group">
		<label for="expiration" class="col-sm-3 control-label">Account Expiration</label>
		<div class="col-sm-9">
			<div class="input-group date datetimepicker" id="expires_datepicker">
				<input type="text" class="form-control time-use-data" id="expiration" name="expiration" value="<?= !empty($user) ? $user['User']['expiration'] : ''; ?>"  required="required" />
				<span class="input-group-addon">
					<span class="glyphicon glyphicon-calendar"></span>
				</span>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-9 col-sm-offset-3">
			<p class="help-block">Please enter "0" if this account will never expire</p>
		</div>
	</div>

	<div class="form-group">
		<label for="active" class="col-sm-3 control-label">Enabled</label>
		<div class="col-sm-9">
			<div class="radio">
				<label>
					<input type="radio" name="active" id="activeYes" value="1"<?= (!empty($user) && $user['User']['active'] == 1) ? ' checked="checked"' : ''; ?> required="required">
					Yes
				</label>
			</div>
			<div class="radio">
				<label>
					<input type="radio" name="active" id="activeNo" value="0"<?= (!empty($user) && $user['User']['active'] == 0) ? ' checked="checked"' : ''; ?> required="required">
					No
				</label>
			</div>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button type="submit" class="btn btn-default"><?= !empty($user) ? 'Edit' : 'Create'; ?> User</button>
		</div>
	</div>
</form>

<script>
$(document).ready(function() {
	$('.datetimepicker').datetimepicker({
		sideBySide: true,
		keepInvalid: true,
	});

	<?php if ( !empty($user) && $user['User']['expiration'] > 0 ): ?>
	$('#expires_datepicker').data('DateTimePicker').date(moment.unix('<?= $user['User']['expiration']; ?>'));
	<?php endif; ?>

	$('form').submit(function() {
		$('.datetimepicker').each(function() {
			dtp = $(this).data('DateTimePicker');
			input = $(this).children('input');

			if ( !$.isNumeric(input.val()) ) {
				// Not a number. Let's get the date from DTP
				input.val(dtp.date().utc().unix());
			}
		});
	});
});
</script>