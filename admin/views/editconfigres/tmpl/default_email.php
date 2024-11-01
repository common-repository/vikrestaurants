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
$tabs['VRE_NOTIFICATIONS_FIELDSET'] = $this->loadTemplate('email_notifications');
$tabs['VRE_TEMPLATES_FIELDSET']     = $this->loadTemplate('email_template');

// prepare default icons
$setup->icons['VRE_NOTIFICATIONS_FIELDSET'] = 'fas fa-bell';
$setup->icons['VRE_TEMPLATES_FIELDSET']     = 'fas fa-palette';

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewConfigresEmail". The event method receives the
 * view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('Email', $setup);

// create display data
$data = [];
$data['id']     = 2;
$data['active'] = $this->selectedTab == $data['id'];
$data['tabs']   = array_merge($tabs, $forms);
$data['setup']  = $setup;
$data['hook']   = 'Email';
$data['suffix'] = 'res';

// render configuration pane with apposite layout
echo JLayoutHelper::render('configuration.tabview', $data);
