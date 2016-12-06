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
	gantt.config.start_date = new Date(window.START);
	gantt.config.end_date   = new Date(window.END);

	gantt.init('master_schedule');
	gantt.load(window.SCHEDULE);

	gantt.attachEvent('onAfterTaskUpdate', function(id, $item) {
		$item.start_ts = ($item.start_date.getTime() / 1000);
		$item.end_ts = ($item.end_date.getTime() / 1000);

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
	});

	$(document).on('click', '.rem', function(e) {
		e.preventDefault();

		$(this).parent().parent().remove();
	});

	$('.save').click(function(e) {
		e.preventDefault();
		$changes = [];

		$('#schedule_changes tbody tr').each(function() {
			$iid = $(this).data('id');
			$start = $(this).data('start');
			$end = $(this).data('end');

			$changes.push({
				id: $iid,
				start: $start,
				end: $end,
			});
		});

		$
			.post(window.SCHEDULE, {changes: $changes})
			.done(function() {
				$('#schedule_changes tbody tr').remove();
			});
	});
});