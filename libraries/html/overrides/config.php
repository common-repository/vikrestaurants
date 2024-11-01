<?php
/** 
 * @package     VikRestaurants - Libraries
 * @subpackage  html.overrides
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

$view = $displayData['view'];

?>

<div class="config-fieldset">

	<div class="config-fieldset-body">

		<p>
			<?php
			_e(
				'From this section it is possible to override the pages and the layouts of the plugin. Click the button below to open the file manager and start editing the pages.',
				'vikrestaurants'
			);
			?>
		</p>

		<?php
		echo $view->formFactory->createField()
			->type('alert')
			->style('warning')
			->hiddenLabel(true)
			->text(__(
				'Go ahead only if you are able to deal with PHP and HTML code.',
				'vikrestaurants'
			));

		echo $view->formFactory->createField()
			->type('link')
			->href('admin.php?page=vikrestaurants&view=overrides')
			->class('button button-hero')
			->id('vr-overrides-btn')
			->hiddenLabel(true)
			->text(__('Open Overrides Manager', 'vikrestaurants'))
			->control([
				'style' => 'text-align: center;',
			]);
		?>

	</div>

</div>

<script>
	(function($, w) {
		'use strict';

		$(function() {
			$('#vr-overrides-btn').on('click', function(event) {
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