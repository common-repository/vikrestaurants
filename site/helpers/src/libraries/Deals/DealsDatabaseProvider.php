<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Deals;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Collection\Providers\DatabaseProvider;
use E4J\VikRestaurants\Platform\Dispatcher\DispatcherInterface;

/**
 * Interface used to provide a dataset of deals into a collection.
 * 
 * @since 1.9
 */
class DealsDatabaseProvider extends DatabaseProvider
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

        // translate the fetched deals
        $this->translate($rows);

        /**
         * Trigger hook to allow external plugins to manipulate the list of
         * available deals through this helper class.
         *
         * @param   object[]  &$rows  An array of deals.
         *
         * @return  void
         *
         * @since   1.9
         */
        $this->dispatcher->trigger('onBeforeRegisterTakeAwayDeals', [&$rows]);

        return $rows;
    }

    /**
     * @inheritDoc
     */
    protected function getQuery()
    {
        $query = $this->db->getQuery(true);

        // query to load all the supported columns
        $columns = $this->db->getTableColumns('#__vikrestaurants_takeaway_deal');

        // select all columns from deals table
        foreach ($columns as $field => $type)
        {
            $query->select($this->db->qn('d.' . $field));
        }

        $query->from($this->db->qn('#__vikrestaurants_takeaway_deal', 'd'));
        $query->where(1);

        // group records since the query might use aggregators
        $query->group($this->db->qn('d.id'));
        // always sort deals by ascending ordering
        $query->order($this->db->qn('d.ordering') . ' ASC');

        /**
         * Trigger hook to allow external plugins to manipulate the query used
         * to load the deals through this helper class.
         *
         * @param   mixed  &$query  A query builder object.
         *
         * @return  void
         *
         * @since   1.9
         */
        $this->dispatcher->trigger('onBeforeQueryTakeAwayDeals', [&$query]);

        return $query;
    }

    /**
     * @inheritDoc
     */
    protected function map(object $item)
    {
        return new Deal($item);
    }

    /**
     * Translates the specified deals.
     *
     * @param   object[]  $deals  The records to translate.
     *
     * @return  void
     */
    protected function translate(array $deals)
    {
        // do not proceed in case multi-lingual feature is turned off
        if (!\VikRestaurants::isMultilanguage() || !count($deals))
        {
            return;
        }

        $ids = [];

        foreach ($deals as $deal)
        {
            $ids[] = $deal->id;
        }

        // get translator
        $translator = \VREFactory::getTranslator();

        // pre-load deals translations
        $dealsLang = $translator->load('tkdeal', array_unique($ids), $this->langtag);

        // apply translations
        foreach ($deals as $deal)
        {
            // get deal translation
            $tx = $dealsLang->getTranslation($deal->id, $this->langtag);

            if ($tx)
            {
                // apply translations
                $deal->name        = \JText::translate($tx->name);
                $deal->description = $tx->description;
            }
        }
    }
}
