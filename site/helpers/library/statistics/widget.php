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
 * Class used to support a statistic widget for the
 * restaurant reservations or take-away orders.
 *
 * @since 1.8
 */
abstract class VREStatisticsWidget
{
	/**
	 * Static counter used to generate unique IDs based on
	 * an auto-incremental integer.
	 *
	 * @var integer
	 * @since 1.9
	 */
	protected static $AUTO_INCREMENT = 0;

	/**
	 * Group identifier ('restaurant' or 'takeaway').
	 * The property is private so that it cannot be
	 * modified at runtime by the children classes.
	 *
	 * @var string
	 */
	private $group;

	/**
	 * The widget ID.
	 *
	 * @var integer
	 */
	protected $id;

	/**
	 * The widget user ID.
	 *
	 * @var integer
	 *
	 * @since 1.8.3
	 */
	protected $id_user;

	/**
	 * An optional title for the widget.
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * The widget position identifier.
	 *
	 * @var string
	 */
	protected $position;

	/**
	 * The widget size identifier.
	 *
	 * @var string
	 */
	protected $size;

	/**
	 * A registry of options.
	 *
	 * @var JObject
	 */
	protected $options;

	/**
	 * Class constructor.
	 *
	 * @param 	string  $group    The section to which the reservations
	 * 							  belong ('restaurant' or 'takeaway').
	 * @param 	mixed 	$options  Either an array or an object of options to be passed 
	 * 							  to the order instance.
	 */
	public function __construct($group, $options = array())
	{
		$this->group   = $group;
		$this->options = new JObject($options);
	}

	/**
	 * Returns the widget name/identifier.
	 *
	 * @return 	string
	 */
	public function getName()
	{
		// get current class name
		$class = get_class($this);

		// extract widget name from class
		if (preg_match("/^VREStatisticsWidget([a-z0-9_]+)$/i", $class, $match))
		{
			// return widget name (lowercase)
			return strtolower(end($match));
		}

		// the widget doesn't follow the standard notation, return full class name
		return $class;
	}

	/**
	 * Returns the ID of the widget.
	 *
	 * @return 	integer  The widget ID.
	 */
	public function getID()
	{
		if (!$this->id)
		{
			// use a random identifier when not specified
			$this->setID(++static::$AUTO_INCREMENT);
		}

		return (int) $this->id;
	}

	/**
	 * Sets the ID of the widget.
	 *
	 * @param 	integer  $id  The widget ID.
	 *
	 * @return 	self 	 This object to support chaining.
	 */
	public function setID($id)
	{
		$this->id = (int) $id;

		return $this;
	}

	/**
	 * Returns the user ID of the widget.
	 *
	 * @return 	integer  The widget user ID.
	 *
	 * @since 	1.8.3
	 */
	public function getUserID()
	{
		return (int) $this->id_user;
	}

	/**
	 * Sets the user ID of the widget.
	 *
	 * @param 	integer  $id  The widget user ID.
	 *
	 * @return 	self 	 This object to support chaining.
	 *
	 * @since 	1.8.3
	 */
	public function setUserID($id)
	{
		$this->id_user = (int) $id;

		return $this;
	}

	/**
	 * Returns the widget title.
	 * By default, the title is a translatable string built
	 * in the following format: VRE_STATS_WIDGET_[NAME]_TITLE.
	 *
	 * @return 	string
	 */
	public function getTitle()
	{
		// use custom title if set
		if (!empty($this->title))
		{
			return $this->title;
		}

		// get widget name (UPPERCASE)
		$widget = strtoupper($this->getName());

		// build language key
		$key = 'VRE_STATS_WIDGET_' . $widget . '_TITLE';

		// try to translate the title
		$title = JText::translate($key);

		// check if the title is equals to language key
		if ($title === $key)
		{
			// missing translation, return widget name
			return $widget;
		}

		// return translated title instead
		return $title;
	}

	/**
	 * Sets a custom title for the widget.
	 *
	 * @param 	string 	$title  The widget title.
	 *
	 * @return 	self 	This object to support chaining.
	 */
	public function setTitle($title)
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * Returns the widget description.
	 * By default, the description is a translatable string built
	 * in the following format: VRE_STATS_WIDGET_[NAME]_DESC.
	 *
	 * @return 	string
	 */
	public function getDescription()
	{
		// build language key
		$key = 'VRE_STATS_WIDGET_' . strtoupper($this->getName()) . '_DESC';

		// try to translate the description
		$desc = JText::translate($key);

		// check if the description is equals to language key
		if ($desc === $key)
		{
			// missing translation, return empty description
			return '';
		}

		// return translated description instead
		return $desc;
	}

	/**
	 * Checks whether the specified group matches the one set
	 * in the widget properties.
	 *
	 * @param 	string 	 $group  The group to check.
	 *
	 * @return 	boolean  True if matching, false otherwise.
	 */
	public function isGroup($group)
	{
		return !strcasecmp($this->group, $group);
	}

	/**
	 * Checks whether the specified group is supported
	 * by the widget. Children classes can override this
	 * method to drop the support for a specific group.
	 *
	 * @param 	string 	 $group  The group to check.
	 *
	 * @return 	boolean  True if supported, false otherwise.
	 */
	public function isSupported($group)
	{
		return true;
	}

	/**
	 * Returns the position of the widget.
	 *
	 * @return 	string 	The position identifier.
	 */
	public function getPosition()
	{
		return $this->position ? $this->position : 'inherit';
	}

