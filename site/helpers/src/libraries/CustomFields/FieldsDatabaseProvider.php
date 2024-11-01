<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\CustomFields;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Collection\Providers\DatabaseProvider;
use E4J\VikRestaurants\Platform\Dispatcher\DispatcherInterface;

/**
 * Interface used to provide a dataset of custom fields into a collection.
 * 
 * @since 1.9
 */
class FieldsDatabaseProvider extends DatabaseProvider
{
    /** @var string */
    protected $langtag;

    /** @var DispatcherInterface */
    protected $dispatcher;

    /**
     * Class constructor.
     * 
     * @param  JDatabaseDriver      $db
     * @param  string               $langtag
     * @param  DispatcherInterface  $dispatcher
     */
    public function __construct($db, string $langtag = null, DispatcherInterface $dispatcher = null)
    {
        parent::__construct($db);

        if ($langtag)
        {
            $this->langtag = $langtag;
        }
        else
        {
            // language tag not provided, use the default one
            $this->langtag = \JFactory::getLanguage()->getTag();
        }

        if ($dispatcher)
        {
            $this->dispatcher = $dispatcher;
        }
        else
        {
            // dispatcher not provider, use the default one
            $this->dispatcher = \VREFactory::getPlatform()->getDispatcher();
        }
    }

    /**
     * @inheritDoc
     */
    protected function loadRecords()
    {
        // load records from the database
        $rows = parent::loadRecords();

        // translate the fetched custom fields
        $this->translate($rows);

        /**
         * Trigger hook to allow external plugins to manipulate the list of
         * supported custom fields through this helper class.
         *
         * @param   object[]  &$rows  An array of custom fields.
         *
         * @return  void
         *
         * @since   1.9
         */
        $this->dispatcher->trigger('onBeforeRegisterCustomFields', [&$rows]);

        return $rows;
    }

    /**
     * @inheritDoc
     */
    protected function getQuery()
    {
        $query = $this->db->getQuery(true);

        // query to load all the supported columns
        $columns = $this->db->getTableColumns('#__vikrestaurants_custfields');

        // select all columns from custom fields table
        foreach ($columns as $field => $type)
        {
            $query->select($this->db->qn('c.' . $field));
        }

        $query->from($this->db->qn('#__vikrestaurants_custfields', 'c'));
        $query->where(1);

        // group records since the query might use aggregators
        $query->group($this->db->qn('c.id'));
        // always sort fields by ascending ordering
        $query->order($this->db->qn('c.ordering') . ' ASC');

        /**
         * Trigger hook to allow external plugins to manipulate the query used
         * to load the custom fields through this helper class.
         *
         * @param   mixed  &$query  A query builder object.
         *
         * @return  void
         *
         * @since   1.9
         */
        $this->dispatcher->trigger('onBeforeQueryCustomFields', [&$query]);

        return $query;
    }

    /**
     * @inheritDoc
     */
    protected function map(object $item)
    {
        return Field::getInstance($item);
    }

    /**
     * Translates the specified custom fields.
     * The translation of the name will be placed in a different column 'langname'. 
     * The original 'name' column won't be altered.
     *
     * @param   object[]  $fields  The records to translate.
     *
     * @return  void
     */
    protected function translate(array $fields)
    {
        $ids = [];

        /**
         * Added support for missing fields in case the multilingual
         * feature is disabled.
         *
         * @since 1.8
         */
        foreach ($fields as $field)
        {
            if (!isset($field->_choose))
            {
                // keep original 'choose' for select
                $field->_choose  = $field->choose;
            }

            if (!isset($field->langname))
            {
                // backward compatibility for old translation technique
                $field->langname = \JText::translate($field->name);
            }

            $ids[] = $field->id;
        }

        // do not proceed in case multi-lingual feature is turned off
        if (!\VikRestaurants::isMultilanguage() || !count($fields))
        {
            return;
        }

        // get translator
        $translator = \VREFactory::getTranslator();

        // pre-load fields translations
        $fieldsLang = $translator->load('custfield', array_unique($ids), $this->langtag);

        // apply translations
        foreach ($fields as $field)
        {
            // get custom field translation
            $tx = $fieldsLang->getTranslation($field->id, $this->langtag);

            if ($tx)
            {
                // apply translations
                $field->langname    = \JText::translate($tx->name);
                $field->description = $tx->description;
                $field->poplink     = $tx->poplink;
                $field->choose      = $tx->choose;
            }
        }
    }
}
