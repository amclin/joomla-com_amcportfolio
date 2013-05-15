<?php
$db =& JFactory::getDBO();
$query = "SELECT * FROM #__amcportfolio_old";
$db->setQuery($query);

echo "<pre>";
echo $db->getQuery();
echo "\n";
$xferprojects = $db->loadObjectList();

foreach($xferprojects as $xferproj) {
	
	$insertproject = new stdclass;
	$insertproject->id = 		$xferproj->id;
	$insertproject->catid = 	$xferproj->catid;
	$insertproject->title = 	$xferproj->title;
	$insertproject->alias =		'';
	$insertproject->teaser =	$xferproj->teaser;
	$insertproject->published = $xferproj->published;
	$insertproject->checked_out =	$xferproj->checked_out;
	$insertproject->checked_out_time =	$xferproj->checked_out_time;
	$insertproject->ordering =		$xferproj->ordering;
	$insertproject->hits = 0;
	$insertproject->description = $xferproj->description;
	$insertproject->outside_link = $xferproj->url;
	$insertproject->outside_link_text = $xferproj->urltext;

	$db->insertObject('#__amcportfolio',$insertproject);
	
//	$xferquery = "INSERT into #__amcportfolio VALUES(".
//				"'".$xferproj->id."',".
//				"'".$xferproj->catid."',".
//				"'".$xferproj->title."',".
//				"'',".
//				"'".$xferproj->teaser."',".
//				"'".$xferproj->published."',".
//				"'".$xferproj->checked_out."',".
//				"'".$xferproj->checked_out_time."',".
//				"'".$xferproj->ordering."',".
//				"'0',".
//				"'',".
//				"'".$xferproj->url."',".
//				"'".$xferproj->urltext."')";
//	$db->setQuery($xferquery);
//	echo $db->getQuery();
//	$result = $db->loadResult();
//	echo $result;
	
	
	$images = explode("\n",$xferproj->images);
	
	$counter = 0;
	
	foreach($images as $image) {
		$image = trim($image);
//		$attrib = explode( '|', trim( $image ) );
		if(!empty($image)) {
			$image = 'images/stories/portfolio/'.$image;
			$xferquery = "INSERT into #__amcportfolio_images (projectid,image) VALUES(".
				"'".$xferproj->id."',".
				"'".$image."')";
		
			$db->setQuery($xferquery);
			echo $db->getQuery();
			$result = $db->loadResult();
			echo $result;
			echo "\n";	
		}
	}
	
}


var_dump($xferprojects);
echo "</pre>";
?>