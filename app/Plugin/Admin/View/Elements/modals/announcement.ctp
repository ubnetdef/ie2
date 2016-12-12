<?php
$this->Html->css('/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min', ['inline' => false]);
$this->Html->css('/vendor/bootstrap3-wysiwyg/bootstrap3-wysihtml5.min', ['inline' => false]);

$this->Html->script('/vendor/moment.min', ['inline' => false]);
$this->Html->script('/vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min', ['inline' => false]);
$this->Html->script('/vendor/bootstrap3-wysiwyg/bootstrap3-wysihtml5.all.min', ['inline' => false]);
?>

<div class="modal fade" id="announcementModal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="post" action="<?= $this->Html->url('/admin/site/announcement'); ?>" class="form-horizontal">
				<input type="hidden" name="id" value="" />

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="content" class="col-sm-2 control-label">Content</label>
						<div class="col-sm-10">
							<textarea class="form-control wysiwyg" rows="10" id="content" name="content"></textarea>
						</div>
					</div>

					<div class="form-group">
						<label for="expiration" class="col-sm-2 control-label">Expiration</label>
						<div class="col-sm-10">
							<div class="input-group date datetimepicker" id="expires_datepicker">
								<input type="text" class="form-control time-use-data" id="expiration" name="expiration" value="" />
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label for="active" class="col-sm-2 control-label">Active</label>
						<div class="col-sm-8">
							<div class="radio">
								<label>
									<input type="radio" name="active" value="1"> Yes
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" name="active" value="0"> No
								</label>
							</div>
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