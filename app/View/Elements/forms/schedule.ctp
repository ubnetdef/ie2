<?php
$this->Html->css('/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min', ['inline' => false]);

$this->Html->script('/vendor/moment.min', ['inline' => false]);
$this->Html->script('/vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min', ['inline' => false]);
?>

<form method="post" class="form-horizontal">
	<div class="row">
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading">Timing</div>
				<div class="panel-body">
					<div class="form-group">
						<label for="start" class="col-sm-3 control-label">Fuzzy Schedule</label>
						<div class="col-sm-9">
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
						<label for="start" class="col-sm-3 control-label">Start</label>
						<div class="col-sm-9">
							<div class="input-group date datetimepicker" id="start_datepicker">
								<input type="text" class="form-control time-use-data" id="start" name="start" required="required" />
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label for="end" class="col-sm-3 control-label">End</label>
						<div class="col-sm-9">
							<div class="input-group date datetimepicker" id="end_datepicker">
								<input type="text" class="form-control time-use-data" id="end" name="end" required="required" />
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
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
						<label for="inject_id" class="col-sm-3 control-label">Mapped Inject</label>
						<div class="col-sm-9">
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
						<label for="group_id" class="col-sm-3 control-label">Assigned Group</label>
						<div class="col-sm-9">
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
						<label for="dependency_id" class="col-sm-3 control-label">Inject Dependency</label>
						<div class="col-sm-9">
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
						<label for="active" class="col-sm-3 control-label">Active</label>
						<div class="col-sm-9">
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
						<label for="start" class="col-sm-3 control-label">UI Order</label>
						<div class="col-sm-9">
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

<script>
function updateDTP(which) {
	isFuzzy = (which == "1");

	$('.datetimepicker').each(function() {
		dtp = $(this).data('DateTimePicker');
		dtp.format(isFuzzy ? 'H[h] mm[m] s[s]' : false);
	});
}

$(document).ready(function() {
	$('.datetimepicker').datetimepicker({
		sideBySide: true,
		keepInvalid: true,
	});

	// Bind to updates
	$('input[name=fuzzy]').click(function() {
		updateDTP($(this).val());
	});

	// Bind on form submit
	$('form').submit(function() {
		isFuzzy = ($('input[name=fuzzy]:checked').val() == "1");

		$('.datetimepicker').each(function() {
			dtp = $(this).data('DateTimePicker');
			input = $(this).children('input');

			if ( !$.isNumeric(input.val()) ) {
				// Not a number. Let's get the date from DTP
				if ( isFuzzy ) {
					diff = dtp.date().unix() - moment().startOf('day').unix();
					input.val(diff);
				} else {
					input.val(dtp.date().utc().unix());
				}
			}
		});
	});

	// First run, let's do some stuff
	<?php foreach ( ['start' => $start, 'end' => $end] AS $k => $v ): ?>
	$('#<?= $k; ?>_datepicker')
	<?php if ( !$fuzzy && $start == 0 ): ?>
		.children('input').val('<?= $v; ?>');
	<?php else: ?>
		.data('DateTimePicker').date(
			<?php if ( $fuzzy ): ?>
			moment().startOf('day').seconds(<?= $v; ?>)
			<?php else: ?>
			moment.unix(<?= $v; ?>)
			<?php endif; ?>
		);
	<?php endif; ?>
	<?php endforeach; ?>

	// Update
	updateDTP($('input[name=fuzzy]:checked').val());
});
</script>