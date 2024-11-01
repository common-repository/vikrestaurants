<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Layout variables
 * -----------------
 * @var  string   $setting    The name of the translatable setting.
 * @var  string   $input      The default rendered input.
 * @var  array    $languages  All the available translations.
 * @var  bool     $enabled    True in case the multi-lingual is enabled.
 * @var  string   $position   Where the management button should be displayed.
 * @var  string   $return     The return page.
 * @var  bool     blank       Whether the page should be displayed on a different page.
 */
extract($displayData);

switch ($position)
{
	case 'left':   $class = ' multi-field left'; break;
	case 'right':  $class = ' multi-field'; break;
	case 'bottom': $class = ' bottom'; break;
	default: $class = '';
}

JText::script('VRE_CONFIRM_MESSAGE_UNSAVE');

?>

<div class="translatable translatable-<?php echo $setting; ?><?php echo $class; ?>">
	<?php echo $input; ?>

	<!-- translation button -->
	
	<a
		href="index.php?option=com_vikrestaurants&amp;view=langconfig&amp;param=<?php echo $setting; ?>&amp;return=<?php echo $return; ?>"
		class="config-trx btn"
		<?php echo $blank ? 'target="_blank"' : 'id="' . $id . '-tx-btn"'; ?>
		id="<?php echo $id; ?>-tx-btn"
		style="<?php echo $enabled ? '' : 'display:none;'; ?>"
	>
		<?php
		foreach ($languages as $lang)
		{
			echo ' ' . JHtml::fetch('vrehtml.site.flag', $lang) . ' ';
		}
		?>
	</a>
</div>

<?php if (!$blank): ?>
	<script>
		(function($, w) {
			'use strict';

			$(function() {
				if (!w.configObserver) {
					// register page observer
					w.configObserver = new VikFormObserver('#adminForm');

					setTimeout(() => {
						// wait some seconds in order to let TinyMCE completes the initialization
						w.configObserver.freeze();
					}, 256);
				}

				$('#<?php echo $id; ?>-tx-btn').on('click', function(event) {
					if (!w.configObserver.isChanged()) {
						// nothing has changed, go ahead
						return true;
					}

					// ask for a confirmation
					if (!confirm(Joomla.JText._('VRE_CONFIRM_MESSAGE_UNSAVE'))) {
						// do not leave the page
						event.preventDefault();
						event.stopPropagation();
						return false;
					}
				});
			});
		})(jQuery, window);
	</script>
<?php endif; ?>