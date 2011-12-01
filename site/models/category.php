<?php

/**
 * @version		$Id$
 * @package		AMC Portfolio
 * @copyright	Copyright (C) 2007 Anthony McLin
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.modellist');
jimport('joomla.application.categories');

/**
 * AMCPortfolio Component Category Model
 */
class AMCPortfolioModelCategory extends JModelList
{
	/**
	 * Category items data
	 *
	 * @var array
	 */
	protected $_item = null;

	protected $_articles = null;

	protected $_siblings = null;

	protected $_children = null;

	protected $_parent = null;
	
	/**
	* The category that applies.
	*
	* @access	protected
	* @var		object
	*/
	protected $_category = null;
	
	/**
	 * The list of other newfeed categories.
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $_categories = null;
	
	protected $_projects = null;
	
	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
// 	function __construct()
// 	{
// 		parent::__construct();

// 		global $mainframe;

// 		$config = JFactory::getConfig();

// 		$id = JRequest::getVar('id', 0, '', 'int');
// 		$this->setId((int)$id);
// 	}

	/**
	 * Method to set the category id
	 *
	 * @access	public
	 * @param	int	Category ID number
	 */
// 	function setId($id)
// 	{
// 		Set category ID and wipe data
// 		$this->_id			= $id;
// 		$this->_category	= null;
// 	}

	/**
	 * Method to get project item data for the category
	 *
	 * @access public
	 * @return array
	 */
// 	function getData()
// 	{
		// Lets load the data if it doesn't already exist
// 		if (empty( $this->_data ))
// 		{
// 			$query = $this->_buildQuery();
// 			$this->_data = $this->_getList( $query );

// 			$total = count($this->_data);
// 			for($i = 0; $i < $total; $i++)
// 			{
				//Build item:alias combos for SEO
// 				$item =& $this->_data[$i];
// 				$item->slug = $item->id.'-'.$item->alias;

				//Get image list for each project
// 				$query = 'SELECT * FROM #__amcportfolio_images' .
// 						' WHERE projectid = ' . $item->id .
// 						' ORDER BY ID';  // Preserves the image ordering
// 				$item->images = $this->_getList($query);

				//Get movie list for each project
// 				$query = 'SELECT * FROM #__amcportfolio_movies' .
// 						' WHERE projectid = ' . $item->id .
// 						' ORDER BY ID';  // Preserves the image ordering
// 				$item->movies = $this->_getList($query);
// 			}
// 		}

// 		return $this->_data;
// 	}

	/**
	* Method to auto-populate the model state.
	*
	* Note. Calling getState in this method will result in recursion.
	*
	* @since	1.6
	*/
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app	= JFactory::getApplication();
		$params	= JComponentHelper::getParams('com_amcportfolio');
	
		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$this->setState('list.limit', $limit);
	
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$this->setState('list.start', $limitstart);
	
		$orderCol	= JRequest::getCmd('filter_order', 'ordering');
		if (!in_array($orderCol, $this->filter_fields)) {
			$orderCol = 'ordering';
		}
		$this->setState('list.ordering', $orderCol);
	
		$listOrder	=  JRequest::getCmd('filter_order_Dir', 'ASC');
		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', ''))) {
			$listOrder = 'ASC';
		}
		$this->setState('list.direction', $listOrder);
	
		$id = JRequest::getVar('id', 0, '', 'int');
		$this->setState('category.id', $id);
	
		$user = JFactory::getUser();
		if ((!$user->authorise('core.edit.state', 'com_amcportfolio')) &&  (!$user->authorise('core.edit', 'com_amcportfolio'))){
			// limit to published for people who can't edit or edit.state.
			$this->setState('filter.state',	1);
		}
	
		// Load the parameters.
		$this->setState('params', $params);
	}
	
	
	
	/**
	 * Method to get the selected category data
	 * @return	object	The data for a category
	 */
// 	function getCategory()
// 	{
		// Load the Category data
// 		if ($this->_loadCategory())
// 		{
			// Initialize some variables
// 			$user = &JFactory::getUser();

			// Make sure the category is published
// 			if (!$this->_category->published) {
// 				JError::raiseError(404, JText::_("Resource Not Found"));
// 				return false;
// 			}
// 			check whether category access level allows access
// 			if ($this->_category->access > $user->get('aid', 0)) {
// 				JError::raiseError(403, JText::_("ALERTNOTAUTH"));
// 				return false;
// 			}
// 		}
// 		return $this->_category;
// 	}
	
	/**
	* Method to get category data for the current category
	*
	* @param	int		An optional ID
	*
	* @return	object
	* @since	1.5
	*/
	public function getCategory($id = null) {
		if(empty($id)) {
			$id = $this->getState('category.id', 'root');
		} else {
			$this->setState('category.id',$id);
		}
		if(!is_object($this->_item))
		{
			$app = JFactory::getApplication();
			$menu = $app->getMenu();
			$active = $menu->getActive();
			$params = new JRegistry();

			if($active)
			{
				$params->loadString($active->params);
			}

			$options = array();
			//$options['countItems'] = $params->get('show_cat_items', 1) || $params->get('show_empty_categories', 0);
			$categories = JCategories::getInstance('AMCPortfolio', $options);
			$this->_item = $categories->get($id);
			if(is_object($this->_item))
			{
				$this->_children = $this->_item->getChildren();
				$this->_parent = false;
				if($this->_item->getParent())
				{
					$this->_parent = $this->_item->getParent();
				}
				$this->_rightsibling = $this->_item->getSibling();
				$this->_leftsibling = $this->_item->getSibling(false);
				
			} else {
				$this->_children = false;
				$this->_parent = false;
			}
		}

		return $this->_item;
	}

	/**
	 * Returns the query
	 * @return string The query to be used to retrieve the rows from the database
	 */
