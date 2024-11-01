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

JHtml::fetch('bootstrap.tooltip', '.hasTooltip');
JHtml::fetch('vrehtml.assets.fontawesome');
JHtml::fetch('vrehtml.scripts.selectflags', '#vre-lang-sel');

$vik = VREApplication::getInstance();

$deflang = VikRestaurants::getDefaultLanguage();

?>

<form name="adminForm" action="index.php" method="post" id="adminForm">

	<?php
	echo $vik->bootStartTabSet('managelangmenusproduct', ['active' => $this->getActiveTab('managelangmenusproduct_details'), 'cookie' => $this->getCookieTab()->name]);
	
	// product details
	echo $vik->bootAddTab('managelangmenusproduct', 'managelangmenusproduct_details', JText::translate('VRMAPDETAILSBUTTON'));
	echo $this->loadTemplate('product');
	echo $vik->bootEndTab();

	// product variations
	if ($this->variations)
	{
		echo $vik->bootAddTab('managelangmenusproduct', 'managelangmenusproduct_options', JText::translate('VRMANAGETKENTRYFIELDSET2'));
		echo $this->loadTemplate('options');
		echo $vik->bootEndTab();
	}

	echo $vik->bootEndTabSet();
	?>

	<?php echo JHtml::fetch('form.token'); ?>
	
	<input type="hidden" name="id_product" value="<?php echo (int) $this->product->id; ?>" />	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikrestaurants" />
</form>

<?php
/**
 * Display utils scripts to enhance the translations management.
 *
 * @since 1.9
 */
echo JLayoutHelper::render('script.langhint');

JText::script('VRE_SAVE_TRX_DEF_LANG');
?>

<script>
	(function($) {
		'use strict';

		$(function() {
			Joomla.submitbutton = (task) => {
				let selectedLanguage = $('#vre-lang-sel').val();

				if (task.indexOf('save') !== -1 && selectedLanguage == '<?php echo $deflang; ?>') {
					// saving translation with default language, ask for confirmation
					let r = confirm(Joomla.JText._('VRE_SAVE_TRX_DEF_LANG').replace(/%s/, selectedLanguage));

					if (!r) {
						return false;
					}
				}

				Joomla.submitform(task, document.adminForm);
			}
		});
	})(jQuery);
</script>
