<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

JHtml::fetch('formbehavior.chosen', 'form#adminForm');
JHtml::fetch('vrehtml.assets.select2');

$vik = VREApplication::getInstance();

?>

<form action="admin.php" method="post" name="adminForm" id="adminForm">

	<div id="poststuff">

		<?php echo $vik->openFieldset(JText::translate('JSHORTCODE')); ?>

			<!-- NAME - Text -->

			<?php
			echo $this->formFactory->createField()
				->type('text')
				->name('name')
				->value($this->shortcode['name'])
				->required(true)
				->label(JText::translate('JNAME'));
			?>

			<!-- TYPE - Select -->

			<?php
			$types = [
				JHtml::fetch('select.option', '', JText::translate('JGLOBAL_SELECT_AN_OPTION')),
			];

			foreach ($this->views as $k => $v)
			{
				$types[] = JHtml::fetch('select.option', $k, JText::translate($v['name']));
			}

			echo $this->formFactory->createField()
				->type('select')
				->name('type')
				->value($this->shortcode['type'])
				->required(true)
				->label(JText::translate('JTYPE'))
				->onchange('shortcodeTypeValueChanged(this)')
				->options($types);
			?>

			<!-- PARENT - Select -->

			<?php
			$parents = [
				JHtml::fetch('select.option', '', '--'),
			];

			foreach ($this->shortcodesList as $item)
			{
				if ($item->id === $this->shortcode['id'])
				{
					// exclude self
					continue;
				}
				
				$parents[] = JHtml::fetch('select.option', $item->id, $item->name);
			}

			echo $this->formFactory->createField()
				->type('select')
				->name('parent_id')
				->value($this->shortcode['parent_id'])
				->label(JText::translate('VRE_SHORTCODE_PARENT_FIELD'))
				->options($parents);
			?>

			<!-- LANGUAGE - Select -->

			<?php
			$languages = [
				JHtml::fetch('select.option', '*', JText::translate('JALL')),
			];

			foreach (JLanguage::getKnownLanguages() as $tag => $lang)
			{
				$languages[] = JHtml::fetch('select.option', $tag, $lang['nativeName']);
			}

			echo $this->formFactory->createField()
				->type('select')
				->name('lang')
				->value($this->shortcode['lang'])
				->label(JText::translate('JLANGUAGE'))
				->options($languages);
			?>

			<!-- VIEW DESCRIPTION - Alert -->

			<?php
			foreach ($this->views as $k => $v)
			{
				echo $this->formFactory->createField()
					->type('alert')
					->style('info')
					->text(JText::translate($v['desc']))
					->control([
						'visible' => $k == $this->shortcode['type'],
						'class'   => 'shortcode-type-desc',
						'id'      => 'shortcode-type-desc-' . $k,
					]);
			}
			?>

		<?php echo $vik->closeFieldset(); ?>

		<!-- PARAMETERS -->

		<div class="shortcode-params">
			<?php
			/**
			 * Immediately render the form fields of the selected shortcode.
			 *
			 * @since 1.3
			 */
			if ($this->form)
			{
				echo $this->form->renderForm(json_decode($this->shortcode['json']));
			}
			?>
		</div>

	</div>

	<?php echo JHtml::fetch('form.token'); ?>

	<input type="hidden" name="id" value="<?php echo (int) $this->shortcode['id']; ?>" />
	<input type="hidden" name="option" value="com_vikrestaurants" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="return" value="<?php echo $this->escape($this->returnLink); ?>" />

</form>

<script>
	(function($, w) {
		'use strict';

		let validator = null;

		w.shortcodeTypeValueChanged = (select) => {
			validator.unregisterFields('.shortcode-params .required');

			if (!$('#adminForm input[name="name"]').val()) {
				// use the page title as shortcode name
				$('#adminForm input[name="name"]').val($(select).find('option:selected').text().trim());
			}

			$('.shortcode-type-desc').hide();
			$('#shortcode-type-desc-' + $(select).val()).show();

			doAjax('admin-ajax.php?action=vikrestaurants&task=shortcode.params', {
				id: <?php echo (int) $this->shortcode['id']; ?>,
				type: $(select).val()
			}, (html) => {
				// destroy current chosen just before updating the params form
				$('.shortcode-params select').chosen('destroy');
				$('.shortcode-params').html(html);

				validator.registerFields('.shortcode-params .required');

				$('.shortcode-params select').chosen();
			});
		}

		$(function() {
			validator = new JFormValidator('#adminForm');

			Joomla.submitbutton = (task) => {
				if (task.indexOf('shortcode.save') == -1 || validator.validate()) {
					Joomla.submitform(task, document.adminForm);
				}
			}
		});
	})(jQuery, window);
</script>