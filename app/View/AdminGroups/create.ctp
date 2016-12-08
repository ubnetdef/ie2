<h2>Backend Panel - Group Manager</h2>
<h4><?= $this->Auth->group('name'); ?></h4>

<?= $this->element('forms/admin_group', ['group' => [], 'groups' => $groups]); ?>