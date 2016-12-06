<ul class="nav nav-pills">
	<li class="<?php echo isset($at_overview) ? 'active' : ''; ?>">
		<?php echo $this->Html->link('Team Dashboard', ['plugin' => 'ScoreEngine', 'controller' => 'team', 'action' => 'index']); ?>
	</li>

	<li class="<?php echo isset($at_config) ? 'active' : ''; ?>">
		<?php echo $this->Html->link('Scoring Engine Config', ['plugin' => 'ScoreEngine', 'controller' => 'team', 'action' => 'config']); ?>
	</li>
</ul>