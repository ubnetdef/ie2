<h2>Backend Panel - Inject Manager</h2>
<h4><?= $this->Auth->group('name'); ?></h4>

<?= $this->element('forms/admin_inject', ['inject' => $inject]); ?>