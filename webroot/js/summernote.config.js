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
	// Whitelist borrowed from https://meta.stackexchange.com/a/135909
	keepOnlyTags: [
		'a', 'b', 'blockquote', 'code', 'del', 'dd', 'dl',
		'em', 'h1', 'h2', 'h3', 'i', 'img', 'li', 'ol', 'p',
		'pre', 's', 'sup', 'sub', 'strong', 'strike', 'ul', 'br',
		'hr'
	],
	keepClasses: false,
	badTags: ['style', 'script', 'applet', 'embed', 'noframes', 'noscript', 'html'],
	badAttributes: ['style', 'start'],
};