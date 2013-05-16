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
						'a.teaser AS teaser,'.
						'a.description AS description,'.
						'a.checked_out AS checked_out,'.
						'a.checked_out_time AS checked_out_time, a.catid AS catid,' .
						'a.hits AS hits,'.
						'a.published AS published, a.ordering AS ordering,'.
						'a.featured AS featured'
			)
		);
		$query->from('`#__amcportfolio` as a');
	
		// Join over the users for the checked out user.
		$query->select('uc.name AS editor')
			->join('LEFT', '#__users AS uc ON uc.id = a.checked_out');
	
		// Join over the categories.
		$query->select('c.title AS category_title')
			->join('LEFT', '#__categories AS c ON c.id = a.catid');
	
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
		if ($orderCol == 'a.ordering' || $orderCol == 'category_title') {
			$orderCol = 'category_title '.$orderDirn.', a.ordering';
		}
		$query->order($db->escape('a.featured DESC, '.$orderCol.' '.$orderDirn));
	
		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}
	
	/**
	 * Override default getItems to include project images
	 */
  	function getItems() {
  		$items = parent::getItems();
  		foreach($items as &$item) {
  			$model = JModelLegacy::getInstance('project','AMCPortfolioModel', array('ignore_request' => true));
 			$model->setState('project.id',$item->id);
  			$item->images = $model->getImages($item->id); 
  		}
 // 		print_r($items);
//  		break;
  		
 		return $items;
 	}
}