(function() {
	tinymce.PluginManager.add('vre-shortcodes', function(editor, url) {
		// add Button to Visual Editor Toolbar
		editor.addButton('vre-shortcodes', {
			title: 'VikRestaurants Shortcodes List',
			cmd: 'vre-shortcodes',
			icon: 'wp_code'
		});

		editor.addCommand('vre-shortcodes', function() {
			openVikRestaurantsShortcodes(editor);
		});

	});
})();

var shortcodes_editor = null;

function openVikRestaurantsShortcodes(editor) {

	shortcodes_editor = editor;

	var html = '';

	for (var group in VIKRESTAURANTS_SHORTCODES) {

		html += '<div class="shortcodes-block">';
		html += '<div class="shortcodes-group"><a href="javascript: void(0);" onclick="toggleVikRestaurantsShortcode(this);">' + group + '</a></div>';
		html += '<div class="shortcodes-container">';

		for (var i = 0; i < VIKRESTAURANTS_SHORTCODES[group].length; i++) {
			var row = VIKRESTAURANTS_SHORTCODES[group][i];

			html += '<div class="shortcode-record" onclick="selectVikRestaurantsShortcode(this);" data-code=\'' + row.shortcode + '\'">';
			html += '<div class="maindetails">' + row.name + '</div>';
			html += '<div class="subdetails">';
			html += '<small class="postid">Post ID: ' + row.post_id + '</small>';
			html += '<small class="createdon">Created On: ' + row.createdon + '</small>';
			html += '</div>';
			html += '</div>';
		}

		html += '</div></div>';
	}

	jQuery('body').append(
		'<div id="vre-shortcodes-backdrop" class="vre-tinymce-backdrop"></div>\n'+
		'<div id="vre-shortcodes-wrap" class="vre-tinymce-modal wp-core-ui has-text-field" role="dialog" aria-labelledby="link-modal-title">\n'+
			'<form id="vre-shortcodes" tabindex="-1">\n'+
				'<h1>VikRestaurants Shortcodes List</h1>\n'+
				'<button type="button" onclick="dismissVikRestaurantsShortcodes();" class="vre-tinymce-dismiss"><span class="screen-reader-text">Close</span></button>\n'+
				'<div class="vre-tinymce-body">' + html + '</div>\n'+
				'<div class="vre-tinymce-submitbox">\n'+
					'<div id="vre-tinymce-cancel">\n'+
						'<button type="button" class="button" onclick="dismissVikRestaurantsShortcodes();">Cancel</button>\n'+
					'</div>\n'+
					'<div id="vre-tinymce-update">\n'+
						'<button type="button" class="button button-primary" disabled onclick="putVikRestaurantsShortcode();">Add</button>\n'+
					'</div>\n'+
				'</div>\n'+
			'</form>\n'+
		'</div>\n'
	);

	jQuery('#vre-shortcodes-backdrop').on('click', function() {
		dismissVikRestaurantsShortcodes();
	});
}

function dismissVikRestaurantsShortcodes() {
	jQuery('#vre-shortcodes-backdrop, #vre-shortcodes-wrap').remove();
}

function toggleVikRestaurantsShortcode(link) {
	var next = jQuery(link).parent().next();
	var show = next.is(':visible') ? false : true;

	jQuery('.shortcodes-container').slideUp();

	if (show) {
		next.slideDown();
	}
}

function selectVikRestaurantsShortcode(record) {
	jQuery('.shortcode-record').removeClass('selected');
	jQuery(record).addClass('selected');

	jQuery('#vre-tinymce-update button').prop('disabled', false);
}

function putVikRestaurantsShortcode() {
	var shortcode = jQuery('.shortcode-record.selected').data('code');

	shortcodes_editor.execCommand('mceReplaceContent', false, shortcode);

	dismissVikRestaurantsShortcodes();
}
