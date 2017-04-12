<?php
$this->Html->scriptStart(['inline' => false, 'safe' => false]);
?>
function updateScoreboard() {
	$.getJSON(window.BASE+"scoreboard/api").done(function(data) {
		$('.scoreboard-round').html(data.round);
		$('.scoreboard-content').html(data.content);
	});
}

$(document).ready(function() {
	$('.footer .container').append('<p><button class="btn btn-sm btn-primary hide_ui">Hide UI</button></p>');

	$('.hide_ui').click(function() {
		$('.navbar').hide();
		$('.footer').hide();
	});

	updateScoreboard();
	setTimeout(updateScoreboard, 30 * 1000);
});
<?php
$this->Html->scriptEnd();
?>

<h2>Scoreboard</h2>
<h4>Round #<span class="scoreboard-round"></span></h4>

<div class="scoreboard-content">Loading...</div>
