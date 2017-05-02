<h2>Team Panel</h2>
<h4><?= $this->Auth->group('name'); ?></h4>

<?= $this->element('navbar', ['at_config' => true]); ?>

<p>&nbsp;</p>

<div class="alert alert-info"><strong>NOTICE</strong>: Any IP changes must be in the same subnet.  IP changes that do not follow this rule will be discarded.</div>

<form method="post" class="form-horizontal">
	<?php foreach ( $data AS $group => $options ): ?>
		<div class="panel panel-default">
			<div class="panel-heading"><?php echo $group; ?></div>
			<div class="panel-body">

			<?php foreach ($options AS $opt): if ( $opt['hidden'] == 1 ) continue; ?>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?= ($opt['key'] == 'USERPASS' ? 'ACCOUNT' : $opt['key']); ?></label>
					
					<?php if ( $opt['key'] == 'USERPASS' ): list($user, $pass) = explode('||', $opt['value'], 2); ?>
					<div class="col-sm-4">
						<div class="input-group">
							<div class="input-group-addon">USER</div>
							<input type="text" name="opt<?= $opt['id']; ?>[user]" class="form-control" value="<?= $user; ?>" />
						</div>
					</div>
					<div class="col-sm-4">
						<div class="input-group">
							<div class="input-group-addon">PASS</div>
							<input type="text" name="opt<?= $opt['id']; ?>[pass]" class="form-control" value="<?= $pass; ?>" />
						</div>
					</div>
					<?php else: ?>
					<div class="col-sm-9">
						<input type="text" name="opt<?= $opt['id']; ?>" class="form-control" value="<?= $opt['value']; ?>"<?= $opt['edit'] == 0 ? ' disabled="disabled"' : ''; ?> />
					</div>
					<?php endif; ?>
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
