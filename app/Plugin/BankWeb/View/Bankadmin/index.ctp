<h2>Backend Panel - BankWeb Manager</h2>

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
					href="<?= $this->Html->url('/admin/bank/delete/'.$a['AccountMapping']['id']); ?>"
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

<?= $this->element('BankWeb.modal', ['groups' => $groups]); ?>

<script>
$(document).ready(function() {
	$('.edit-btn').click(function() {
		$('#bankModal .modal-title').html('Account Mapping Modification');
		$('#bankModal form input[name=id]').val($(this).data('id'));

		$.getJSON('<?= $this->Html->url(['plugin' => 'BankWeb', 'controller' => 'bankadmin', 'action' => 'api']); ?>'+$(this).data('id'), function(data) {
			$('#bankModal form input[name=username]').val(data.username);
			$('#bankModal form input[name=password]').val(data.password);
			$('#bankModal form select[name=group_id] option[value='+data.group_id+']').prop('selected', true);
		});
	});

	$('.create-btn').click(function() {
		$('#bankModal .modal-title').html('Account Mapping Creation');
		$('#bankModal form input[name=id]').val('0');

		$('#bankModal form input[name=username]').val('');
		$('#bankModal form input[name=password]').val('');
		$('#bankModal form select[name=group_id] option[value=1]').prop('selected', true);
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