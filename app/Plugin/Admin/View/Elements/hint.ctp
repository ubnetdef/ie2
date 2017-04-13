<?php
// Summernote
$this->Html->css('/vendor/summernote/summernote', ['inline' => false]);
$this->Html->script('/vendor/summernote/summernote.min', ['inline' => false]);

// DateTimePicker
$this->Html->css('/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min', ['inline' => false]);
$this->Html->script('/vendor/moment.min', ['inline' => false]);
$this->Html->script('/vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min', ['inline' => false]);
?>

<form method="post" class="form-horizontal">
	<div class="form-group">
		<label for="title" class="col-sm-3 control-label">Title</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="title" name="title" value="<?= !empty($hint) ? $hint['Hint']['title'] : ''; ?>" required="required" />
		</div>
	</div>
	<div class="row">
		<div class="col-sm-9 col-sm-offset-3">
			<p class="help-block">The Hint Title has to be unique</p>
		</div>
	</div>

	<div class="form-group">
		<label for="content_editor" class="col-sm-3 control-label">Content</label>
		<div class="col-sm-9">
			<input type="hidden" name="content" id="content" />
			<div id="content_editor" class="wysiwyg"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-9 col-sm-offset-3">
			<p class="help-block">The well...hint content.</p>
		</div>
	</div>

	<hr />

	<div class="form-group">
		<label for="time_wait" class="col-sm-3 control-label">Time Wait</label>
		<div class="col-sm-9">
			<div class="input-group date datetimepicker" id="time_wait_datepicker">
				<input type="text" class="form-control time-use-data" id="time_wait" name="time_wait" required="required" />
				<span class="input-group-addon">
					<span class="glyphicon glyphicon-calendar"></span>
				</span>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-9 col-sm-offset-3">
			<p class="help-block">Time wait</p>
		</div>
	</div>

	<div class="form-group">
		<label for="cost" class="col-sm-3 control-label">Cost</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="cost" name="cost" value="<?= !empty($hint) ? $hint['Hint']['cost'] : ''; ?>" required="required" />
		</div>
	</div>
	<div class="row">
		<div class="col-sm-9 col-sm-offset-3">
			<p class="help-block">Hint cost</p>
		</div>
	</div>

	<div class="form-group">
		<label for="inject_id" class="col-sm-3 control-label">Inject</label>
		<div class="col-sm-9">
			<select class="form-control" id="inject_id" name="inject_id" required="required">
				<?php foreach($injects AS $i): ?>
				<option value="<?= $i['Inject']['id']; ?>"<?= (!empty($hint) && $hint['Hint']['inject_id'] == $i['Inject']['id']) ? ' selected="selected"' : ''; ?>>
					<?= $i['Inject']['title']; ?>
				</option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-9 col-sm-offset-3">
			<p class="help-block">Inject associated with this hint.</p>
		</div>
	</div>

	<div class="form-group">
		<label for="parent_id" class="col-sm-3 control-label">Hint Parent</label>
		<div class="col-sm-9">
			<select class="form-control" id="parent_id" name="parent_id" required="required">
				<option value="0"<?= !empty($hint) && 0 == $hint['Hint']['parent_id'] ? ' selected="selected"' : ''; ?>>None</option>
				<option disabled>──────────</option>
				<?php foreach($hints AS $i): if ( !empty($hint) && $i['Hint']['id'] == $hint['Hint']['id'] ) continue; ?>
				<option value="<?= $i['Hint']['id']; ?>"<?= (!empty($hint) && $hint['Hint']['parent_id'] == $i['Hint']['id']) ? ' selected="selected"' : ''; ?>>
					<?= $i['Hint']['title']; ?>
				</option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-9 col-sm-offset-3">
			<p class="help-block">The parent of this hint.  Used for ordering.</p>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button type="submit" class="btn btn-default"><?= !empty($hint) ? 'Edit' : 'Create'; ?> Hint</button>
		</div>
	</div>
</form>

<script>
$(document).ready(function() {
	$('.datetimepicker').datetimepicker({
		sideBySide: true,
		keepInvalid: true,
		format: "H[h] mm[m] s[s]",
	});

	$('.wysiwyg').summernote({
		height: 200,
	});

	// Bind on form submit
	$('form').submit(function() {
		$('.datetimepicker').each(function() {
			dtp = $(this).data('DateTimePicker');
			input = $(this).children('input');

			if ( !$.isNumeric(input.val()) ) {
				diff = dtp.date().unix() - moment().startOf('day').unix();
				input.val(diff);
			}
		});

		$('#content').val($('#content_editor').summernote('code'));
	});

	<?php if ( !empty($hint) ): ?>
	$('#content_editor').summernote('code', '<?= addslashes($hint['Hint']['content']); ?>');

	$('#time_wait_datepicker').data('DateTimePicker').date(
		moment().startOf('day').seconds(<?= $hint['Hint']['time_wait']; ?>)
	);
	<?php endif; ?>
});
</script>