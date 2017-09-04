<?php
// Summernote
$this->Html->css('/vendor/summernote/summernote', ['inline' => false]);
$this->Html->script('/vendor/summernote/summernote.min', ['inline' => false]);
$this->Html->script('/vendor/summernote-cleaner/summernote-cleaner', ['inline' => false]);
$this->Html->script('/js/summernote.config', ['inline' => false]);

// Datepicker
$this->Html->script('/vendor/moment.min', ['inline' => false]);
$this->Html->script('/vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min', ['inline' => false]);
$this->Html->css('/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min', ['inline' => false]);
?>

<h2>Backend Panel - Site Manager</h2>

<div class="row">
	<div class="col-md-8">
		<h3>Announcement Manager</h3>
		<table class="table">
			<thead>
				<tr>
					<td>Announcement</td>
					<td>Expiration</td>
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

<div class="row">
	<div class="col-md-12">
		<h3>Competition Start Manager</h3>

		<form method="post" action="<?= $this->Html->url('/admin/site/config'); ?>" class="form-horizontal">
			<?php foreach ( $config AS $c ): if ( $c['Config']['key'] != 'competition.start' ) continue; ?>
			<input type="hidden" name="id" value="<?= $c['Config']['id']; ?>" />
			<input type="hidden" name="key" value="<?= $c['Config']['key']; ?>" />
			<?php endforeach; ?>			

			<div class="form-group">
				<label for="value" class="col-sm-2 control-label">Competition Start</label>
				<div class="col-sm-10">
					<div class="input-group date datetimepicker" id="start_datepicker">
						<input type="text" class="form-control time-use-data" id="value" name="value" required="required" />
						<span class="input-group-addon">
							<span class="glyphicon glyphicon-calendar"></span>
						</span>
					</div>
				</div>
			</div>

			<input type="submit" class="btn btn-primary" />
		</form>
	</div>
</div>

<?= $this->element('Admin.modals/config'); ?>
<?= $this->element('Admin.modals/announcement'); ?>

<script>
$(document).ready(function() {
	$('.wysiwyg').summernote({
		height: 200,
		cleaner: window.SUMMERNOTE_CLEANER_CONFIG,
	});

	$('.datetimepicker').datetimepicker({
		sideBySide: true,
		keepInvalid: true,
	});

	<?php foreach ( $config AS $c ): if ( $c['Config']['key'] != 'competition.start' ) continue; ?>
	$('#start_datepicker').data('DateTimePicker').date(moment.unix(<?= $c['Config']['value']; ?>));
	<?php endforeach; ?>

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
				$(target+' #content_editor').summernote('code', data.content);
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
			$(target+' #content_editor').summernote('code', '');
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

		$('#content').val($('#content_editor').summernote('code'));
	});
});
</script>
