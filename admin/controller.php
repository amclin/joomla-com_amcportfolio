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
	protected $default_view = 'projects';
	
	public function display($cachable = false, $urlparams = false)
	{
		$layout = JRequest::getCmd('layout', 'default');
		$id		= JRequest::getInt('id');

		parent::display();
		
		return $this;
	}
}
?>