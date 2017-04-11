<?php
$this->Html->scriptStart(['inline' => false, 'safe' => false]);
echo 'setTimeout(window.location.reload.bind(window.location), 30 * 1000);';
$this->Html->scriptEnd();
?>

<h2>ScoreBoard</h2>
<h4>Round #<?= $round; ?></h4>

<?= $this->EngineOutputter->generateScoreBoard(); ?>
