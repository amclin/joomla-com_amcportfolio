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

/**
 * Project View
 */
class AMCPortfolioViewProject extends JView
{
	/**
	 * display method of Project view
	 * @return void
	 **/
	function display($tpl = null)
	{
		global $option;

		$lists = array();
		//get the project
		$project		=& $this->get('Data');
		$isNew		= ($project->id < 1);

		$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
		JToolBarHelper::title(   JText::_( 'AMC Portfolio : Project Editor' ).' : <small>[ ' . $text.' ]</small>' );
		JToolBarHelper::save();
		if ($isNew)  {
			JToolBarHelper::cancel();
		} else {
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}

		// build the html select list of categories
		$lists['catid'] 		= JHTML::_('list.category',  'catid', $option, intval( $project->catid ) );

		// build the html select list for Published state
		$lists['published'] 	= JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $project->published );

		// build the html select list for image folders
			// Get the tree of folders
			$folders = JFolder::listFolderTree(JPATH_ROOT.DS.'images','', 7);

			// Make sure the root images folder is an option
			$imagefolder = array();
			$imagefolder[0]['id'] 		= 0;
			$imagefolder[0]['parent'] 	= NULL;
			$imagefolder[0]['name'] 	= '/';
			$imagefolder[0]['fullname'] = JPATH_ROOT . DS . 'images';
			$imagefolder[0]['relname'] 	= '/images';
			$folders = array_merge( $imagefolder, $folders);

			//Ensure we get URL valid slashes on Windows systems			
			foreach($folders as $key=>$folder) {
				$folders[$key]['relname'] = str_replace(DS,'/',$folder['relname']);			
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
						if(eregi( "\.bmp|\.gif|\.jpg|\.png", $file) )
						{
							$imageFile = $folder['relname'] . '/' . $file;
							$images[$folder['relname']][] = JHTML::_('select.option', $imageFile, $file );
						}
						//Add movie to list
						if(eregi( "\.flv|\.mov|\.avi|\.mpg|\.mpeg|\.divx", $file) )
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
		for($i = 0, $n = count($project->images); $i < $n; $i++)
		{
			$image = &$project->images[$i];
			$image->name = JFile::getName($image->image);
		}

		//Build options for the saved movie list
		for($i = 0, $n = count($project->movies); $i < $n; $i++)
		{
			$movie = &$project->movies[$i];
			$movie->name = JFile::getName($movie->movie);
		}

			$javascript     = "onchange=\"previewImage( 'imagelist', 'view_imagelist', '" . JURI::root() . "images/' ) \"";
		$lists['imagelist']		= JHTML::_('select.genericlist', $project->images, 'imagelist', 'class="inputbox" size="10" '. $javascript, 'image', 'name' );
		$lists['movielist']		= JHTML::_('select.genericlist', $project->movies, 'movielist', 'class="inputbox" size="10" '             , 'movie', 'name' );

		//Allow Joomla WYSIWYG editor
		$editor =& JFactory::getEditor();

		// Set template variables
		$this->assignRef('folders',		$folders);
		$this->assignRef('images',		$images);
		$this->assignRef('movies',      $movies);
		$this->assignRef('lists',		$lists);
		$this->assignRef('project',		$project);
		$this->assignRef('editor',		$editor );

		parent::display($tpl);
	}
}
?>