<?php
$this->Html->css('BankWeb.products', ['inline' => false]);

$count = 0;
?>

<div class="container">
	<div class="jumbotron">
		<h2>Welcome to <?= env('COMPETITION_NAME'); ?>'s Bank!</h2>
		<p>We take pride in providing the best service to our clients.</p>
	</div>

	<div class="row">
	<?php foreach ( $products AS $product ): ?>
		<div class="col-md-3">
			<div class="db-wrapper">
				<div class="db-pricing-seven">
					<ul>
						<li class="price">
							<i class="glyphicon glyphicon-console"></i>
							<?= $product['Product']['name']; ?>
						</li>

						<li><?= $product['Product']['description']; ?></li>
						<li><strong>PRICE</strong>: <?= money_format('%.2n', $product['Product']['cost']); ?></li>
					</ul>

					<div class="pricing-footer">
						<a href="<?= $this->Html->url(['plugin' => 'BankWeb', 'controller' => 'products', 'action' => 'confirm', $product['Product']['id']]); ?>" class="btn btn-default btn-lg">
							BUY <i class="glyphicon glyphicon-play-circle"></i>
						</a>
					</div>
				</div>
			</div>
		</div>

	<?php if ( $count++ % 4 == 3 ): ?>
	</div>
	<div class="row">
	<?php endif; ?>
	<?php endforeach; ?>
	</div>
</div>