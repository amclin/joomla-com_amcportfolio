<?php

/**
 * @version		$Id$
 * @package		AMC Portfolio
 * @copyright	Copyright (C) 2007 Anthony McLin
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.modelitem');

/**
 * AMCPortfolio Component Project Model
 */
class AMCPortfolioModelProject extends JModelItem
{
	/**
	 * Model context string.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_context = 'com_amcportfolio.project';
	protected $_data = false;

	/**
	* Method to auto-populate the model state.
	*
	* Note. Calling getState in this method will result in recursion.
	*
	* @since	4.0
	*/
	protected function populateState()
	{
		$app = JFactory::getApplication();
		$params	= $app->getParams();
	
		// Load the object state.
		$id	= JRequest::getInt('id');
		$this->setState('project.id', $id);
	
		// Load the parameters.
		$this->setState('params', $params);
	}
	
	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
// 	function __construct()
// 	{
// 		parent::__construct();

// 		$id = JRequest::getVar('id', 0, '', 'int');
// 		$this->setId((int)$id);
// 	}

	/**
	 * Method to set the project identifier
	 *
	 * @access	public
	 * @param	int Project identifier
	 */
// 	function setId($id)
// 	{
// 		Set project id and wipe data
// 		$this->_id		= $id;
// 		$this->_data	= null;
// 	}

	/**
	 * Method to get a Project
	 * @param	integer	The id of the project to get.
	 * @return	mixed	Object on success, false on failure.
	 * @since 4.0
	 */
// 	public function &getItem($id = null) {
		
// 		if ($this->_item === null) {
// 			$this->_item = false;
			
// 			if (empty($id)) {
// 				$id = $this->getState('project.id');
// 			}
			
// 			$this->_item = parent::getItem($id);
// 			$this->_item->images = $this->getImages();
// 			$this->_item->movies = $this->getMovies();
// 			return $this->_item;
// 		}
		
// 		return $this->_item;
// 	}
	
	
	/**
	* Method to get a single record.
	* Copied from JModelAdmin
	*
	* @param   integer  $pk  The id of the primary key.
	*
	* @return  mixed    Object on success, false on failure.
	* @since   4.0
	*/
	public function getItem($pk = null)
	{
		if(empty($this->_data)) {
			// Initialise variables.
			$pk		= (!empty($pk)) ? $pk : (int) $this->getState($this->getName().'.id');
			$table	= $this->getTable();
		
			if ($pk > 0) {
				// Attempt to load the row.
				$return = $table->load($pk);
		
				// Check for a table object error.
				if ($return === false && $table->getError()) {
					$this->setError($table->getError());
					return false;
				}
			}
		
			// Convert to the JObject before adding other data.
			$properties = $table->getProperties(1);
			$this->_data->item = JArrayHelper::toObject($properties, 'JObject');
		
			if (property_exists($this->_data->item, 'params')) {
				$registry = new JRegistry;
				$registry->loadString($item->params);
				$this->_data->item->params = $registry->toArray();
			}
			
			$this->_data->item->images = $this->getImages();
			$this->_data->item->movies = $this->getMovies();
		}
		
		return $this->_data->item;
	}
	
	
	
	/**
	 * Method to get a project
	 * @return object with data
	 */
// 	function &getData()
// 	{
		// Load the data
// 		if (empty( $this->_data )) {
// 			$this->_data = $this->getTable();
// 			$this->_data->load($this->_id);

			// Make sure the user has permissions and the project is published
// 			$user = &JFactory::getUser();

			// Make sure the project is published
// 			if (!$this->_data->published) {
// 				JError::raiseError(404, JText::_("The selectetd project is not currently available"));
// 				return false;
// 			}

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

// 			$this->getImages();
// 			$this->getMovies();
// 		}

// 		return $this->_data;
// 	}


	/**
	 * Method to increment the hit counter for the project
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function hit($id = null)
	{
		if (empty($id)) {
			$id = $this->getState('project.id');
		}
		
		$query = 'UPDATE #__amcportfolio'
		. ' SET hits = ( hits + 1 )'
		. ' WHERE id = ' . (int) $id;

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
	 * @param $id int Category ID
	 * @access	public
	 * @return	boolean	True on success
	 */
	function getCategory($id = null)
	{
		if(empty($id)) {
			$id = $this->getState('category.id',$this->_data->item->catid);
		}
// 		require_once (JPATH_COMPONENT.DS.'models'.DS.'category.php');
// 		$category = new AMCPortfolioModelCategory();
		$category = JModel::getInstance('Category','AMCPortfolioModel',array('ignore_request' => true));
		$category->setState('category.id',$id);
//		$category->setID($this->_data->catid);

//		$categories = JCategories::getInstance('AMCPortfolio', $options);
//		$this->_category = $categories->get($this->getState('category.id', 'root'));
		$this->_category = $category->getCategory();
		$this->_category->projects = $category->getItems();
// 		$this->_category = $category->getCategory();
// 		$this->_category->projects = $category->getData();
		return $this->_category;
	}

	/**
	 * Method to get the list of images for this project
	 */
	public function &getImages()
	{
		if(!isset($this->_data->images))
		{
			$query = ' SELECT * FROM #__amcportfolio_images' .
					 ' WHERE projectid = '.$this->getState('project.id') .
					 ' ORDER BY ID';  // Preserves the image ordering

			$this->_db->setQuery($query);
			$this->_data->images = $this->_db->loadObjectList();
			
			foreach($this->_data->images as &$image)
			{
				$image->path = JURI::root() . (ltrim( $image->image, '/') );
			}
		}
		return $this->_data->images;
	}

	/**
	 * Method to get the list of movies for this project
	 * @return	array	List of movies
	 */
	public function &getMovies()
	{
		//Don't reload if we don't have to
		if(!isset($this->_data->movies))
		{
			$query = ' SELECT * FROM #__amcportfolio_movies' .
					 ' WHERE projectid = '.$this->getState('project.id') .
					 ' ORDER BY ID';  // Preserves the image ordering

			$this->_db->setQuery($query);
			$this->_data->movies = $this->_db->loadObjectList();
		}
		return $this->_data->movies;
	}
}
?>