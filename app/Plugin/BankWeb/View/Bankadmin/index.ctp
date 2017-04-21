<h2>Backend Panel - BankWeb Manager</h2>

<div class="row">
	<div class="col-md-6">
		<h3>Products</h3>
		<table class="table">
			<thead>
				<tr>
					<td>ID</td>
					<td>Cost</td>
					<td>Name</td>
					<td>Actions</td>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $products AS $p ): ?>
				<tr>
					<td><?= $p['Product']['id']; ?></td>
					<td><?= $p['Product']['cost']; ?></td>
					<td><?= $p['Product']['name']; ?></td>
					<td>
						<a
							href="#"
							class="btn btn-primary btn-xs edit-btn"
							data-toggle="modal"
							data-target="#productModal"
							data-id="<?= $p['Product']['id']; ?>"
						>
							Edit
						</a>
						<a
							href="<?= $this->Html->url('/admin/bank/deleteProduct/'.$p['Product']['id']); ?>"
							class="btn btn-danger btn-xs delete-btn"
						>
							Delete
						</a>
					</td>
				</tr>
				<?php endforeach; ?>
				<tr>
				<td colspan="4">
					<a href="#" class="btn btn-primary pull-right create-btn" data-toggle="modal" data-target="#productModal">
						New Product
					</a>
				</td>
			</tr>
			</tbody>
		</table>
	</div>

	<div class="col-md-6">
		<h3>Account Mappings</h3>
		<table class="table">
			<thead>
				<tr>
					<td>Group</td>
					<td>Username</td>
					<td>Password</td>
					<td>Actions</td>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $accounts AS $a ): ?>
				<tr>
					<td><?= $a['Group']['name']; ?></td>
					<td><?= $a['AccountMapping']['username']; ?></td>
					<td>
						<a href="#" class="pw-hide" data-password="<?= $a['AccountMapping']['password']; ?>">
							<?= substr($a['AccountMapping']['password'], 0, 2) . str_repeat('*', strlen($a['AccountMapping']['password']) - 2); ?>
						</a>
					</td>
					<td>
						<a
							href="#"
							class="btn btn-primary btn-xs edit-btn"
							data-toggle="modal"
							data-target="#bankModal"
							data-id="<?= $a['AccountMapping']['id']; ?>"
						>
							Edit
						</a>
						<a
							href="<?= $this->Html->url('/admin/bank/deleteMapping/'.$a['AccountMapping']['id']); ?>"
							class="btn btn-danger btn-xs delete-btn"
						>
							Delete
						</a>
					</td>
				</tr>
				<?php endforeach; ?>
				<tr>
				<td colspan="4">
					<a href="#" class="btn btn-primary pull-right create-btn" data-toggle="modal" data-target="#bankModal">
						New Account Mapping
					</a>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
</div>

<?= $this->element('BankWeb.product_modal'); ?>
<?= $this->element('BankWeb.account_modal', ['groups' => $groups]); ?>

<script>
$(document).ready(function() {
	$('#bankModal').on('show.bs.modal', function(e) {
		source = $(e.relatedTarget);

		if ( source.data('id') === undefined ) {
			$('#bankModal .modal-title').html('Account Mapping Creation');
			$('#bankModal form [name=id]').val('0');

			$('#bankModal form [name=username]').val('');
			$('#bankModal form [name=password]').val('');
			$('#bankModal form [name=group_id] option[value=1]').prop('selected', true);
		} else {
			$('#bankModal .modal-title').html('Account Mapping Modification');
			$('#bankModal form [name=id]').val(source.data('id'));

			$.getJSON('<?= $this->Html->url(['plugin' => 'BankWeb', 'controller' => 'bankadmin', 'action' => 'api', 'mapping']); ?>/'+source.data('id'), function(data) {
				$('#bankModal form [name=username]').val(data.username);
				$('#bankModal form [name=password]').val(data.password);
				$('#bankModal form [name=group_id] option[value='+data.group_id+']').prop('selected', true);
			});
		}
	});

	$('#productModal').on('show.bs.modal', function(e) {
		source = $(e.relatedTarget);

		if ( source.data('id') === undefined ) {
			$('#productModal .modal-title').html('Product Creation');
			$('#productModal form input[name=id]').val('0');

			$('#productModal form [name=name]').val('');
			$('#productModal form [name=description]').val('');
			$('#productModal form [name=cost]').val('');
			$('#productModal form [name=user_input]').val('');
			$('#productModal form [name=message_user]').val('');
			$('#productModal form [name=message_slack]').val('');

			$('#productModal form [name=enabled]').removeAttr('checked');
			$('#productModal form [name=enabled][value=0]').attr('checked', 'checked');
		} else {
			$('#productModal .modal-title').html('Product Modification');
			$('#productModal form input[name=id]').val(source.data('id'));

			$.getJSON('<?= $this->Html->url(['plugin' => 'BankWeb', 'controller' => 'bankadmin', 'action' => 'api', 'product']); ?>/'+source.data('id'), function(data) {
				$('#productModal form [name=name]').val(data.name);
				$('#productModal form [name=description]').val(data.description);
				$('#productModal form [name=cost]').val(data.cost);
				$('#productModal form [name=user_input]').val(data.user_input);
				$('#productModal form [name=message_user]').val(data.message_user);
				$('#productModal form [name=message_slack]').val(data.message_slack);

				$('#productModal form [name=enabled]').removeAttr('checked');
				$('#productModal form [name=enabled][value='+(data.enabled ? 1 : 0)+']').attr('checked', 'checked');
			});
		}
	});

	$('.pw-hide').click(function(e) {
		oldPW = $(this).html().trim();
		newPW = $(this).data('password');

		$(this).data('password', oldPW).html(newPW);

		e.preventDefault();
		return false;
	});
});
</script>