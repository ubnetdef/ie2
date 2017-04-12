<ol class="breadcrumb">
	<li><a href="<?= $this->Html->url(['plugin' => 'admin', 'controller' => 'hints', 'action' => 'index']); ?>">Hint Manager</a></li>
	<li class="active">Create Hint</li>
</ol>

<h2>Backend Panel - Hint Manager</h2>

<?= $this->element('Admin.hint', ['hint' => [], 'injects' => $injects, 'hints' => $hints]); ?>