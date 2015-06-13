<?php
/**
 * @version          $Id: mod_obrss.php 46 2014-01-01 16:12:18Z thongta $
 * @package          obRSS Feed Creator for Joomla.
 * @copyright    (C) 2007-2012 foobla.com. All rights reserved.
 * @author           foobla.com
 * @license          GNU/GPL, see LICENSE
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
global $mainframe, $option, $isJ25, $obRootSite;
$obRootSite = JURI::root();
$version    = new JVersion();
$obJVer     = $version->getShortVersion();
$isJ25      = substr( $obJVer, 0, 3 ) == '1.5';
if ( ! $isJ25 ) {
	$option    = JRequest::getVar( 'option', '' );
	$mainframe = JFactory::getApplication();
	jimport( 'joomla.html.parameter' );
}

require_once( dirname( __FILE__ ) . DS . 'helper.php' );
$m_params = JModuleHelper::getModule( 'obrss' );
$rows     = modOBRSSHelper::getFeeds( $m_params->params );
if ( count( $rows ) < 1 ) {
	echo '<i>None feed</i>';
} else {
	require( JModuleHelper::getLayoutPath( 'mod_obrss' ) );
}

