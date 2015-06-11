<?php
/**
 * @version          $Id: form_feedburner.php 55 2014-01-22 03:50:36Z thongta $
 * @package          obRSS Feed Creator for Joomla.
 * @copyright    (C) 2007-2012 foobla.com. All rights reserved.
 * @author           foobla.com
 * @license          GNU/GPL, see LICENSE
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

global $isJ25, $boolean_array, $option, $obRootSite, $mainframe;
require_once( JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'itemshelper.php' );
$format = itemsHelper::getFeedTypePrefix( $this->feed->feed_type );
$Itemid = obRSSUrl::getItemid();
if ( $Itemid ) {
	$link_feed = "index.php?option=com_obrss&task=feed&id={$this->feed->id}" . ':' . $this->feed->alias . '&format=' . $format . '&Itemid=' . $Itemid;
} else {
	$link_feed = "index.php?option=com_obrss&task=feed&id={$this->feed->id}" . ':' . $this->feed->alias . '&format=' . $format;
}
$app = JFactory::getApplication();
$params = JComponentHelper::getParams( 'com_obrss' );

if ( $params->get( 'admin_link_sef', 0 ) ) {
	$app_route = $app->getInstance( 'site' );

	$router = $app_route->getRouter();

	$newUrl    = $router->build( $link_feed );
	$link_feed = $newUrl->toString();

	$link_feed = preg_replace( '/[\s\S]*?administrator\//i', '', $link_feed );
}

$link_feed = JUri::root() . $link_feed;
?>
<h4><?php echo JText::_( 'OBRSS_FEEDBURNER_BURN' ); ?></h4>
<p class="alert alert-info"><?php echo JText::_( 'OBRSS_FEEDBURNER_BURN_DESC' ); ?></p>
<fieldset class="adminform">
	<ul class="unstyled">
		<li>
			<label id="" for="">1. <?php echo JText::_( 'OBRSS_FEEDBURNER_COPY' ); ?></label>
			<input type="text" name="feed_name" readonly class="input-block-level" value="<?php echo $link_feed; ?>" />
		</li>
		<li>
			<label id="" for="">2. <?php echo JText::_( 'OBRSS_FEEDBURNER_LOGIN' ); ?></label>
		</li>
		<li>
			<label id="" for="">3. <?php echo JText::_( 'OBRSS_FEEDBURNER_BURNFEED' ); ?></label>
		</li>
		<li>
			<label id="" for="">4. <?php echo JText::_( 'OBRSS_FEEDBURNER_COPYURI' ); ?></label>

			<div class="input-prepend">
				<span class="add-on">http://feeds.feedburner.com/</span>
				<input type="text" name="uri" id="prependedInput" class="span8" value="<?php echo $this->feed->uri; ?>" placeholder="<?php echo JText::_( 'OBRSS_FEEDBURNER_FEEDBURN_URI' ); ?>" />
			</div>
		</li>
	</ul>
</fieldset>