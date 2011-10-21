<?php

/**
 * @version		$Id$
 * @package		AMC Portfolio
 * @copyright	Copyright (C) 2007 Anthony McLin
 * @license		GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * AMCPortfolio Project Table class
 */
class TableProject extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id 				= null;

	/**
	 * Category ID
	 * @var int
	 */
	var $catid = 0;

	/**
	 * Project Title
	 * @var string
	 */
	var $title 				= null;

	/**
	 * Project Title Alias
	 * @var string
	 */
	var $alias				= null;

	/**
	 * Project Teaser text
	 * @var string
	 */
	var $teaser 			= null;

	/**
	 * Published State
	 * @var boolean
	 */
	var $published			= 0;

	/**
	 * If a project is checked out, and by who
	 * @var int
	 */
	var $checked_out = 0;

	/**
	 * @var time
	 */
	var $checked_out_time = 0;

	/**
	 * Project Ordering
	 * @var int
	 */
	var $ordering = 0;

	/**
	 * Project descriptive text
	 */
	var $description = '';

	/**
	 * Project Visits
	 * @var int
	 */
	var $hits = 0;

	/**
	 * Project external link
	 */
	var $outside_link = '';

	/**
	 * Project external link text
	 */
	var $outside_link_text = '';

	/**
	 * Number of visits to the project
	 * @var int				= 0;
	 */

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableProject(& $db) {
		parent::__construct('#__amcportfolio', 'id', $db);
	}

	/**
	 * Overloaded check function
	 *
	 * @access public
	 * @return boolean
	 */
	function check()
	{
		// check for valid name
		if (trim( $this->title ) == '')
		{
			$this->setError(JText::_( 'Your Project must contain a title.' ));
			return false;
		}

		$alias = JFilterOutput::stringURLSafe($this->title);

		if(empty($this->alias) || $this->alias === $alias ) {
			$this->alias = $alias;
		}

		return true;
	}
}
?>