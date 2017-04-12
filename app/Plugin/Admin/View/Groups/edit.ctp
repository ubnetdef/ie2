<ol class="breadcrumb">
	<li><a href="<?= $this->Html->url(['plugin' => 'admin', 'controller' => 'groups', 'action' => 'index']); ?>">Group Manager</a></li>
	<li class="active">Edit Group</li>
</ol>

<h2>Backend Panel - Group Manager</h2>

<?= $this->element('Admin.group', ['group' => $group, 'groups' => $groups]); ?>