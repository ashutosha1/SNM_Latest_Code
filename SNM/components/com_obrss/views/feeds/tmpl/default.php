<?php
/**
 * @version          $Id: default.php 55 2014-01-22 03:50:36Z thongta $
 * @package          obRSS Feed Creator for Joomla.
 * @copyright    (C) 2007-2012 foobla.com. All rights reserved.
 * @author           foobla.com
 * @license          GNU/GPL, see LICENSE
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
global $mainframe, $isJ25;
$rss     = $this->rss;
$configs = $rss->configs;
$items   = $rss->items;
$params  = $mainframe->getParams();
$pageNav = $this->rss->pageNav;
// if ($isJ25) {
// 	echo $this->loadTemplate('j15');
// } else {
echo $this->loadTemplate( 'j17' ); // for both J25 and J3, no longer support Joomla 1.5
// }
?>