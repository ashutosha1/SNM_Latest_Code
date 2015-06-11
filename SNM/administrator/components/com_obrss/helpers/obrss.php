<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined( '_JEXEC' ) or die;

/**
 * Contact component helper.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 * @since       1.6
 */
class obRSSHelper {
	/**
	 * Configure the Linkbar.
	 *
	 * @param    string $vName The name of the active view.
	 *
	 * @return    void
	 * @since    1.6
	 */
	public static function addSubmenu( $vName ) {
		global $isJ25;
		$jv    = new JVersion();
		$isJ25 = $jv->RELEASE == '2.5';
		if ( $isJ25 ) {
			$option     = 'com_obrss';
			$controller = JRequest::getVar( 'controller' );
			JHTML::stylesheet( 'obstyle.css', 'administrator/components/' . $option . '/assets/' );
			JToolBarHelper::title( JText::_( 'OBRSS_BRANDNAME' ), 'feed' );
			JToolBarHelper::preferences( $option, '500', '700' );

			// Sidebar
			JSubMenuHelper::addEntry(
				'<i class="fa fa-dashboard"></i> ' . JText::_( 'OBRSS_DASHBOARD' ),
				'index.php?option=com_obrss&controller=cpanel',
				( ! $controller || $controller == 'cpanel' )
			);

			JSubMenuHelper::addEntry(
				'<i class="fa fa-rss"></i> ' . JText::_( 'OBRSS_FEED_MANAGER' ),
				'index.php?option=com_obrss&controller=feed',
				( $controller == 'feed' )
			);

			JSubMenuHelper::addEntry(
				'<i class="fa fa-rss"></i> ' . JText::_( 'OBRSS_ADDNEWFEED' ),
				'index.php?option=com_obrss&controller=feed&task=add',
				( $controller == 'feed' )
			);

			$manager_link = 'index.php?option=com_installer&view=manage&filters[type]=plugin&filters[group]=obrss';

			JSubMenuHelper::addEntry(
				'<i class="fa fa-puzzle-piece"></i> ' . JText::_( 'OBRSS_ADDONS' ),
				$manager_link
			);

			JSubMenuHelper::addEntry(
				JText::_( 'OBRSS_SUBMENU_CATEGORIES' ),
				'index.php?option=com_categories&extension=com_obrss',
				( $controller == 'categories' )
			);
		} else {
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

			JHtmlSidebar::addEntry(
				JText::_( 'OBRSS_SUBMENU_CATEGORIES' ),
				'index.php?option=com_categories&extension=com_obrss',
				( $controller == 'categories' )
			);
		}
	}

	public static function getStateOptions() {
		$options   = array();
		$options[] = JHtml::_( 'select.option', '1', JText::_( 'JPUBLISHED' ) );
		$options[] = JHtml::_( 'select.option', '0', JText::_( 'JUNPUBLISHED' ) );

		return $options;
	}
}
