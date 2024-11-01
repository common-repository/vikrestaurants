<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Taxing;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Helper class used to translate the taxes and the rules.
 *
 * @since 1.9
 */
class TaxesTranslator
{
	/** @var Tax */
	protected $tax;

	/** @var mixed */
	protected $translator;

	/**
	 * Class constructor.
	 * 
	 * @param  Tax  $tax
	 */
	public function __construct(Tax $tax, $translator = null)
	{
		$this->tax = $tax;

		if ($translator)
		{
			// use the provided translator
			$this->translator = $translator;
		}
		else
		{
			// use the default translator
			$this->translator = \VREFactory::getTranslator();
		}
	}

	/**
	 * Translates the provided tax and all the attached rules.
	 *
	 * @param   string|null  $tag  The language tag to use for translations.
	 *
	 * @return  Tax
	 */
	public function translate(string $tag = null)
	{
		if (!$tag)
		{
			// get current language tag
			$tag = \JFactory::getLanguage()->getTag();
		}

		// register a clone to avoid translating a shared object
		$tax = clone $this->tax;

		// translate tax details
		$taxLang = $this->translator->translate('tax', $tax->get('id'), $tag);

		if ($taxLang)
		{
			// update name with translation found
			$tax->set('name', $taxLang->name);
		}

		$rule_ids = [];

		// iterate rules
		foreach ($tax->getRules() as $rule)
		{
			// translate tax rule details
			$taxRuleLang = $this->translator->translate('taxrule', $rule->get('id'), $tag);

			if ($taxRuleLang)
			{
				// update name with translation found
				$rule->set('name', $taxRuleLang->name);

				// JSON decode breakdown translation
				$breakdownLang = $taxRuleLang->breakdown ? json_decode($taxRuleLang->breakdown, true) : array();

				if ($rule->get('breakdown') && $breakdownLang)
				{
					$breakdown = $rule->get('breakdown', null);

					if (is_string($breakdown))
					{
						// decode original breakdown
						$breakdown = json_decode($breakdown);
					}

					foreach (($breakdown ?: []) as $i => $bd)
					{
						// replace name with translation only if not empty and we didn't receive
						// an array, which would mean that we have the default BD
						if (!empty($breakdownLang[$bd->id]) && is_string($breakdownLang[$bd->id]))
						{
							$bd->name = $breakdownLang[$bd->id];
						}

						// update array
						$breakdown[$i] = $bd;
					}

					$rule->set('breakdown', $breakdown);
				}
			}
		}

		// return translated tax
		return $tax;
	}
}
