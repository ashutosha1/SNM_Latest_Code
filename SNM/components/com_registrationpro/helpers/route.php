<?php
/**
 * @version		v.3.2 registrationpro $
 * @package		registrationpro
 * @copyright	Copyright © 2009 - All rights reserved.
 * @license  	GNU/GPL		
 * @author		JoomlaShowroom.com
 * @author mail	info@JoomlaShowroom.com
 * @website		www.JoomlaShowroom.com
 *
*/

defined('_JEXEC') or die('Restricted access');

// Component Helper
jimport('joomla.application.component.helper');

/**
 * egistrationpro Component Route Helper
 * based on Joomla ContentHelperRoute
 *
 * @static
 * @package		Joomla
 * @subpackage	Registrationpro
 * @since 0.9
 */
class RegistrationproHelperRoute
{
	/**
	 * Determines an Registrationpro Link
	 *
	 * @param int The id of an Registrationpro item
	 * @param string The view
	 * @since 1.5.9
	 *
	 * @return string determined Link
	 */
	function getRoute($id, $view = 'event')
	{
		//Not needed currently but kept because of a possible hierarchic link structure in future
		$needles = array(
			$view  => (int) $id
		);

		//Create the link
		$link = 'index.php?option=com_registrationpro&view='.$view.'&id='. $id;

		if($item = RegistrationproHelperRoute::_findItem($needles)) {
			$link .= '&Itemid='.$item[0]->id;
		};

		return $link;
	}

	/**
	 * Determines the Itemid
	 *
	 * searches if a menuitem for this item exists
	 * if not the first match will be returned
	 *
	 * @param array The id and view
	 * @since 1.5.9
	 *
	 * @return int Itemid
	 */
	function _findItem($needles)
	{
		static $items;

		// Get the menu items for this component.
		if (!isset($items)) {
			// Include the site app in case we are loading this from the admin.
			require_once JPATH_SITE.'/libraries/legacy/application/application.php';

			$app	= JFactory::getApplication();
			$menu	= $app->getMenu();
			$com	= JComponentHelper::getComponent('com_registrationpro');
			$items	= $menu->getItems('component_id', $com->id);

			// If no items found, set to empty array.
			if (!$items) {
				$items = array();
			}
		}
		
		//echo "<pre>"; print_r($items); exit;		
		return $items;
	}
}
?>