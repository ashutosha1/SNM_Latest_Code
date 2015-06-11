<?php
/**
 * @package          foobla RSS Feed Creator for Joomla.
 * @subpackage       : install.jlord_rss.php
 * @created          : Setember 2008.
 * @updated          : 2009/06/30
 * @copyright    (C) 2007-2012 foobla.com. All rights reserved.
 * @author           foobla
 * @license          GNU/GPL, see LICENSE
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
// ensure a valid entry point
defined( '_JEXEC' ) or die( 'Restricted Access' );

jimport( 'joomla.application.component.view' );
jimport( 'joomla.html.pane' );
require_once( JPATH_COMPONENT . DS . 'helpers' . DS . 'jlordcore.php' );
JHTML::_( 'behavior.tooltip' );
JHTML::_( 'behavior.modal' );

class obRSSViewCpanel extends obView {
	function display( $tpl = null ) {
		global $isJ25;
		if ( $isJ25 ) {
			require_once( JPATH_COMPONENT . '/helpers/html/sidebar.php' );
		}
		$option     = 'com_obrss';
		$controller = JRequest::getVar( 'controller' );
		JHTML::stylesheet( 'obstyle.css', 'administrator/components/' . $option . '/assets/' );
		JToolBarHelper::title( JText::_( 'OBRSS_BRANDNAME' ), 'feed' );
		JToolBarHelper::preferences( $option, '500', '700' );

		// Sidebar
		JHtmlSidebar::addEntry(
			'<i class="fa fa-dashboard"></i> ' . JText::_( 'OBRSS_DASHBOARD' ),
			'index.php?option=com_obrss&controller=cpanel',
			( ! $controller || $controller == 'cpanel' )
		);

		JHtmlSidebar::addEntry(
			'<i class="fa fa-rss"></i> ' . JText::_( 'OBRSS_FEED_MANAGER' ),
			'index.php?option=com_obrss&controller=feed',
			( $controller == 'feed' )
		);

		JHtmlSidebar::addEntry(
			'<i class="fa fa-rss"></i> ' . JText::_( 'OBRSS_ADDNEWFEED' ),
			'index.php?option=com_obrss&controller=feed&task=add',
			( $controller == 'feed' )
		);

		if ( $isJ25 ) {
			$manager_link = 'index.php?option=com_installer&view=manage&filters[type]=plugin&filters[group]=obrss';
		} else {
			$manager_link = 'index.php?option=com_installer&view=manage&filter_type=plugin&filter_group=obrss';
		}

		JHtmlSidebar::addEntry(
			'<i class="fa fa-puzzle-piece"></i> ' . JText::_( 'OBRSS_ADDONS' ),
			$manager_link
		);
		/*
				JHtmlSidebar::addEntry(
					JText::_('OBRSS_SUBMENU_CATEGORIES'),
					'index.php?option=com_categories&extension=com_obrss',
					( $controller == 'categories' )
				);
		*/
		$this->sidebar = JHtmlSidebar::render();

		// display
		parent::display( $tpl );
	}
} // end class
?>