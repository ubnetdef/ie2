<?php
// Handlebars
$this->Html->script('/vendor/handlebars', array('inline' => false));

// Gantt Chart Lib
$this->Html->script('/vendor/dhtmlxgantt/dhtmlxgantt', ['inline' => false]);
$this->Html->css('/vendor/dhtmlxgantt/dhtmlxgantt', ['inline' => false]);

// Sue me
$min_date = 0;
$max_date = 0;
foreach ( $injects AS $i ) {
	$start = $i->getStart();
	$end   = $i->getEnd();

	if ( $start > 0 && $start > $min_date ) {
		$min_date = $start;
	}
	if ( $end > 0 && $end > $max_date ) {
		$max_date = $end;
	}
}

$min = DateTime::createFromFormat('Y-m-d H:00:00', date('Y-m-d H:00:00', $min_date));
$max = DateTime::createFromFormat('Y-m-d H:00:00', date('Y-m-d H:00:00', $max_date));

$min->modify('-1 hour');
$max->modify('+1 hour');
?>
<ul class="nav nav-pills">
	<li class="active"><a href="<?= $this->Html->url('/schedule'); ?>">Overview</a></li>
	<li class=""><a href="<?= $this->Html->url('/schedule/manager'); ?>">Manager</a></li>
</ul>

<hr />

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
		</table>
	</div>
</div>

<script id="change-tpl" type="text/x-handlebars-template">
<tr data-id="{{ id }}">
	<td>{{ id }}. {{ text }}</td>
	<td class="start">{{ start_date }}</td>
	<td class="end">{{ end_date }}</td>
	<td><i class="glyphicon glyphicon-remove"></i></td>
</tr>
</script>

<script>
function setupTemplate() {
	// Setup the template
	src = $("#change-tpl").html();
	return Handlebars.compile(src);
}

function setupChart(tpl) {
	// Setup our scale and default steps between time periods
	gantt.config.date_scale = '%h:%i %A';
	gantt.config.scale_unit = 'minute';
	gantt.config.step = 15; // We want the scale to show in 15 minute increments

	// Setup our duration for our schedule
	gantt.config.duration_unit = 'minute';
	gantt.config.duration_step = 1; // Every 'duration' maps to one minute
	gantt.config.min_duration = (60 * 1000); // 1 Minute
	gantt.config.time_step = 1;

	// Disable rounding
	gantt.config.round_dnd_dates = false;

	// Disable the popup
	gantt.config.details_on_dblclick = false;

	// Disable making links + progress
	gantt.config.drag_links = false;
	gantt.config.drag_progress = false;

	// UTC
	gantt.config.server_utc = true;

	// Disable initial scroll
	gantt.config.initial_scroll = false;

	// Autosize me up!
	gantt.config.autosize = 'y';

	// We're read only
	gantt.config.readonly = true;

	// Overwrite the columns
	gantt.config.columns = [
		{name: 'text', label: 'Inject', width: '*', tree: true},
		// {name: 'text', label: 'Group', template: function(obj) {
		// 	return obj.group;
		// }},
		{name: 'start_date', label: 'Start Time', align: 'center', template: function(obj) {
			if ( obj.start_ts == 0 ) {
				return 'Immediately';
			}

			hours = obj.start_date.getHours();
			minutes = obj.start_date.getMinutes();
			amPM = 'AM';

			if ( hours > 12 ) {
				hours -= 12;
				amPM = 'PM';
			}

			if ( minutes < 10 ) {
				minutes = '0'+minutes;
			}

			return hours+':'+minutes+' '+amPM;
		}},
		{name: 'duration', label: 'Duration', width: '*', align: 'center', template: function(obj) {
			return (obj.end_ts > 0 ? obj.duration+' Minutes' : 'Forever');
		}},
	];

	// Set our bounds. This is needed, otherwise we
	// will get a *super* ugly graph
	gantt.config.start_date = new Date(<?= $min->getTimestamp() * 1000; ?>);
	gantt.config.end_date   = new Date(<?= $max->getTimestamp() * 1000; ?>);

	gantt.init('master_schedule');
	gantt.parse({
		data: [
			<?php foreach ( $injects AS $i ): ?>
			{
				id: <?= $i->getScheduleId(); ?>,
				inject_id: <?= $i->getInjectId(); ?>,
				text: '<?= $i->getTitle(); ?> (<?= $i->getGroupName(); ?>)',
				group: '<?= $i->getGroupName(); ?>',

				start_date: '<?= date('d-m-Y G:i:s', $i->getStart() > 0 ? $i->getStart() : $min->getTimestamp()); ?>',
				start_ts: <?= $i->getStart(); ?>,

				end_date: '<?= date('d-m-Y G:i:s', $i->getEnd() > 0 ? $i->getEnd() : $max->getTimestamp()); ?>',
				end_ts: <?= $i->getEnd(); ?>,
			},
			<?php endforeach; ?>
		]
	});

	gantt.attachEvent('onAfterTaskUpdate', function(id, $item) {
		if ( $('tr[data-id='+$item.id+']').length == 0 ) {
			$('#schedule_changes tbody').append(tpl($item));
		} else {
			$el = $('tr[data-id='+$item.id+']');

			$el.children('.start').html($item.start_date);
			$el.children('.end').html($item.end_date);
		}
	});
}

$(document).ready(function() {
	tpl = setupTemplate();
	setupChart(tpl);

	$('.unlock-chart').click(function() {
		if ( $(this).data('locked') ) {
			gantt.config.readonly = false;
			gantt.render();

			$('.change-table').removeClass('hidden');
			$(this).html('Exit').data('locked', false).removeClass('btn-success').addClass('btn-danger');
		} else {
			gantt.config.readonly = true;
			gantt.render();
			
			$('.change-table').addClass('hidden');
			$(this).html('Edit Mode').data('locked', true).removeClass('btn-danger').addClass('btn-success');
		}
	})
});
</script>