<ol class="breadcrumb">
	<li><a href="<?= $this->Html->url(['plugin' => 'admin', 'controller' => 'users', 'action' => 'index']); ?>">User Manager</a></li>
	<li class="active">Edit User</li>
</ol>

<h2>Backend Panel - User Manager</h2>

<?= $this->element('Admin.user', ['user' => $user, 'groups' => $groups]); ?>