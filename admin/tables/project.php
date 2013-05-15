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
	
	/**
	 * Method to set the featured state for a row or list of rows in the database
	 * table.  The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.
	 *
	 * @param   mixed    $pk      An optional array of primary key values to update.  If not
	 *                            set the instance property value is used.
	 * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]
	 * @param   integer  $userId  The user id of the user performing the operation.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link    http://docs.joomla.org/JTable/publish
	 * @since   4.1
	 */
	public function feature($pks = null, $state = 1, $userId = 0)
	{
		// Initialise variables.
		$k = $this->_tbl_key;

		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state  = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks)) {
			if ($this->$k) {
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else {
				$e = new JException(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				$this->setError($e);

				return false;
			}
		}

		// Update the publishing state for rows with the given primary keys.
		$query = $this->_db->getQuery(true);
		$query->update($this->_tbl);
		$query->set('featured = '.(int) $state);

		// Determine if there is checkin support for the table.
		if (property_exists($this, 'checked_out') || property_exists($this, 'checked_out_time')) {
			$query->where('(checked_out = 0 OR checked_out = '.(int) $userId.')');
			$checkin = true;
		}
		else {
			$checkin = false;
		}

		// Build the WHERE clause for the primary keys.
		$query->where($k.' = '.implode(' OR '.$k.' = ', $pks));

		$this->_db->setQuery($query);

		// Check for a database error.
		if (!$this->_db->query()) {
			$e = new JException(JText::sprintf('Changing featured state failed', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);

			return false;
		}

		// If checkin is supported and all rows were adjusted, check them in.
		if ($checkin && (count($pks) == $this->_db->getAffectedRows())) {
			// Checkin the rows.
			foreach($pks as $pk)
			{
				$this->checkin($pk);
			}
		}

		// If the JTable instance value is in the list of primary keys that were set, set the instance.
		if (in_array($this->$k, $pks)) {
			$this->featured = $state;
		}

		$this->setError('');
		return true;
	}
}
?>