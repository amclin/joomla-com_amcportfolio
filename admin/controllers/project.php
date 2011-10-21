<?php

/**
 * @version		$Id$
 * @package		AMC Portfolio
 * @copyright	Copyright (C) 2007 Anthony McLin
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * AMCPortfolio Project Controller
 */
class AMCPortfolioControllerProject extends AMCPortfolioController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
	}

	/**
	 * display the edit form
	 * @return void
	 */
	function edit()
	{
		JRequest::setVar( 'view', 'project' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar('hidemainmenu', 1);

		// Checkout the project
		$model = $this->getModel('project');
		$model->checkout();

		parent::display();
	}

	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save()
	{
		$model = $this->getModel('project');

		if ($model->store()) {
			$msg = JText::_( 'Project Saved!' );
		} else {
			$msg = JText::_( 'Error Saving Project' );
			$msg .= $model->getError();
			JError::raiseError(500,$msg);
		}

		// Check in the project
		$model->checkin();
		// Redirect to list view
		$link = 'index.php?option=com_amcportfolio';
		$this->setRedirect($link, $msg);
	}

	/**
	 * remove record(s)
	 * @return void
	 */
	function remove()
	{
		$model = $this->getModel('project');
		if(!$model->delete()) {
			$msg = JText::_( 'Error: One or More Projects Could not be Deleted' );
		} else {
			$msg = JText::_( 'Project(s) Deleted' );
		}

		$this->setRedirect( 'index.php?option=com_amcportfolio', $msg );
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		// Checkin the project
		$model = $this->getModel('project');
		$model->checkin();
		// Redirect to list
		$msg = JText::_( 'Operation Cancelled' );
		$this->setRedirect( 'index.php?option=com_amcportfolio', $msg );
	}

	/**
	 * Publish a list of items
	 * @return	void
	 */
	function publish()
	{
		$model = $this->getModel('project');

		//Get the list of items to publish
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		//Publish
		if($model->publish($cids, 1, NULL))
		{
			$msg = "Project(s) Published.";
		}

		$this->setRedirect( 'index.php?option=com_amcportfolio', $msg );
	}

	/**
	 * UnPublish a list of items
	 * @return	void
	 */
	function unpublish()
	{
		$model = $this->getModel('project');

		//Get the list of items to unpublish
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		//Unpublish
		if($model->publish($cids, 0, NULL))
		{
			$msg = "Project(s) Unpublished.";
		}

		$this->setRedirect( 'index.php?option=com_amcportfolio', $msg );
	}

	/**
	* Move an item up one in the ordering
	*/
	function orderup()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (isset($cid[0]) && $cid[0]) {
			$id = $cid[0];
		} else {
			$this->setRedirect( 'index.php?option=com_amcportfolio', JText::_('No Projects Selected') );
			return false;
		}

		$model =& $this->getModel( 'project' );
		if ($model->orderItem($id, -1)) {
			$msg = JText::_( 'Project Moved Up' );
		} else {
			$msg = $model->getError();
		}
		$this->setRedirect( 'index.php?option=com_amcportfolio', $msg );
	}

	/**
	* Move an item down one in the ordering
	*/
	function orderdown()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (isset($cid[0]) && $cid[0]) {
			$id = $cid[0];
		} else {
			$this->setRedirect( 'index.php?option=com_amcportfolio', JText::_('No Projects Selected') );
			return false;
		}

		$model =& $this->getModel( 'project' );
		if ($model->orderItem($id, 1)) {
			$msg = JText::_( 'Project Moved Down' );
		} else {
			$msg = $model->getError();
		}
		$this->setRedirect( 'index.php?option=com_amcportfolio', $msg );
	}

	/**
	 * Method to reorder a group of listings
	 */
	function saveOrder()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		// Initialize variables
		$cid		= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$order		= JRequest::getVar( 'order', array (0), 'post', 'array' );

		JArrayHelper::toInteger($cid, array(0));
		JArrayHelper::toInteger($order, array(0));

		$model = $this->getModel('project');

		if(!$model->saveOrder($cid,$order))
		{
			$msg = $model->getError();
			JError::raiseError(500,$msg);
		}

		$cache = & JFactory::getCache('com_amcportfolio');
		$cache->clean();

		$msg = JText::_('New ordering saved');

		$link = 'index.php?option=com_amcportfolio';
		$this->setRedirect($link, $msg);
	}
}