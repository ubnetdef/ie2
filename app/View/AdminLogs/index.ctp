<h2>Backend Panel - Log Manager</h2>

<table class="table table-bordered">
	<tr>
		<td>Who?</td>
		<td>When?</td>
		<td>Type</td>
		<td>Message</td>
		<td></td>
	</tr>
	<?php foreach ( $recent_logs AS $r ): ?>
	<tr>
		<td width="25%"><?= $r['User']['Group']['name']; ?> - <strong><?= $r['User']['username']; ?></strong></td>
		<td width="15%"><?= $this->Time->timeAgoInWords($r['Log']['time']); ?>
		<td width="10%"><?= $r['Log']['type']; ?></td>
		<td width="45%"><?= $r['Log']['message']; ?></td>
		<td width="5%"><?= $this->Html->link('View', '/admin/logs/view/'.$r['Log']['id']); ?></td>
	</tr>
	<?php endforeach; ?>
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