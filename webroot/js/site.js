$(document).ready(function() {
	// Bind closing of an announcement
	$('.alert-announcement').on('closed.bs.alert', function () {
		$aid = $(this).data('aid');

		$.get(window.BASE+"/pages/announcement_read/"+$aid);
	});
});