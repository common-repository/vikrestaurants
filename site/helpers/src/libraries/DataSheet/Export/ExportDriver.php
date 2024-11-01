<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\DataSheet\Export;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\DataSheet\DataSheet;

/**
 * Interface for the drivers able to export a datasheet.
 * 
 * @since 1.9
 */
interface ExportDriver
{
	/**
	 * Generates an exportable string starting from the provided datasheet.
	 * 
	 * @param   DataSheet  $dataSheet  The datasheet holding the data to export.
	 * 
	 * @return  string
	 */
	public function generate(DataSheet $dataSheet);

    /**
     * Downloads a file starting from the provided datasheet.
     * 
     * @param   DataSheet  $dataSheet  The datasheet holding the data to export..
     * 
     * @return  void
     */
    public function download(DataSheet $dataSheet);
}
