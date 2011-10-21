<?php

/**
 * @version		$Id$
 * @package		AMC Portfolio
 * @copyright	Copyright (C) 2007 Anthony McLin
 * @license		GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');

//Add toolbar items
JToolBarHelper::title(   JText::_( 'AMC Portfolio : Projects Manager' ), 'generic.png' );
JToolBarHelper::deleteList();
JToolBarHelper::editListX();
JToolBarHelper::publishList();
JToolBarHelper::unpublishList();
JToolBarHelper::addNewX();
?>
<form action="index.php" method="post" name="adminForm">
<table>
<tr>
	<td align="left" width="100%">
		<?php echo JText::_( 'Filter' ); ?>:
		<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
		<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_catid').value='0';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
	</td>
	<td nowrap="nowrap">
		<?php
			echo $this->lists['catid'];
			echo $this->lists['state'];
		?>
	</td>
</tr>
</table>
<div id="editcell">
	<table class="adminlist">
	<thead>
		<tr>
			<th width="5">
				<?php echo JText::_( 'ID' ); ?>
			</th>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
			</th>
			<th>
				<?php echo JHTML::_('grid.sort',  'Title', 'p.title', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort',  'Alias', 'p.alias', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th width="5%" align="center">
				<?php echo JHTML::_('grid.sort',   'Published', 'p.published', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th width="90" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort',   'Order', 'p.ordering', @$lists['order_Dir'], @$lists['order'] ); ?>
				<?php echo JHTML::_('grid.order',  $this->items ); ?>
			</th>
			<th width="15%"  class="title">
				<?php echo JHTML::_('grid.sort',  'Category', 'category', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th width="5%" align="center">
				<?php echo JHTML::_('grid.sort',   '# of Images', 'numimages', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th width="5%" align="center">
				<?php echo JHTML::_('grid.sort',   'Hits', 'p.hits', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="12">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<?php
	$k = 0;
	for($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row 		= &$this->items[$i];

		$published 	= JHTML::_('grid.published', $row, $i );
		$checked 	= JHTML::_('grid.checkedout', $row, $i );
		$link 		= JRoute::_( 'index.php?option=com_amcportfolio&controller=project&task=edit&cid[]='. $row->id );
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $row->id; ?>
			</td>
			<td>
				<?php echo $checked; ?>
			</td>
			<td>
				<a href="<?php echo $link; ?>"><?php echo $row->title; ?></a>
			</td>
			<td>
				<?php echo $row->alias; ?>
			</td>
			<td align="center">
				<?php echo $published;?>
			</td>
			<td class="order" nowrap="nowrap">
				<span><?php echo $this->pagination->orderUpIcon( $i, TRUE, 'orderup', 'Move Up'); ?></span>
				<span><?php echo $this->pagination->orderDownIcon( $i, $n, TRUE, 'orderdown', 'Move Down'); ?></span>
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
			</td>
			<td>
				<a href="<?php echo $row->cat_link; ?>" title="<?php echo JText::_( 'Edit Category' ); ?>">
				<?php echo $row->category; ?>
				</a>
			</td>
			<td align="center">
				<?php echo $row->numimages; ?>
			</td>
			<td align="center">
				<?php echo $row->hits; ?>
			</td>
		</tr>
		<?php	$k = 1 - $k;
	}
	?>
	</table>
</div>

<input type="hidden" name="option" value="com_amcportfolio" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="projects" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
<input type="hidden" name="controller" value="project" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
