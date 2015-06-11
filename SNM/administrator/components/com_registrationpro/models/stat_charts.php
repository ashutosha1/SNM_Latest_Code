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

class registrationproModelStat_Charts extends JModelLegacy
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

	function getData() {
		$where = $this->_buildContentWhere();
		$query = "SELECT t.*, edt.event_discount_amount, edt.event_discount_type "
				 ."\n FROM #__registrationpro_register as r, #__registrationpro_transactions as t "
				 ."\n LEFT JOIN #__registrationpro_event_discount_transactions AS edt ON t.id = edt.trans_id"
				 . $where;		
		$this->_db->setQuery($query);
		$data = $this->_db->loadObjectList();
		return $data;
	}


	function _buildContentWhere() {
		global $mainframe, $option;
		$month = $mainframe->getUserStateFromRequest( $option.'.report_month', 'month', '', 'int' );
		$year = $mainframe->getUserStateFromRequest( $option.'.report_year', 'year', '', 'int' );
		$payment_status	= $mainframe->getUserStateFromRequest( $option.'.payment_status', 'payment_status', '', 'int' );
		$where = array();
		$where[] = 'r.rid = t.reg_id';
		//if ($month && $year) $where[] = "(MONTH(FROM_UNIXTIME(r.uregdate)) =".$month." AND YEAR(FROM_UNIXTIME(r.uregdate)) = ".$year.")" ;
		
		$dates = JRequest::getVar('dates', date('Y-m-').'01');
		$datef = JRequest::getVar('datef', date('Y-m-d'));
		$where[] = "((DATE_FORMAT(FROM_UNIXTIME(r.uregdate), '%Y-%m-%d') >= '".$dates."') AND (DATE_FORMAT(FROM_UNIXTIME(r.uregdate), '%Y-%m-%d') <= '".$datef."'))" ;
		
		if ($payment_status == 1) {	$where[] = "r.status = 1"; } elseif ($payment_status == 2) { $where[] = "r.status != 1 "; }
		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		return $where;
	}

	function getEventReportData()
	{
		global $mainframe, $option;

		$month 			= $mainframe->getUserStateFromRequest( $option.'.report_month', 'month', '', 'int' );
		$year			= $mainframe->getUserStateFromRequest( $option.'.report_year', 'year', '', 'int' );
		$category		= $mainframe->getUserStateFromRequest( $option.'.report_category', 'cat', '', 'int' );
		$event_id		= $mainframe->getUserStateFromRequest( $option.'.report_eventid', 'event_id', '', 'int' );
		$payment_status	= $mainframe->getUserStateFromRequest( $option.'.payment_status', 'payment_status', '', 'int' );
		
		$payment_status_where = "";
		if ($payment_status == 1){
			$payment_status_where = " AND r.status = 1 ";
		}elseif($payment_status == 2){
			$payment_status_where = "AND r.status != 1 ";
		}

		if($category > 0) {
			$category_status = " AND (e.catsid =".$category.")";
			if($event_id > 0) $category_status = $category_status . " AND (e.id =".$event_id.")";
		}else{
			$category_status = "";
		}

		$dates = JRequest::getVar('dates', date('Y-m-').'01');
		$datef = JRequest::getVar('datef', date('Y-m-d'));
		$where = "((DATE_FORMAT(FROM_UNIXTIME(r.uregdate), '%Y-%m-%d') >= '".$dates."') AND (DATE_FORMAT(FROM_UNIXTIME(r.uregdate), '%Y-%m-%d') <= '".$datef."'))" ;

		$where_state = '(e.published IS NOT NULL)';
		$show_published = JRequest::getVar('show_published', '');
		$show_unpublished = JRequest::getVar('show_unpublished', '');
		$show_archived = JRequest::getVar('show_archived', '');
		
		if($show_published != '') $where_state = $where_state . 'OR (e.published = 1)';
		if($show_unpublished != '') $where_state = $where_state . 'OR (e.published = 0)';
		if($show_archived != '') $where_state = $where_state . 'OR (e.published = -1)';
		
		$query = "SELECT e.*,r.*,l.*, t.* "
				 ."\n FROM #__registrationpro_dates as e, "
				 ."\n #__registrationpro_register as r, "
				 ."\n #__registrationpro_locate as l, "
				 ."\n #__registrationpro_transactions as t "
				 ."\n WHERE e.locid=l.id AND e.id=r.rdid AND r.rid = t.reg_id "
				 .$payment_status_where
				 .$category_status
				 ."\n AND (".$where_state.")"
				 ."\n AND ".$where." GROUP BY r.rid";

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