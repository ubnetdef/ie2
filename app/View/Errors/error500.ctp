<h2>Uh Oh!</h2>

<div class="well">
	<p>We apologize, however an error has occured.  Please grab the nearest staff member.</p>

	<hr />

	<p><strong>Name</strong>: <?= $name; ?></p>
	<p><strong>Message</strong>: <?= $error->getMessage(); ?></p>
</div>

<?php
if (Configure::read('debug') > 0):
	echo $this->element('exception_stack_trace');
endif;
?>
