<h2>Backend Panel - Site Manager</h2>

<div class="row">
	<div class="col-md-8">
		<h3>Announcement Manager</h3>
		<table class="table">
			<thead>
				<tr>
					<td>Announcement</td>
					<td>Expiraion</td>
					<td>Active</td>
					<td>Actions</td>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $announce AS $a ): ?>
				<tr>
					<td><?= $a['Announcement']['content']; ?></td>
					<td><?= $a['Announcement']['expiration']; ?></td>
					<td><?= $a['Announcement']['active'] ? 'Yes' : 'No'; ?></td>
					<td>
						<a
							href="#"
							class="btn btn-primary btn-xs edit-btn"
							data-toggle="modal"
							data-target="#announcementModal"
							data-id="<?= $a['Announcement']['id']; ?>"
						>
							Edit
						</a>
						<a
							href="<?= $this->Html->url('/admin/site/delete/announcement/'.$a['Announcement']['id']); ?>"
							class="btn btn-danger btn-xs delete-btn"
						>
							Delete
						</a>
					</td>
				</tr>
				<?php endforeach; ?>
				<tr>
				<td colspan="4">
					<a href="#" class="btn btn-primary pull-right create-btn" data-toggle="modal" data-target="#announcementModal">
						New Announcement
					</a>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
	<div class="col-md-4">
		<h3>Config Manager</h3>
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
							data-id="<?= $c['Config']['id']; ?>"
						>
							Edit
						</a>
						<a
							href="<?= $this->Html->url('/admin/site/delete/config/'.$c['Config']['id']); ?>"
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
	</div>
</div>

<?= $this->element('Admin.config_modal'); ?>
<?= $this->element('Admin.announcement_modal'); ?>

<script>
$(document).ready(function() {
	$('.wysiwyg').wysihtml5({
		toolbar: {
			html: true,
			size: "xs",
		},
	});

	$('.datetimepicker').datetimepicker({
		sideBySide: true,
		keepInvalid: true,
	});

	$('.edit-btn').click(function() {
		target = $(this).data('target');
		type = (target == '#configModal' ? 'Config' : 'Announcement');
		endpoint = (target == '#configModal' ? 'config' : 'announcement');

		$(target+' .modal-title').html(type+' Edit');
		$(target+' form input[name=id]').val($(this).data('id'));

		$.getJSON('<?= $this->Html->url('/admin/site/api/'); ?>'+endpoint+'/'+$(this).data('id'), function(data) {
			if ( target == '#configModal' ) {
				$(target+' form input[name=key]').val(data.key);
				$(target+' form textarea[name=value]').val(data.value);
			} else {
				$(target+' form textarea[name=content]').data('wysihtml5').editor.setValue(data.content);
				$(target+' form input[name=active][value='+(data.active ? 1 : 0)+']').prop('checked', true);

				if ( data.expiration > 0 ) {
					$(target+' form input[name=expiration]').parent().data('DateTimePicker').date(moment.unix(data.expiration));
				} else {
					$(target+' form input[name=expiration]').val(data.expiration);
				}
			}
		});
	});

	$('.create-btn').click(function() {
		target = $(this).data('target');
		type = (target == '#configModal' ? 'Config' : 'Announcement');

		$(target+' .modal-title').html(type+' Creation');
		$(target+' form input[name=id]').val('0');

		if ( target == '#configModal' ) {
			$(target+' form input[name=key]').val('');
			$(target+' form textarea[name=value]').val('');
		} else {
			$(target+' form textarea[name=content]').data('wysihtml5').editor.setValue('');
			$(target+' form input[name=expiration]').val('0');
			$(target+' form input[name=active][value=0]').prop('checked', true);
		}
	});

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