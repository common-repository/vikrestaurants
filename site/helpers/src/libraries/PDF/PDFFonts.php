<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\PDF;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * PDF fonts helper class.
 *
 * @since 1.9
 */
class PDFFonts
{
	/**
	 * Returns a list containing all the font families that can be used while
	 * generating PDF pages.
	 * 
	 * @return  string[]  A list of fonts.
	 */
	public static function getSupportedFamilies()
	{
		// set up default fonts
		$fonts = [
			'courier',
			'dejavusans',
			'helvetica',
			'times',
		];

		/**
		 * Fires an event to load additional font families while generating PDF pages.
		 * 
		 * @param   array  &$fonts  A list of supported font families.
		 * 
		 * @return  void
		 * 
		 * @since   1.9
		 */
		\VREFactory::getPlatform()->getDispatcher()->trigger('onLoadSupportedFontsPDF', [&$fonts]);

		// take only the fonts that are actually listed under the fonts folder
		$fonts = array_filter(array_unique($fonts), function($font)
		{
			return \JFile::exists(VREHELPERS . '/pdf/tcpdf/fonts/' . $font . '.php');
		});

		// reset array keys
		return array_values($fonts);
	}

	/**
	 * Checks whether the specified font is supported.
	 * 
	 * @param   string  $font  The font family.
	 * 
	 * @return  bool    True if supported, false otherwise.
	 */
	public static function isSupported(string $font)
	{
		// make sure the specified font is within the list of supported fonts
		return in_array(
			strtolower($font),
			static::getSupportedFamilies()
		);
	}
}
