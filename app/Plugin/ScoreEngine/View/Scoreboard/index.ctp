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
	updateScoreboard();
	setInterval(updateScoreboard, 30 * 1000);
});
<?php
$this->Html->scriptEnd();
?>

<h2>Scoreboard</h2>
<h4>Round #<span class="scoreboard-round"></span></h4>

<div class="scoreboard-content">Loading...</div>

<?php if ( !empty($sponsors) ): $first = true; ?>
<h3 class="sponsor-header">Our Sponsors</h3>
<div class="carousel slide" data-ride="carousel" data-interval="5000">
	<div class="carousel-inner" role="listbox">
		<?php foreach ( $sponsors AS $s ): ?>
		<div class="item<?= $first ? ' active' : ''; ?>">
			<img src="<?= $s; ?>" />
		</div>
		<?php $first && ($first = false); endforeach; ?>
	</div>
</div>
<?php endif; ?>