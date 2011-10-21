<?php

/**
 * @version		$Id$
 * @package		AMC Portfolio
 * @copyright	Copyright (C) 2007 Anthony McLin
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

/**
 * AMCPortfolio Projects Model
 */
class AMCPortfolioModelProjects extends JModel
{
	/**
	 * Projects data array
	 *
	 * @var array
	 */
	var $_data;

	function __construct()
	{
		global $mainframe;

		parent::__construct();

		$config = JFactory::getConfig();

		// Get the pagination request variables
		$this->setState('limit', $mainframe->getUserStateFromRequest('com_amcportfolio.limit', 'limit', $config->getValue('config.list_limit'), 'int'));
		$this->setState('limitstart', JRequest::getVar('limitstart', 0, '', 'int'));

		$this->_data	= null;
	}

	/**
	 * Returns the query
	 * @return string The query to be used to retrieve the rows from the database
	 */
	function _buildQuery()
	{
		// Get the WHERE clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();
		
		$query = 'SELECT p.*,' .
				' c.title AS category, u.name AS editor, COUNT(i.id) AS numimages '.
				' FROM #__amcportfolio as p' .
				' LEFT JOIN #__categories AS c ON c.id = p.catid' .
				' LEFT JOIN #__users AS u ON u.id = p.checked_out' .
				' LEFT JOIN #__amcportfolio_images AS i ON (p.id = i.projectid)' .
				$where .
				' GROUP BY p.id' .
				$orderby;

		return $query;
	}
	/**
	 * Returns a combined set of WHERE clauses for the SQL query
	 * @return string The where clauses for the query
	 */
	function _buildContentWhere()
	{
		global $mainframe, $option;
		$db					=& JFactory::getDBO();
		
		//Get stuff from published state filtering
		$filter_state		= $mainframe->getUserStateFromRequest( $option.'filter_state',		'filter_state',		'',				'word' );
		//Filtering by category
		$filter_catid		= $mainframe->getUserStateFromRequest( $option.'filter_catid',		'filter_catid',		0,				'int' );
		//Ordering
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'filter_order',		'filter_order',		'p.ordering',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		//Get stuff put into the search field
		$search				= $mainframe->getUserStateFromRequest( $option.'search',			'search',			'',				'string' );
		$search				= JString::strtolower( $search );

		//Build the array of WHERE clauses
		$where = array();

		//If we're filtering by category
		if ($filter_catid > 0) {
			$where[] = 'p.catid = '.(int) $filter_catid;
		}
		//If we have values in the search box
		if ($search) {
			$where[] = 'LOWER(p.title) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}
		//If we are filtering by published state
		if ( $filter_state ) {
			if ( $filter_state == 'P' ) {
				$where[] = 'p.published = 1';
			} else if ($filter_state == 'U' ) {
				$where[] = 'p.published = 0';
			}
		}

		//Collapse the array into a string of clauses
		$where 		= ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );

		return $where;
	}

	/**
	 * Builds a list of ORDER BY statements
	 * @return string	The ORDER BY clauses for the query
	 */
	function _buildContentOrderBy()
	{
		global $mainframe, $option;

		$filter_order		= $mainframe->getUserStateFromRequest( $option.'filter_order',		'filter_order',		'p.ordering',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );

		if ($filter_order == 'a.ordering'){
			$orderby 	= ' ORDER BY category, p.ordering '.$filter_order_Dir;
		} else {
			$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.' , category, p.ordering ';
		}

		return $orderby;
	}
	
	
	/**
	 * Retrieves the projects data and stores it
	 * @return array Array of objects containing the data from the database
	 */
	function getData()
	{
		// Lets load the data if it doesn't already exist
		if (empty( $this->_data ))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}

	/**
	 * Method to get a pagination object of the collections for the gallery
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to get the total number of projects
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			//TODO: This is SQL HEAVY!!! a count(*) would work better
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}
}