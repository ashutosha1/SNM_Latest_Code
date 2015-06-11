<?php
/**
 * @version          $Id: view.html.php 55 2014-01-22 03:50:36Z thongta $
 * @package          obRSS Feed Creator for Joomla.
 * @copyright    (C) 2007-2012 foobla.com. All rights reserved.
 * @author           foobla.com
 * @license          GNU/GPL, see LICENSE
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view' );
// Set the table directory
JTable::addIncludePath( JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' );

class JLORDRSSViewUpgrade extends obView {
	function display( $tpl = null ) {
		global $option;
		JHTML::stylesheet( 'obstyle.css', 'administrator/components/' . $option . '/assets/' );
		JHTML::_( 'behavior.tooltip' );
		JToolBarHelper::title( JText::_( 'OBRSS_BRANDNAME' ) . ' - ' . JText::_( 'OBRSS_SUPPORT_CENTRE' ), 'support.png' );
		$this->assign( 'isOldVer', $this->get( 'Checkversion' ) ? true : false );
		$this->assign( 'report', $this->get( 'Report' ) );
		$this->assign( 'logs', $this->get( 'Log' ) );
		parent::display();
	}
} // end class
?>