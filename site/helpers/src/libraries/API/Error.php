<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\API;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * The API error representation.
 *
 * @since 1.9
 */
class Error implements \JsonSerializable
{
	/**
	 * The identifier code of the error.
	 *
	 * @var int
	 */
	public $errcode;

	/**
	 * The text description of the error.
	 *
	 * @var string
	 */
	public $error;

	/**
	 * Class constructor.
	 * 
	 * @param  int     $errcode  The code identifier.
	 * @param  string  $error    The text description.
	 */
	public function __construct($errcode, $error)
	{
		$this->errcode = $errcode;
		$this->error   = $error;
	}

	/**
	 * Creates an exception with the details of this error.
	 * 
	 * @return  \Exception
	 */
	public function asException()
	{
		return new \Exception($this->error, $this->errcode);
	}

	/**
	 * Returns this object encoded in JSON.
	 *
	 * @return  string  This object in JSON.
	 */
	public function toJSON()
	{
		return json_encode($this);
	}

	/**
	 * @inheritDoc
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize()
	{
		return get_object_vars($this);
	}
}
