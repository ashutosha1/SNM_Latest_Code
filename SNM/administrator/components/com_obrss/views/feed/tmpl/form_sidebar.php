<?php
/**
 * @version          $Id: form_sidebar.php 55 2014-01-22 03:50:36Z thongta $
 * @package          obRSS Feed Creator for Joomla.
 * @copyright    (C) 2007-2012 foobla.com. All rights reserved.
 * @author           foobla.com
 * @license          GNU/GPL, see LICENSE
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

global $isJ25, $boolean_array;
?>
<h4><?php echo JText::_( 'JDETAILS' ); ?></h4>
<hr />
<fieldset class="form-vertical">
	<div class="control-group">
		<div class="control-label">
			<label id="published-lbl" for="jform_published" class="hasTip" title=""><?php echo JText::_( 'OBRSS_PUBLISHED' ); ?></label>
		</div>
		<?php echo JHTML::_( 'obinputs.radiolist', $boolean_array, 'published', 'class="radio btn-group"', 'value', 'text', $this->feed->published ); ?>
		<?php //echo JHTML::_('select.genericlist', $boolean_array, 'published', 'class="radio btn-group"', 'value', 'text', $this->feed->published); ?>
	</div>

	<div class="control-group">
		<div class="control-label">
			<label id="use_feedburner-lbl" for="use_feedburner" class="hasTip" title="">
				<?php echo JText::_( 'OBRSS_USE_URL_FEEDBURNER' ); ?>
			</label>
		</div>
		<?php
		$tuse_feedburner_opt = array(
			JHTML::_( 'select.option', '2', JText::_( 'JGLOBAL_USE_GLOBAL' ) ),
			JHTML::_( 'select.option', '0', JText::_( 'JNO' ) ),
			JHTML::_( 'select.option', '1', JText::_( 'JYES' ) )
		);
		// 					echo JHTML::_('obinputs.radiolist', $tuse_feedburner_opt, 'use_feedburner', 'class="radio btn-group"', 'value', 'text', $this->feed->use_feedburner );
		echo JHTML::_( 'select.genericlist', $tuse_feedburner_opt, 'use_feedburner', 'class="span12" size="1"', 'value', 'text', $this->feed->use_feedburner ); ?>
	</div>

	<div class="control-group">
		<div class="control-label">
			<label id="ordering-lbl" for="ordering" class="hasTip" title="">
				<?php echo JText::_( 'OBRSS_ORDERING' ); ?>
			</label>
		</div>
		<div class="controls">
			<?php echo $this->lists1['ordering']; ?>
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<label id="hits-lbl" for="hits" class="hasTip" title="">
				<?php echo JText::_( 'OBRSS_HITS' ); ?>
			</label>
		</div>
		<div class="controls">
			<?php
			$hist = (int) $this->feed->hits;
			echo $hist;
			if ( $hist > 0 ) {
				echo '&nbsp;&nbsp;<input style="float: none;" name="reset_hits" type="button" class="btn btn-inverse btn-small inputbox" value="' . JText::_( 'Reset' ) . '" onclick="obRssResetHits();" />';
			}
			?>
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<label id="type_feed-lbl" for="type_feed" class="hasTip" title="">
				<?php echo JText::_( 'OBRSS_TYPE_FEED' ); ?>
			</label>
		</div>
		<div class="controls">
			<?php echo( $this->lists1['feedButtons'] ); ?>&nbsp;&nbsp;
			<img id="feedButton" src="<?php echo( JURI::root() . "components/com_obrss/images/buttons/" . $this->button ); ?>" valign="middle" />
		</div>
	</div>

	<div class="control-group">
		<label id="feeded-lbl" for="feeded" class="hasTip" title="">
			<?php echo JText::_( 'OBRSS_FEED_HEADTAG' ); ?>
		</label>

		<div class="controls">
			<?php // echo JHTML::_('select.genericlist', $boolean_array, 'feeded', 'class="span12"', 'value', 'text', $this->feed->feeded); ?>
			<?php echo JHTML::_( 'obinputs.radiolist', $boolean_array, 'feeded', 'class="radio btn-group"', 'value', 'text', $this->feed->feeded ); ?>
		</div>
	</div>

	<div class="control-group">
		<label id="display_feed_module-lbl" for="display_feed_module" class="hasTip" title="">
			<?php echo JText::_( 'OBRSS_DISPLAY_FEED' ); ?>
		</label>

		<div class="controls">
			<?php //echo JHTML::_('select.genericlist', $boolean_array, 'display_feed_module', 'class="span12"', 'value', 'text', $this->feed->display_feed_module); ?>
			<?php echo JHTML::_( 'obinputs.radiolist', $boolean_array, 'published', 'class="radio btn-group"', 'value', 'text', $this->feed->published ); ?>
		</div>
	</div>
</fieldset>