<div class="modal fade" id="bankModal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="post" action="<?= $this->Html->url(['plugin' => 'BankWeb', 'controller' => 'bankadmin', 'action' => 'save']); ?>" class="form-horizontal">
				<input type="hidden" name="id" value="" />
				<input type="hidden" name="table" value="account" />

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="value" class="col-sm-2 control-label">Group</label>
						<div class="col-sm-10">
							<select class="form-control" id="group_id" name="group_id">
								<?php foreach ( $groups AS $id => $g ): ?>
								<option value="<?= $id; ?>">
									<?= $g; ?>
								</option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label for="username" class="col-sm-2 control-label">Username</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="username" name="username" placeholder="john.doe">
						</div>
					</div>

					<div class="form-group">
						<label for="password" class="col-sm-2 control-label">Password</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="password" name="password" placeholder="not.so.secret">
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