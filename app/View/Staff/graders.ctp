<?php
$this->Html->scriptStart(['inline' => false, 'safe' => false]);
echo 'setInterval(window.location.reload.bind(window.location), 30 * 1000);';
$this->Html->scriptEnd();
?>

<h2>Grader Island</h2>

<h3>Ungraded Injects</h3>
<table class="table table-bordered">
	<tr>
		<td>Team</td>
		<td>Submitted</td>
		<td>Inject</td>
		<td>Type</td>
		<td></td>
	</tr>
	
	<?php foreach ( $ungraded AS $sub ): ?>
	<tr>
		<td width="15%"><?= $sub['Group']['name']; ?></td>
		<td width="20%"><?= $this->Time->timeAgoInWords($sub['Submission']['created']); ?></td>
		<td width="40%"><?= $sub['Inject']['title']; ?></td>
		<td width="15%"><?= $this->InjectStyler->getName($sub['Inject']['type']); ?></td>
		<td width="10%"><a href="<?= $this->Html->url('/staff/grade/'.$sub['Submission']['id']); ?>" class="btn btn-info">Grade</a></td>
	</tr>
	<?php endforeach; ?>

	<?php if ( empty($ungraded) ): ?>
	<tr>
		<td colspan="5">There are no ungraded injects.  Good job!</td>
	</tr>
	<?php endif; ?>
</table>

<h3>Graded Injects</h3>
<table class="table table-bordered">
	<tr>
		<td>Team</td>
		<td>Grader</td>
		<td>Graded</td>
		<td>Inject</td>
		<td>Type</td>
		<td></td>
	</tr>
	
	<?php foreach ( $graded AS $sub ): ?>
	<tr>
		<td width="15%"><?= $sub['Group']['name']; ?></td>
		<td width="10%"><?= empty($sub['Grader']['username']) ? 'N/A' : $sub['Grader']['username']; ?></td>
		<td width="20%"><?= $sub['Submission']['deleted'] ? '<strong>Submission Deleted</strong>' : $this->Time->timeAgoInWords($sub['Grade']['created']); ?></td>
		<td width="30%"><?= $sub['Inject']['title']; ?></td>
		<td width="15%"><?= $this->InjectStyler->getName($sub['Inject']['type']); ?></td>
		<td width="10%"><a href="<?= $this->Html->url('/staff/grade/'.$sub['Submission']['id']); ?>" class="btn btn-default">View</a></td>
	</tr>
	<?php endforeach; ?>

	<?php if ( empty($graded) ): ?>
	<tr>
		<td colspan="6">There are no graded injects.  Get working!</td>
	</tr>
	<?php endif; ?>
</table>

<?php
	echo $this->Paginator->counter([
		'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	]);
?>
</p>
<ul class="pagination">
	<?php
		echo $this->Paginator->first(
			'&laquo;',
			[
				'tag' => 'li',
				'escape' => false
			]
		);
		echo $this->Paginator->prev(
			'<',
			[
				'tag' => 'li',
				'escape' => false
			],
			'<a href="#"><</a>',
			[
				'class' => 'prev disabled',
				'tag' => 'li',
				'escape' => false
			]
		);
		echo $this->Paginator->numbers([
			'separator' => '',
			'tag' => 'li',
			'currentLink' => true,
			'currentClass' => 'active',
			'currentTag' => 'a'
		]);

		echo $this->Paginator->next(
			'>',
			[
				'tag' => 'li',
				'escape' => false
			],
			'<a href="#">></a>',
			[
				'class' => 'prev disabled',
				'tag' => 'li',
				'escape' => false
			]
		);
		echo $this->Paginator->last(
			'&raquo;',
			[
				'tag' => 'li',
				'escape' => false
			]
		);
	?>
</ul>