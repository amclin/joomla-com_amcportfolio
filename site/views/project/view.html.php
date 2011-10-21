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
	function display($tpl = null)
	{
		global $mainframe;

		//get the project
		$project =& $this->get('data');

		//get the project's category
		$category =& $this->get('category');
		
		// Get the page/component configuration
		$params = &$mainframe->getParams();

		//Cleanup the image URLs and make them absolute
		foreach($project->images as $key=>$image)
		{
			$image->path = JURI::root() . (ltrim( $image->image, '/') );
			$project->images[$key] = $image;
		}


		//build links for projects
		$k = 0;
		$count = count($category->projects);
		for($i = 0; $i < $count; $i++)
		{
			$item =& $category->projects[$i];

			$link = JRoute::_( 'index.php?view=project&catid='.$category->slug.'&id='. $item->slug);

			$item->link = $link;

			//Cleanup the image URLs and make them absolute
			foreach($item->images as $key=>$image)
			{
				$image->path = JURI::root() . (ltrim( $image->image, '/') );
				$item->images[$key] = $image;
			}

			$item->odd		= $k;
			$item->count	= $i;
			$k = 1 - $k;
		}

		// Set template variables
		$this->assignRef('params',		$params);
		$this->assignRef('project',		$project);
		$this->assignRef('category',	$category);

		parent::display($tpl);
	}
}
?>