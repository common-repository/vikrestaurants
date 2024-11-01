<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\CodeHub;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Code block wrapper.
 * 
 * @since 1.9
 */
class CodeBlock extends \JObject implements \JsonSerializable
{
	/**
	 * Returns the ID of the code block.
	 * 
	 * @return  string
	 */
	public function getID()
	{
		$id = $this->get('id');

		if (!$id)
		{
			// generate a new unique ID at runtime
			$id = md5(implode('.', [
				$this->getExtension(),
				$this->getCode(),
				uniqid(),
			]));
		}

		return $id;
	}

	/**
	 * Returns the title of the code block.
	 * 
	 * @return  string
	 */
	public function getTitle()
	{
		return $this->get('title', 'Custom Code');
	}

	/**
	 * Sets the title of the code block.
	 * 
	 * @param   string  $title
	 * 
	 * @return  self
	 */
	public function setTitle(string $title)
	{
		$this->set('title', $title);

		return $this;
	}

	/**
	 * Returns the description of the code block.
	 * 
	 * @return  string
	 */
	public function getDescription()
	{
		return $this->get('description', '');
	}

	/**
	 * Sets the description of the code block.
	 * 
	 * @param   string  $description
	 * 
	 * @return  self
	 */
	public function setDescription(string $description)
	{
		$this->set('description', $description);

		return $this;
	}

	/**
	 * Returns the author of the code block.
	 * 
	 * @return  string
	 */
	public function getAuthor()
	{
		return (string) ($this->get('author') ?: \JFactory::getUser()->name);
	}

	/**
	 * Sets the author of the code block.
	 * 
	 * @param   string  $author
	 * 
	 * @return  self
	 */
	public function setAuthor(string $author)
	{
		$this->set('author', $author);

		return $this;
	}

	/**
	 * Returns the version of the code block.
	 * 
	 * @return  string
	 */
	public function getVersion()
	{
		return $this->get('version') ?: \JFactory::getDate()->format('Y-m-d H:i:s');
	}

	/**
	 * Sets the version of the code block.
	 * 
	 * @param   string  $version
	 * 
	 * @return  self
	 */
	public function setVersion(string $version)
	{
		$this->set('version', $version);

		return $this;
	}

	/**
	 * Returns the snippet of the code block.
	 * 
	 * @return  string
	 */
	public function getCode()
	{
		return trim($this->get('code', ''));
	}

	/**
	 * Sets the snippet of the code block.
	 * 
	 * @param   string  $code
	 * 
	 * @return  self
	 */
	public function setCode(string $code)
	{
		$this->set('code', $code);

		return $this;
	}

	/**
	 * Returns the extension of the code block (e.g. php).
	 * 
	 * @return  string
	 */
	public function getExtension()
	{
		return $this->get('extension', '');
	}

	/**
	 * Sets the extension (e.g. php) of the code block.
	 * 
	 * @param   string  $extension
	 * 
	 * @return  self
	 */
	public function setExtension(string $extension)
	{
		$this->set('extension', $extension);

		return $this;
	}

	/**
	 * @inheritDoc
	 *
	 * @see \JsonSerializable
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize()
	{
		return [
			'id'          => $this->getID(),
			'title'       => $this->getTitle(),
			'description' => $this->getDescription(),
			'author'      => $this->getAuthor(),
			'version'     => $this->getVersion(),
			'code'        => $this->getCode(),
			'extension'   => $this->getExtension(),
		];
	}
}
