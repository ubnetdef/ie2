<?php
$this->Html->scriptStart(['inline' => false, 'safe' => false]);
?>
function loadCompetitionCentral() {
	$.get(window.BASE+"staff/api").done(function(content) {
		$('#competition-central').html(content);
	});
}

loadCompetitionCentral();
setInterval(loadCompetitionCentral, 30 * 1000);
<?php
$this->Html->scriptEnd();
?>
<h2>Competition Central</h2>

<div id="competition-central">Loading...</div>