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
$tabs['VRMENUREVIEWS']               = $this->loadTemplate('reviews_details');
$tabs['VRMANAGEREVIEW9']             = $this->loadTemplate('reviews_comment');
$tabs['JGLOBAL_FIELDSET_PUBLISHING'] = $this->loadTemplate('reviews_publishing');

// prepare default icons
$setup->icons['VRMENUREVIEWS']               = 'fas fa-star';
$setup->icons['VRMANAGEREVIEW9']             = 'fas fa-comment';
$setup->icons['JGLOBAL_FIELDSET_PUBLISHING'] = 'fas fa-check-circle';

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewConfigReviews". The event method receives the
 * view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('Reviews', $setup);

// create display data
$data = [];
$data['id']     = 2;
$data['active'] = $this->selectedTab == $data['id'];
$data['tabs']   = array_merge($tabs, $forms);
$data['setup']  = $setup;
$data['hook']   = 'Reviews';

// render configuration pane with apposite layout
echo JLayoutHelper::render('configuration.tabview', $data);
