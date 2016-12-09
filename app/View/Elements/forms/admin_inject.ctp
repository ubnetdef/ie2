<?php
$this->Html->css('/vendor/bootstrap3-wysiwyg/bootstrap3-wysihtml5.min', ['inline' => false]);

$this->Html->script('/vendor/bootstrap3-wysiwyg/bootstrap3-wysihtml5.all.min', ['inline' => false]);
?>
<form method="post" class="form-horizontal">
	<div class="form-group">
		<label for="title" class="col-sm-3 control-label">Title</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="title" name="title" value="<?= !empty($inject) ? $inject['Inject']['title'] : ''; ?>" required="required" />
		</div>
	</div>
	<div class="row">
		<div class="col-sm-9 col-sm-offset-3">
			<p class="help-block">The Inject Title has to be unique</p>
		</div>
	</div>

	<div class="form-group">
		<label for="content" class="col-sm-3 control-label">Content</label>
		<div class="col-sm-9">
			<textarea class="form-control wysiwyg" name="content" id="content" rows="10"></textarea>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-9 col-sm-offset-3">
			<p class="help-block">This will be shown to the assigned group.</p>
		</div>
	</div>

	<div class="form-group">
		<label for="grading_guide" class="col-sm-3 control-label">Grading Guide</label>
		<div class="col-sm-9">
			<textarea class="form-control wysiwyg" name="grading_guide" id="grading_guide" rows="10"></textarea>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-9 col-sm-offset-3">
			<p class="help-block">This will be shown to White Team members when grading the inject.</p>
		</div>
	</div>

	<div class="form-group">
		<label for="max_points" class="col-sm-3 control-label">Max Points</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="max_points" name="max_points" value="<?= !empty($inject) ? $inject['Inject']['max_points'] : ''; ?>" required="required" />
		</div>
	</div>
	<div class="row">
		<div class="col-sm-9 col-sm-offset-3">
			<p class="help-block">The max number of points a team can score on this inject.</p>
		</div>
	</div>

	<div class="form-group">
		<label for="max_submissions" class="col-sm-3 control-label">Max Submissions</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="max_submissions" name="max_submissions" value="<?= !empty($inject) ? $inject['Inject']['max_submissions'] : ''; ?>" required="required" />
		</div>
	</div>
	<div class="row">
		<div class="col-sm-9 col-sm-offset-3">
			<p class="help-block">The max number of submissions a team can submit for this inject to be graded.</p>
		</div>
	</div>

	<div class="form-group">
		<label for="type" class="col-sm-3 control-label">Type</label>
		<div class="col-sm-9">
			<select class="form-control" id="type" name="type" required="required">
				<?php foreach($this->InjectStyler->getAllTypes() AS $type): ?>
				<option value="<?= $type->getID(); ?>"<?= (!empty($inject) && $inject['Inject']['type'] == $type->getID()) ? ' selected="selected"' : ''; ?>>
					<?= $type->getName(); ?>
				</option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-9 col-sm-offset-3">
			<p class="help-block">The submission type for this inject.</p>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button type="submit" class="btn btn-default"><?= !empty($inject) ? 'Edit' : 'Create'; ?> Inject</button>
		</div>
	</div>
</form>

<script>
$(document).ready(function() {
	$('.wysiwyg').wysihtml5({
		toolbar: {
			html: true,
			size: "xs",
		},
	});

	<?php if ( !empty($inject) ): ?>
	$('#content').html('<?php echo addslashes($inject['Inject']['content']); ?>');
	$('#grading_guide').html('<?php echo addslashes($inject['Inject']['grading_guide']); ?>');
	<?php endif; ?>
});
</script>