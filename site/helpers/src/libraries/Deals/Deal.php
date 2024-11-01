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

use E4J\VikRestaurants\Collection\Item;

/**
 * VikRestaurants take-away deal holder.
 *
 * @since 1.9
 */
class Deal extends Item
{
    /**
     * inheritDoc
     */
    public function __construct($data)
    {
        parent::__construct($data);

        // JSON decode working shifts
        $shifts = (array) json_decode($this->get('shifts', ''));
        $this->set('shifts', $shifts);

        // JSON decode working shifts
        $params = (object) json_decode($this->get('params', ''));
        $this->set('params', $params);
    }

    /**
     * Returns the working days for which the deal is available.
     * 
     * @return  int[]
     */
    public function getDays()
    {
        $days = $this->get('days', null);

        if ($days === null)
        {
            $db = \JFactory::getDbo();

            // fetch available days
            $query = $db->getQuery(true)
                ->select($db->qn('id_weekday'))
                ->from($db->qn('#__vikrestaurants_takeaway_deal_day_assoc'))
                ->where($db->qn('id_deal') . ' = ' . (int) $this->get('id', 0));

            $db->setQuery($query);
            $days = array_map('intval', $db->loadColumn());

            if (!$days)
            {
                // no days specified, therefore it is available for any day of the week
                $days = range(0, 6);
            }

            $this->set('days', $days);
        }

        return $days;
    }

    /**
     * Returns the target products configured for this deal.
     * 
     * @param   bool   $asArray  Whether the array should contain objects (false) or
     *                           associative arrays (true).
     * 
     * @return  array
     */
    public function getProducts(bool $asArray = false)
    {
        $products = $this->get('products', null);

        if ($products === null)
        {
            $products = [];

            $db = \JFactory::getDbo();

            // recover targets
            $query = $db->getQuery(true);

            $query->select('*')
                ->from($db->qn('#__vikrestaurants_takeaway_deal_product_assoc'))
                ->where($db->qn('id_deal') . ' = ' . (int) $this->get('id'));

            $db->setQuery($query);

            foreach ($db->loadObjectList() as $product)
            {
                $product->params = json_decode((string) $product->params);

                // register product only if published
                if (($product->params->published ?? 1))
                {
                    $products[] = $product;
                }
            }

            $this->set('products', $products);
        }

        if ($asArray)
        {
            // convert objects into arrays
            $products = array_map(function($p)
            {
                return (array) $p;
            }, $products);
        }

        return $products;
    }

    /**
     * Returns the gift products configured for this deal.
     * 
     * @param   bool   $asArray  Whether the array should contain objects (false) or
     *                           associative arrays (true).
     * 
     * @return  array
     */
    public function getGifts(bool $asArray = false)
    {
        $gifts = $this->get('gifts', null);

        if ($gifts === null)
        {
            $gifts = [];

            $db = \JFactory::getDbo();

            // recover gifts
            $query = $db->getQuery(true);

            $query->select('g.*');
            $query->select($db->qn('p.name', 'product_name'));
            $query->select($db->qn('p.price', 'product_price'));
            $query->select($db->qn('p.ready'));
            $query->select($db->qn('p.id_takeaway_menu'));
            $query->select($db->qn('o.name', 'option_name'));
            $query->select($db->qn('o.inc_price', 'option_price'));

            $query->from($db->qn('#__vikrestaurants_takeaway_deal_free_assoc', 'g'));
            $query->leftjoin($db->qn('#__vikrestaurants_takeaway_menus_entry', 'p') . ' ON ' . $db->qn('g.id_product') . ' = ' . $db->qn('p.id'));
            $query->leftjoin($db->qn('#__vikrestaurants_takeaway_menus_entry_option', 'o') . ' ON ' . $db->qn('g.id_option') . ' = ' . $db->qn('o.id'));

            $query->where($db->qn('g.id_deal') . ' = ' . (int) $this->get('id'));

            $db->setQuery($query);

            foreach ($db->loadObjectList() as $gift)
            {
                $gift->params = json_decode((string) $gift->params);

                // register gift only if published
                if (($gift->params->published ?? 1))
                {
                    $gifts[] = $gift;
                }
            }

            $this->set('gifts', $gifts);
        }

        if ($asArray)
        {
            // convert objects into arrays
            $gifts = array_map(function($p)
            {
                return (array) $p;
            }, $gifts);
        }

        return $gifts;
    }

    /**
     * Returns the details of the given product.
     * 
     * @param   int  $id        The product ID.
     * @param   int  $idOption  the product variatio ID.
     * 
     * @return  object|null
     */
    public function getProduct(int $id, int $idOption = 0)
    {
        $products = $this->getProducts();

        foreach ($products as $prod)
        {
            if ($prod->id_product == $id && ($idOption <= 0 || $prod->id_option <= 0 || $idOption == $prod->id_option))
            {
                // product found
                return clone $prod;
            }
        }

        // product not found
        return null;
    }

    /**
     * @inheritDoc
     *
     * @see \ArrayAccess
     * 
     * @deprecated 1.10  For BC we need to adjust the products and the gifts when requested
     *                   as array attributes.
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        if ($offset === 'products')
        {
            $products = [];

            foreach ($this->getProducts(true) as $prod)
            {
                $prod['required'] = $prod['params']->required ?? 0;
                $prod['quantity'] = $prod['params']->units ?? 1;

                $products[] = $prod;
            }

            return $products;
        }
        else if ($offset === 'gifts')
        {
            $gifts = [];

            foreach ($this->getGifts(true) as $gift)
            {
                $gift['quantity'] = $gift['params']->units ?? 1;

                $gifts[] = $gift;
            }

            return $gifts;
        }
        else if (in_array($offset, ['percentot', 'amount']))
        {
            $products = $this->getProducts(true);
            $prod = array_shift($products);

            $this->set('percentot', $prod['params']->percentot ?? 1);
            $this->set('amount', $prod['params']->amount ?? 0);
        }

        return parent::offsetGet($offset);
    }
}
