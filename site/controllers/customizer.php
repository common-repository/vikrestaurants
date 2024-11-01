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

VRELoader::import('library.mvc.controllers.admin');

/**
 * VikRestaurants customizer view controller.
 *
 * @since 1.9
 */
class VikRestaurantsControllerCustomizer extends VREControllerAdmin
{
	/**
	 * A preview displaying how the custom fields look in the front-end.
	 * 
	 * @return  void
	 */
	public function button_preview()
	{
		?>
		<div style="padding: 20px; margin: 0 auto;">
			<table>
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th style="text-align: center;">Primary</th>
						<th style="text-align: center;">Secondary</th>
						<th style="text-align: center;">Success</th>
						<th style="text-align: center;">Danger</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Small</td>
						<td style="text-align: center; padding: 20px;"><button class="vre-btn primary small">Button</button></td>
						<td style="text-align: center; padding: 20px;"><button class="vre-btn secondary small">Button</button></td>
						<td style="text-align: center; padding: 20px;"><button class="vre-btn success small">Button</button></td>
						<td style="text-align: center; padding: 20px;"><button class="vre-btn danger small">Button</button></td>
					</tr>
					<tr>
						<td>Normal</td>
						<td style="text-align: center; padding: 20px;"><button class="vre-btn primary">Button</button></td>
						<td style="text-align: center; padding: 20px;"><button class="vre-btn secondary">Button</button></td>
						<td style="text-align: center; padding: 20px;"><button class="vre-btn success">Button</button></td>
						<td style="text-align: center; padding: 20px;"><button class="vre-btn danger">Button</button></td>
					</tr>
					<tr>
						<td>Large</td>
						<td style="text-align: center; padding: 20px;"><button class="vre-btn primary large">Button</button></td>
						<td style="text-align: center; padding: 20px;"><button class="vre-btn secondary large">Button</button></td>
						<td style="text-align: center; padding: 20px;"><button class="vre-btn success large">Button</button></td>
						<td style="text-align: center; padding: 20px;"><button class="vre-btn danger large">Button</button></td>
					</tr>
					<tr>
						<td>Big</td>
						<td style="text-align: center; padding: 20px;"><button class="vre-btn primary big">Button</button></td>
						<td style="text-align: center; padding: 20px;"><button class="vre-btn secondary big">Button</button></td>
						<td style="text-align: center; padding: 20px;"><button class="vre-btn success big">Button</button></td>
						<td style="text-align: center; padding: 20px;"><button class="vre-btn danger big">Button</button></td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * A preview displaying how the custom fields look in the front-end.
	 * 
	 * @return  void
	 */
	public function custom_fields_preview()
	{
		$app = JFactory::getApplication();

		// fetch layout style from the request
		$style = $app->input->getString('layout', '');

		/** @var E4J\VikRestaurants\CustomFields\FieldsCollection */
		$customFields = E4J\VikRestaurants\CustomFields\FieldsCollection::getInstance();

		if (VikRestaurants::isTakeAwayEnabled())
		{
			// display the take-away fields as they usually contain more fields
			$customFields = $customFields->filter(new E4J\VikRestaurants\CustomFields\Filters\TakeAwayGroupFilter);
		}
		else
		{
			// take-away disabled, display the fields for the restaurant
			$customFields = $customFields->filter(new E4J\VikRestaurants\CustomFields\Filters\RestaurantGroupFilter);
		}

		// create custom fields renderer for the restaurant group
		$renderer = new E4J\VikRestaurants\CustomFields\FieldsRenderer($customFields);

		// wrap the field within the chosen control style
		$renderer->setControl('form.control.' . $style);

		echo '<div class="custom-fields-' . $style . '" style="padding: 20px; margin: 0 auto;">' . $renderer->display() . '</div>';
	}
}
