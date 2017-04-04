<?php
// Handlebars
$this->Html->script('/vendor/handlebars', array('inline' => false));

// Gantt Chart Lib
$this->Html->script('/vendor/dhtmlxgantt/dhtmlxgantt', ['inline' => false]);
$this->Html->css('/vendor/dhtmlxgantt/dhtmlxgantt', ['inline' => false]);

// Custom JS
$this->Html->script('schedule', ['inline' => false]);

// Javascript
$this->Html->scriptStart(['inline' => false, 'safe' => false]);
echo 'window.START = '.($start * 1000).';';
echo 'window.END = '.($end * 1000).';';
echo 'window.SCHEDULE = "'.$this->Html->url('/schedule/api').'";';
$this->Html->scriptEnd();
?>

<h2>
	Schedule Overview
	<a href="#" class="btn btn-success pull-right unlock-chart" data-locked="true">Edit Mode</a>
</h2>

<div class="row">
	<div class="col-md-12">
		<div id="master_schedule"></div>
	</div>
</div>

<div class="row hidden change-table">
	<div class="col-md-12">
		<h3>Changes</h3>
		<table class="table" id="schedule_changes">
			<thead>
				<tr>
					<td>Inject</td>
					<td>Start Time</td>
					<td>End Time</td>
					<td></td>
				</tr>
			</thead>
			<tbody></tbody>
			<tfoot>
				<tr>
					<td colspan="4">
						<a href="#" class="btn btn-default pull-right save">Save</a>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

<script id="change-tpl" type="text/x-handlebars-template">
<tr data-id="{{ id }}" data-start="{{ start_ts }}" data-end="{{ end_ts }}">
	<td>{{ id }}. {{ text }}</td>
	<td class="start">{{ start_date }}</td>
	<td class="end">{{ end_date }}</td>
	<td><a href="#" class="rem"><i class="glyphicon glyphicon-remove"></i></a></td>
</tr>
</script>