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

// create object to pass to the hook, so that external plugins
// can extend the appearance of any additional tab
$setup = new stdClass;
$setup->icons = [];

$tabs = [];

$previewScripts = [];

foreach ($this->customizerTree as $nodeName => $nodeLevels)
{
	$tab_lang_key = 'VRE_CUSTOMIZER_TAB_' . strtoupper($nodeName);

	// attempt to translate tab label
	$tab_label = JText::translate($tab_lang_key);

	if ($tab_label === $tab_lang_key)
	{
		// prettify default name
		$tab_label = ucfirst($nodeName);
	}

	$this->customizerNode = $nodeLevels;
	$tabs[$tab_label]     = $this->loadTemplate('customizer_form');

	$previewScripts[] = strtolower($tab_label);

	switch ($nodeName)
	{
		case 'button':
			$setup->icons[$tab_label] = 'fas fa-square';
			break;
	}
}

// add tab to customize the fields
$tabs['VRMENUCUSTOMFIELDS']         = $this->loadTemplate('customizer_fields');
$setup->icons['VRMENUCUSTOMFIELDS'] = 'fas fa-align-left';

// add tab to write custom CSS code
$tabs['VRE_CUSTOMIZER_TAB_ADDITIONALCSS']         = $this->loadTemplate('customizer_addcss');
$setup->icons['VRE_CUSTOMIZER_TAB_ADDITIONALCSS'] = 'fas fa-code';

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewConfigappCustomizer". The event method receives the
 * view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('Customizer', $setup);

// create display data
$data = [];
$data['id']     = 3;
$data['active'] = $this->selectedTab == $data['id'];
$data['tabs']   = array_merge($tabs, $forms);
$data['setup']  = $setup;
$data['hook']   = 'Customizer';
$data['suffix'] = 'app';

$data['before'] = $this->loadTemplate('customizer_toolbar');
$data['after']  = $this->loadTemplate('customizer_preview');

// render configuration pane with apposite layout
echo JLayoutHelper::render('configuration.tabview', $data);

JText::script('VRE_CUSTOMIZER_RESTORE_FACTORY_SETTINGS');
?>

<script>
	(function($) {
		'use strict';

		// get default customizer settings
		const defaultSettings = <?php echo json_encode($this->customizerModel->getItem()); ?>;

		$(function() {
			$('.restore-customizer-settings').on('click', function() {
				// ask for a confirmation before to proceed
				const r = confirm(Joomla.JText._('VRE_CUSTOMIZER_RESTORE_FACTORY_SETTINGS'));

				if (!r) {
					return false;
				}

				// find all fields related to the clicked button
				const fields = $(this).closest('.config-fieldset-body').find('[name^="customizer["]');

				fields.each(function() {
					// extract CSS var from name
					const match = $(this).attr('name').match(/^customizer\[(.*?)\]$/);
					const key   = match[1];

					if (!defaultSettings.hasOwnProperty(key)) {
						// var not found...
						return false;
					}

					let value = defaultSettings[key];

					// replace prefix and suffix
					value = value.replace(/^#/, '');
					value = value.replace(/px$/, '');

					// restore original value
					$(this).val(value).trigger('change');
				});
			});

			const tabs = <?php echo json_encode($previewScripts); ?>;
			const customizerUrl = '<?php echo VREFactory::getPlatform()->getUri()->route('index.php?option=com_vikrestaurants&task=customizer.%s_preview&tmpl=component', false); ?>';

			tabs.forEach((tab) => {
				$('li[data-id="' + tab + '"]').on('click', () => {
					/**
					 * In WordPress the "%s" might be safely encoded as "%25s".
					 * Therefore the regex should support both the possibilities.
					 */
					changeCustomizerPreviewPage(customizerUrl.replace(/%(25)?s/, tab));
				});
			});
		});
	})(jQuery);
</script>
