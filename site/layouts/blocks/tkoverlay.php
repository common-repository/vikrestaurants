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

$itemid = isset($displayData['Itemid']) ? $displayData['Itemid'] : null;

if (is_null($itemid))
{
	$itemid = JFactory::getApplication()->input->get('Itemid', null, 'uint');
}

?>

<div class="vr-overlay" id="vrnewitemoverlay" style="display: none;">

	<div class="vr-modal-box">

		<div class="vr-modal-head">

			<div class="vr-modal-head-title">
				<h3></h3>
			</div>

			<div class="vr-modal-head-dismiss">
				<a href="javascript: void(0);" onClick="vrCloseOverlay('vrnewitemoverlay');">×</a>
			</div>

		</div>

		<div class="vr-modal-body">
			
		</div>

	</div>

</div>

<?php
JText::script('VRTKADDITEMERR2');
?>

<script>
	(function($) {
		'use strict';

		window.vrOpenOverlay = (ref, title, id_entry, id_option, index) => {
			// change overlay title
			$('.vr-modal-head-title h3').text(title);
			
			// add loading image
			$('.vr-modal-body').html(
				'<div class="vr-modal-overlay-loading">\n'+
					'<img id="img-loading" src="<?php echo VREASSETS_URI . 'css/images/hor-loader.gif'; ?>" />\n'+
				'</div>\n'
			);
			
			// show modal
			$('#' + ref).show();

			// prevent body from scrolling
			$('body').css('overflow', 'hidden');
			
			// make request to load product details
			UIAjax.do(
				'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&view=tkadditem&tmpl=component' . ($itemid ? '&Itemid=' . $itemid : '')); ?>',
				{
					eid:   id_entry,
					oid:   id_option,
					index: index,
				},
				(resp) => {
					$('.vr-modal-body').html(resp);
				},
				(error) => {
					if (!error.responseText || error.responseText.length > 1024) {
						// use default generic error
						error.responseText = Joomla.JText._('VRTKADDITEMERR2');
					}

					alert(error.responseText);
				}
			);
		}

		window.vrCloseOverlay = (ref) => {
			// make body scrollable again
			$('body').css('overflow', 'auto');

			// hide overlay
			$('#' + ref).hide();
			// clear overlay body
			$('.vr-modal-body').html('');
		}

		$(function() {
			$('.vr-modal-box').on('click', function(e) {
				// ignore outside click
				e.stopPropagation();
			});

			$('.vr-overlay').on('click', function() {
				// close overlay when the background is clicked
				vrCloseOverlay($(this).attr('id'));
			});
		});
	})(jQuery);
</script>