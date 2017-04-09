<div class="row">
	<p>This inject has <strong><?php echo count($hints); ?> hint<?php echo count($hints) > 1 ? 's' : ''; ?> available</strong>.</p>
	
	<table class="table table-vertalign">
		<tbody>
			<?php foreach ( $hints AS $hint ): ?>
			<tr>
				<td width="80%" colspan="<?= $hint['Hint']['unlocked'] ? '2' : '1'; ?>">
					<?php if ( $hint['Hint']['unlocked'] ): ?>

					<p><?php echo $hint['Hint']['content']; ?></p>

					<?php else: ?>

					<p><em>Hint not unlocked</em></p>

					<?php if ( $hint['Hint']['time_wait'] > 0 && $inject->getStart()+$hint['Hint']['time_wait'] > time() ): ?>
					<p>Hint will be available after <?php echo $hint['Hint']['time_wait']; ?> seconds</p>
					<?php endif; ?>

					<?php endif; ?>
				</td>

				<?php if ( !$hint['Hint']['unlocked'] ): ?>
				<?php if ( $hint['Hint']['dependency_met'] ): ?>
					<?php if ( $inject->getStart()+$hint['Hint']['time_wait'] <= time() ): ?>
					
					<td>
						<button class="btn btn-primary unlock_hint" data-hint="<?= $hint['Hint']['id']; ?>">
							Reveal for <?= $hint['Hint']['cost']; ?> points
						</button>
					</td>
					
					<?php else: ?>
					
					<td><button class="btn btn-primary" disabled="disabled">Reveal for <?= $hint['Hint']['cost']; ?> points</button></td>
					
					<?php endif; ?>
				<?php else: ?>

				<td><button class="btn btn-primary" disabled="disabled">Requires previous hint</button></td>

				<?php endif; ?>
				<?php endif; ?>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>