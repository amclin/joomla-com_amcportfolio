<?php

/**
 * @version		$Id$
 * @package		AMC Portfolio
 * @copyright	Copyright (C) 2007 Anthony McLin
 * @license		GNU/GPL
 */

jimport('joomla.application.component.controller');

/**
 * AMCPortfolio Component Controller
 */
class AMCPortfolioController extends JController
{
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function display()
	{
		//Set the default view if none exists
		$view = JRequest::getCmd('view');
		if(empty($view)) {
			JRequest::setVar('view', 'projects');
		};

		parent::display();
	}
}
?>