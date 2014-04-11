<?php

// no direct access
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * AMCPortfolio Component Category Tree
 *
 * @static
 * @package		AMCPortfolio
 * @since 4.0
 */
class AMCPortfolioCategories extends JCategories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__amcportfolio';
		$options['extension'] = 'com_amcportfolio';
		parent::__construct($options);
	}
}
