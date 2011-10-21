<?php
/**
 * sh404SEF support for AMC Portfolio component
 * @version		$Id$
 * @package		AMC Portfolio
 * @copyright	Copyright (C) 2009 Anthony McLin
 * @license		GNU/GPL
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

// ------------------  standard plugin initialize function - don't change ---------------------------
global $sh_LANG, $sefConfig;
$shLangName = '';
$shLangIso = '';
$title = array();
$shItemidString = '';
$dosef = shInitializePlugin( $lang, $shLangName, $shLangIso, $option);
if ($dosef == false) return;


//Attempt to get the project name via component prefix
$shProjectName = shGetComponentPrefix($option);

//If there's no project name, try to get it from the menu system
if(empty($shProjectName))
{
	$shProjectName = getMenuTitle($option, $view, $Itemid, null, $shLangName);
}

//If we still don't have a proper name, use "Portfolio"
if(empty($shProjectName) || $shProjectName == '/') {
	$shProjectName = 'Porfolio';
}

//Provide the name for sh404sef
$title[] = $shProjectName;


$view = isset($view) ? $view : null;

switch ($view) {
	case 'project':
		if (!empty($catid)) { // V 1.2.4.q
			$title[] = sef_404::getcategories($catid, $shLangName);
		}

		if (!empty($id)) {

			$query = 'SELECT title, id FROM #__amcportfolio WHERE id = "'.$id.'"';
			$database->setQuery($query);

			if (shTranslateURL($option, $shLangName))

			$rows = $database->loadObjectList( );

			else  $rows = $database->loadObjectList( null, false);

			if ($database->getErrorNum()) {

				JError::raiseError(500, $database->stderr() );

			}elseif( @count( $rows ) > 0 ){

				if( !empty( $rows[0]->name ) ){

					$title[] = $rows[0]->name;

				}

			}

		}

		else $title[] = '/'; // V 1.2.4.s

		break;

	case 'category':
		if (!empty($id)) { // V 1.2.4.q
			$title[] = sef_404::getcategories($id, $shLangName);
			$title[] = '/';
		}
		break;

	default:
		$title[] = '/'; // V 1.2.4.s
		break;
}

//Strip out stuff we don't need from the URL now that SEO has been applied
shRemoveFromGETVarsList('option');
if (!empty($Itemid)) 	shRemoveFromGETVarsList('Itemid');
shRemoveFromGETVarsList('lang');
if (!empty($catid)) 	shRemoveFromGETVarsList('catid');
if (!empty($id)) 		shRemoveFromGETVarsList('id');
if (!empty($view))		shRemoveFromGETVarsList('view');

// ------------------  standard plugin finalize function - don't change ---------------------------

if ($dosef){
	$string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString,
	(isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null),
	(isset($shLangName) ? @$shLangName : null));
}
?>