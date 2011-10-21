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
 * AMCPortfolio Component Project Model
 */
class AMCPortfolioModelProject extends JModel
{
	/**
	 * Project id
	 *
	 * @var int
	 */
	var $_id = null;

	/**
	 * Project data
	 *
	 * @var array
	 */
	var $_data = null;
	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();

		$id = JRequest::getVar('id', 0, '', 'int');
		$this->setId((int)$id);
	}

	/**
	 * Method to set the project identifier
	 *
	 * @access	public
	 * @param	int Project identifier
	 */
	function setId($id)
	{
		// Set project id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}

	/**
	 * Method to get a project
	 * @return object with data
	 */
	function &getData()
	{
		// Load the data
		if (empty( $this->_data )) {
			$this->_data = $this->getTable();
			$this->_data->load($this->_id);

			// Make sure the user has permissions and the project is published
			$user = &JFactory::getUser();

			// Make sure the project is published
			if (!$this->_data->published) {
				JError::raiseError(404, JText::_("The selectetd project is not currently available"));
				return false;
			}

//TODO			// Check to see if the category is published
//			if (!$this->_data->cat_pub) {
//				JError::raiseError( 404, JText::_("The selected category of projects is not currenty available") );
//				return;
//			}

//TODO			// Check whether category access level allows access
//			if ($this->_data->cat_access > $user->get('aid', 0)) {
//				JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
//				return;
//			}

			$this->getImages();
			$this->getMovies();
		}

		return $this->_data;
	}


	/**
	 * Method to increment the hit counter for the project
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function hit()
	{
		$query = 'UPDATE #__amcportfolio'
		. ' SET hits = ( hits + 1 )'
		. ' WHERE id = ' . (int) $this->_id;

		$this->_db->setQuery( $query );

		if(	$this->_db->query() )
		{
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Method to grab the parent category's data and the sibling projects
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function getCategory()
	{
		require_once (JPATH_COMPONENT.DS.'models'.DS.'category.php');
		$category = new AMCPortfolioModelCategory();
		$category->setID($this->_data->catid);

		$this->_category = $category->getCategory();
		$this->_category->projects = $category->getData();
		return $this->_category;
	}

	/**
	 * Method to get the list of images for this project
	 */
	function &getImages()
	{
		if(!isset($this->_data->images))
		{
			$query = ' SELECT * FROM #__amcportfolio_images' .
					 ' WHERE projectid = '.$this->_id .
					 ' ORDER BY ID';  // Preserves the image ordering

			$this->_db->setQuery($query);
			$this->_data->images = $this->_db->loadObjectList();
		}
		return $this->_data->images;
	}

	/**
	 * Method to get the list of movies for this project
	 * @return	array	List of movies
	 */
	function &getMovies()
	{
		//Don't reload if we don't have to
		if(!isset($this->_data->movies))
		{
			$query = ' SELECT * FROM #__amcportfolio_movies' .
					 ' WHERE projectid = '.$this->_id .
					 ' ORDER BY ID';  // Preserves the image ordering

			$this->_db->setQuery($query);
			$this->_data->movies = $this->_db->loadObjectList();
		}
		return $this->_data->movies;
	}

}
?>