<?php

/**
 * @version		$Id$
 * @package		AMC Portfolio
 * @copyright	Copyright (C) 2007 Anthony McLin
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

/**
 * AMCPortfolio Component Controller
 */
class AMCPortfolioController extends JController
{
	/**
	 * Method to show a project view
	 *
	 * @access	public
	 */
	function display()
	{
		// Set a default view if none exists
		if ( ! JRequest::getCmd( 'view' ) ) {
			JRequest::setVar('view', 'categories' );
		}

		//update the hit count for the weblink
		if(JRequest::getCmd('view') == 'project')
		{
			$model =& $this->getModel('project');
			$model->hit();
		}

		parent::display();
	}
}
?>