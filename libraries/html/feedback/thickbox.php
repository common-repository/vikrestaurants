<?php
/** 
 * @package     VikRestaurants - Libraries
 * @subpackage  html.feedback
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Auto set CSRF token to ajaxSetup so all jQuery ajax call will contain CSRF token.
 *
 * @since 1.3
 */
JHtml::fetch('vrehtml.sitescripts.ajaxcsrf');

$deactivate_url_js = !empty($displayData['url']) ? $displayData['url'] : '#';
$is_pro            = isset($displayData['pro'])  ? $displayData['pro'] : false;

$plain_deactivation_url = JUri::getInstance();
$plain_deactivation_url->setVar('feedback', 0);

// $doc_url = 'https://vikwp.com/support/documentation/vik-restaurants/';
$doc_url = 'https://vikwp.com/support/knowledge-base/vik-restaurants/installation';

$options = array();

$options[] = array(
	'value'   => '0',
	'text'    => __('No, thanks', 'vikrestaurants'),
	'checked' => true,
);

$wp = new JVersion();

if (version_compare($wp->getShortVersion(), '5.5', '>='))
{
	$options[] = array(
		'value'  => 'The plugin is facing strange behaviors on WP 5.5',
		'text'   => __('The plugin is facing strange behaviors on WP 5.5', 'vikrestaurants'),
		'help'   => __('Have you tried to install <b>Enable jQuery Migrate Helper</b> plugin by WordPress Team? WordPress 5.5 update might have brought a few javascript issues, which should be solved by installing the mentioned plugin.', 'vikrestaurants'),
	);
}

$options[] = array(
	'value'  => 'The plugin is not working',
	'text'   => __('The plugin is not working', 'vikrestaurants'),
	'notes'  => true,
);

$options[] = array(
	'value'  => 'The plugin didn\'t work as expected',
	'text'   => __('The plugin didn\'t work as expected', 'vikrestaurants'),
	'notes'  => true,
);

if (!$is_pro)
{
	$options[] = array(
		'value'  => 'The FREE version is too limited',
		'text'   => __('The FREE version is too limited', 'vikrestaurants'),
		'notes'  => array(
			'required'    => false,
			'placeholder' => __('What feature would you like to use?', 'vikrestaurants'),
		),
	);
}

$options[] = array(
	'value'  => 'The plugin suddenly stopped working',
	'text'   => __('The plugin suddenly stopped working', 'vikrestaurants'),
	'notes'  => true,
);

$options[] = array(
	'value'  => 'The plugin broke my site',
	'text'   => __('The plugin broke my site', 'vikrestaurants'),
	'notes'  => array(
		'placeholder' => __('Please, could you explain what happened?', 'vikrestaurants'),
	),
);

$options[] = array(
	'value'  => 'I couldn\'t understand how to get it work',
	'text'   => __('I couldn\'t understand how to get it work', 'vikrestaurants'),
	'help'   => sprintf(__('Did you read the plugin documentation? You can find it <a href="%s" target="_blank">here</a>.', 'vikrestaurants'), $doc_url),
);

$options[] = array(
	'value'  => 'I no longer need the plugin',
	'text'   => __('I no longer need the plugin', 'vikrestaurants'),
);

$options[] = array(
	'value'  => 'I\'m just debugging an issue',
	'text'   => __('I\'m just debugging an issue', 'vikrestaurants'),
);

$options[] = array(
	'value'  => 'I\'m not able to deactivate this plugin!',
	'text'   => __('I\'m not able to deactivate this plugin!', 'vikrestaurants'),
	'help'   => sprintf(__('In case you are experiencing some issues with the deactivation of this plugin, try to relaunch the page by appending <code>&feedback=0</code> to the URL. Otherwise just click <a href="%s">HERE</a>.', 'vikrestaurants'), (string) $plain_deactivation_url),
);

$options[] = array(
	'value'  => 'Other',
	'text'   => __('Other', 'vikrestaurants'),
	'notes'  => array(
		'required'    => true,
		'placeholder' => __('Please, provide us some information', 'vikrestaurants'),
	),
);

?>

<style>
	.thickbox-loading {
		height: auto !important;
	}
	#TB_ajaxContent {
		width: calc(100% - 30px) !important;
		height: calc(100% - 45px) !important;
	}
	.th-box-body {
		height: calc(100% - 45px);
	}
	.th-box-footer {
		text-align: right;
	}

	.th-box-body div.form-field {
		margin: 5px 0;
	}

	.th-box-body blockquote {
		color: #7f8fa4;
		font-size: inherit;
		padding: 8px 24px;
		border-left: 4px solid #eaeaea;
		background: #fff;
	}

	textarea.invalid {
		border: 1px solid #900;
	}
