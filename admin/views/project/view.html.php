<?php

/**
 * @version		$Id$
 * @package		AMC Portfolio
 * @copyright	Copyright (C) 2007 Anthony McLin
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );
jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder');

/**
 * Project View
 */
class AMCPortfolioViewProject extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;
	
	/**
	 * display method of Project view
	 * @return void
	 **/
	function display($tpl = null)
	{
		// Initialise variables.
		$this->form	= $this->get('Form');
		$this->item	= $this->get('Item');
		$this->state	= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$lists = array();
		//get the project
		$isNew		= ($this->item->id == 0);

// 		$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
// 		JToolBarHelper::title(   JText::_( 'AMC Portfolio : Project Editor' ).' : <small>[ ' . $text.' ]</small>' );
// 		JToolBarHelper::save();
// 		if ($isNew)  {
// 			JToolBarHelper::cancel();
// 		} else {
//			for existing items the button is renamed `close`
// 			JToolBarHelper::cancel( 'cancel', 'Close' );
// 		}

		// build the html select list of categories
// 		$lists['catid'] 		= JHTML::_('list.category',  'catid', $option, intval( $project->catid ) );

		// build the html select list for Published state
// 		$lists['published'] 	= JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $project->state );

		// build the html select list for image folders
			// Get the tree of folders
			$folders = JFolder::listFolderTree(JPATH_ROOT.DIRECTORY_SEPARATOR .'images','', 7);

			// Make sure the root images folder is an option
			$imagefolder = array();
			$imagefolder[0]['id'] 		= 0;
			$imagefolder[0]['parent'] 	= NULL;
			$imagefolder[0]['name'] 	= '/';
			$imagefolder[0]['fullname'] = JPATH_ROOT . DIRECTORY_SEPARATOR  . 'images';
			$imagefolder[0]['relname'] 	= '/images';
			$folders = array_merge( $imagefolder, $folders);

			//Ensure we get URL valid slashes on Windows systems			
			foreach($folders as $key=>$folder) {
				$folders[$key]['relname'] = str_replace(DIRECTORY_SEPARATOR ,'/',$folder['relname']);			
			}
			
//TODO: Parse Folders list and drop any entries without images in them

			// Parse folder list and find image files
			$images = array();
			$movies = array();
			//Add an emtpy item to the movie list just in case there are no movies
			$movies['/images'][] = JHTML::_('select.option', '', '');
			foreach($folders as $folder)
			{
				$imgFiles = JFolder::files( $folder['fullname']);
				if(count($imgFiles) >= 1)  // Ignore folders without files
				{
					$folderlist[] = $folder;
					foreach($imgFiles as $file)
					{
						//Add image to list
						if(preg_match( "/\.bmp|\.gif|\.jpg|\.png/i", $file) )
						{
							$imageFile = $folder['relname'] . '/' . $file;
							$images[$folder['relname']][] = JHTML::_('select.option', $imageFile, $file );
						}
						//Add movie to list
						if(preg_match( "/\.flv|\.mov|\.avi|\.mpg|\.mpeg|\.divx/i", $file) )
						{
							$movieFile = $folder['relname'] . '/' . $file;
							$movies[$folder['relname']][] = JHTML::_('select.option', $movieFile, $file );
						}
					}
				}
			}

			// Set javascript for the input
			$javascript			= "onchange=\"changeDynaList( 'imagefiles', folderimages, document.adminForm.folders.options[document.adminForm.folders.selectedIndex].value, 0, 0); previewImage( 'imagefiles', 'view_imagefiles', '".JURI::root() . "images/' )\"";
		$lists['folders']		= JHTML::_('select.genericlist', $folderlist, 'folders', 'class="inputbox" size="1" style="vertical-align:top;" '. $javascript, 'relname', 'relname', $folders[0]['relname'] );
			$javascript			= "onchange=\"changeDynaList( 'moviefiles', foldermovies, document.adminForm.folders2.options[document.adminForm.folders2.selectedIndex].value, 0, 0); \"";
		$lists['folders2']		= JHTML::_('select.genericlist', $folderlist, 'folders2', 'class="inputbox" size="1" style="vertical-align:top;" '. $javascript, 'relname', 'relname', $folders[0]['relname'] );


		// build the html select list for images in folders

			// Set the javascript for the input
			$javascript     	= "onchange=\"previewImage( 'imagefiles', 'view_imagefiles', '".JURI::root() . "images/' )\" onfocus=\"previewImage( 'imagefiles', 'view_imagefiles', '".JURI::root() . "images/' )\"";
		$lists['imagefiles']	= JHTML::_('select.genericlist', $images['/images'], 'imagefiles', 'class="inputbox" size="10" multiple="multiple" '. $javascript, 'value', 'text' );
		$lists['moviefiles']	= JHTML::_('select.genericlist', $movies['/images'], 'moviefiles', 'class="inputbox" size="10" multiple="multiple" '             , 'value', 'text' );

		// Take the saved image list and make
		for($i = 0, $n = count($this->item->images); $i < $n; $i++)
		{
			$image = &$this->item->images[$i];
			$image->name = JFile::getName($image->image);
		}

		//Build options for the saved movie list
		for($i = 0, $n = count($this->item->movies); $i < $n; $i++)
		{
			$movie = &$this->item->movies[$i];
			$movie->name = JFile::getName($movie->movie);
		}

			$javascript     = "onchange=\"previewImage( 'imagelist', 'view_imagelist', '" . JURI::root() . "images/' ) \"";
		$lists['imagelist']		= JHTML::_('select.genericlist', $this->item->images, 'imagelist', 'class="inputbox" size="10" '. $javascript, 'image', 'name' );
		$lists['movielist']		= JHTML::_('select.genericlist', $this->item->movies, 'movielist', 'class="inputbox" size="10" '             , 'movie', 'name' );

		//Allow Joomla WYSIWYG editor
		$editor = JFactory::getEditor();

		// Set template variables
		$this->assignRef('folders',		$folders);
		$this->assignRef('images',		$images);
		$this->assignRef('movies',      $movies);
		$this->assignRef('lists',		$lists);
		$this->assignRef('project',		$this->item);
		$this->assignRef('editor',		$editor );

		$this->addToolbar();
		parent::display($tpl);
	}
	
	
	/**
	* Add the page title and toolbar.
	*
	* @since	4.0
	*/
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);
		
		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		
		$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
		JToolBarHelper::title(   JText::_( 'AMC Portfolio : Project Editor' ).' : ' . $text );
		
		// If not checked out, can save the item.
		if (!$checkedOut) {
			JToolBarHelper::apply('project.apply');
			JToolBarHelper::save('project.save');
			JToolBarHelper::save2new('project.save2new');
		}
	
		if (empty($this->item->id))  {
			JToolBarHelper::cancel('project.cancel');
		}
		else {
			JToolBarHelper::cancel('project.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
?>