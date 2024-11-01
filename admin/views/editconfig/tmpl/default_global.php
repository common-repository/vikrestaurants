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
$tabs['VRMANAGECONFIGGLOBSECTION1'] = $this->loadTemplate('global_system');
$tabs['VRMANAGECONFIGGLOBSECTION2'] = $this->loadTemplate('global_email');
$tabs['VRMANAGECONFIGGLOBSECTION3'] = $this->loadTemplate('global_currency');
$tabs['GDPR']                       = $this->loadTemplate('global_gdpr');
$tabs['Google']                     = $this->loadTemplate('global_google');

// prepare default icons
$setup->icons['VRMANAGECONFIGGLOBSECTION1'] = 'fas fa-tools';
$setup->icons['VRMANAGECONFIGGLOBSECTION2'] = 'fas fa-envelope';
$setup->icons['VRMANAGECONFIGGLOBSECTION3'] = 'fas fa-wallet';
$setup->icons['GDPR']                       = 'fas fa-gavel';
$setup->icons['Google']                     = 'fab fa-google';

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewConfigGlobal". The event method receives the
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

// render configuration pane with apposite layout
echo JLayoutHelper::render('configuration.tabview', $data);
