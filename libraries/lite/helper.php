<?php
/** 
 * @package     VikRestaurants - Libraries
 * @subpackage  system
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Helper implementor used to apply the restrictions of the LITE version.
 *
 * @since 1.2.4
 */
class VikRestaurantsLiteHelper
{
	/**
	 * The platform application instance.
	 * 
	 * @var JApplication
	 */
	private $app;

	/**
	 * The platform database instance.
	 * 
	 * @var JDatabase
	 */
	private $db;

	/**
	 * Class constructor.
	 */
	public function __construct()
	{
		$this->app = JFactory::getApplication();
		$this->db  = JFactory::getDbo();
	}

	/**
	 * Helper method used to disable the capabilities according
	 * to the restrictions applied by the LITE version.
	 * 
	 * @param 	array   $capabilities  Array of key/value pairs where keys represent a capability name and boolean values
	 *                                 represent whether the role has that capability.
	 * 
	 * @return 	array   The resulting capabilities lookup.
	 */
	public function restrictCapabilities(array $capabilities)
	{
		switch ($this->app->input->get('view'))
		{
			case 'customf':
			case 'tkmenuattr':
			case 'rescodes':
			case 'rescodesorder':
			case 'tkreservations':
				// disable both CREATE and EDIT capabilities
				$capabilities['com_vikrestaurants_create'] = false;
				$capabilities['com_vikrestaurants_edit']   = false;
				break;

			case 'reservations':
				// disable only EDIT capability
				$capabilities['com_vikrestaurants_edit'] = false;
				break;
		}

		return $capabilities;
	}

	/**
	 * Helper method used to display an advertsing banner while trying
	 * to reach a page available only in the PRO version.
	 * 
	 * @return 	void
	 */
	public function displayBanners()
	{
		$input = $this->app->input;

		// get current view
		$view = $input->get('view');

		// define list of pages not supported by the LITE version
		$lookup = array(
			'acl',
			'customers',
			'invoices',
			'managemap',
			'operators',
			'reviews',
			'tkdeals',
		);

		// check whether the view is supported
		if (!$view || !in_array($view, $lookup))
		{
			return;
		}

		// display menu before unsetting the view
		RestaurantsHelper::printMenu();

		// use a missing view to display blank contents
		$input->set('view', 'liteview');

		// display LITE banner
		echo JLayoutHelper::render('html.license.lite', array('view' => $view));
	}

	/**
	 * Helper function used to auto-redirect the customers to the tables management
	 * page while trying to access the map.
	 * 
	 * @return 	void
	 */
	public function preventMapAccessFromWizard()
	{
		$input = $this->app->input;

		// map disabled, go to tables creation instead
		if ($input->get('task') == 'map.edit' && $input->getBool('wizard'))
		{
			$input->set('task', 'table.add');
			$input->set('id_room', $input->get('selectedroom', 0, 'uint'));
		}
	}

	/**
	 * Helper function used to auto-redirect the customers to the creation page of a
	 * new reservation while trying to manually edit an existing one.
	 * 
	 * @return 	void
	 */
	public function preventEditReservationAccess()
	{
		$input = $this->app->input;

		// edit disabled, reach add reservation instead
		if ($input->get('task') == 'reservation.edit')
		{
			$input->set('task', 'reservation.add');
			$input->set('cid', []);
		}
	}

	/**
	 * Helper method used to pre-load the resources needed by the LITE version.
	 * 
	 * @return 	void
	 */
	public function includeLiteAssets()
	{
		JFactory::getDocument()->addStyleSheet(
			VIKRESTAURANTS_CORE_MEDIA_URI . 'css/lite.css',
			['version' => VIKRESTAURANTS_SOFTWARE_VERSION],
			['id' => 'vre-lite-style']
		);
	}

	/**
	 * Helper method used to remove all wizard steps that refer to
	 * a feature that is not supported by the LITE version.
	 * 
	 * @param 	VREWizard  $wizard  The wizard instance.
	 * 
	 * @return 	void
	 */
	public function removeWizardSteps($wizard)
	{
		// remove payments step from LITE version
		$wizard->removeStep('payments');
	}

