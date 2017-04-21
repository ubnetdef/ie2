<div class="modal fade" id="productModal" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form method="post" action="<?= $this->Html->url(['plugin' => 'BankWeb', 'controller' => 'bankadmin', 'action' => 'save']); ?>" class="form-horizontal">
				<input type="hidden" name="id" value="" />
				<input type="hidden" name="table" value="product" />

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="username" class="col-sm-2 control-label">Name</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="name" name="name" placeholder="john.doe">
						</div>
					</div>

					<div class="form-group">
						<label for="description" class="col-sm-2 control-label">Description</label>
						<div class="col-sm-10">
							<textarea class="form-control" rows="5" id="description" name="description"></textarea>
						</div>
					</div>

					<div class="form-group">
						<label for="cost" class="col-sm-2 control-label">Cost</label>
						<div class="col-sm-10">
							<div class="input-group">
								<div class="input-group-addon">$</div>
								<input type="text" class="form-control" id="cost" name="cost" placeholder="1337">
							</div>
						</div>
					</div>

					<div class="form-group">
						<label for="user_input" class="col-sm-2 control-label">User Input</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="user_input" name="user_input" placeholder="">
						</div>
					</div>

					<div class="form-group">
						<label for="message_user" class="col-sm-2 control-label">User Message</label>
						<div class="col-sm-10">
							<textarea class="form-control" rows="5" id="message_user" name="message_user"></textarea>
						</div>
					</div>

					<div class="form-group">
						<label for="message_slack" class="col-sm-2 control-label">Slack Message</label>
						<div class="col-sm-10">
							<textarea class="form-control" rows="5" id="message_slack" name="message_slack"></textarea>
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