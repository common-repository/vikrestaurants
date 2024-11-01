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

<h3><?php echo JText::translate('VRMANAGERESERVATIONTITLE2'); ?></h3>

<div class="order-fields">

	<!-- Define role to detect the supported hook -->
	<!-- {"rule":"customizer","event":"onDisplayViewTkorderinfo","key":"fields.start","type":"field"} -->

	<?php
	// plugins can use the "fields.start" key to introduce custom
	// HTML before the custom fields
	if (isset($this->addons['fields.start']))
	{
		echo $this->addons['fields.start'];

		// unset fields start form to avoid displaying it twice
		unset($this->addons['fields.start']);
	}

	foreach ($this->customFields as $field)
	{
		$key = $field->getName();

		if (isset($this->order->fields[$key]) && strlen($this->order->fields[$key]))
		{
			?>
			<div class="order-field">
				<label><?php echo $field->langname; ?></label>

				<div class="order-field-value">
					<b><?php echo nl2br($this->order->fields[$key]); ?></b>
				</div>
			</div>
			<?php
		}
	}
	?>

	<!-- Define role to detect the supported hook -->
	<!-- {"rule":"customizer","event":"onDisplayViewTkorderinfo","key":"fields.end","type":"field"} -->

	<?php
	// plugins can use the "fields.end" key to introduce custom
	// HTML next to the specified custom fields
	if (isset($this->addons['fields.end']))
	{
		echo $this->addons['fields.end'];

		// unset fields end form to avoid displaying it twice
		unset($this->addons['fields.end']);
	}
	?>

</div>
