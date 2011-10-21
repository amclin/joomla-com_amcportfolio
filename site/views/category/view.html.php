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
class AMCPortfolioViewCategory extends JView
{
	function display( $tpl = null )
	{
		global $mainframe;

		// Initialize some variables
		$document	= &JFactory::getDocument();
		$uri 		= &JFactory::getURI();
		$pathway	= &$mainframe->getPathway();

		// Get the parameters of the active menu item
		$menus = &JSite::getMenu();
		$menu  = $menus->getActive();

		// Get some data from the model
		$items		= &$this->get('data' );
		$category	= &$this->get('category' );

		// Get the page/component configuration
		$params = &$mainframe->getParams();

		// Set page title per category
		$document->setTitle( $category->title);

		//set breadcrumbs
		if(is_object($menu) && $menu->query['view'] != 'category') {
			$pathway->addItem($category->title, '');
		}

		// Set some defaults if not set for params
		$params->def('com_description', JText::_('AMC Portfolio categories'));

		$k = 0;
		$count = count($items);
		for($i = 0; $i < $count; $i++)
		{
			$item =& $items[$i];

			$item->link = JRoute::_( 'index.php?view=project&catid='.$category->slug.'&id='. $item->slug);

			$menuclass = 'category'.$params->get( 'pageclass_sfx' );

//			$itemParams = new JParameter($item->params);

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

		$this->assignRef('params',		$params);
		$this->assignRef('category',	$category);
		$this->assignRef('projects',		$items);

		$this->assign('action',	$uri->toString());

		parent::display($tpl);
	}
}
?>