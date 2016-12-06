<h2>Team Panel</h2>
<h4><?= $this->Auth->group('name'); ?></h4>

<?= $this->element('navbar', ['at_config' => true]); ?>

<p>&nbsp;</p>

<form method="post" class="form-horizontal">
	<?php foreach ( $data AS $group => $options ): ?>
		<div class="panel panel-default">
			<div class="panel-heading"><?php echo $group; ?></div>
			<div class="panel-body">

			<div class="alert alert-info"><strong>WARNING</strong>: USERPASS's value must be in the format username||password.  If you do not follow this, your service will not score.</div>

			<?php foreach ($options AS $opt): ?>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $opt['key']; ?></label>
					<div class="col-sm-9">
						<input type="text" name="opt<?php echo $opt['id']; ?>" class="form-control" value="<?php echo $opt['value']; ?>"<?php echo ($opt['edit'] != 1 ? ' readonly="readonly"' : ''); ?>) />
					</div>
				</div>
			<?php endforeach; ?>

			</div>
		</div>
	<?php endforeach; ?>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button type="submit" class="btn btn-default">Update Information</button>
		</div>
	</div>
</form>
