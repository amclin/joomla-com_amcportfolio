<?php

/**
 * @version		$Id$
 * @package		AMC Portfolio
 * @copyright	Copyright (C) 2007 Anthony McLin
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.modellist' );

/**
 * AMCPortfolio Projects Model
 */
class AMCPortfolioModelProjects extends JModelList
{
	/**
	 * Projects data array
	 *
	 * @var array
	 */
// 	var $_data;

	function __construct()
	{
// 		global $mainframe;

// 		if (empty($config['filter_fields'])) {
// 			$config['filter_fields'] = array(
// 						'id', 'a.id',
// 						'cid', 'a.cid', 'client_name',
// 						'title', 'a.title',
// 						'alias', 'a.alias',
// 						'state', 'a.state',
// 						'ordering', 'a.ordering',
// 						'catid', 'a.catid', 'category_title',
// 						'checked_out', 'a.checked_out',
// 						'checked_out_time', 'a.checked_out_time',
// 						'created', 'a.created',
// 						'hits', 'a.hits',
// 						'state'
// 			);
// 		}
		
		parent::__construct();

// 		$config = JFactory::getConfig();

// 		Get the pagination request variables
// 		$this->setState('limit', $mainframe->getUserStateFromRequest('com_amcportfolio.limit', 'limit', $config->getValue('config.list_limit'), 'int'));
// 		$this->setState('limitstart', JRequest::getVar('limitstart', 0, '', 'int'));

// 		$this->_data	= null;
	}

	/**
	 * Returns the query
	 * @return string The query to be used to retrieve the rows from the database
	 */
// 	function _buildQuery()
// 	{
		// Get the WHERE clauses for the query
// 		$where		= $this->_buildContentWhere();
// 		$orderby	= $this->_buildContentOrderBy();
		
// 		$query = 'SELECT p.*,' .
// 				' c.title AS category, u.name AS editor, COUNT(i.id) AS numimages '.
// 				' FROM #__amcportfolio as p' .
// 				' LEFT JOIN #__categories AS c ON c.id = p.catid' .
// 				' LEFT JOIN #__users AS u ON u.id = p.checked_out' .
// 				' LEFT JOIN #__amcportfolio_images AS i ON (p.id = i.projectid)' .
// 				$where .
// 				' GROUP BY p.id' .
// 				$orderby;

// 		return $query;
// 	}

	/**
	* Method to get the maximum ordering value for each category.
	*
	* @since	4.0
	*/
// 	function &getCategoryOrders()
// 	{
// 		if (!isset($this->cache['categoryorders'])) {
// 			$db		= $this->getDbo();
// 			$query	= $db->getQuery(true);
// 			$query->select('MAX(ordering) as `max`, catid');
// 			$query->select('catid');
// 			$query->from('#__amcportfolio');
// 			$query->group('catid');
// 			$db->setQuery($query);
// 			$this->cache['categoryorders'] = $db->loadAssocList('catid', 0);
// 		}
// 		return $this->cache['categoryorders'];
// 	}
	
	/**
	* Build an SQL query to load the list data.
	*
	* @return	JDatabaseQuery
	* @since	4.0
	*/
	protected function getListQuery()
	{
		// Initialise variables.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
	
		// Select the required fields from the table.
		$query->select(
			$this->getState(
						'list.select',
						'a.id AS id, a.title AS title, a.alias AS alias,'.
						'a.checked_out AS checked_out,'.
						'a.checked_out_time AS checked_out_time, a.catid AS catid,' .
						'a.hits AS hits,'.
						'a.published AS published, a.ordering AS ordering,'.
						'a.featured AS featured'
			)
		);
		$query->from('`#__amcportfolio` as a');
	
		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id = a.checked_out');
	
		// Join over the categories.
		$query->select('c.title AS category_title');
		$query->join('LEFT', '#__categories AS c ON c.id = a.catid');
	
		// Join over the image counts per project.
		//$query->select('COUNT(i.id) AS numimages');
		//$query->join('LEFT', '#__amcportfolio_images AS i ON (a.id = i.projectid)');
	
		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.published = '.(int) $published);
		} else if ($published === '') {
			$query->where('(a.published IN (0, 1))');
		}
	
		// Filter by category.
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId)) {
			$query->where('a.catid = '.(int) $categoryId);
		}
	
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
				$query->where('(a.title LIKE '.$search.' OR a.alias LIKE '.$search.')');
			}
		}
	
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering','ordering');
		$orderDirn	= $this->state->get('list.direction','ASC');
