<div class="modal fade" id="productModal" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form method="post" action="<?= $this->Html->url(['plugin' => 'BankWeb', 'controller' => 'bankadmin', 'action' => 'saveProduct']); ?>" class="form-horizontal">
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
							<input type="text" class="form-control" id="name" name="name" placeholder="Some Awesome Product">
						</div>
					</div>

					<div class="form-group">
						<label for="description" class="col-sm-2 control-label">Description</label>
						<div class="col-sm-10">
							<textarea class="form-control" rows="5" id="description" name="description"></textarea>
						</div>
					</div>

					<div class="form-group">
						<label for="enabled" class="col-sm-2 control-label">Enabled</label>
						<div class="col-sm-10">
							<div class="radio">
								<label>
									<input type="radio" name="enabled" id="enabledYes" value="1" required="required">
									Yes
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" name="enabled" id="enabledNo" value="0" required="required">
									No
								</label>
							</div>
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

					<hr />

					<div class="form-group">
						<label for="user_input" class="col-sm-2 control-label">User Input</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="user_input" name="user_input">
							<span class="help-block">
								This item is <strong>optional</strong>.
							</span>
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
							<span class="help-block">
								This item is <strong>optional</strong>.<br />
								You can use the following variables: <code>#USERNAME#</code>, <code>#GROUP#</code>, <code>#INPUT#</code>.
								They will be automatically replaced with the correct values upon purchase.<br />
								If you are trying to "tag" a user in slack, format it like this: <code><@james></code>
							</span>
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