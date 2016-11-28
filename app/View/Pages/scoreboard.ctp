<?php
$services = [
	'ICMP (1)', 'ICMP (2)', 'AD', 'HTTP', 'FTP', 'IMAP'
];
$teams = [
	'Team 1', 'Team 2', 'Team 3', 'Team 4', 'Team 5', 'Team 6',
	'Team 7', 'Team 8', 'Team 9', 'Team 10', 'Team 11', 'Team 12'
];
?>
<h2>Scoreboard</h2>

<!--
<meta http-equiv="refresh" content="15">
-->

<table class="table table-bordered text-center">
	<tr>
		<td>Team</td>
		<?php foreach ( $services AS $s ): ?>
		<td><?=$s; ?></td>
		<?php endforeach; ?>
	</tr>
	<?php foreach ( $teams AS $t ): ?>
	<tr>
		<td width="15%"><?=$t?></td>
		<?php
			foreach ( $services AS $s ) {
				echo '<td class="success" width="12%"></td>';
			}
		?>
	</tr>
	<?php endforeach; ?>
</table>