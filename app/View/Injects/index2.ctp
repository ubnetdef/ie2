<div class="alert alert-info alert-dismissible" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<p><strong>FYI!</strong></p>
	Did you sleep last night? You look tired.
</div>

<div class="row">
	<div class="col-md-12">
		<h2>Inject Inbox</h2>

		<div class="panel-group" id="accordion">
			<?php foreach ( $injects AS $inject ): ?>
			<div class="panel panel-default">
				<div class="panel-heading" role="tab">
					<h4 class="panel-title">
						<a role="button" data-toggle="collapse" href="#inject-<?= $inject->getInjectID(); ?>">
							<?= $inject->getTitle(); ?>
						</a>
					</h4>
				</div>
				<div id="inject-<?= $inject->getInjectID(); ?>" class="panel-collapse collapse in" role="tabpanel">
					<div class="panel-body">
						<div class="row">
							<div class="col-sm-10">
								<?= $inject->getContent(); ?>
							</div>
							<div class="col-sm-2">
								<?php if ( $inject->getSubmissionCount() > 0 ): ?>
								<p><button class="btn btn-xs btn-success">SUBMITTED</button></p>
								<?php endif; ?>

								<?php if ( $inject->isExpired() ): ?>
								<p><button class="btn btn-xs btn-danger">EXPIRED</button></p>
								<?php endif; ?>

								<p><button class="btn btn-xs btn-info">HINTS (4)</button></p>
								<p><button class="btn btn-xs btn-info">REQUEST HELP</button></p>
							</div>
						</div>
						<hr />
						<div class="row">
							<div class="col-sm-12">
								<p>submission would go here. maybe a file selection. or a WYSIWYG editor. crazy.</p>
							</div>
						</div>
					</div>
					<div class="panel-footer">
						<strong>Start</strong>: <?= $inject->getStartString(); ?><br />
						<strong>End</strong>: <?= $inject->getEndString(); ?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
		</div>
	</div>
</div>
