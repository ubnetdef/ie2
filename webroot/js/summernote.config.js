(function($) {
	// Overwrite the Summernote Cleaner text, because it's annoying.
	$.extend(true, $.summernote.lang, {
		'en-US': {
			cleaner: {
				tooltip: 'Cleaner',
				not: 'Detected pasted text from Microsoft Word. Cleaned!'
			}
		}
	});
})(jQuery);

// Export the cleaner config
window.SUMMERNOTE_CLEANER_CONFIG = {
	notTime: 1500,
	action: 'paste',
	newline: '<br>',
	notStyle: 'position:absolute;top:0;left:0;right:0',
	keepHtml: true,
	keepClasses: false,
	badTags: ['style', 'script', 'applet', 'embed', 'noframes', 'noscript', 'html'],
	badAttributes: ['style', 'start'],
};