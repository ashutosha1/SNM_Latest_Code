<?php
/**
 * @version          $Id: cpanel.php 55 2014-01-22 03:50:36Z thongta $
 * @package          obRSS Feed Creator for Joomla.
 * @copyright    (C) 2007-2012 foobla.com. All rights reserved.
 * @author           foobla.com
 * @license          GNU/GPL, see LICENSE
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.modeladmin' );

class obRSSModelCpanel extends obModel {
	function getLatestItem() {
		$db    = JFactory::getDBO();
		$query = '
			SELECT f.`id` AS `id`, f.`alias` AS `alias`, f.`name` AS `name`, f.`created` AS `created`, a.`name` AS `addon`, f.`feed_type`
			FROM
				#__obrss AS f,
				#__extensions AS a
			WHERE
				(f.`components` = a.`element` OR
				f.`components` = CONCAT(a.`element`, ".xml"))
				and `a`.`type`="plugin" 
				and `a`.`folder`="obrss" 
				and `a`.`enabled`=1
			ORDER BY `created` DESC
			LIMIT 0,10
		';
		$db->setQuery( $query );
		if ( ! $rows = $db->loadObjectList() ) {
			echo $db->loadErrorMsg();
			echo $db->getQuery();
		}

		return $rows;
	}

	function getAddonList() {
		$db  = JFactory::getDBO();
		$qry = "
			SELECT
				`extension_id` as `id`, `element` as `file`, `name`, `enabled`as `published`,
				`manifest_cache`
			FROM #__extensions AS a
			WHERE
				a.`type`	= 'plugin' AND
				a.`folder`	= 'obrss' AND
				a.`enabled`	= 1 AND
				a.`access`	= 1 AND
				a.`state`	= 0
		";
		$db->setQuery( $qry );
		$exAddons = $db->loadObjectList();

		return $exAddons;
	}
}