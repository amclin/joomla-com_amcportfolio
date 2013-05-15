<?php defined('_JEXEC') or die('Restricted access'); ?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border: 1px solid #CCCCCC">
<tr>
	<th>Project Title</th>
	<th>Project Teaser</th>
	<th>Hits</th>
</tr>
<?php foreach ($this->projects as $project) : ?>
<tr class="sectiontableentry<?php echo $project->odd + 1; ?>">
	<td align="left">
		<?php echo $project->link; ?>
	</td>
	<td height="20">
		<?php echo $project->teaser; ?>
	</td>
	<td align="center">
		<?php echo $project->hits; ?>
	</td>
</tr>
<?php endforeach; ?>
</table>