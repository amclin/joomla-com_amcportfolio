<?php

/**
 * @version		$Id$
 * @package		AMC Portfolio
 * @copyright	Copyright (C) 2007 Anthony McLin
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the AMCPortfolio component
 *
 * @static
 */
class AMCPortfolioViewProject extends JView
{
	protected $state;
	protected $item;
	
	function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$params		= $app->getParams();
		
		// Get some data from the models
		$state		= $this->get('State');
		$item		= $this->get('Item');
		$category	= $this->get('Category');
		
		if ($this->getLayout() == 'edit') {
			$this->_displayEdit($tpl);
			return;
		}
		
		// Add router helpers.
		$item->slug			= $item->alias ? ($item->id.':'.$item->alias) : $item->id;
		$category->slug		= $category->alias ? ($item->catid.':'.$category->alias) : $item->catid;
				
// 		global $mainframe;

		//get the project
// 		$project =& $this->get('data');

		//get the project's category
// 		$category =& $this->get('category');
		
		// Get the page/component configuration
// 		$params = &$mainframe->getParams();

		//Cleanup the image URLs and make them absolute
		foreach($item->images as $key=>$image)
		{
			$image->path = JURI::root() . (ltrim( $image->image, '/') );
			$item->images[$key] = $image;
		}


		//build links for sibling projects
		if(count($category->projects) > 0) {
			foreach($category->projects as $key=>$project) {
				$project->slug	= $project->alias ? ($project->id.':'.$project->alias) : $project->id;
				$project->link = JRoute::_( 'index.php?view=project&catid='.$category->slug.'&id='. $project->slug);
				//Cleanup the image URLs and make them absolute
				foreach($item->images as $count=>$image) {
					$image->path = JURI::root() . (ltrim( $image->image, '/') );
					$item->images[$count] = $image;
				}
				$category->projects[$key] = $project;
			}
		}

// 		$k = 0;
// 		$count = count($category->projects);
// 		for($i = 0; $i < $count; $i++)
// 		{
// 			$item =& $category->projects[$i];

// 			$link = JRoute::_( 'index.php?view=project&catid='.$category->slug.'&id='. $item->slug);

// 			$item->link = $link;

			//Cleanup the image URLs and make them absolute
// 			foreach($item->images as $key=>$image)
// 			{
// 				$image->path = JURI::root() . (ltrim( $image->image, '/') );
// 				$item->images[$key] = $image;
// 			}

// 			$item->odd		= $k;
// 			$item->count	= $i;
// 			$k = 1 - $k;
// 		}

		// Set template variables
		$this->assignRef('state',		$state);
		$this->assignRef('params',		$params);
		$this->assignRef('project',		$item);
		$this->assignRef('category',	$category);

		parent::display($tpl);
	}
}
?>