	/**
	 * Helper method used to display the scripts and the HTML needed to
	 * allow the management of the terms-of-service custom field.
	 * 
	 * @param 	JView  $view  The view instance.
	 * 
	 * @return 	void
	 */
	public function displayTosFieldManagementForm($view)
	{
		// iterate all custom fields
		foreach ($view->rows as $cf)
		{
			// check if we have a checkbox field
			if ($cf['type'] == 'checkbox')
			{
				// use scripts to manage ToS
				echo JLayoutHelper::render('html.managetos.script', array('field' => $cf));
			}
		}
	}

	/**
	 * Helper method used to intercept the custom request used to update
	 * the terms-of-service custom field.
	 * 
	 * @return 	void
	 */
	public function listenTosFieldSavingTask()
	{
		$input = $this->app->input;

		// check if we should save the TOS field
		if ($input->get('task') == 'customf.savetosajax')
		{
			$user = JFactory::getUser();

			$args = array();
			$args['name']    = $input->get('name', '', 'raw');
			$args['poplink'] = $input->get('poplink', '', 'string');
			$args['id']      = $input->get('id', 0, 'uint');

			if (!$args['id'])
			{
				UIErrorFactory::raiseError(403, JText::translate('JERROR_ALERTNOAUTHOR'));
			}

			// get record table
			$field = JTableVRE::getInstance('customf', 'VRETable');

			// try to save arguments
			if (!$field->save($args))
			{
				// get string error
				$error = $field->getError(null, true);
				$error = JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error);

				UIErrorFactory::raiseError(403, $error);
			}

			echo json_encode($field->getProperties());
			wp_die();
		}
	}

	/**
	 * Helper method used to detach the Save button from the toolbar, since
	 * the edit feature is not supported. Renames also the Save & Close button.
	 * 
	 * @return 	void
	 */
	public function adjustToolbarFromReservationManagement()
	{
		$toolbar = JToolbar::getInstance();

		// load the list of registered buttons
		$buttons = $toolbar->getButtons();

		// iterate all buttons
		foreach ($buttons as $btn)
		{
			// access button properties
			$options = $btn->getDisplayData();

			if ($options['id'] === 'jbutton-reservation-save' || $options['id'] === 'jbutton-reservation-saveclose')
			{
				// delete button from toolbar
				$toolbar->removeButton($btn);
			}
		}

		// register at the beginning a new save button that automatically goes back to the list
		$toolbar->prependButton('Standard', 'apply', JText::translate('VRSAVE'), 'reservation.saveclose', false);
	}

	/**
	 * Helper method used to replace the HREF defined by the links used to access the
	 * management page of the bills. Since the LITE version doesn't support the bills,
	 * we need to replace all those links with a direct task to toggle the status of
	 * the bill (open/close).
	 * 
	 * @return 	void
	 */
	public function replaceBillManagementLink($view)
	{
		$lookup = [];

		// create an ID-STATE lookup for the fetched reservations
		foreach ($view->rows as $row)
		{
			$lookup[$row['id']] = $row['bill_closed'] == 1 ? 0 : 1; 
		}

		$lookup = json_encode($lookup);

		JFactory::getDocument()->addScriptDeclaration(
<<<JS
(function($) {
	'use strict';

	$(function() {
		const billStateLookup = {$lookup};

		// iterate all links inside the table
		$('#adminForm table td a').not('[href^="javascript"]').each(function() {
			// extract the HREF of the current link
			let href = $(this).attr('href');

			// check whether the links points to the management of the bill
			if (href.match(/task=reservation.editbill/)) {
				// replace task
				href = href.replace(/task=reservation.editbill/, 'task=reservation.changebill');

				// extract ID from URL
				let id = href.match(/cid\[\]=(\d*)/);
				id = parseInt(id.pop());

				// append missing state
				href += '&state=' + billStateLookup[id];

				// update link href
				$(this).attr('href', href);
			}
		});
	});
})(jQuery);
JS
		);
	}

	/**
	 * Applies a runtime restriction to limit the maximum number of restaurant menus
	 * that can be created.
	 * 
	 * @param 	boolean  $save   False to abort saving.
	 * @param 	mixed 	 &$src 	 The array/object to bind.
	 * @param 	JTable   $table  The table instance.
	 * 
	 * @return 	boolean
	 */
	public function applyRestaurantMenuSaveRestrictions($save, &$data, $table)
	{
		if (!empty($data['id']))
		{
			return $save;
		}

		$dbo = $this->db;

		$q = $this->db->getQuery(true)
			->select('COUNT(1)')
			->from($this->db->qn('#__vikrestaurants_menus'));

		$this->db->setQuery($q, 0, 1);
		$this->db->execute();

		if ($this->db->getNumRows() && (int) $this->db->loadResult() >= static::MAX_RESTAURANT_MENUS)
		{
			$save = false;
			$table->setError(
				__('You already reached the maximum number of menus.', 'vikrestaurants') .
				'<br />' . __('Please consider to purchase the <b>PRO</b> version to remove this restriction.', 'vikrestaurants')
			);
		}

		return $save;
	}

	/**
	 * Applies a runtime restriction to limit the maximum number of restaurant products
	 * that can be created.
	 * 
	 * @param 	boolean  $save   False to abort saving.
	 * @param 	mixed 	 &$src 	 The array/object to bind.
	 * @param 	JTable   $table  The table instance.
	 * 
	 * @return 	boolean
	 */
	public function applyRestaurantProductSaveRestrictions($save, &$data, $table)
	{
		if (!empty($data['id']))
		{
			return $save;
		}

		$dbo = $this->db;

		$q = $this->db->getQuery(true)
			->select('COUNT(1)')
			->from($this->db->qn('#__vikrestaurants_section_product'));

		$this->db->setQuery($q, 0, 1);
		$this->db->execute();

		if ($this->db->getNumRows() && (int) $this->db->loadResult() >= static::MAX_RESTAURANT_PRODUCTS)
		{
			$save = false;
			$table->setError(
				__('You already reached the maximum number of products.', 'vikrestaurants') .
				'<br />' . __('Please consider to purchase the <b>PRO</b> version to remove this restriction.', 'vikrestaurants')
			);
		}

		return $save;
	}

	/**
	 * Applies a runtime restriction to limit the maximum number of take-away menus
	 * that can be created.
	 * 
	 * @param 	boolean  $save   False to abort saving.
	 * @param 	mixed 	 &$src 	 The array/object to bind.
	 * @param 	JTable   $table  The table instance.
	 * 
	 * @return 	boolean
	 */
	public function applyTakeAwayMenuSaveRestrictions($save, &$data, $table)
	{
		if (!empty($data['id']))
		{
			return $save;
		}

		$dbo = $this->db;

		$q = $this->db->getQuery(true)
			->select('COUNT(1)')
			->from($this->db->qn('#__vikrestaurants_takeaway_menus'));

		$this->db->setQuery($q, 0, 1);
		$this->db->execute();

		if ($this->db->getNumRows() && (int) $this->db->loadResult() >= static::MAX_TAKEAWAY_MENUS)
		{
			$save = false;
			$table->setError(
				__('You already reached the maximum number of menus.', 'vikrestaurants') .
				'<br />' . __('Please consider to purchase the <b>PRO</b> version to remove this restriction.', 'vikrestaurants')
			);
		}

		return $save;
	}

	/**
	 * Helper function used to restrict the number of products that can be created
	 * from the management page. The restrictions are applied via javascript in
	 * this case.
	 * 
	 * @param 	JView  $view  The view instance.
	 * 
	 * @return 	void
	 */
	public function applyTakeAwayMenuVisualRestrictions($view)
	{
		$max = static::MAX_TAKEAWAY_PRODUCTS_PER_MENU;

		$alert = addslashes(
			__('You already reached the maximum number of products. It is possible to create up to 7 products per menu.', 'vikrestaurants') . ' ' .
			strip_tags(__('Please consider to purchase the <b>PRO</b> version to remove this restriction.', 'vikrestaurants'))
		);

		JFactory::getDocument()->addScriptDeclaration(
<<<JS
(function($) {
	'use strict';

	$(function() {
		$('#menu-entry-inspector').on('inspector.show', function(event) {
			// count the total number of created products
			const createdItems = $('.delivery-fieldset.vre-card-fieldset').not('#add-delivery-location').length;

			// check if we are creating a new product and the number of existing products is
			// equals or higher than the specified threshold
			if ((SELECTED_INDEX === undefined || SELECTED_INDEX === null) && createdItems >= {$max}) {
				// number exceeded, do not display the inspector and prompt a warning message
				event.stopPropagation();

				setTimeout(() => {
					alert('{$alert}');
				}, 128);

				return false;
			}
		});
	});
})(jQuery);
JS
		);
	}

	/**
	 * Applies a runtime restriction to limit the maximum number of take-away products
	 * that can be created.
	 * 
	 * @param 	boolean  $save   False to abort saving.
	 * @param 	mixed 	 &$src 	 The array/object to bind.
	 * @param 	JTable   $table  The table instance.
	 * 
	 * @return 	boolean
	 */
	public function applyTakeAwayProductSaveRestrictions($save, &$data, $table)
	{
		if (!empty($data['id']))
		{
			return $save;
		}

		$dbo = $this->db;

		$q = $this->db->getQuery(true)
			->select('COUNT(1)')
			->from($this->db->qn('#__vikrestaurants_takeaway_menus_entry'))
			->where($this->db->qn('id_takeaway_menu') . ' = ' . (int) $data['id_takeaway_menu']);

		$this->db->setQuery($q, 0, 1);
		$this->db->execute();

		if ($this->db->getNumRows() && (int) $this->db->loadResult() >= static::MAX_TAKEAWAY_PRODUCTS_PER_MENU)
		{
			$save = false;
			$table->setError(
				__('You already reached the maximum number of products. It is possible to create up to 7 products per menu.', 'vikrestaurants') .
				'<br />' . __('Please consider to purchase the <b>PRO</b> version to remove this restriction.', 'vikrestaurants')
			);
		}

		return $save;
	}

	/**
	 * Helper function used to restrict the number of toppings groups that can be
	 * created from the management page. The restrictions are applied via javascript
	 * in this case.
	 * 
	 * @param 	JView  $view  The view instance.
	 * 
	 * @return 	void
	 */
	public function applyTakeAwayProductVisualRestrictions($view)
	{
		$max = static::MAX_TAKEAWAY_TOPPINGS_GROUPS_PER_PRODUCT;

		$alert = addslashes(
			__('You already reached the maximum number of toppings. It is possible to create only 2 toppings per product.', 'vikrestaurants') . ' ' .
			strip_tags(__('Please consider to purchase the <b>PRO</b> version to remove this restriction.', 'vikrestaurants'))
		);

		JFactory::getDocument()->addScriptDeclaration(
<<<JS
(function($) {
	'use strict';

	$(function() {
		$('#entry-group-inspector').on('inspector.show', function(event) {
			// count the total number of created groups
			const createdItems = $('#groups-card-container .delivery-fieldset.vre-card-fieldset').not('#add-delivery-location').length;

			// check if we are creating a new group and the number of existing groups is
			// equals or higher than the specified threshold
			if ((SELECTED_INDEX === undefined || SELECTED_INDEX === null) && createdItems >= {$max}) {
				// number exceeded, do not display the inspector and prompt a warning message
				event.stopPropagation();

				setTimeout(() => {
					alert('{$alert}');
				}, 128);

				return false;
			}
		});
	});
})(jQuery);
JS
		);
	}

	/**
	 * Applies a runtime restriction to limit the maximum number of take-away toppings
	 * groups that can be created.
	 * 
	 * @param 	boolean  $save   False to abort saving.
	 * @param 	mixed 	 &$src 	 The array/object to bind.
	 * @param 	JTable   $table  The table instance.
	 * 
	 * @return 	boolean
	 */
	public function applyTakeAwayToppingsGroupSaveRestrictions($save, &$data, $table)
	{
		if (!empty($data['id']))
		{
			return $save;
		}

		$dbo = $this->db;

		$q = $this->db->getQuery(true)
			->select('COUNT(1)')
			->from($this->db->qn('#__vikrestaurants_takeaway_entry_group_assoc'))
			->where($this->db->qn('id_entry') . ' = ' . (int) $data['id_entry']);

		$this->db->setQuery($q, 0, 1);
		$this->db->execute();

		if ($this->db->getNumRows() && (int) $this->db->loadResult() >= static::MAX_TAKEAWAY_TOPPINGS_GROUPS_PER_PRODUCT)
		{
			$save = false;
			$table->setError(
				__('You already reached the maximum number of toppings. It is possible to create only 2 toppings per product.', 'vikrestaurants') .
				'<br />' . __('Please consider to purchase the <b>PRO</b> version to remove this restriction.', 'vikrestaurants')
			);
		}

		return $save;
	}

	/**
	 * Applies a runtime restriction to limit the maximum number of take-away delivery
	 * areas that can be created.
	 * 
	 * @param 	boolean  $save   False to abort saving.
	 * @param 	mixed 	 &$src 	 The array/object to bind.
	 * @param 	JTable   $table  The table instance.
	 * 
	 * @return 	boolean
	 */
	public function applyTakeAwayDeliveryAreaSaveRestrictions($save, &$data, $table)
	{
		if (!empty($data['id']))
		{
			return $save;
		}

		$q = $this->db->getQuery(true)
			->select('COUNT(1)')
			->from($this->db->qn('#__vikrestaurants_takeaway_delivery_area'));

		$this->db->setQuery($q, 0, 1);
		$this->db->execute();

		if ($this->db->getNumRows() && (int) $this->db->loadResult() >= static::MAX_TAKEAWAY_DELIVERY_AREAS)
		{
			$save = false;
			$table->setError(
				__('You already reached the maximum number of delivery areas.', 'vikrestaurants') .
				'<br />' . __('Please consider to purchase the <b>PRO</b> version to remove this restriction.', 'vikrestaurants')
			);
		}

		return $save;
	}

	/**
	 * Since the reviews are not supported by the LITE version, we need to remove the
	 * related fieldset from the global configuration of the program.
	 * 
	 * @param 	JView  $view  The view instance.
	 * 
	 * @return 	void
	 */
	public function removeReviewsPanelFromConfiguration($view)
	{
		$document = JFactory::getDocument();

		// hide via CSS first to avoid weird behaviors due to loading delayes
		$document->addStyleDeclaration('#reviews-panel { display: none !important; }');
		// then remove the whole block via JS to prevent issues with the search bar
		$document->addScriptDeclaration(
		<<<JS
(function($) {
	'use strict';

	$(function() {
		$('#reviews-panel').remove();
	});
})(jQuery);
JS
		);
	}

	/**
	 * Since the take-away stocks are not supported by the LITE version, we need
	 * to remove the related fieldset from the take-away configuration of the program.
	 * 
	 * @param 	JView  $view  The view instance.
	 * 
	 * @return 	void
	 */
	public function removeStocksPanelFromConfiguration($view)
	{
		$document = JFactory::getDocument();

		// hide via CSS first to avoid weird behaviors due to loading delayes
		$document->addStyleDeclaration('#stocks-panel { display: none !important; }');
		// then remove the whole block via JS to prevent issues with the search bar
		$document->addScriptDeclaration(
		<<<JS
(function($) {
	'use strict';

	$(function() {
		$('#stocks-panel').remove();
	});
})(jQuery);
JS
		);
	}

	/**
	 * Since the API framework is not supported by the LITE version, we need to
	 * remove the related fieldset from the configuration page of the program.
	 * 
	 * @param 	JView  $view  The view instance.
	 * 
	 * @return 	void
	 */
	public function removeApplicationsTabFromConfiguration($view)
	{
		$document = JFactory::getDocument();

		// hide via CSS first to avoid weird behaviors due to loading delayes
		$document->addStyleDeclaration('#vretabli5, #vretabview5 { display: none !important; }');
		// then remove the whole block via JS
		$document->addScriptDeclaration(
		<<<JS
(function($) {
	'use strict';

	$(function() {
		$('#vretabli5, #vretabview5').remove();
	});
})(jQuery);
JS
		);
	}

	/**
	 * Defines the maximum number of restaurant menus that can be created.
	 * 
	 * @var integer
	 */
	const MAX_RESTAURANT_MENUS = 3;

	/**
	 * Defines the maximum number of restaurant products that can be created.
	 * 
	 * @var integer
	 */
	const MAX_RESTAURANT_PRODUCTS = 20;

	/**
	 * Defines the maximum number of take-away menus that can be created.
	 * 
	 * @var integer
	 */
	const MAX_TAKEAWAY_MENUS = 3;

	/**
	 * Defines the maximum number of take-away products (per menu) that can be created.
	 * 
	 * @var integer
	 */
	const MAX_TAKEAWAY_PRODUCTS_PER_MENU = 7;

	/**
	 * Defines the maximum number of take-away toppings groups (per product) that can be created.
	 * 
	 * @var integer
	 */
	const MAX_TAKEAWAY_TOPPINGS_GROUPS_PER_PRODUCT = 2;

	/**
	 * Defines the maximum number of take-away delivery areas that can be created.
	 * 
	 * @var integer
	 */
	const MAX_TAKEAWAY_DELIVERY_AREAS = 1;
}
