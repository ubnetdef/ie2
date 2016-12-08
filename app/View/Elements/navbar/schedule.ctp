<ul class="nav nav-pills">
	<li class="<?= isset($at_overview) ? 'active' : ''; ?>"><a href="<?= $this->Html->url('/schedule'); ?>">Overview</a></li>
	<li class="<?= isset($at_manager) ? 'active' : ''; ?>"><a href="<?= $this->Html->url('/schedule/manager'); ?>">Manager</a></li>
</ul>

<hr />