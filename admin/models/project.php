<?php

/**
 * @version		$Id$
 * @package		AMC Portfolio
 * @copyright	Copyright (C) 2007 Anthony McLin
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.modeladmin');

/**
 * AMCPortfolio Project Model
 */
class AMCPortfolioModelProject extends JModelAdmin
{
	/**
	 * Constructor that retrieves the ID from the request
	 *
	 * @access	public
	 * @return	void
	 */
// 	function __construct()
// 	{
// 		parent::__construct();

// 		$array = JRequest::getVar('cid',  0, '', 'array');
// 		$this->setId((int)$array[0]);
// 	}

	/**
	* Method to get the record form.
	*
	* @param	array	$data		Data for the form.
	* @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	* @return	mixed	A JForm object on success, false on failure
	* @since	4.0
	*/
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_amcportfolio.project', 'project', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
	
		// Determine correct permissions to check.
// 		if ($this->getState('project.id')) {
			// Existing record. Can only edit in selected categories.
// 			$form->setFieldAttribute('catid', 'action', 'core.edit');
// 		} else {
			// New record. Can only create in selected categories.
// 			$form->setFieldAttribute('catid', 'action', 'core.create');
// 		}
	
		// Modify the form based on access controls.
// 		if (!$this->canEditState((object) $data)) {
			// Disable fields for display.
// 			$form->setFieldAttribute('ordering', 'disabled', 'true');
// 			$form->setFieldAttribute('published', 'disabled', 'true');
	
			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
// 			$form->setFieldAttribute('ordering', 'filter', 'unset');
// 			$form->setFieldAttribute('published', 'filter', 'unset');
// 		}
	
		return $form;
	}	
	
	
	
	
	/**
	 * Method to set the project identifier
	 *
	 * @access	public
	 * @param	int Project ID
	 * @return	void
	 */
// 	function setId($id)
// 	{
// 		// Set id and wipe data
// 		$this->_id		= $id;
// 		$this->_data	= null;
// 	}

	/**
	 * Method to get a single project
	 * @see JModelAdmin::getItem()
	 * @return object
	 * @since 4.0
	 */
	public function getItem($pk = null) {
		$item = parent::getItem($pk);
		$item->images = $this->getImages($pk);
		$item->movies = $this->getMovies($pk);
		return $item;
	}
	
	/**
	* Method to get the data that should be injected in the form.
	*
	* @return	mixed	The data for the form.
	* @since	4.0
	*/
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app = JFactory::getApplication();
		$data = $app->getUserState('com_amcportfolio.edit.project.data', array());
	
		if (empty($data)) {
			$pks  = $app->getUserState('com_amcportfolio.edit.project.id');
			$pk = isset($pks[0]) ? $pks[0] : null;
			$data = $this->getItem($pk);
	
			// Prime some default values.
			if ($this->getState('project.id') == 0) {
				
				$data->set('catid', JRequest::getInt('catid', $app->getUserState('com_amcportolio.projects.filter.category_id')));
			}
		}
	
		return $data;
	}	
	
	/**
	 * Method to get the list of images for this project
	 */
	function getImages($pk = null)
	{
		if(!isset($this->_data->images))
		{
			$this->_data = new stdClass();
			$this->_data->images = array();
			if(!is_null($pk)) {
				$query = ' SELECT * FROM #__amcportfolio_images' .
						 ' WHERE projectid = '. $pk .
						 ' ORDER BY ID';  // Preserves the image ordering
	
				$this->_db->setQuery($query);
				$this->_data->images = $this->_db->loadObjectList();
			}
		}
		return $this->_data->images;
	}

	/**
	 * Method to get the list of movies for this project
	 * @return	array	List of movies
	 */
	function getMovies($pk = null)
	{
		//Don't reload if we don't have to
		if(!isset($this->_data->movies))
		{
			if(!is_null($pk)) {
				$query = ' SELECT * FROM #__amcportfolio_movies' .
						 ' WHERE projectid = '. $pk .
						 ' ORDER BY ID';  // Preserves the image ordering
	
				$this->_db->setQuery($query);
				$this->_data->movies = $this->_db->loadObjectList();
			} else {
				$this->_data->movies = array();
			}
			
		}
		return $this->_data->movies;
	}

	/**
	 * Save form data
	 * @see JModelAdmin::save()
	 * 
	 */
	public function save($data) {
		
		if (parent::save($data))
		{
			//Successfully saved main project data.
			//Time to update the secondary tables
			$images = explode("|", trim($data['images']));
			$movies = explode("|", trim($data['movies']));
			
			$pk = $this->getState('project.id');
			
			//Clear out existing image and movie lists
			if(!$this->getState('project.new'));
			{
				// For existing projects, drop the list of images
				$query = "DELETE FROM #__amcportfolio_images WHERE projectid = " . $pk;
				$this->_db->setQuery( $query );
				$this->_db->query();
			
				// For existing projects, drop the list of movies
				$query = "DELETE FROM #__amcportfolio_movies WHERE projectid = " . $pk;
				$this->_db->setQuery( $query );
				$this->_db->query();
			}
			
			//Insert images into image table
			foreach($images as $image)
			{
				// Safety check to prevent blank inserts
				if($image != '')
				{
					$query = 'INSERT INTO #__amcportfolio_images'
							.' (projectid,image)'
									.' VALUES (' . (int)$pk . ', "' . $image . '" )';
					$this->_db->setQuery( $query);
					$this->_db->query();
				}
			}
			
			//Insert movies into movie table
			foreach($movies as $movie)
			{
				// Safety check to prevent blank inserts
				if($movie != '')
				{
					$query = 'INSERT INTO #__amcportfolio_movies'
							.' (projectid,movie)'
									.' VALUES (' . (int)$pk . ', "' . $movie . '" )';
					$this->_db->setQuery( $query);
					$this->_db->query();
				}
			}

			return true;
		}
		
		return false;
	}


	/**
	 * Method to delete record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function delete(&$pks)
	{

		$row =& $this->getTable();

		if (count( $pks ))
		{
			foreach($pks as $pk) {
				if (!$row->delete( $pk )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
				$query = 'DELETE FROM #__amcportfolio_images' .
						' WHERE projectid = ' . $pk;
				$this->_db->setQuery( $query );
				if (!$this->_db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}
		return true;
	}
}