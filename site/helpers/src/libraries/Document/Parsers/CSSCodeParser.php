<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Document\Parsers;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Document\CodeParser;

/**
 * Parses the CSS codes and returns an associative array containing the rules
 * for each specified selector.
 * 
 * @since 1.9
 */
class CSSCodeParser implements CodeParser
{
	/**
	 * @inheritDoc
	 */
	public function parse(string $code)
    {
        $rules = [];

        // Extract all CSS rules with a single regex.
        // A selector can start with a "." (class) or "#" (ID).
        // Example: "selector-node1_UPPERCASE".
        // Pseudo elements are ignored by the regex, such as ":hover" or ":not()".
        // The comma and the space are used to fetch contiguous selectors.
        if (!preg_match_all("/([#.a-zA-Z0-9\-_,\s]+){\s*(.*?)\s*}/s", $code, $matches))
        {
            return $rules;
        }

        // calculate the number of statements found
        $statementsCount = count($matches[0]);

        for ($i = 0; $i < $statementsCount; $i++)
        {
            $selector   = $matches[1][$i];
            $properties = $matches[2][$i];

            // extract all the selectors
            $selectors = array_filter(array_map('trim', preg_split("/\s*,\s*/", $selector)));

            if (!$selectors)
            {
                // invalid selector...
                continue;
            }

            // get rid of any comments
            $properties = preg_replace("/\/\*(.*?)\*\//s", '', $properties);

            // map the properties
            if (!preg_match_all("/([a-zA-Z\-]+)\s*:\s*(.*?);/s", $properties, $propertiesMatch))
            {
                // no properties found...
                continue;
            }

            // calculate the number of properties found
            $propertiesCount = count($propertiesMatch[0]);

            for ($j = 0; $j < $propertiesCount; $j++)
            {
                $key = $propertiesMatch[1][$j];
                $val = $propertiesMatch[2][$j];

                foreach ($selectors as $selector)
                {
                    if (!isset($rules[$selector]))
                    {
                        // init selector lookup
                        $rules[$selector] = [];
                    }

                    // register the property for the current selector
                    $rules[$selector][$key] = $val;
                }
            }
        }

        return $rules;
    }
}
