<h2>Backend Panel - Log Manager</h2>

<div class="well">
	<p>
		<strong>Who</strong>: <?= $log['User']['username']; ?> (<?= $log['User']['Group']['name']; ?>)<br />
		<strong>IP</strong>: <?= $log['Log']['ip']; ?><br />
		<strong>Type</strong>: <?= $log['Log']['type']; ?><br />
		<strong>What</strong>: <?= $log['Log']['message']; ?><br />
		<strong>When</strong>: <?= $this->Time->timeAgoInWords($log['Log']['time']); ?>
	</p>

	<hr />

	<pre><?= json_encode(json_decode($log['Log']['data']), JSON_PRETTY_PRINT); ?></pre>
</div>