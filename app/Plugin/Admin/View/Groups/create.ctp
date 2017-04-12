<ol class="breadcrumb">
	<li><a href="<?= $this->Html->url(['plugin' => 'admin', 'controller' => 'groups', 'action' => 'index']); ?>">Group Manager</a></li>
	<li class="active">Create Group</li>
</ol>

<h2>Backend Panel - Group Manager</h2>

<?= $this->element('Admin.group', ['group' => [], 'groups' => $groups]); ?>