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

?>

<div class="vr-overlay" id="vrdishoverlay" style="display: none;">

	<div class="vr-modal-box">

		<div class="vr-modal-head">

			<div class="vr-modal-head-title">
				<h3></h3>
			</div>

			<div class="vr-modal-head-dismiss">
				<a href="javascript: void(0);" onClick="vrCloseDishOverlay();">×</a>
			</div>

		</div>

		<div class="vr-modal-body">
			
		</div>

	</div>

</div>

<?php
JText::script('VRTKADDITEMERR2');
JText::script('VRTKADDTOTALBUTTON');
JText::script('VRTKADDEDITDISHTITLE');
?>

<script>
	(function($) {
		'use strict';

		let VRE_OVERLAY_XHR = null;

		window.vrOpenDishOverlay = (id_dish, index) => {
			// fetch title
			let title;

			if (index !== undefined && index != -1) {
				title = Joomla.JText._('VRTKADDEDITDISHTITLE');
			} else {
				title = jQuery('.vre-order-dishes-product[data-id="' + id_dish + '"]').data('name');
			}

			// change overlay title
			$('.vr-modal-head-title h3').text(title);
			
			// add loading image
			$('#vrdishoverlay .vr-modal-body').html(
				'<div class="vr-modal-overlay-loading">\n'+
					'<img id="img-loading" src="<?php echo VREASSETS_URI . 'css/images/hor-loader.gif'; ?>" />\n'+
				'</div>\n'
			);
			
			// show modal
			$('#vrdishoverlay').show();

			// prevent body from scrolling
			$('body').css('overflow', 'hidden');
			
			// make request to load product details
			VRE_OVERLAY_XHR = UIAjax.do(
				'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=orderdish.add' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>',
				{
					ordnum: <?php echo (int) $this->reservation->id; ?>,
					ordkey: '<?php echo $this->reservation->sid; ?>',
					id:     id_dish,
					index:  typeof index === 'undefined' ? -1 : index,
				},
				(resp) => {
					$('#vrdishoverlay .vr-modal-body').html(resp);
				},
				(error) => {
					if (error.statusText !== 'abort') {
						if (!error.responseText || error.responseText.length > 1024) {
							// use default generic error
							error.responseText = Joomla.JText._('VRTKADDITEMERR2');
						}

						// auto-close overlay on error
						vrCloseDishOverlay();

						setTimeout(() => {
							// raise error with a short delay to complete
							// the closure of the overlay
							alert(error.responseText);
						}, 32);
					}
				}
			);
		}

		window.vrCloseDishOverlay = () => {
			// make body scrollable again
			$('body').css('overflow', 'auto');

			// hide overlay
			$('#vrdishoverlay').hide();
			// clear overlay body
			$('#vrdishoverlay .vr-modal-body').html('');

			if (VRE_OVERLAY_XHR) {
				// abort request
				VRE_OVERLAY_XHR.abort();
			}
		}

		$(function() {
			$('.vr-modal-box').on('click', (e) => {
				// ignore outside click
				e.stopPropagation();
			});

			$('#vrdishoverlay').on('click', () => {
				// close overlay when the background is clicked
				vrCloseDishOverlay();
			});
		});

	})(jQuery);
</script>