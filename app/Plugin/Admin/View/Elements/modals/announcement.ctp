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
						<label for="content_editor" class="col-sm-2 control-label">Content</label>
						<div class="col-sm-10">
							<input type="hidden" name="content" id="content" />
							<div id="content_editor" class="wysiwyg"></div>
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