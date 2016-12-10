<h2>Backend Panel - Site Manager</h2>

<table class="table">
	<thead>
		<tr>
			<td>Key</td>
			<td>Actions</td>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $config AS $c ): ?>
		<tr>
			<td><?= $c['Config']['key']; ?></td>
			<td>
				<a
					href="#"
					class="btn btn-primary btn-xs edit-btn"
					data-toggle="modal"
					data-target="#configModal"
					data-cid="<?= $c['Config']['id']; ?>"
				>
					Edit
				</a>
				<a
					href="<?= $this->Html->url('/admin/site/delete/'.$c['Config']['id']); ?>"
					class="btn btn-danger btn-xs delete-btn"
				>
					Delete
				</a>
			</td>
		</tr>
		<?php endforeach; ?>
		<tr>
		<td colspan="2">
			<a href="#" class="btn btn-primary pull-right create-btn" data-toggle="modal" data-target="#configModal">
				New Config Item
			</a>
		</td>
	</tr>
	</tbody>
</table>

<div class="modal fade" id="configModal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="post" action="<?= $this->Html->url('/admin/site/config'); ?>" class="form-horizontal">
				<input type="hidden" name="id" value="" />

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="key" class="col-sm-2 control-label">Key</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="key" name="key" placeholder="some.name">
						</div>
					</div>

					<div class="form-group">
						<label for="value" class="col-sm-2 control-label">Value</label>
						<div class="col-sm-10">
							<textarea class="form-control" rows="10" id="value" name="value"></textarea>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<input type="submit" class="btn btn-primary" />
				</div>
			</form>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	$('.edit-btn').click(function() {
		$('#configModal .modal-title').html('Config Edit');
		$('#configModal form input[name=id]').val($(this).data('cid'));

		$.getJSON('<?= $this->Html->url('/admin/site/api/'); ?>'+$(this).data('cid'), function(data) {
			$('#configModal form input[name=key]').val(data.key);
			$('#configModal form textarea[name=value]').val(data.value);
		});
	});

	$('.create-btn').click(function() {
		$('#configModal .modal-title').html('Config Creation');
		$('#configModal form input[name=id]').val('0');
		$('#configModal form input[name=key]').val('');
		$('#configModal form textarea[name=value]').val('');
	});
});
</script>