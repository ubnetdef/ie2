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