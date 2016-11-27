<?php
//echo $this->Html->script('injectengine', array('inline' => false));
//$this->Inject->setup($injects);

$map = [
	1 => ['class' => 'btn-danger', 'text' => 'EXPIRED'],
	2 => ['class' => 'btn-success', 'text' => 'COMPLETED'],
	3 => ['class' =>'btn-info', 'text' => 'ACTIVE'],
]
?>

<div class="alert alert-info alert-dismissible" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<p><strong>FYI!</strong></p>
	Did you sleep last night? You look tired.
</div>

<div class="row">
	<div class="col-md-12">
		<h2>Inject Inbox</h2>

		<ul class="nav nav-tabs">
			<li class="active"><a href="#active_injects" data-toggle="tab">ACTIVE</a></li>
			<li class=""><a href="#all_injects" data-toggle="tab">ALL</a></li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane fade" id="all_injects">
				<div class="list-group">
					<?php foreach ( $injects AS $inject ): ?>
					<a href="<?= $this->Html->url('/injects/view/'.$inject->getScheduleID()); ?>" class="list-group-item">
						<span class="btn <?= $map[$inject->getInjectID()]['class']; ?> pull-right"><?= $map[$inject->getInjectID()]['text']; ?></span>
						<h4 class="list-group-item-heading"><?= $inject->getTitle(); ?></h4>
						<p class="text-muted">
							Start: <?= $inject->getStartString(); ?><br />
							End: <?= $inject->getEndString(); ?>
						</p>
					</a>
					<?php endforeach; ?>
					<?php foreach ( $injects AS $inject ): ?>
					<a href="<?= $this->Html->url('/injects/view/'.$inject->getScheduleID()); ?>" class="list-group-item">
						<span class="btn btn-info pull-right">ACTIVE</span>
						<h4 class="list-group-item-heading"><?= $inject->getTitle(); ?></h4>
						<p class="text-muted">
							Start: <?= $inject->getStartString(); ?><br />
							End: <?= $inject->getEndString(); ?>
						</p>
					</a>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="tab-pane fade in active" id="active_injects">
				<div class="list-group">
					<?php foreach ( $injects AS $inject ): ?>
					<a href="<?= $this->Html->url('/injects/view/'.$inject->getScheduleID()); ?>" class="list-group-item">
						<span class="btn btn-info pull-right">ACTIVE</span>
						<h4 class="list-group-item-heading"><?= $inject->getTitle(); ?></h4>
						<p class="text-muted">
							Start: <?= $inject->getStartString(); ?><br />
							End: <?= $inject->getEndString(); ?>
						</p>
					</a>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</div>
