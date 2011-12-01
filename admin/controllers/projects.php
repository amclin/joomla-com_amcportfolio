<?php

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Projects list controller class.
 *
 * @package		AMCPortfolio
 * @since		4.0
 */
class AMCPortfolioControllerProjects extends JControllerAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	4.0
	 */
	protected $text_prefix = 'projects';

	/**
	 * Proxy for getModel.
	 * @since	4.0
	 */
	public function &getModel($name = 'Project', $prefix = 'AMCPortfolioModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}