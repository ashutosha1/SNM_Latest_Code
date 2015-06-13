<?php
/**
* @version		v.3.2 registrationpro $
* @package		registrationpro
* @copyright	Copyright © 2009 - All rights reserved.
* @license  	GNU/GPL
* @author		JoomlaShowroom.com
* @author mail	info@JoomlaShowroom.com
* @website		www.JoomlaShowroom.com
*/

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model' );

class registrationproModelMyHistory extends JModelLegacy
{
	function __construct() {
		parent::__construct();
		global $mainframe, $option;
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config = $registrationproAdmin->config();
	}

	function getHistory() {
		$user = JFactory::getUser();
		$query = "SELECT rr.rdid, rr.uid, rr.uregdate, rr.firstname, rr.lastname, rr.email, IFNULL(ru.id, 0) AS onid,"
			." IFNULL(rd.dates,'-') AS dates, IFNULL(rd.times,'-') AS times, IFNULL(rd.enddates,'-') AS enddates, IFNULL(rd.endtimes,'-') AS endtimes, IFNULL(rd.titel,'-') AS titel"
			.' FROM #__registrationpro_register rr'
			.' LEFT JOIN #__registrationpro_dates rd ON (rd.id=rr.rdid)'
			.' LEFT JOIN #__users ru ON (LCASE(rr.email)=LCASE(ru.email))'
			." WHERE ((rr.uid=".$user->get('id').")AND(IFNULL(ru.id, 0)=0)) OR (IFNULL(ru.id, 0)=".$user->get('id').")"
			.' ORDER BY rr.uregdate';
		$this->_db->setQuery($query);
		$rows = $this->_db->loadAssocList();
		return $rows;
	}
}
?>