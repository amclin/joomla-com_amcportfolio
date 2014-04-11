<?php

/**
 * @version		$Id$
 * @package		AMC Portfolio
 * @copyright	Copyright (C) 2007 Anthony McLin
 * @license		GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base().'components/com_amcportfolio/assets/imagesorter.css');
//JHTML::stylesheet('medialist-thumbs.css','administrator/components/com_amcportfolio/assets/');
?>

<script language="javascript" type="text/javascript">
	var foldermovies = new Array;
	<?php
	$i = 0;
	foreach ($this->movies as $k => $items) {
		foreach ($items as $v) {
			echo "\n	foldermovies[".$i ++."] = new Array( '$k','".addslashes($v->value)."','".addslashes($v->text)."' );";
		}
	}
	?>

	//Override submit button to build Images and Movies list
	Joomla.submitbutton = function(task,form) {
		if (task == 'project.cancel') {
			$('task').value = task;
			Joomla.submitform(task);
		}

		if (document.formvalidator.isValid(document.id('project-form'))) {
			<?php echo $this->form->getField('description')->save(); ?>
			// Compile the assigned image list
			var imagelist = $$('.imgOutline .image img');
			var temp = [];
			for (var i=0, n=imagelist.length; i < n; i++)
			{
				//Get the image names
				temp[i] = imagelist[i].getProperty('src').replace('<?php echo JURI::root();?>','');
			}
			//Append the names into a single string
			$('images').value = temp.join('|');

			//Compile the assigned movie list
			var temp = new Array;
			for (var i=0, n=$('movielist').options.length; i < n; i++)
			{
				temp[i] = $('movielist').options[i].value;
			}
			$('movies').value = temp.join('|');
			$('task').value = task;

			Joomla.submitform(task, document.id('project-form'));
		} else {
			alert("Invalid Form");
		}
		return false;
	};
</script>


<form action="<?php echo JRoute::_('index.php?option=com_amcportfolio&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="project-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->id) ? "New Project" : JText::sprintf('DETAILS', $this->item->id); ?></legend>
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('title'); ?>
				<?php echo $this->form->getInput('title'); ?></li>

				<li><?php echo $this->form->getLabel('alias'); ?>
				<?php echo $this->form->getInput('alias'); ?></li>

				<li><?php echo $this->form->getLabel('catid'); ?>
				<?php echo $this->form->getInput('catid'); ?></li>

				<li><?php echo $this->form->getLabel('published'); ?>
				<?php echo $this->form->getInput('published'); ?></li>
				<li><?php echo $this->form->getLabel('featured'); ?>
				<?php echo $this->form->getInput('featured'); ?></li>
				<li><?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?></li>
				<li><?php echo $this->form->getLabel('hits'); ?>
				<?php echo $this->form->getInput('hits'); ?></li>
				<li><?php echo $this->form->getLabel('ordering'); ?>
				<?php echo $this->form->getInput('ordering'); ?></li>
			</ul>
		</fieldset>
	</div>
<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Details' ); ?></legend>

		<table class="admintable">
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
				<?php echo $this->form->getLabel('teaser'); ?>
			</td>
			<td>
				<?php echo $this->form->getInput('teaser'); ?>
			</td>
			<td valign="top" align="right" class="key">

			</td>
		</tr>
				<tr>
			<td align="right" class="key">
				<label for="folders">
					<?php echo JText::_('Images'); ?>:
				</label>
			</td>
			<td style="text-align: left; border: 1px solid #CCCCCC; padding: 4px; width: 300px; max-width: 805px;">
				<ul id='sortable-images' class="manager">
				<?php
					foreach($this->project->images as $image) {
						$image->filename = end(explode('/',$image->image));
						$image->image = ltrim($image->image,'/');
						
				?>
					<li class="imgOutline">
						<div class="imgTotal">
							<div class="imgBorder">
								<div class="image">
									<a href="<?php echo JURI::root().$image->image; ?>" class="preview" rel="lightbox"><img src="<?php echo JURI::root().$image->image; ?>" alt="<?php echo $image->image; ?>" /></a>
								</div>
							</div>
						</div>
						<div class="controls">
							<a class="delete-item" href="#" ><img src="<?php echo JURI::root();?>media/media/images/remove.png" alt="<?php echo JText::_( 'Delete' ); ?>" /></a>
						</div>
						<div class="imginfoBorder">
							<a href="<?php echo JURI::root().$image->image; ?>" class="preview" rel="lightbox"><?php echo $this->escape( substr( $image->filename, 0, 10 ) . ( strlen( $image->filename ) > 10 ? '...' : '')); ?></a>
						</div>
					</li>
				<?php
					}
				?>							
				</ul>
			</td>
			<td colspan='2' style="text-align: center;" class="key">
				<div class="button2-left">
					<div class="image">
						<a class="modal-button" title="Image" href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;e_name=project_images" onclick="IeCursorFix(); return false;" rel="{handler: 'iframe', size: {x: 800, y: 500}}">Add Image</a>
					</div>
				</div>
				<br/>
				<br/>
				<p>Please note that removing images from this list only unlinks them from this project. It does not unlink from other projects, and it does NOT delete them from this server.</p>
				<p>In order to completely remove an image from this server, please make sure you've first unlinked it from all projects, and then remove it using the Media Manager.</p>
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
			<td colspan="4">
				<div class="clr"></div>
				<?php echo $this->form->getLabel('description'); ?>
				<div class="clr"></div>
				<?php echo $this->form->getInput('description'); ?>
			</td>
		</tr>
	</table>
	</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="task" id="task" value="" />
<input type="hidden" name="images" id="images" value="" />
<input type="hidden" name="movies" id="movies" value="" />
<?php echo JHtml::_('form.token'); ?>

<script type="text/javascript">




//This overwrites the builtin return from popup MediaManager
//So that we can insert images wherever we want!
jInsertEditorText = function( text, editor ) {
	switch(editor) {
		//Insert the result into our image sorter
		case 'project_images' :
			
			//Create a new thumbnail box
			var imgContainer = new Element('li', {'class':'imgOutline'});
			var imgTotal = new Element('div', {'class':'imgTotal'});
			var imgBorder = new Element('div', {'class':'imgBorder'});
			var controls = new Element('div', {'class':'controls'});
			var imginfoBorder = new Element('div', {'class':'imginfoBorder'});
			var image = new Element('div', {'class':'image item'});
			imgContainer.adopt(imgTotal);
			imgTotal.adopt(imgBorder);
			imgBorder.adopt(image);
			imgContainer.adopt(controls);
			imgContainer.adopt(imginfoBorder);
			
			//Stick a delete button in
			controls.set('html','<a class="delete-item" href="#" ><img src="<?php echo JURI::root();?>media/media/images/remove.png" width="16" height="16" border="0" alt="<?php echo JText::_( 'Delete' ); ?>" /></a>');
			//Make the delete button work
			var deleteLink = controls.getFirst();
			deleteLink.addEvent('click', function(el) {
				new Event(el).stop();
				amcPortImageSorter.removeItems(el.getParent().getParent()).destroy();
			});

			
			//Stick the image into the box
			image.set('html',text);
			//Show the image properly
			image.getFirst().setProperty('src','<?php echo JURI::root(); ?>'+image.getFirst('img').getProperty('src'));

			//Show the image name
			var imagename = image.getFirst('img').getProperty('src').split('/').pop();
			imginfoBorder.set('html','<a href="'+image.getFirst('img').getProperty('src')+'" class="preview">'+imagename+'</a>');

			
			//Add the image to the sortable list
//			amcPortImageSorter.addImage(imgContainer);
			$('sortable-images').adopt(imgContainer);
			amcPortImageSorter.addItems(imgContainer);

			break;
			
		//Normal Joomla Behavior passes it to a content editor
		default :
			if (isBrowserIE()) {
				if (window.parent.tinyMCE) {
					window.parent.tinyMCE.selectedInstance.selection.moveToBookmark(window.parent.global_ie_bookmark);
				}
			}
			tinyMCE.execInstanceCommand(editor, 'mceInsertContent',false,text);
			break;
	};
};

	//Build our fancy image sorter
	amcPortImageSorter = new Sortables('sortable-images', {
		clone : false,
		handle : '.imgOutline',
		opacity: .5
	});
		

	
	//Build our fancy image sorter
//	amcPortImageSorter = new imageSorter('sortable-images','.imgOutline',{
//		handle:	'imgBorder'
//	});

	//Add lightbox functionality for previewing images
	$$('a.preview').each(function(el) {
		el.addEvent('click', function(e) {
			new Event(e).stop();
			SqueezeBox.fromElement(el);
		});
	});

	//Add functionality to drop items
	$$('a.delete-item').each(function(el) {
		//Remove the thumbnail box from the documet
		el.addEvent('click', function(e) {
			new Event(e).stop();
			amcPortImageSorter.removeItems(el.getParent().getParent()).destroy();
		});
	});

</script>

</form>