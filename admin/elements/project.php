<?php

/**
 * @version		$Id$
 * @package		AMC Portfolio
 * @copyright	Copyright (C) 2007 Anthony McLin
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Provides a list of projects for a popup select
 */
class JFormFieldProject extends JFormField
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	function getInput()
	{	
		$options = array();
		$db =& JFactory::getDBO();

		$query = 'SELECT p.id, p.title'
		. ' FROM #__amcportfolio AS p'
		. ' WHERE p.published = 1'
		. ' ORDER BY p.title'
		;
		$db->setQuery( $query );
		$options = $db->loadObjectList();

 		foreach($options as &$option) {
 			$option->title = $option->title.'  (id:'.$option->id.')';
 		}

 		return JHTML::_('select.genericlist',  $options, 'jform[request][id]', 'class="inputbox"', 'id', 'title', $this->value, 'jform_request_id' );
	}
}