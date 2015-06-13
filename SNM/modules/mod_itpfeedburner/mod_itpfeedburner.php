<?php
/*
 * @package      ITPrism Modules
 * @subpackage   ITPFeedBurner
 * @copyright    Copyright (C) 2010 JoomlaMind.com. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * ITPFeedBurner is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.

* Some UI customization done in this file 
*/

defined( '_JEXEC' ) or die;

$url        = $params->get('itp_fb_url');

$feedName   = basename($url);

$title      = $params->get('itp_fb_title');

$bgColour	= $params->get('itp_fb_bg');
$textColour = $params->get('itp_fb_text');
$animation  = $params->get('itp_fb_animation');

$type  		= $params->get('itp_fb_types');

switch ($type) {

	case 1;
	
		$rss	='<p class="category-name1"><a href="' . $url . '"><img src="http://feeds.feedburner.com/~fc/' . $feedName . '?bg=' . $bgColour . '&amp;fg=' . $textColour . '&amp;anim=' . $animation . '" height="26" width="88" style="border:0" alt="" /></a></p>';
	
	break;
	
	default;
	
		$rss    ='<p class="category-name1"><a href="' . $url . '" rel="alternate" type="application/rss+xml"><img src="http://www.feedburner.com/fb/images/pub/feed-icon16x16.png" alt="" style="vertical-align:middle;border:0"/></a>';   
	
		if ( !empty( $title ) ) {
			$rss   .= '&nbsp;<a href="' . $url . '" rel="alternate" type="application/rss+xml">' . $title .'</a></p>';
		}
		
	break;

}

echo $rss;

?>
