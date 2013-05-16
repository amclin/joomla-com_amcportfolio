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
$document->addScript(JURI::base().'components/com_amcportfolio/assets/script.js');
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
	Joomla.submitbutton = function(task) {

		if (task == 'project.cancel')
		{
			Joomla.submitform(task, document.getElementById('project-form'));
		}

		if (document.formvalidator.isValid(document.id('project-form'))) {
			<?php echo $this->form->getField('description')->save(); ?>
			// Compile the assigned image list
			var imagelist = $$('.imgOutline .height-50 img');
			var temp = [];
			for (var i=0, n=imagelist.length; i < n; i++)
			{
				//Get the image names
				temp[i] = imagelist[i].getProperty('src').replace('<?php echo JURI::root();?>','');
			}

			//Append the names into a single string
			$('jform_images').value = temp.join('|');

			//Compile the assigned movie list
			var temp = new Array;
			for (var i=0, n=$('movielist').options.length; i < n; i++)
			{
				temp[i] = $('movielist').options[i].value;
			}
			$('jform_movies').value = temp.join('|');
			$('task').value = task;

			Joomla.submitform(task, document.id('project-form'));
		} else {
			alert("Invalid Form");
		}
		return false;
	};
</script>


<form action="<?php echo JRoute::_('index.php?option=com_amcportfolio&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="project-form" class="form-validate">

<div class="span10 form-horizontal">
	<fieldset>
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
	
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('Details', true)); ?>
				<?php foreach ($this->form->getFieldset('details') as $field) : ?>
					<div class="control-group">
						<div class="control-label">
							<?php echo $field->label; ?>
						</div>
						<div class="controls">
							<?php echo $field->input; ?>
						</div>
					</div>
				<?php endforeach; ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>

			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('Publishing Options', true)); ?>
				<?php foreach ($this->form->getFieldset('publish') as $field) : ?>
					<div class="control-group">
						<div class="control-label">
							<?php echo $field->label; ?>
						</div>
						<div class="controls">
							<?php echo $field->input; ?>
						</div>
					</div>
				<?php endforeach; ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
			
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'description', JText::_('Project Description', true)); ?>
				<?php foreach ($this->form->getFieldset('description') as $field) : ?>
					<div class="control-group">
						<div class="control-label">
							<?php echo $field->label; ?>
						</div>
						<div class="controls">
							<?php echo $field->input; ?>
						</div>
					</div>
				<?php endforeach; ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
			
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'images', JText::_('Project Images', true)); ?>
				<div class="control-group">
					<div class="control-label">
					<div class="span5">
						<?php echo JText::_('Selected images to be displayed as part of this project.'); ?>
						<p>
							<strong>Please note</strong> that removing images from this list
							only unlinks them from this project. It does not unlink from
							other projects, and it does NOT delete them from this server.
						</p>
						<p>In order to completely remove an image from this server, please
							make sure you've first unlinked it from all projects, and then
							remove it using the Media Manager.</p>

					</div>
					<div class="span5">
						<a class="modal-button btn btn-primary" title="Image"
							href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;e_name=project_images"
							onclick="IeCursorFix(); return false;"
							rel="{handler: 'iframe', size: {x: 800, y: 500}}">Add Image</a>
					</div>
				</div>
				</div>

			


			<div style="border: 1px solid #CCCCCC; padding: 4px" class="span12">
				<ul id='sortable-images' class="manager thumbnails">
				<?php
					$imagelist = Array();
					foreach($this->project->images as $image) {

						$image->image = ltrim($image->image,'/');
						
						//build up value list for hidden input field at bottom of page
						$imagelist[] = $image->image;
						
				?>
					<li class="imgOutline thumbnail height-80 width-80 center">
						<a class="close delete-item" href="#" title="<?php echo JText::_( 'Delete' ); ?>" >x</a>
						<div class="height-50">
							<img src="<?php echo JURI::root().$image->image; ?>" alt="<?php echo $image->image; ?>" />
						</div>
						<div class="small">
							<a href="<?php echo JURI::root().$image->image; ?>" class="preview" rel="lightbox"><?php echo $this->escape( substr( $image->name, 0, 10 ) ). ( strlen( $image->name ) > 10 ? "&hellip;" : ''); ?></a>
						</div>
					</li>
				<?php
					}
				?>							
				</ul>
			</div>
				<?php echo JHtml::_('bootstrap.endTab'); ?>
			
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'videos', JText::_('Project Videos', true)); ?>
	
	<table>
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
	</table>

			
				<?php echo JHtml::_('bootstrap.endTab'); ?>		
			<?php echo JHtml::_('bootstrap.endTabSet'); ?>
		</fieldset>
	</div>

<div class="clr"></div>
<input type="hidden" name="task" id="task" value="" />
<input type="hidden" name="jform[images]" id="jform_images" value="<?php echo implode('|',$imagelist); ?>" />
<input type="hidden" name="jform[movies]" id="jform_movies" value="<?php //echo implode('|',$movielist); ?>" />
<?php echo JHtml::_('form.token'); ?>

<script type="text/javascript">




//This overwrites the builtin return from popup MediaManager
//So that we can insert images wherever we want!
jInsertEditorText = function( text, editor ) {
	switch(editor) {
		//Insert the result into our image sorter
		case 'project_images' :
			
			//Create a new thumbnail box
			var imgContainer = new Element('li', {'class':'imgOutline thumbnail height-80 width-80 center'});
			var controls = new Element( 'a', {
				href : 		'#',
				title : 	'<?php echo JText::_( 'Delete' ); ?>',
				'class' :	'close delete-item',
				text :		'x',
				events :	{
					click : function(e) {
						e.stop();
						amcPortImageSorter.removeItems(controls.getParent()).destroy();
					}	
				}
			});



			var imginfoBorder = new Element('div.small');
			var image = new Element('div.height-50');
			imgContainer.adopt(controls);
			imgContainer.adopt(image);
			imgContainer.adopt(imginfoBorder);

			
			//Stick the image into the box
			image.set('html',text);
			//Show the image properly
			image.getFirst().setProperty('src','<?php echo JURI::root(); ?>'+image.getFirst('img').getProperty('src'));

			//Show the image name
			var imagename = image.getFirst('img').getProperty('src').split('/').pop();
			if(imagename.length > 10 ) {
				imagename = imagename.substring(0, 10) + '&hellip;';
			}
			
			imginfoBorder.set('html','<a href="'+image.getFirst('img').getProperty('src')+'" class="preview" rel="lightbox">'+imagename+'</a>');

			
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


	//Add lightbox functionality for previewing images
	$$('a.preview').each(function(el) {
		el.addEvent('click', function(e) {
			e.stop();
			SqueezeBox.fromElement(el);
		});
	});

	//Add functionality to drop items
	$$('a.delete-item').each(function(el) {
		//Remove the thumbnail box from the documet
		el.addEvent('click', function(e) {
			e.stop();
			amcPortImageSorter.removeItems(el.getParent()).destroy();
		});
	});

</script>

</form>