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
 * VikRestaurants customizer model.
 * 
 * NOTE: in case the customizer should implement new rules, it is possible to
 * automatically inject them within the custom environment file by using the
 * code below:
 * 
 * $model = JModelVRE::getInstance('customizer');
 * $model->save($model->getItem('environment'));
 * 
 * This because the getItem() method always merge the default settings with the
 * ones modified by the custom environment file. Then, by calling the save method,
 * the missing rules will be automatically saved within the custom environment.
 *
 * @since 1.9
 */
class VikRestaurantsModelCustomizer extends JModelVRE
{
	/**
	 * Returns the path in which the default environment CSS file is stored.
	 * 
	 * @return  string
	 */
	public function getDefaultPath()
	{
		return JPath::clean(VREBASE . '/assets/css/environment.css');
	}

	/**
	 * Returns the URL in which the default environment CSS file is stored.
	 * 
	 * @return  string
	 */
	public function getDefaultUrl()
	{
		return VREASSETS_URI . 'css/environment.css';
	}

	/**
	 * Returns the path in which the custom environment CSS file is stored.
	 * 
	 * @param   string  $pk  The name of the file to use ("environment" by default).
	 * 
	 * @return  string
	 */
	public function getCustomPath($pk = null)
	{
		if (!$pk)
		{
			$pk = 'environment';
		}
		else
		{
			$pk = preg_replace("/\.css$/i", '', $pk);
		}

		return JPath::clean(VRE_CSS_CUSTOMIZER . '/' . $pk . '.css');
	}

	/**
	 * Returns the URL in which the custom environment CSS file is stored.
	 * 
	 * @param   string  $pk  The name of the file to use ("environment" by default).
	 * 
	 * @return  string
	 */
	public function getCustomUrl($pk = null)
	{
		if (!$pk)
		{
			$pk = 'environment';
		}
		else
		{
			$pk = preg_replace("/\.css$/i", '', $pk);
		}

		return VRE_CSS_CUSTOMIZER_URI . $pk . '.css';
	}

	/**
	 * Returns the path of the environment file to load.
	 * 
	 * @param   string  $pk  The name of the file to use ("environment" by default).
	 * 
	 * @return  string
	 */
	public function getEnvironmentFile($pk = null)
	{
		// get custom path first
		$path = $this->getCustomPath($pk);

		// make sure the custom file exists
		if (JFile::exists($path))
		{
			return $path;
		}

		// nope, return the default one
		return $this->getDefaultPath();
	}

	/**
	 * Returns the URL of the environment file to load.
	 * 
	 * @param   string  $pk  The name of the file to use ("environment" by default).
	 * 
	 * @return  string
	 */
	public function getEnvironmentUrl($pk = null)
	{
		// get custom path first
		$path = $this->getCustomPath($pk);

		// make sure the custom file exists
		if (JFile::exists($path))
		{
			return $this->getCustomUrl($pk);
		}

		// nope, return the default one
		return $this->getDefaultUrl();
	}

	/**
	 * Basic item loading implementation.
	 *
	 * @param   mixed  $pk   An optional primary key value to load the row by, or an array of fields to match.
	 *                       If not set the instance property value is used.
	 * @param   bool   $new  True to return an empty object if missing.
	 *
	 * @return  mixed  The record object on success, null otherwise.
	 */
	public function getItem($pk = null, $new = false)
	{
		// build default path
		$defaultPath = $this->getDefaultPath();

		if (!$pk)
		{
			// use default file
			$pk = $defaultPath;
		}
		else
		{
			// use custom file
			$pk = $this->getEnvironmentFile($pk);

			if ($pk === $defaultPath && !$new)
			{
				// Custom file not found...
				// There's no need to return a default item.
				return [];
			}
		}

		if (!JFile::exists($pk))
		{
			// nothing to parse
			return [];
		}

		// read css variables from default environment file
		$buffer = file_get_contents($pk);

		$vars = [];

		// extract variables from buffer
		if (preg_match_all("/\s*(--[a-zA-Z0-9_\-]+):\s*(.*?);/s", $buffer, $matches))
		{
			for ($i = 0; $i < count($matches[0]); $i++)
			{
				$k = $matches[1][$i];
				$v = $matches[2][$i];

				$vars[$k] = $v;
			}
		}

		if ($pk !== $defaultPath)
		{
			// get default vars
			$defaultVars = $this->getItem(null, true);

			// scan the default vars, because new properties might have
			// been introduced in a second time
			foreach ($defaultVars as $k => $v)
			{
				// check whether the custom file supports the property
				if (isset($vars[$k]))
				{
					// yep, overwrite with custom value
					$defaultVars[$k] = $vars[$k];
				}
			}

			// replace vars array
			$vars = $defaultVars;
		}

		return $vars;
	}

