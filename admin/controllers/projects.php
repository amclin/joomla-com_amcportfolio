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
	* Constructor.
	*
	* @param	array An optional associative array of configuration settings.
	* @see		JController
	* @since	4.1
	*/
	public function __construct($config = array())
	{
		parent::__construct($config);
	
		$this->registerTask('featured_unpublish',	'featured_publish');
	}
	
	/**
	 * Proxy for getModel.
	 * @since	4.0
	 */
	public function &getModel($name = 'Project', $prefix = 'AMCPortfolioModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	/**
	* Toggles featured state 
	* @since	4.1
	*/
	public function featured_publish()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
	
		// Initialise variables.
		$user	= JFactory::getUser();
		$ids	= JRequest::getVar('cid', array(), '', 'array');
		$values	= array('featured_publish' => 1, 'featured_unpublish' => 0);
		$task	= $this->getTask();
		$value	= JArrayHelper::getValue($values, $task, 0, 'int');
	
		if (empty($ids)) {
			JError::raiseWarning(500, JText::_('No projects selected.'));
		} else {
			// Get the model.
			$model	= $this->getModel();
	
			// Change the state of the records.
			if (!$model->feature($ids, $value)) {
				JError::raiseWarning(500, $model->getError());
			} else {
				if ($value == 1) {
					$ntext = 'Projects flagged as featured.';
				} else {
					$ntext = 'Projects unflagged.';
				}
				$this->setMessage(JText::plural($ntext, count($ids)));
			}
		}
	
		$this->setRedirect('index.php?option=com_amcportfolio&view=projects');
	}
}