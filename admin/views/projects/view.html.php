<?php

/**
 * @version		$Id$
 * @package		AMC Portfolio
 * @copyright	Copyright (C) 2007 Anthony McLin
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

/**
 * Projects View
 */
class AMCPortfolioViewProjects extends JView
{
	protected $categories;
	protected $items;
	protected $pagination;
	protected $state;
	
	/**
	 * Projects view display method
	 * @return void
	 **/
	function display($tpl = null)
	{			
		// Initialise variables.
		$this->categories	= $this->get('CategoryOrders');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

//		global $mainframe, $option;
		
// 		$db		=& JFactory::getDBO();
// 		$uri	=& JFactory::getURI();
		
		//Setup filters
// 		$filter_state		= $mainframe->getUserStateFromRequest( $option.'filter_state',		'filter_state',		'',				'word' );
// 		$filter_catid		= $mainframe->getUserStateFromRequest( $option.'filter_catid',		'filter_catid',		0,				'int' );
// 		$filter_order		= $mainframe->getUserStateFromRequest( $option.'filter_order',		'filter_order',		'a.ordering',	'cmd' );
// 		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );
// 		$search				= $mainframe->getUserStateFromRequest( $option.'search',			'search',			'',				'string' );
// 		$search				= JString::strtolower( $search );
		

		// Get data from the model
// 		$items		= & $this->get( 'Data');

		// Get a pagination object
// 		$pagination = $this->get('Pagination');
		
		// build list of categories
// 		$javascript 	= 'onchange="document.adminForm.submit();"';
// 		$lists['catid'] = JHTML::_('list.category',  'filter_catid', $option, intval( $filter_catid ), $javascript );

		// state filter
// 		$lists['state']	= JHTML::_('grid.state',  $filter_state );

		// table ordering
// 		$lists['order_Dir'] = $filter_order_Dir;
// 		$lists['order'] = $filter_order;
		
		// search filter
// 		$lists['search']= $search;

		// Assign variables for template
// 		$this->assignRef('items',		$items);
// 		$this->assignRef('lists',		$lists);
// 		$this->assignRef('user',		JFactory::getUser());
// 		$this->assignRef('pagination',	$pagination);


		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->addToolbar();
		parent::display($tpl);
	}
	
	/**
	* Add the page title and toolbar.
	*
	* @since	4.0
	*/
	protected function addToolbar() {
		JToolBarHelper::title(   JText::_( 'AMC Portfolio : Projects Manager' ), 'generic.png' );
		
		JToolBarHelper::addNew('project.add');
		JToolBarHelper::editList('project.edit');
		JToolBarHelper::publish('projects.publish', 'JTOOLBAR_PUBLISH', true);
		JToolBarHelper::unpublish('projects.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		JToolBarHelper::custom('projects.featured_publish', 'featured.png', 'featured_f2.png', 'JFEATURED', true);
		JToolBarHelper::custom('projects.featured_unpublish', 'remove.png', 'remove.png', 'UnFeature', true);
		JToolBarHelper::deleteList('','projects.delete');
	}
}
?>