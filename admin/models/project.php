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
 * AMCPortfolio Project Model
 */
class AMCPortfolioModelProject extends JModel
{
	/**
	 * Constructor that retrieves the ID from the request
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		parent::__construct();

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
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
	 * Method to get a project
	 * @return object with data
	 */
	function &getData()
	{
		// Load the data
		if (empty( $this->_data )) {
			$this->_data = $this->getTable();
			$this->_data->load($this->_id);

			$this->getImages();
			$this->getMovies();
		}

		return $this->_data;
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

	/**
	 * Method to store a record
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function store()
	{
		$row =& $this->getTable();

		$data = JRequest::get( 'post' );
		//Allow raw data for description text to allow HTML formatting
		$data['description'] = JRequest::getVar('description', '', 'post', 'string', JREQUEST_ALLOWRAW);

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
	 * Method to checkin/unlock the project
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function checkin()
	{
		if ($this->_id)
		{
			$weblink = & $this->getTable();
			if(! $weblink->checkin($this->_id)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return false;
	}

	/**
	 * Method to checkout/lock the project
	 *
	 * @access	public
	 * @param	int	$uid	User ID of the user checking the article out
	 * @return	boolean	True on success
	 */
	function checkout($uid = null)
	{
		if ($this->_id)
		{
			// Make sure we have a user id to checkout the article with
			if (is_null($uid)) {
				$user	=& JFactory::getUser();
				$uid	= $user->get('id');
			}
			// Lets get to it and checkout the thing...
			$weblink = & $this->getTable();
			if(!$weblink->checkout($uid, $this->_id)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			return true;
		}
		return false;
	}

	/**
	 * Tests if project is checked out
	 *
	 * @access	public
	 * @param	int	A user id
	 * @return	boolean	True if checked out
	 */
	function isCheckedOut( $uid=0 )
	{
		if ($this->_loadData())
		{
			if ($uid) {
				return ($this->_data->checked_out && $this->_data->checked_out != $uid);
			} else {
				return $this->_data->checked_out;
			}
		}
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