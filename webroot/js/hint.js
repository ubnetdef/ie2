var Hint = Hint || {};

Hint = {
	_updateInterval: null,

	init: function() {
		console.log('Hint-JS: Init');

		// Setup our binds
		$('.hint_modal').on('show.bs.modal', Hint.onModalShow);
		$('.hint_modal').on('hide.bs.modal', Hint.onModalClose);

		$(document).on('click', '.unlock_hint', function() {
			hint_id = $(this).data('hint');
			$.get(window.BASE+'injects/unlock/'+window.INJECT+'/'+hint_id).done(function() {
				$('.hint_modal').modal('show');
			});
		});
	},

	onModalShow: function(event) {
		modal = $(this);

		$.get(window.BASE+'injects/hints/'+window.INJECT).done(function(data) {
			modal.find('.modal-body').html(data);

			Hint.countdown();
			Hint._updateInterval = setInterval(Hint.countdown, 1000);
		});
	},

	onModalClose: function(event) {
		clearTimeout(Hint._updateInterval);
	},

	countdown: function() {
		$('.countdown').each(function() {
			el = $(this);

			now   = Math.floor(Date.now() / 1000);
			until = el.data('until');

			if ( now >= until ) {
				$('.hint_modal').modal('show');
			} else {
				el.text('Please wait '+(until-now)+' seconds');
			}
		});
	},
};