</style>

<div id="vikrestaurants-feedback" style="display: none;">

	<div class="th-box-body">
		<h2><?php echo __('Feedback', 'vikrestaurants'); ?></h2>
		<h3><?php echo __('If you have a moment, please let us know why you are deactivating.', 'vikrestaurants'); ?></h3>

		<form id="vre-feedback-form">

			<?php
			foreach ($options as $i => $opt)
			{
				?>
				<div class="form-field">
					<input
						type="radio"
						id="vre-feed-opt-<?php echo $i; ?>"
						name="feedback_type"
						value="<?php echo esc_attr($opt['value']); ?>"
						<?php echo !empty($opt['checked']) ? 'checked="checked"' : ''; ?>
					/>
					<label for="vre-feed-opt-<?php echo $i; ?>"><?php echo $opt['text']; ?></label>

					<?php
					if (!empty($opt['help']))
					{
						?>
						<blockquote
							class="feedback-help"
							style="<?php echo !empty($opt['checked']) ? '' : 'display:none;'; ?>"
						>
							<?php echo $opt['help']; ?>
						</blockquote>
						<?php
					}

					if (!empty($opt['notes']))
					{
						$opt['notes'] = (array) $opt['notes'];

						$placeholder = !empty($opt['notes']['placeholder'])
							? $opt['notes']['placeholder']
							: __('Please, could you write some extra notes?', 'vikrestaurants');

						?>
						<textarea
							class="feedback-notes<?php echo !empty($opt['notes']['required']) ? ' required' : ''; ?>"
							placeholder="<?php echo $placeholder; ?>"
							style="<?php echo !empty($opt['checked']) ? '' : 'display:none;'; ?>"
						></textarea>
						<?php
					}
					?>

				</div>
				<?php
			}
			?>

			<p>
				<small>
					<span class="dashicons dashicons-editor-help"></span>&nbsp;
					<?php echo __('In addition to the specified information, the feedback will contain your IP address (for security reasons), your PHP version, your WordPress version and the plugin version.', 'vikrestaurants'); ?>
				</small>
			</p>

		</form>
	</div>

	<div class="th-box-footer">
		<a href="javascript: void(0);" id="vikrestaurants-deactivate" class="button button-primary">
			<?php echo __('Deactivate'); ?>
		</a>
	</div>

</div>

<script>
	jQuery(document).ready(function() {

		jQuery('.form-field input[name="feedback_type"]').on('change', function() {
			var radio = jQuery('input[name="feedback_type"]:checked');

			jQuery('.feedback-help,.feedback-notes').hide();
			radio.siblings('.feedback-help,.feedback-notes').show().focus();
		});

		jQuery('#vikrestaurants-deactivate').on('click', function() {
			var radio = jQuery('input[name="feedback_type"]:checked');

			// get selected feedback type
			var type = radio.val();

			if (type == '0') {
				// deactivate automatically
				document.location.href = '<?php echo $deactivate_url_js; ?>';
				return;
			}

			// get textarea
			var textarea = radio.siblings('.feedback-notes');
			var comment  = textarea.length ? textarea.val() : null;

			if (textarea.length && !textarea.val().length && textarea.hasClass('required')) {
				textarea.addClass('invalid');
				return;
			}

			// disable button
			jQuery(this).attr('disabled', true);

			textarea.removeClass('invalid');

			// do ajax and redirect on success/failure
			jQuery.ajax({
				url: 'admin-ajax.php?action=vikrestaurants&task=feedback.submit',
				type: 'post',
				data: {
					type:  type,
					notes: comment,
				},
			}).done(function(resp) {
				// keep a cookie for 7 days in order to stop showing the feedback
				// modal again and again
				var date = new Date();
				date.setDate(date.getDate() + 7);
				document.cookie = 'vikrestaurants_feedback=1; expires=' + date.toUTCString() + '; path=/';
				// complete deactivation
				document.location.href = '<?php echo $deactivate_url_js; ?>';
			}).fail(function(err) {
				console.log(err);
				// complete deactivation
				document.location.href = '<?php echo $deactivate_url_js; ?>';
			});
		});

	});
</script>
