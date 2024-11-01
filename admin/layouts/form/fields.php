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

$fields = !empty($displayData['fields']) ? $displayData['fields'] : [];
$params = !empty($displayData['params']) ? $displayData['params'] : [];
$prefix = !empty($displayData['prefix']) ? $displayData['prefix'] : '';

if (count($fields))
{
	/** @var E4J\VikRestaurants\Platform\Form\FormFactoryInterface */
	$formFactory = VREFactory::getPlatform()->getFormFactory();

	foreach ($fields as $key => $f)
	{
		if (is_string($f))
		{
			/**
			 * String provided, directly echo the HTML.
			 * 
			 * @since 1.7.4
			 */
			echo $f;
			continue;
		}

		if (isset($params[$key]))
		{
			if ($f['type'] !== 'checkbox')
			{
				// always overwrite value with the given one
				$f['value'] = $params[$key];
			}
			else
			{
				// toggle checkbox
				$f['checked'] = $params[$key];
			}
		}

		if (!isset($f['value']) || $f['value'] === '')
		{
			if (!empty($f['default']))
			{
				if ($f['type'] !== 'checkbox')
				{
					// use default value
					$f['value'] = $f['default'];
				}
				else if (!isset($f['checked']))
				{
					// toggle checkbox
					$f['checked'] = $f['default'];
				}
			}
		}

		if (!empty($f['help']))
		{
			// register help within description
			$f['description'] = $f['help'];
		}

		if (!empty($f['label']) && strpos($f['label'], '//') !== false)
		{
			// extract help string from label
			$_label_arr = explode('//', $f['label']);
			// trim trailing colon
			$f['label'] = str_replace(':', '', $_label_arr[0]);
			// overwrite field description
			$f['description'] = $_label_arr[1];
		}

		if (empty($f['name']))
		{
			$f['name'] = $key;
		}

		if ($prefix)
		{
			// add prefix before name
			$f['name'] = $prefix . $f['name'];
			
			// add prefix before ID
			if (!empty($f['id']))
			{
				$f['id'] = $prefix . $f['id'];
			}
		}

		if ($f['type'] == 'custom')
		{
			$f['type'] = !empty($f['html']) ? 'html' : 'separator';
		}
		else if ($f['type'] == 'checkbox' && !empty($f['value']))
		{
			$f['checked'] = true;
		}
		else if ($f['type'] == 'calendar')
		{
			$f['type'] = 'date';
		}

		if ($f['type'] == 'password' && isset($f['toggle']))
		{
			// enable/disable password/text toggle button
			$f['toggle'] = (bool) $f['toggle'];
		}

		// render field
		$field = $formFactory->createField($f);

		if (!empty($f['renderer']))
		{
			// use the provided renderer
			echo $field->render($f['renderer']);
		}
		else
		{
			// default field rendering
			echo $field->render();
		}
	}
}
else
{
	// no parameters found
	echo VREApplication::getInstance()->alert(JText::translate('VRMANAGEPAYMENT9'));
}
