<?php $enabledBtn = false; ?>

<div class="row">
	<p>This inject has <strong><?php echo count($hints); ?> hint<?php echo count($hints) > 1 ? 's' : ''; ?> available</strong>.</p>
	
	<table class="table table-vertalign">
		<tbody>
			<?php foreach ( $hints AS $hint ): ?>
			<tr>
				<td width="80%">
					<?php if ( false ): ?>

					<p><?php echo $hint['Hint']['content']; ?></p>

					<?php else: ?>

					<p><em>Hint not unlocked</em></p>

					<?php if ( true ): ?>
					<p>Hint will be available after <?php echo $hint['Hint']['time_wait']; ?> seconds</p>
					<?php endif; ?>

					<?php if ( $hint['Hint']['time_wait'] > 0  ): ?>
					<p>Hint will be available on <?php echo $hint['Hint']['time_wait']; ?></p>
					<?php endif; ?>

					<?php endif; ?>
				</td>

				<?php if ( true ): ?>
				
				<td><button class="btn btn-primary" disabled="disabled">Reveal</button></td>

				<?php else: ?>

				<?php if ( !$enabledBtn ): ?>

					<?php if ( $hint['Hint']['time_wait'] > 0 && $prevInjectCompleted > 0 && $hint['Hint']['time_wait']+$prevInjectCompleted > time() ): ?>

					<td>
						<button class="btn btn-primary hint-disabled-countdown" data-until="<?php echo $hint['Hint']['time_wait']+$prevInjectCompleted; ?>" disabled="disabled">
							Please wait <?php echo $hint['Hint']['time_wait']; ?> seconds
						</button>
					</td>

					<?php elseif ( $hint['Hint']['time_available'] > 0 && $hint['Hint']['time_available'] > time() ): ?>

					<td>
						<button class="btn btn-primary hint-disabled-countdown" data-until="<?php echo $hint['Hint']['time_available']; ?>" disabled="disabled">
							Please wait <?php echo $hint['Hint']['time_available']-time(); ?> seconds
						</button>
					</td>

					<?php else: ?>

					<td><button class="btn btn-primary hint-btn">Reveal</button></td>

					<?php endif; ?>

				<?php else: ?>

				<td><button class="btn btn-primary" disabled="disabled">Reveal</button></td>

				<?php endif; ?>

				<?php $enabledBtn = true; endif; ?>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>