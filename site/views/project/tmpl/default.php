<?php

/**
 * @version		$Id$
 * @package		AMC Portfolio
 * @copyright	Copyright (C) 2007 Anthony McLin
 * @license		GNU/GPL
 */

defined('_JEXEC') or die('Restricted access'); ?>

<h3>Title: <?php echo $this->project->title; ?></h3>
<h4>Category: <?php echo $this->project->category; ?></h4>
<h4>Teaser: <?php echo $this->project->teaser; ?></h4>
<ul>
<?php
foreach($this->project->images as $image)
{
	echo "	<li><img src='" . JURI::root() . $image->image . "' alt='' /><li>\n";
}
?>
</ul>

<pre style="border: 1px solid #CCCCCC; background: #EEEEEE;">
	<?php print_r($this->project); ?>
</pre>

<pre style="border: 1px solid #CCCCCC; background: #EEEEEE;">
	<?php print_r($this->category); ?>
</pre>