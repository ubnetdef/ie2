<ol class="breadcrumb">
	<li><a href="<?= $this->Html->url(['plugin' => 'admin', 'controller' => 'injects', 'action' => 'index']); ?>">Inject Manager</a></li>
	<li class="active">Edit Inject</li>
</ol>

<h2>Backend Panel - Inject Manager</h2>

<?= $this->element('Admin.inject', ['inject' => $inject]); ?>