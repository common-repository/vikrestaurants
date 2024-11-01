<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\CustomFields\Filters;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Collection\CollectionFilter;
use E4J\VikRestaurants\Collection\Item;
use E4J\VikRestaurants\CustomFields\Field;

/**
 * Filters the custom fields to obtain only the ones with matching language tag.
 * 
 * @since 1.9
 */
class LanguageFilter implements CollectionFilter
{
    /** @var string */
    protected $locale;

    /**
     * Class constructor.
     * 
     * @param  string  $language  The language tag to set.
     *                            Use '*' to ignore this filter.
     */
    public function __construct(string $language = 'auto')
    {
        if (strcasecmp($language, 'auto') == 0 || $language === '')
        {
            // use the current language tag
            $this->locale = \JFactory::getLanguage()->getTag();
        }
        else
        {
            // use the specified language tag
            $this->locale = $language;
        }
    }

    /**
     * @inheritDoc
     * 
     * @throws  \InvalidArgumentException  Only Field instances are accepted.
     */
    public function match(Item $item)
    {
        if (!$item instanceof Field)
        {
            // can handle only objects that inherit the Field class
            throw new \InvalidArgumentException('Field item expected, ' . get_class($item) . ' given');
        }

        // filter the custom fields by language tag
        if ($this->locale === '*')
        {
            // ignore language filter
            return true;
        }

        // fetch custom field language tag
        $fieldLang = $item->get('tag', '*');

        // make sure the field language matches the specified one
        return $fieldLang === $this->locale || $fieldLang === '*';
    }
}
