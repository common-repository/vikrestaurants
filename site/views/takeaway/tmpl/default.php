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

JHtml::fetch('bootstrap.tooltip');
JHtml::fetch('vrehtml.sitescripts.animate');
JHtml::fetch('vrehtml.assets.fancybox');
JHtml::fetch('vrehtml.assets.fontawesome');
JHtml::fetch('vrehtml.assets.toast', 'bottom-center');

?>

<div class="vrtkitemspagediv">

	<?php
	/**
	 * Displays the top section of the page, containing
	 * the front-end take-away notes, the menus filter
	 * and the date selection.
	 */
	echo $this->loadTemplate('head');
	?>

	<div class="vrtkitemsdiv">

		<?php
		foreach ($this->menus as $menu)
		{
			// keep a reference of the current menu for
			// being used in a sub-template
			$this->forMenu = $menu;

			/**
			 * Displays the current menu as a section and
			 * the list of all its children products.
			 */
			echo $this->loadTemplate('menu');
		}
		?>

	</div>

	<div class="vrtkgotopaydiv">
		<a href="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&view=takeawayconfirm' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" class="vre-btn primary big">
			<?php echo JText::translate('VRTAKEAWAYORDERBUTTON'); ?>
		</a>
	</div>

</div>

<?php if (count($this->attributes)): ?>
	<div class="vrtk-attributes-legend">
		<?php foreach ($this->attributes as $attr): ?>
			<div class="vrtk-attribute-box">
				<?php
				echo JHtml::fetch('vrehtml.media.display', $attr->icon, [
					'alt' => $attr->name,
				]);
				?>
				<span><?php echo $attr->name; ?></span>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>

<?php
/**
 * Creates the popup that will be used to display the details
 * of the products that are going to be added.
 *
 * The popup will be used according to the "Use Items Overlay"
 * setting in the Take-Away configuration.
 */
echo $this->loadTemplate('overlay');

JText::script('VRTKADDITEMSUCC');
JText::script('VRTKADDITEMERR2');
?>

<script>
	(function($) {
		'use strict';

		const GALLERY_DATA = <?php echo json_encode($this->getGalleryData()); ?>;

		// adjust gallery data for being used by FontAwesome
		for (let id in GALLERY_DATA.images) {
			if (GALLERY_DATA.images.hasOwnProperty(id)) {
				// iterate images
				for (let i = 0; i < GALLERY_DATA.images[id].length; i++) {
					let img = GALLERY_DATA.images[id][i];

					GALLERY_DATA.images[id][i] = {
						src:  img.uri,
						type: 'image',
						opts: {
							caption: img.caption,
							thumb:   img.thumb,
						},
					};
				}
			}
		}

		window.vreOpenGallery = (link) => {
			// get clicked image
			const img = $(link).find('img');

			if (GALLERY_DATA.groupBy == 'menu') {
				// get menu ID
				let id_menu = img.data('menu');

				if (!GALLERY_DATA.images.hasOwnProperty(id_menu)) {
					return false;
				}

				// open fancybox to show only the items that belong to this menu
				const instance = $.fancybox.open(GALLERY_DATA.images[id_menu]);

				// get clicked image index
				var index = $('a.vremodal img[data-menu="' + id_menu + '"]').index(img);

				if (index > 0) {
					// jump to selected image ('0' turns off the animation)
					instance.jumpTo(index, 0);
				}
			} else {
				// get product ID
				let id_prod = img.data('prod');

				if (!GALLERY_DATA.images.hasOwnProperty(id_prod)) {
					return false;
				}

				// open fancybox to show only the items that belong to this product
				$.fancybox.open(GALLERY_DATA.images[id_prod]);
			}
		}

		window.showMoreDesc = (id) => {
			setDescriptionVisible(id, true);
		}

		window.showLessDesc = (id) => {
			setDescriptionVisible(id, false);
		}

		const setDescriptionVisible = (id, status) => {
			if (status) {
				$('#vrtkitemshortdescsp' + id).hide();
				$('#vrtkitemlongdescsp' + id).show();
			} else {
				$('#vrtkitemlongdescsp' + id).hide();
				$('#vrtkitemshortdescsp' + id).show();
			}
		}

		window.vrInsertTakeAwayItem = (id_entry, id_option) => {
			const data = {
				id_entry:   id_entry,
				id_option:  id_option,
				item_index: -1,
			};

			vrMakeAddCartRequest(data).then((response) => {
				// do nothing here
			}).catch((error) => {
				// do nothing here
			});
		}

		window.vrMakeAddCartRequest = (data) => {
			// create promise
			return new Promise((resolve, reject) => {
				// make request to add the item within the cart
				UIAjax.do(
					'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=takeaway.addtocartajax' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>',
					data,
					(obj) => {
						let msg = {
							status: 0,
							text:   '',
						};

						if (vrIsCartPublished()) {
							// refresh cart module in case it is published
							vrCartRefreshItems(obj.items, obj.total, obj.discount, obj.finalTotal);
						}

						// resolve promise
						resolve(obj);

						if (obj.message) {
							// use the message fetched by the controller
							msg = obj.message;
						}

						// Display the default successful message only in case there is no message text
						// and the cart is not published (or currently not visible on the screen).
						if (msg.text.length == 0 && (!vrIsCartPublished() || !vrIsCartVisibleOnScreen())) {
							msg.text   = Joomla.JText._('VRTKADDITEMSUCC');
							msg.status = 1;
						}

						if (msg.text.length) {
							// dispatch toast message
							VREToast.dispatch(msg);
						}
					},
					(error) => {
						if (!error.responseText || error.responseText.length > 1024) {
							// use default generic error
							error.responseText = Joomla.JText._('VRTKADDITEMERR2');
						}

						// reject promise
						reject(error);

						// raise error
						VREToast.dispatch({
							text:   error.responseText,
							status: VREToast.ERROR_STATUS,
						});
					}
				);
			});
		}
	})(jQuery);
</script>