	/**
	 * Scans the supported environment variables and group them in sections.
	 * 
	 * @param   int    $max_levels  The maximum number of levels.
	 * 
	 * @return  array
	 */
	public function getVarsTree(int $max_levels = 2)
	{
		// get array holding all the declared variables
		$vars = $this->getItem('environment', $blank = true);

		$tree = [];

		foreach ($vars as $k => $v)
		{
			// extract nodes from variable (exclude initial prefix)
			$nodes = preg_split('/[\-_]+/', preg_replace("/^--vre-/i", '', $k));

			// keep only up to 2 nodes
			$levels = array_splice($nodes, 0, $max_levels);

			// create pointer to tree object
			$seek = &$tree;

			// scan the variable levels
			foreach ($levels as $node)
			{
				// create node if not yet specified
				if (!isset($seek[$node]))
				{
					$seek[$node] = [];
				}

				// register pointer to the current node
				$seek = &$seek[$node];
			}

			// detect type from name

			// prepare leaf data
			$data = [
				'key'  => $k,
				'val'  => $v,
				'type' => $this->detectType($k),
			];

			// create leaf by merging all the remaining levels
			$leaf = implode('_', $nodes);

			if ($leaf)
			{
				// register leaf into the last created node
				$seek[$leaf] = $data;
			}
			else
			{
				// no leaf to register, overwrite the last created node
				$seek = $data;
			}
		}

		return $tree;
	}

	/**
	 * Basic save implementation.
	 *
	 * @param   mixed  $data  Either an array or an object of data to save.
	 *
	 * @return  mixed  The ID of the record on success, false otherwise.
	 */
	public function save($data)
	{
		$data = (array) $data;

		$buffer = '';

		// register all CSS variables into a buffer
		foreach ($data as $k => $v)
		{
			$type = $this->detectType($k);

			// check if we are dealing with a color property
			if ($type === 'color')
			{
				// make HEX safe
				$v = '#' . ltrim($v, '#');
			}
			else if ($type === 'number')
			{
				// include pixel suffix
				$v = $v . 'px';
			}

			$buffer .= "\t{$k}: {$v};\n";
		}

		// set up CSS file
		$buffer = sprintf(":root {\n%s}", $buffer);

		// fetch custom path
		$path = $this->getCustomPath();

		// save CSS into the customizer file
		$saved = JFile::write($path, $buffer);

		if ($saved)
		{
			return $path;
		}

		return false;
	}

	/**
	 * Extend delete implementation to delete any related records
	 * stored within a separated table.
	 *
	 * @param   mixed  $ids  Either the record ID or a list of records.
	 *
	 * @return  bool   True on success, false otherwise.
	 */
	public function delete($ids)
	{
		$res = false;

		if (!$ids)
		{
			// use default environment file
			$ids = 'environment';
		}

		// iterate all the specified files
		foreach ((array) $ids as $id)
		{
			// delete the current custom environment file
			$res = JFile::delete($this->getCustomPath($id)) || $res;
		}
		
		return $res;
	}

	/**
	 * Reads the custom CSS code from the default file loaded in the front-end.
	 * 
	 * @return 	string
	 */
	public function getCustomCSS()
	{
		// build path of the custom CSS file
		$path = JPath::clean(VREBASE . '/assets/css/vre-custom.css');

		if (!JFile::exists($path))
		{
			// file missing
			return '';
		}

		// read contents from CSS file
		return file_get_contents($path);
	}

	/**
	 * Writes the specified CSS code into the custom file.
	 * 
	 * @param 	string 	 $css  The CSS code to write.
	 * 
	 * @return 	boolean  True on success, false otherwise.
	 */
	public function setCustomCSS($css)
	{
		// build path of the custom CSS file
		$path = JPath::clean(VREBASE . '/assets/css/vre-custom.css');

		return (bool) JFile::write($path, $css);
	}

	/**
	 * Detects the field type that mostly fit the name of the provided CSS rule.
	 * 
	 * @param   string  $rule  The CSS rule to analyze.
	 * 
	 * @return  string  The preferred rule type.
	 */
	protected function detectType(string $rule)
	{
		// check if we are dealing with a color property
		if (preg_match("/-(?:color|background)$/", $rule))
		{
			return 'color';
		}
		// check if we are dealing with a size (always in pixel)
		else if (preg_match("/-(?:width|radius|size|padding|margin)/", $rule))
		{
			return 'number';
		}

		// default to text
		return 'text';
	}
}
