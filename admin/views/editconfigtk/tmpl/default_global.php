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
$tabs['VRE_SHOP_FIELDSET']        = $this->loadTemplate('global_shop');
$tabs['VRE_PURCHASE_FIELDSET']    = $this->loadTemplate('global_purchase');
$tabs['VRCONFIGFIELDSETDELIVERY'] = $this->loadTemplate('global_delivery');
$tabs['VRCONFIGFIELDSETTAXES']    = $this->loadTemplate('global_taxes');
$tabs['VRE_COLUMNS_FIELDSET']     = $this->loadTemplate('global_columns');

// prepare default icons
$setup->icons['VRE_SHOP_FIELDSET']        = 'fas fa-store';
$setup->icons['VRE_PURCHASE_FIELDSET']    = 'fas fa-shopping-basket';
$setup->icons['VRCONFIGFIELDSETDELIVERY'] = 'fas fa-truck';
$setup->icons['VRCONFIGFIELDSETTAXES']    = 'fas fa-calculator';
$setup->icons['VRE_COLUMNS_FIELDSET']     = 'fas fa-columns';

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewConfigtkGlobal". The event method receives the
 * view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('Global', $setup);

// create display data
$data = [];
$data['id']     = 1;
$data['active'] = $this->selectedTab == $data['id'];
$data['tabs']   = array_merge($tabs, $forms);
$data['setup']  = $setup;
$data['hook']   = 'Global';
$data['suffix'] = 'tk';

// render configuration pane with apposite layout
echo JLayoutHelper::render('configuration.tabview', $data);
