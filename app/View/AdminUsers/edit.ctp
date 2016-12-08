<h2>Backend Panel - User Manager</h2>
<h4><?= $this->Auth->group('name'); ?></h4>

<?= $this->element('forms/admin_user', ['user' => $user, 'groups' => $groups]); ?>