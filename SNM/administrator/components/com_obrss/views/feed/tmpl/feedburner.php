<?php
/**
 * @version		$Id: feedburner.php 1 2013-07-30 09:25:32Z thongta $
 * @package	foobla RSS Feed Creator for Joomla.
 * @copyright	(C) 2007-2012 foobla.com. All rights reserved.
 * @author: foobla.com
 * @license: GNU/GPL, see LICENSE
 */
// ensure a valid entry point
defined('_JEXEC') or die('Restricted Access');
global $option, $obRootSite, $mainframe;
$link_feed = $obRootSite.'index.php?option='.$this->option.'&amp;task=feed&amp;id='.$this->cid;
$link_save = 'index.php?option='.$option.'&controller='.$this->controller.'&task=save_fb&cid='.$this->cid;
?>
<script type='text/javascript'>
<!--
function notEmpty(uri)
{
	if (uri.value.length == 0) {
		alert('<?php echo JText::_('OBRSS_FEEDBURNER_MSG_INPUTURI'); ?>');
		uri.focus();
		return false;
	} else {
		reloadPage();
		//form.submit();
	}
}
function reloadPage()
{
	//window.parent.document.getElementById( 'sbox-window' ).close();
	window.parent.document.getElementById( 'sbox-window' ).style.display='none';
	setTimeout("window.parent.location.reload();",10);
}
</script>
<form action="<?php echo $link_save;?>" method="post" name="fb_form">
<div class="configuration" style="font-size: 2em;">FeedBurner Integration</div>
<fieldset class="adminform">
	<legend>[ <?php if($this->task == "add_fb") echo JText::_('OBRSS_FEEDBURNER_BURN'); else echo JText::_('OBRSS_FEEDBURNER_LEDEND_EDIT');?> ]</legend>
	<ul class="config-option-list">
		<li>
			<label id="" for="">1. <?php echo JText::_('OBRSS_FEEDBURNER_COPY'); ?></label>
			<input type="text" name="feed_name" disabled size="100" value="<?php echo $link_feed; ?>" />
		</li>
		<li style="clear: left;">
			<label id="" for="">2. <?php echo JText::_('OBRSS_FEEDBURNER_LOGIN'); ?></label>
		</li>
		<li style="clear: left;">
			<label id="" for="">3. <?php echo JText::_('OBRSS_FEEDBURNER_BURNFEED'); ?></label>
		</li>
		<li style="clear: left;">
			<label id="" for="">4. <?php echo JText::_('OBRSS_FEEDBURNER_COPYURI'); ?> <i>http://feeds.feedburner.com/</i></label>
			<input type="text" name="uri" size="20" value="<?php echo $this->uri;?>" />
		</li>
		<li style="clear: left;">
			<label id="" for="">5. <?php echo JText::_('OBRSS_FEEDBURNER_DONE'); ?></label>
			<?php if ($this->task == "add_fb") { ?>
			<input class="submit" type="submit" value="<?php echo JText::_('SAVE'); ?>" onclick="return notEmpty(uri);"/>
			<?php } else { ?>
			<input class="submit" type="submit" value="<?php echo JText::_('SAVE'); ?>" onclick="reloadPage();"/>
			<?php } ?>
		</li>
	</ul>
</fieldset>
</form>
