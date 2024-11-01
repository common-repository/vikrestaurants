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

// build layout used to render the parameters form
$formLayout = new JLayoutFile('form.fields');

foreach ($this->customizerNode as $k => $fields)
{
	$fieldset_lang_key = 'VRE_CUSTOMIZER_FIELDSET_' . strtoupper($k);

	// attempt to translate parameter label
	$fieldset_label = JText::translate($fieldset_lang_key);

	if ($fieldset_label === $fieldset_lang_key)
	{
		// prettify default name
		$fieldset_label = ucfirst($k);
	}

	$params = [];

	foreach ($fields as $name => $field)
	{
		$lang_key = 'VRE_CUSTOMIZER_PARAM_' . strtoupper($field['label']);

		// attempt to translate parameter label
		$label = JText::translate($lang_key);

		if ($label === $lang_key)
		{
			// prettify default name
			$label = ucwords(str_replace('_', ' ', $field['label']));
		}

		$formNameKey = $field['key'];

		$params[$formNameKey] = [
			'type'  => $field['type'],
			'name'  => 'customizer[' . $field['key'] . ']',
			'value' => $field['val'],
			'label' => $label,
			'class' => $field['type'],
		];

		if ($field['type'] === 'color')
		{
			// enable color preview
			$params[$formNameKey]['preview'] = true;
		}
		else if ($field['type'] === 'number')
		{
			// accept decimals too
			$params[$formNameKey]['step'] = 'any';
			// attach "px" suffix
			$params[$formNameKey]['renderer'] = new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer('px');
		}
	}

	?>
	<div class="config-fieldset full-width">

		<div class="config-fieldset-head">
			<h3><?php echo $fieldset_label; ?></h3>
			<i class="fas fa-plus fa-2x collapse-fieldset"></i>
		</div>

		<div class="config-fieldset-body" style="display: none;">
			<?php
			// render parameters form
			echo $formLayout->render(['fields' => $params]);
			?>

			<!-- RESTORE - Button -->

			<?php
			echo $this->formFactory->createField()
				->type('button')
				->class('restore-customizer-settings')
				->text(JText::translate('VRMAPGPRESTOREBUTTON'));
			?>
		</div>

	</div>
	<?php
}
?>

<style>
	.config-fieldset-head {
		position: relative;
	}
	.config-fieldset-head i.collapse-fieldset {
		position: absolute;
		right: 15px;
		top: 50%;
		transform: translateY(-50%);
		opacity: 0.3;
		cursor: pointer;
	}
	.config-fieldset-head i.collapse-fieldset:hover {
		opacity: 0.6;
	}
</style>

<script>
	(function($) {
		'use strict';

		$(function() {
			$('.config-fieldset-head i.collapse-fieldset').on('click', function() {
				const body = $(this).parent().next();

				if (body.is(':visible')) {
					body.slideUp();
					$(this).removeClass('fa-minus').addClass('fa-plus');
				} else {
					body.slideDown();
					$(this).removeClass('fa-plus').addClass('fa-minus');
				}
			});
		});
	})(jQuery);
</script>