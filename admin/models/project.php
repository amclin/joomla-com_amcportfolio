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
	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}

	/**
	 * Method to get a single project
	 * @see JModelAdmin::getItem()
	 * @return object
	 * @since 4.0
	 */
	public function getItem($pk = null) {
		$item = parent::getItem($pk);
		$item->images = $this->getImages();
		$item->movies = $this->getMovies();
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
		$data = JFactory::getApplication()->getUserState('com_amcportfolio.edit.roject.data', array());
	
		if (empty($data)) {
			$data = $this->getItem();
	
			// Prime some default values.
			if ($this->getState('project.id') == 0) {
				$app = JFactory::getApplication();
				$data->set('catid', JRequest::getInt('catid', $app->getUserState('com_amcportolio.projects.filter.category_id')));
			}
		}
	
		return $data;
	}	
	
	/**
	 * Method to get the list of images for this project
	 */
	function &getImages()
	{
		if(!isset($this->_data->images))
		{
			$query = ' SELECT * FROM #__amcportfolio_images' .
					 ' WHERE projectid = '.$this->getState('project.id') .
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
					 ' WHERE projectid = '.$this->getState('project.id') .
					 ' ORDER BY ID';  // Preserves the image ordering

			$this->_db->setQuery($query);
			$this->_data->movies = $this->_db->loadObjectList();
		}
		return $this->_data->movies;
	}

	/**
	 * Save form data
	 * @see JModelAdmin::save()
	 * 
	 */
	public function save($data) {
		$row =& $this->getTable();

		$jinput = JFactory::getApplication()->input;
		$data['images'] = $jinput->post->get('images',NULL,'string');
		$data['movies'] = $jinput->post->get('movies',NULL,'string');
		
		//$data = JRequest::get( 'post' );
		//Allow raw data for description text to allow HTML formatting
		//$data['description'] = JRequest::getVar('description', '', 'post', 'string', JREQUEST_ALLOWRAW);
		
		$images = explode("|", trim($data['images']));
		$movies = explode("|", trim($data['movies']));
		
		// Bind the form fields to the project table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		$isNew = ($row->id == 0);
		
		// Make sure the project record is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		// Store the project table to the database
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg() );
			return false;
		}
		
		if(!$isNew)
		{
			// For existing projects, drop the list of images
			$query = "DELETE FROM #__amcportfolio_images WHERE projectid = " . $row->id;
			$this->_db->setQuery( $query );
			$this->_db->query();
		
			// For existing projects, drop the list of movies
			$query = "DELETE FROM #__amcportfolio_movies WHERE projectid = " . $row->id;
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
				.' VALUES (' . (int)$row->id . ', "' . $image . '" )';
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
				.' VALUES (' . (int)$row->id . ', "' . $movie . '" )';
				$this->_db->setQuery( $query);
				$this->_db->query();
			}
		}
		return true;
	}


	/**
	 * Method to delete record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function delete()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		$row =& $this->getTable();

		if (count( $cids ))
		{
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
				$query = 'DELETE FROM #__amcportfolio_images' .
						' WHERE projectid = ' . $cid;
				$this->_db->setQuery( $query );
				if (!$this->_db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Method to toggle the published state of a collection.
	 * Wrapper for the Joomla table function
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function publish($cid, $publish = 1, $uid = null)
	{
		$row =& $this->getTable();

		if (!$row->publish( $cid, $publish, $uid )) {
			return JError::raiseWarning( 500, $row->getError() );
		}
		return true;
	}


	/**
	 * Moves the collection up or down in the ordering
	 * @param	int	The increment value to move up or down
	 */
	function orderItem($item, $movement)
	{
		$row =& $this->getTable();
		$row->load( $item );
		if (!$row->move( $movement )) {
			$this->setError($row->getError());
			return false;
		}

		// clean menu cache
		$cache =& JFactory::getCache('com_amcportfolio');
		$cache->clean();

		return true;
	}


	/**
	 * Reorders a group of items
	 * @access	Public
	 * @param	Array List of Proect IDs
	 * @param	Array List of ordering values
	 * @return	Boolean	True on success
	 */
	function saveOrder($cid, $order)
	{
		//Prep variables
		$total		= count($cid);
		$row 		= $this->getTable();
		$groups = array();

		// update ordering values
		for ($i = 0; $i < $total; $i++)
		{
			$row->load( (int) $cid[$i] );

			//Snag a list of categories for reordering
			$groups[] = $row->catid;

			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					JError::raiseWarning( 500, $row->getError() );
					return false;
				}
			}
		}

		//Filter for unique groups
		$groups = array_unique($groups);
		foreach($groups as $group)
		{
			//Reorder each group independently
			if(!$row->reorder('catid= ' . $group))
			{
				JError::raiseWarning( 500, $row->getError() );
				return false;
			}
		}
		return true;
	}
}