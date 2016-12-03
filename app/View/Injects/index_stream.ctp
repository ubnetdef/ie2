<div class="row">
	<div class="col-md-12">
		<h2>Inject Inbox</h2>

		<div class="panel-group" id="accordion">
			<?php foreach ( $injects AS $inject ): $this->InjectStyler->setInject($inject); ?>
			<div class="panel panel-default">
				<div class="panel-heading" role="tab">
					<h4 class="panel-title">
						<a role="button" data-toggle="collapse" href="#inject-<?= $inject->getInjectId(); ?>">
							<?= $inject->getTitle(); ?>
						</a>
					</h4>
				</div>
				<div id="inject-<?= $inject->getInjectId(); ?>" class="panel-collapse collapse<?= $inject->isAcceptingSubmissions() ? ' in': ''; ?>" role="tabpanel">
					<div class="panel-body">
						<div class="row">
							<div class="col-sm-10">
								<?= $this->InjectStyler->contentOutput($inject->getContent(), $this->Auth->item()); ?>
							</div>
							<div class="col-sm-2">
								<?php if ( (bool)env('FEATURE_HINT_SUBSYSTEM') ): ?>
								<p><button class="btn btn-xs btn-info">HINTS</button></p>
								<?php endif; ?>

								<?php if ( (bool)env('FEATURE_HELP_SUBSYSTEM') ): ?>
								<p><button class="btn btn-xs btn-info">REQUEST HELP</button></p>
								<?php endif; ?>

								<?php if ( $inject->getSubmissionCount() > 0 ): ?>
								<p><button class="btn btn-xs btn-success">SUBMITTED</button></p>
								<?php endif; ?>

								<?php if ( $inject->isExpired() ): ?>
								<p><button class="btn btn-xs btn-danger">EXPIRED</button></p>
								<?php endif; ?>
							</div>
						</div>
						<hr />
						<div class="row">
							<div class="col-sm-12">
								<?= $this->InjectStyler->typeOutput($inject->getType()); ?>
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