// 		if ($orderCol == 'ordering' || $orderCol == 'category_title') {
// 			$orderCol = 'category_title '.$orderDirn.', ordering';
// 		}
		$query->order($db->getEscaped('a.featured DESC, '.$orderCol.' '.$orderDirn));
	
		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}
	
	/**
	 * Override default getItems to include project images
	 */
 	function getItems() {
 		$items = parent::getItems();
 		foreach($items as &$item) {
 			$model = JModel::getInstance('project','AMCPortfolioModel', array('ignore_request' => true));
 			$model->setState('project.id',$item->id);
 			$item->images = $model->getImages(); 
 		}
 		return $items;
 	}
	
	/**
	 * Returns a combined set of WHERE clauses for the SQL query
	 * @return string The where clauses for the query
	 */
// 	function _buildContentWhere()
// 	{
// 		global $mainframe, $option;
// 		$db					=& JFactory::getDBO();
		
		//Get stuff from published state filtering
// 		$filter_state		= $mainframe->getUserStateFromRequest( $option.'filter_state',		'filter_state',		'',				'word' );
		//Filtering by category
// 		$filter_catid		= $mainframe->getUserStateFromRequest( $option.'filter_catid',		'filter_catid',		0,				'int' );
		//Ordering
// 		$filter_order		= $mainframe->getUserStateFromRequest( $option.'filter_order',		'filter_order',		'p.ordering',	'cmd' );
// 		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		//Get stuff put into the search field
// 		$search				= $mainframe->getUserStateFromRequest( $option.'search',			'search',			'',				'string' );
// 		$search				= JString::strtolower( $search );

		//Build the array of WHERE clauses
// 		$where = array();

		//If we're filtering by category
// 		if ($filter_catid > 0) {
// 			$where[] = 'p.catid = '.(int) $filter_catid;
// 		}
		//If we have values in the search box
// 		if ($search) {
// 			$where[] = 'LOWER(p.title) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
// 		}
		//If we are filtering by published state
// 		if ( $filter_state ) {
// 			if ( $filter_state == 'P' ) {
// 				$where[] = 'p.published = 1';
// 			} else if ($filter_state == 'U' ) {
// 				$where[] = 'p.published = 0';
// 			}
// 		}

		//Collapse the array into a string of clauses
// 		$where 		= ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );

// 		return $where;
// 	}

	/**
	 * Builds a list of ORDER BY statements
	 * @return string	The ORDER BY clauses for the query
	 */
// 	function _buildContentOrderBy()
// 	{
// 		global $mainframe, $option;

// 		$filter_order		= $mainframe->getUserStateFromRequest( $option.'filter_order',		'filter_order',		'p.ordering',	'cmd' );
// 		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );

// 		if ($filter_order == 'a.ordering'){
// 			$orderby 	= ' ORDER BY category, p.ordering '.$filter_order_Dir;
// 		} else {
// 			$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.' , category, p.ordering ';
// 		}

// 		return $orderby;
// 	}
	
	
	/**
	 * Retrieves the projects data and stores it
	 * @return array Array of objects containing the data from the database
	 */
// 	function getData()
// 	{
//		Lets load the data if it doesn't already exist
// 		if (empty( $this->_data ))
// 		{
// 			$query = $this->_buildQuery();
// 			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
// 		}

// 		return $this->_data;
// 	}

	/**
	 * Method to get a pagination object of the collections for the gallery
	 *
	 * @access public
	 * @return integer
	 */
// 	function getPagination()
// 	{
//		Lets load the content if it doesn't already exist
// 		if (empty($this->_pagination))
// 		{
// 			jimport('joomla.html.pagination');
// 			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
// 		}

// 		return $this->_pagination;
// 	}

	/**
	 * Method to get the total number of projects
	 *
	 * @access public
	 * @return integer
	 */
// 	function getTotal()
// 	{
		// Lets load the content if it doesn't already exist
// 		if (empty($this->_total))
// 		{
			//TODO: This is SQL HEAVY!!! a count(*) would work better
// 			$query = $this->_buildQuery();
// 			$this->_total = $this->_getListCount($query);
// 		}

// 		return $this->_total;
// 	}

	/**
	* Method to auto-populate the model state.
	*
	* Note. Calling getState in this method will result in recursion.
	*
	* @since	4.0
	*/
// 	protected function populateState($ordering = null, $direction = null)
// 	{
//		Initialise variables.
// 		$app = JFactory::getApplication('administrator');
	
//		Load the filter state.
// 		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
// 		$this->setState('filter.search', $search);
	
// 		$state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
// 		$this->setState('filter.state', $state);
	
// 		$categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id', '');
// 		$this->setState('filter.category_id', $categoryId);
	
//		Load the parameters.
// 		$params = JComponentHelper::getParams('com_amcportfolio');
// 		$this->setState('params', $params);
	
//		List state information.
// 		parent::populateState('title', 'asc');
// 	}
}