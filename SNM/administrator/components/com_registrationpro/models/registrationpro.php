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
jimport('joomla.application.component.model');

class registrationproModelregistrationpro extends JModelLegacy
{
	var $_data = null;
	var $_total = null;
	var $_pagination = null;

	function __construct()
	{
		parent::__construct();

		global $mainframe, $option;

		$mainframe = JFactory::getApplication();
		$limit		= $mainframe->getUserStateFromRequest( $option.'report.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( $option.'report.limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	function getData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			$this->_data = $this->_additionals($this->_data);
		}

		return $this->_data;
	}

	function getTotal()
	{
		// Lets load the total nr if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}


	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	function _buildQuery()
	{
		$where = ' WHERE 1 ';
		$per = JRequest::getVar('stat_chart_period','');

		if($per == 3) {
			$year =  date('Y') * 1;
			$where = $where . " AND (YEAR(FROM_UNIXTIME(r.uregdate)) = ".$year.")";
		} else {
			$month = date('n') * 1;
			$year =  date('Y') * 1;
			$where = $where . " AND (MONTH(FROM_UNIXTIME(r.uregdate)) =".$month." AND YEAR(FROM_UNIXTIME(r.uregdate)) = ".$year.")";
		}
		
		$query = "SELECT t.*, edt.*, r.* "
				 ."\n FROM #__registrationpro_register as r "
				 ."\n LEFT JOIN #__registrationpro_transactions as t ON (r.rid = t.reg_id)"
				 ."\n LEFT JOIN #__registrationpro_event_discount_transactions AS edt ON t.id = edt.trans_id"
				 ." ".$where;

		return $query;
	}

	function _additionals($rows)
	{
		return $rows;
	}


	function getEventReportData() {
		global $mainframe, $option;
		$per = JRequest::getVar('stat_chart_period','');
		$where = '';
		if($per == 3) {
			$year =  date('Y') * 1;
			$where = "\n AND (YEAR(FROM_UNIXTIME(r.uregdate)) = ".$year.")";
		} else {
			$month = date('n') * 1;
			$year =  date('Y') * 1;
			$where = "\n AND (MONTH(FROM_UNIXTIME(r.uregdate))=".$month." AND YEAR(FROM_UNIXTIME(r.uregdate))=".$year.")";
		}
		
		$query = "SELECT e.*,r.*,l.*, t.* "
				 ."\n FROM #__registrationpro_dates as e, "
				 ."\n #__registrationpro_register as r, "
				 ."\n #__registrationpro_locate as l, "
				 ."\n #__registrationpro_transactions as t "
				 ."\n WHERE e.locid=l.id AND e.id=r.rdid AND r.rid = t.reg_id "
				 .$where
				 ."\n  GROUP BY r.rid";
				 

		$this->_db->setQuery($query);
		$data = $this->_db->loadObjectList();

		foreach($data as $key=>$value)
		{
			$data[$key]->params = unserialize($data[$key]->params);
		}

		return $data;
	}
}

?>