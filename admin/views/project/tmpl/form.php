<?php

/**
 * @version		$Id$
 * @package		AMC Portfolio
 * @copyright	Copyright (C) 2007 Anthony McLin
 * @license		GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');?>

<script language="javascript" type="text/javascript">
	var folderimages = new Array;
	var foldermovies = new Array;
	<?php
	$i = 0;
	foreach ($this->images as $k => $items) {
		foreach ($items as $v) {
			echo "\n	folderimages[".$i ++."] = new Array( '$k','".addslashes($v->value)."','".addslashes($v->text)."' );";
		}
	}

	$i = 0;
	foreach ($this->movies as $k => $items) {
		foreach ($items as $v) {
			echo "\n	foldermovies[".$i ++."] = new Array( '$k','".addslashes($v->value)."','".addslashes($v->text)."' );";
		}
	}
	?>

	function previewImage( list, image, base_path ) {
		form = document.adminForm;
		srcList = eval( "form." + list );
		srcImage = eval( "document." + image );
		var fileName = srcList.options[srcList.selectedIndex].text;
		var fileName2 = srcList.options[srcList.selectedIndex].value;
		if (fileName.length == 0 || fileName2.length == 0) {
			srcImage.src = base_path + 'images/blank.gif';
		} else {
			srcImage.src = fileName2;
		}
	}

	// Allow javascript data manipulation and validation on save
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}

		// Compile the assigned image list
		var temp = new Array;
		for (var i=0, n=form.imagelist.options.length; i < n; i++)
		{
			temp[i] = form.imagelist.options[i].value;
		}
		form.images.value = temp.join('|');
		try {
			form.onsubmit();
		}
		catch(e) {}

		//Compile the assigned movie list
		var temp = new Array;
		for (var i=0, n=form.movielist.options.length; i < n; i++)
		{
			temp[i] = form.movielist.options[i].value;
		}
		form.movies.value = temp.join('|');
		try {
			form.onsubmit();
		}
		catch(e) {}

		submitform(pressbutton);
	}
</script>


<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Details' ); ?></legend>

		<table class="admintable">
		<tr>
			<td width="100" align="right" class="key">
				<label for="title">
					<?php echo JText::_( 'Project Title' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="title" id="title" size="32" maxlength="255" value="<?php echo $this->project->title;?>" />
			</td>
			<td valign="top" align="right" class="key">
				<?php echo JText::_( 'Published' ); ?>:
			</td>
			<td>
				<?php echo $this->lists['published']; ?>
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key">
				<label for="outside_link">
					<?php echo JText::_( 'Link URL' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="outside_link" id="outside_link" size="32" maxlength="255" value="<?php echo $this->project->outside_link;?>" />
			</td>
			<td width="100" align="right" class="key">
				<label for="outside_link_text">
					<?php echo JText::_( 'Link Text' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="outside_link_text" id="outside_link_text" size="32" maxlength="255" value="<?php echo $this->project->outside_link_text;?>" />
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key">
				<label for="alias">
					<?php echo JText::_( 'Project Alias' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="alias" id="alias" size="32" maxlength="255" value="<?php echo $this->project->alias;?>" />
			</td>
			<td width="100" align="right" class="key">
				<label for="hits">
					<?php echo JText::_( 'Hits' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="hits" id="hits" size="10" maxlength="10" value="<?php echo $this->project->hits;?>" />
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key">
				<label for="title">
					<?php echo JText::_( 'Teaser Line' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="teaser" id="teaser" size="64" maxlength="255" value="<?php echo $this->project->teaser;?>" />
			</td>
			<td valign="top" align="right" class="key">
				<label for="catid">
					<?php echo JText::_( 'Category' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->lists['catid']; ?>
			</td>
		</tr>
		<tr>
			<td align="right" class="key">
				<label for="folders">
					<?php echo JText::_('Images'); ?>:
				</label>
			</td>
			<td style="text-align: left; border: 1px solid #CCCCCC; padding: 4px;">
				<?php echo $this->lists['folders'] . "\n\n"; ?>
				<br />
				<br />
				<img name="view_imagefiles" src="<?php echo JURI::root();?>images/M_images/blank.png" width="100" alt="No Image" style="margin: 10px; border: 1px solid #CCCCCC; padding: 4px; float: left;"/>
				<?php echo $this->lists['imagefiles']; ?>
			</td>
			<td style="text-align: center;" class="key">
				<?php echo JText::_('Remove'); ?> | <?php echo JText::_('Assign'); ?>&nbsp;&nbsp;<br /><br />
				<input class="button" type="button" value="<<" onclick="delSelectedFromList('adminForm','imagelist')" title="Remove"/>&nbsp;&nbsp;&nbsp;<input class="button" type="button" value=">>" onclick="addSelectedToList('adminForm','imagefiles','imagelist')" title="Assign"/>
			</td>
			<td style="text-align: left; border: 1px solid #CCCCCC; padding: 4px; width: 300px;">
				<table>
					<tr>
						<td>
							<img name="view_imagelist" src="<?php echo JURI::root();?>images/M_images/blank.png" width="100" alt="No Image" style="margin: 10px; border: 1px solid #CCCCCC; padding: 4px;"/>
						</td>
						<td>
							<?php echo $this->lists['imagelist']; ?>
						</td>
						<td>
							<input class="button" type="button" value="&Lambda; - Move Up&nbsp;&nbsp;&nbsp;&nbsp;" onclick="moveInList('adminForm','imagelist',adminForm.imagelist.selectedIndex,-1)" /><br /><br />
							<input class="button" type="button" value="V - Move Down" onclick="moveInList('adminForm','imagelist',adminForm.imagelist.selectedIndex,+1)" />
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align="right" class="key">
				<label for="folders2">
					<?php echo JText::_('Movies'); ?>:
				</label>
			</td>
			<td style="text-align: left; border: 1px solid #CCCCCC; padding: 4px;">
				<?php echo $this->lists['folders2'] . "\n\n"; ?>
				<br />
				<br />
				<?php echo $this->lists['moviefiles']; ?>
			</td>
			<td style="text-align: center;" class="key">
				<?php echo JText::_('Remove'); ?> | <?php echo JText::_('Assign'); ?>&nbsp;&nbsp;<br /><br />
				<input class="button" type="button" value="<<" onclick="delSelectedFromList('adminForm','movielist')" title="Remove"/>&nbsp;&nbsp;&nbsp;<input class="button" type="button" value=">>" onclick="addSelectedToList('adminForm','moviefiles','movielist')" title="Assign"/>
			</td>
			<td style="text-align: left; border: 1px solid #CCCCCC; padding: 4px; width: 300px;">
				<table>
					<tr>
						<td>
							<?php echo $this->lists['movielist']; ?>
						</td>
						<td>
							<input class="button" type="button" value="&Lambda; - Move Up&nbsp;&nbsp;&nbsp;&nbsp;" onclick="moveInList('adminForm','movielist',adminForm.movielist.selectedIndex,-1)" /><br /><br />
							<input class="button" type="button" value="V - Move Down" onclick="moveInList('adminForm','movielist',adminForm.movielist.selectedIndex,+1)" />
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td align="right" class="key">
				<label for="description">
					<?php echo JText::_('Project Description'); ?>:
				</label>
			</td>
			<td colspan="3">
				<?php echo $this->editor->display('description', $this->project->description, '100%', '200', '40', '6') ?>
			</td>
	</table>
	</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="option" value="com_amcportfolio" />
<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="images" value="" />
<input type="hidden" name="movies" value="" />
<input type="hidden" name="ordering" value="<?php echo $this->project->ordering; ?>" />
<input type="hidden" name="controller" value="project" />
</form>