// 	function _buildQuery()
// 	{
// 		$where = ' WHERE p.catid = ' . $this->_id;
// 		$where .= ' AND p.published = 1';

// 		$query = 'SELECT p.*,' .
// 				' cc.title AS category, u.name AS editor, COUNT(i.id) AS numimages' .
// 				' FROM #__amcportfolio as p' .
// 				' LEFT JOIN #__categories AS cc ON cc.id = p.catid' .
// 				' LEFT JOIN #__users AS u ON u.id = p.checked_out' .
// 				' LEFT JOIN #__amcportfolio_images AS i ON i.projectid = p.id' .
// 				$where .
// 				' GROUP BY p.id' .
// 				' ORDER BY p.ordering';

// 		return $query;
// 	}

	/**
	 * Method to load category data if it doesn't exist.
	 *
	 * @access	private
	 * @return	boolean	True on success
	 */
// 	function _loadCategory()
// 	{
// 		if (empty($this->_category))
// 		{
//			current category info
// 			$query = 'SELECT c.*, ' .
// 				' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as slug '.
// 				' FROM #__categories AS c' .
// 				' WHERE c.id = '. (int) $this->_id .
// 				' AND c.section = "com_amcportfolio"';
// 			$this->_db->setQuery($query, 0, 1);
// 			$this->_category = $this->_db->loadObject();
// 		}
// 		return true;
// 	}

	/**
	* Get the articles in the category
	*
	* @return	mixed	An array of articles or false if an error occurs.
	* @since	4.0
	*/
	function getItems()
	{
		$params = $this->getState()->get('params');
// 		$limit = $this->getState('list.limit');
	
 		if ($this->_projects === null && $category = $this->getCategory()) {
// 			$model = JModel::getInstance('Projects', 'AMCPortfolioModel', array('ignore_request' => true));
// 			$model->setState('params', JFactory::getApplication()->getParams());
// 			$model->setState('filter.category_id', $category->id);
// 			$model->setState('filter.published', $this->getState('filter.published'));
// 			$model->setState('filter.access', $this->getState('filter.access'));
// 			$model->setState('list.ordering', 'a.ordering');
// 			$model->setState('list.start', $this->getState('list.start'));
// 			$model->setState('list.limit', $limit);
// 			$model->setState('list.direction', $this->getState('list.direction'));
// 			$model->setState('list.filter', $this->getState('list.filter'));
// 			filter.subcategories indicates whether to include articles from subcategories in the list or blog
// 			$model->setState('filter.subcategories', $this->getState('filter.subcategories'));
// 			$model->setState('filter.max_category_levels', $this->setState('filter.max_category_levels'));
// 			$model->setState('list.links', $this->getState('list.links'));
	
// 			if ($limit >= 0) {
// 				$this->_projects = $model->getItems();
	
// 				if ($this->_projects === false) {
// 					$this->setError($model->getError());
// 				}
// 			}
// 			else {
// 				$this->_projects=array();
// 			}
	
// 			$this->_pagination = $model->getPagination();

 			$model = JModel::getInstance('Projects','AMCPortfolioModel', array('ignore_request' => true));
 			$model->setState('params',JFactory::getApplication()->getParams());
 			$model->setState('list.ordering','ordering');
 			$model->setState('list.direction',$this->getState('list.direction','ASC'));
 			$model->setState('filter.published', $this->getState('filter.published',1));
 			
 			//$model->setState('filter.state',1);
 			$model->setState('filter.category_id',$this->_item->id);
 			$this->_projects = $model->getItems();
 			
 			return $this->_projects;
 		}
	}
	
	/**
	* Build the orderby for the query
	*
	* @return	string	$orderby portion of query
	* @since	4.0
	*/
	protected function _buildContentOrderBy()
	{
		$app		= JFactory::getApplication('site');
		$db			= $this->getDbo();
		$params		= $this->state->params;
		$itemid		= JRequest::getInt('id', 0) . ':' . JRequest::getInt('Itemid', 0);
		$orderCol	= $app->getUserStateFromRequest('com_amcportfolio.category.list.' . $itemid . '.filter_order', 'filter_order', '', 'string');
		$orderDirn	= $app->getUserStateFromRequest('com_amcportfolio.category.list.' . $itemid . '.filter_order_Dir', 'filter_order_Dir', '', 'cmd');
		$orderby	= ' ';
	
		if (!in_array($orderCol, $this->filter_fields)) {
			$orderCol = null;
		}
	
		if (!in_array(strtoupper($orderDirn), array('ASC', 'DESC', ''))) {
			$orderDirn = 'ASC';
		}
	
		if ($orderCol && $orderDirn) {
			$orderby .= $db->getEscaped($orderCol) . ' ' . $db->getEscaped($orderDirn) . ', ';
		}
	
		$articleOrderby		= $params->get('orderby_sec', 'rdate');
		$articleOrderDate	= $params->get('order_date');
		$categoryOrderby	= $params->def('orderby_pri', '');
		$secondary			= ContentHelperQuery::orderbySecondary($articleOrderby, $articleOrderDate) . ', ';
		$primary			= ContentHelperQuery::orderbyPrimary($categoryOrderby);
	
		$orderby .= $db->getEscaped($primary) . ' ' . $db->getEscaped($secondary) . ' a.created ';
	
		return $orderby;
	}
}
?>