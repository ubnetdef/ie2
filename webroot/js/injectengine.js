var InjectEngine = InjectEngine || {};

InjectEngine = {
	_updateInterval: null,

	_tpl: null,
	_tplEmpty: null,

	init: function() {
		console.log('InjectEngine-JS: Init');

		// Setup the template
		src = $("#inject-list-tpl").html();
		this._tpl = Handlebars.compile(src);

		// Setup the template
		src = $("#inject-list-empty-tpl").html();
		this._tplEmpty = Handlebars.compile(src);

		// Auto update every 5 seconds
		this.update();
		this._updateInterval = setInterval(this.update.bind(this), 5000);
	},

	update: function() {
		injectURL = window.BASE+"injects";
		tpl = this._tpl;
		tplEmpty = this._tplEmpty;

		$.getJSON(injectURL+"/api", function(data) {
			// Clear out everything
			$('#all_injects > div').children().remove();
			$('#active_injects > div').children().remove();

			// Redraw the UI
			data.injects.forEach(function($d) {
				// Add the URL to the API response data
				$d.injectURL = injectURL;

				// Add to all the injects
				$('#all_injects > div').append(tpl($d));

				// If it's active, add it to the other tab
				if ( $d.expired == false ) {
					$('#active_injects > div').append(tpl($d));
				}
			});

			if ( $('#active_injects > div').children().length == 0 ) {
				$('#active_injects > div').append(tplEmpty());
			}

			if ( $('#all_injects > div').children().length == 0 ) {
				$('#all_injects > div').append(tplEmpty());
			}
		});
	},
};
