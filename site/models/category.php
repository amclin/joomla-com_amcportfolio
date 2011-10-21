<?php

/**
 * @version		$Id$
 * @package		AMC Portfolio
 * @copyright	Copyright (C) 2007 Anthony McLin
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.model');

/**
 * AMCPortfolio Component Category Model
 */
class AMCPortfolioModelCategory extends JModel
{
	/**
	 * Category id
	 *
	 * @var int
	 */
	var $_id = null;

	/**
	 * Category data array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Category data
	 *
	 * @var object
	 */
	var $_category = null;

	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();

		global $mainframe;

		$config = JFactory::getConfig();

		$id = JRequest::getVar('id', 0, '', 'int');
		$this->setId((int)$id);
	}

	/**
	 * Method to set the category id
	 *
	 * @access	public
	 * @param	int	Category ID number
	 */
	function setId($id)
	{
		// Set category ID and wipe data
		$this->_id			= $id;
		$this->_category	= null;
	}

	/**
	 * Method to get project item data for the category
	 *
	 * @access public
	 * @return array
	 */
	function getData()
	{
		// Lets load the data if it doesn't already exist
		if (empty( $this->_data ))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList( $query );

			$total = count($this->_data);
			for($i = 0; $i < $total; $i++)
			{
				//Build item:alias combos for SEO
				$item =& $this->_data[$i];
				$item->slug = $item->id.'-'.$item->alias;

				//Get image list for each project
				$query = 'SELECT * FROM #__amcportfolio_images' .
						' WHERE projectid = ' . $item->id .
						' ORDER BY ID';  // Preserves the image ordering
				$item->images = $this->_getList($query);

				//Get movie list for each project
				$query = 'SELECT * FROM #__amcportfolio_movies' .
						' WHERE projectid = ' . $item->id .
						' ORDER BY ID';  // Preserves the image ordering
				$item->movies = $this->_getList($query);
			}
		}

		return $this->_data;
	}

	/**
	 * Method to get the selected category data
	 * @return	object	The data for a category
	 */
	function getCategory()
	{
		// Load the Category data
		if ($this->_loadCategory())
		{
			// Initialize some variables
			$user = &JFactory::getUser();

			// Make sure the category is published
			if (!$this->_category->published) {
				JError::raiseError(404, JText::_("Resource Not Found"));
				return false;
			}
			// check whether category access level allows access
			if ($this->_category->access > $user->get('aid', 0)) {
				JError::raiseError(403, JText::_("ALERTNOTAUTH"));
				return false;
			}
		}
		return $this->_category;
	}

	/**
	 * Returns the query
	 * @return string The query to be used to retrieve the rows from the database
	 */
	function _buildQuery()
	{
		$where = ' WHERE p.catid = ' . $this->_id;
		$where .= ' AND p.published = 1';

		$query = 'SELECT p.*,' .
				' cc.title AS category, u.name AS editor, COUNT(i.id) AS numimages' .
				' FROM #__amcportfolio as p' .
				' LEFT JOIN #__categories AS cc ON cc.id = p.catid' .
				' LEFT JOIN #__users AS u ON u.id = p.checked_out' .
				' LEFT JOIN #__amcportfolio_images AS i ON i.projectid = p.id' .
				$where .
				' GROUP BY p.id' .
				' ORDER BY p.ordering';

		return $query;
	}

	/**
	 * Method to load category data if it doesn't exist.
	 *
	 * @access	private
	 * @return	boolean	True on success
	 */
	function _loadCategory()
	{
		if (empty($this->_category))
		{
			// current category info
			$query = 'SELECT c.*, ' .
				' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as slug '.
				' FROM #__categories AS c' .
				' WHERE c.id = '. (int) $this->_id .
				' AND c.section = "com_amcportfolio"';
			$this->_db->setQuery($query, 0, 1);
			$this->_category = $this->_db->loadObject();
		}
		return true;
	}
}
?>