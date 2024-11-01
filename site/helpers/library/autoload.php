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

/**
 * The correct autoloader has been placed within the src/ folder.
 * This file is meant to be a backward compatibility for all those
 * modules and plugins that still point to this file.
 * 
 * @deprecated 1.10  Load the correct file instead.
 */
require_once dirname(__FILE__) . '/../src/autoload.php';