	/**
	 * Sets the position of the widget.
	 *
	 * @param 	string 	$position  The position identifier.
	 *
	 * @return 	self 	This object to support chaining.
	 */
	public function setPosition($position)
	{
		$this->position = $position;

		return $this;
	}

	/**
	 * Returns the size of the widget.
	 *
	 * @return 	string 	The size identifier.
	 */
	public function getSize()
	{
		return (string) $this->size;
	}

	/**
	 * Sets the size of the widget.
	 *
	 * @param 	string 	$size  The size identifier.
	 *
	 * @return 	self 	This object to support chaining.
	 */
	public function setSize($size)
	{
		$this->size = $size;

		return $this;
	}

	/**
	 * Returns the configuration options
	 *
	 * @return 	array
	 */
	public function getOptions()
	{
		static $form = null;

		if (is_null($form))
		{
			// get the configuration array only once
			$form = $this->getForm();
		}

		// get all properties
		$options = $this->options->getProperties();

		// scan all the supported form parameters
		foreach ($form as $k => $param)
		{
			// if not specified, use the default value of the current paremeter
			if (!isset($options[$k]) && isset($param['default']))
			{
				$options[$k] = $param['default'];
			}
		}

		return $options;
	}

	/**
	 * Returns a configuration option.
	 *
	 * @param 	string 	$key  The option key.
	 * @param 	mixed 	$def  The default value.
	 *
	 * @return 	mixed 	The option value if exists, the default value otherwise.
	 */
	public function getOption($key, $def = null)
	{
		$value = $this->options->get($key, null);

		/**
		 * Return default value in case the option is
		 * null or contains an empty string.
		 *
		 * @since 1.8.1
		 */
		if ($value === null || $value === '')
		{
			return $def;
		}

		return $value;
	}

	/**
	 * Updates the options of the registry.
	 *
	 * @param 	mixed 	$options  Either an array or an object of options to be passed 
	 * 							  to the order instance.
	 *
	 * @return 	self 	This object to support chaining.
	 */
	public function setOptions($options = array())
	{
		$this->options->setProperties($options);

		return $this;
	}

	/**
	 * Updates or insert a value within the configuration.
	 *
	 * @param 	string 	$key  The option key.
	 * @param 	mixed 	$val  The option value.
	 *
	 * @return 	self 	This object to support chaining.
	 */
	public function setOption($key, $val)
	{
		$this->options->set($key, $val);

		return $this;
	}

	/**
	 * Override this method to return a configuration
	 * form of the widget.
	 *
	 * @return 	array
	 */
	public function getForm()
	{
		return array();
	}

	/**
	 * Returns the value of the parameters used the
	 * last time this widget was invoked.
	 *
	 * @return 	array
	 */
	public function getParams()
	{
		// get widget identifier
		$widget = $this->getName();

		// get configuration stored in the user state to 
		// retrieve those arguments that don't have to
		// be stored permanently
		$state = JFactory::getApplication()->getUserState('vre.statistics.' . $widget . '.' . $this->getID(), array());

		// get widget model
		$model = JModelVRE::getInstance('statswidget');

		// load widget parameters
		$params = $model->getParams($this->getID());

		// merge user state with configuration
		return array_merge($params, $state);
	}

	/**
	 * Saves the last used parameters of the widget
	 * within the configuration/user state.
	 *
	 * @return 	void
	 */
	public function saveParams()
	{
		$app = JFactory::getApplication();

		// get widget identifier
		$widget = $this->getName();

		// get configuration stored in the user state to 
		// retrieve those arguments that don't have to
		// be stored permanently
		$state = $app->getUserState('vre.statistics.' . $widget . '.' . $this->getID(), array());

		// get widget model
		$model = JModelVRE::getInstance('statswidget');

		// load widget parameters
		$params = $model->getParams($this->getID());

		// iterate the widget form to save only the internal parameters
		foreach ($this->getForm() as $k => $field)
		{
			// check if the field is volatile or permanent
			if (empty($field['volatile']))
			{
				// save widget param in configuration
				$params[$k] = $this->getOption($k);
			}
			else
			{
				// save widget param in user state
				$state[$k] = $this->getOption($k);
			}
		}

		// prepare record data to be saved
		$data = array(
			'id'     => $this->getID(),
			'params' => $params,
		);

		// save table parameters
		$model->save($data);

		// update user state
		$app->setUserState('vre.statistics.' . $widget . '.' . $this->getID(), $state);
	}

	/**
	 * Megic method to return the widget name
	 * when the object is casted to string.
	 *
	 * @return 	string
	 */
	public function __toString()
	{
		return $this->getName();
	}

	/**
	 * Returns the HTML used to display the widget.
	 *
	 * By default, the class tries to use a layout
	 * file under this path:
	 * /layouts/statistics/widgets/[WIDGET].php
	 *
	 * In case the widget doesn't support a layout file,
	 * it is possible to override this method to return
	 * a HTML string.
	 *
	 * @return 	string
	 */
	public function display()
	{
		// get layout file
		$layout = new JLayoutFile('statistics.widgets.' . $this->getName());

		// prepare layout data
		$data = array(
			'widget' => $this,
		);

		// return layout HTML string
		return $layout->render($data);
	}

	/**
	 * Loads the dataset(s) that will be recovered asynchronously
	 * for being displayed within the widget.
	 *
	 * It is possible to return an array of records to be passed
	 * to a chart or directly the HTML to replace.
	 *
	 * @return 	mixed
	 */
	abstract public function getData();
}
