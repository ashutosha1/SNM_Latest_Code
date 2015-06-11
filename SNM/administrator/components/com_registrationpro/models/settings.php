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

class registrationproModelSettings extends JModelLegacy
{
	var $_id = null;
	var $_data = null;
	var $_userdata = null;
	var $_currency = null;
	var $_total = null;
	var $_pagination = null;

	function __construct() {
		global $mainframe;
		$mainframe = JFactory::getApplication();
		parent::__construct();

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);

		$limit = $mainframe->getUserStateFromRequest( $option.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( $option.'selectusers_limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	function setId($id) {
		$this->_id = $id;
		$this->_data = null;
		$this->_currency = null;
	}

	function &getData() {
		if ($this->_loadData()) {}
		else  $this->_initData();
		return $this->_data;
	}

	function _loadData() {
		if (empty($this->_data)) {
			$query = 'SELECT * FROM #__registrationpro_config';
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadRowList();
			return (boolean) $this->_data;
		}
		return true;
	}

	function _initData() {
		if (empty($this->_data)) {
			$setting = new stdClass();
			$setting->id = 1;
			$setting->setting_name = null;
			$setting->setting_value = null;
			$this->_data = $setting;
			return (boolean) $this->_data;
		}
		return true;
	}

	function store($data) {
		$registrationproAdmin = new registrationproAdmin;
		$repgrosettings	= $registrationproAdmin->config();
		$user = JFactory::getUser();
		$config = JFactory::getConfig();
		$tzoffset = $config->get('config.offset');
		$row = $this->getTable('registrationpro_config', '');

		if (!$row->bind($data)) {
			JError::raiseError(500, $this->_db->getErrorMsg() );
			return false;
		}

		$nullDate	= $this->_db->getNullDate();

		// Make sure the data is valid
		if (!$row->check()) {
			$this->setError($row->getError());
			return false;
		}

		// Store it in the db
		if (!$row->store()) {
			JError::raiseError(500, $this->_db->getErrorMsg() );
			return false;
		}

		return $row->id;
	}

	function getCurrencyList() {
		$query = 'SELECT * FROM #__registrationpro_currency ORDER BY currency_name';
		$this->_db->setQuery($query);
		$this->_currency = $this->_db->loadRowList();
		return $this->_currency;
	}

	function publish($cid = array(), $publish = 1) {
		$user 	=JFactory::getUser();
		$userid = (int) $user->get('id');

		if (count( $cid )) {
			$cids = implode(',', $cid);
			$query = 'UPDATE #__registrationpro_usersconfig SET published = '. (int) $publish . ' WHERE user_id IN ('. $cids .')';
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
	}

	function getUsersTotal() {
		// Lets load the total nr if it doesn't already exist
		if (empty($this->_total)) {
			$query = $this->_buildUserQuery();
			$this->_total = $this->_getListCount($query);
		}
		return $this->_total;
	}

	function getUsersPagination() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getUsersTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}

	function _buildUserQuery() {
		$where   = $this->_buildUserContentWhere();
		$orderby = $this->_buildUserContentOrderBy();
		$query = "SELECT u.* FROM #__users AS u LEFT JOIN #__registrationpro_usersconfig AS uc ON u.id = uc.user_id" . $where . $orderby;
		return $query;
	}

	/**
	 * Build the order clause
	 * @access private
	 * @return string
	 */
	function _buildUserContentOrderBy() {
		global $mainframe, $option;

		$mainframe = JFactory::getApplication();

		$filter_order = $mainframe->getUserStateFromRequest( $option.'.selectusers.filter_order', 'filter_order', 'u.name', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $option.'.selectusers.filter_order_Dir', 'filter_order_Dir', '', 'word' );

		if ($filter_order == ''){
			$orderby 	= ' ORDER BY u.name';
		}else $orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	function _buildUserContentWhere() {
		global $mainframe, $option;

		$mainframe = JFactory::getApplication();

		$filter_state = $mainframe->getUserStateFromRequest( $option.'.selectusers.filter_state', 'filter_state', '', 'word' );
		$filter = $mainframe->getUserStateFromRequest( $option.'.selectusers.filter', 'filter', '', 'int' );
		$search = $mainframe->getUserStateFromRequest( $option.'.selectuserssearch', 'search', '', 'string' );
		$search = $this->_db->escape( trim(JString::strtolower( $search ) ) );

		$where = array();

		if ($filter_state) {
			if ($filter_state == 'P') {
				$where[] = 'uc.published = 1';
			} else if ($filter_state == 'U') {
				$where[] = 'uc.published = 0';
			}
		} else $where[] = 'u.block = 0';

		if ($search) $where[] = ' LOWER(username) LIKE \'%'.$search.'%\' ||  LOWER(name) LIKE \'%'.$search.'%\' ||  LOWER(email) LIKE \'%'.$search.'%\'';

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		return $where;
	}

	function getUsersList() {
		if (empty($this->_userdata)) {
			$query = $this->_buildUserQuery();
			$this->_userdata = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}
		for($i=0;$i<count($this->_userdata);$i++){
			$this->_userdata[$i]->published = $this->getPublishStatus($this->_userdata[$i]->id);
		}
		return $this->_userdata;
	}

	function getPublishUsers()
	{
		$db=JFactory::getDBO();
		$query = "SELECT u.name FROM #__users as u, #__registrationpro_usersconfig as uc WHERE uc.published = 1 AND uc.user_id = u.id";
		$db->setQuery($query);
		$results=$db->loadAssocList();
		return $results;
	}

	function getPublishUsersIds()
	{
		$db=JFactory::getDBO();
		$query = "SELECT uc.user_id FROM #__registrationpro_usersconfig as uc WHERE uc.published = 1";
		$db->setQuery($query);
		$results=$db->loadResult();
		return $results;
	}
	function getPublishStatus($uid)
	{
		$db=JFactory::getDBO();
		$query = "SELECT published FROM #__registrationpro_usersconfig WHERE user_id =".$uid;
		$db->setQuery($query);
		$results=$db->loadResult();
		return $results;
	}

	function getModeratorUsersIds()
	{
		$db=JFactory::getDBO();
		$query = "SELECT uc.user_id FROM #__registrationpro_usersconfig as uc WHERE uc.moderator = 1";
		$db->setQuery($query);
		$results=$db->loadResult();
		return $results;
	}

	function saveUserIds($userids = array(), $status = 0) {
		$count = 0;
		foreach($userids as $userid)
		{	$db=JFactory::getDBO();
			$result = "";
			$strquery = "SELECT id,published FROM #__registrationpro_usersconfig WHERE user_id =".$userid;
			$db->setQuery($strquery);
			$result=$db->loadAssocList();

			if(!$result) {
				$query1 = "INSERT INTO #__registrationpro_usersconfig SET published = ".$status.", user_id = ".$userid;
				$this->_db->setQuery($query1);
				if (!$this->_db->query()) {
					echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
					exit();
				}
			}else{
				if($result[0]['published']==0){
					$status = 1;
				}else{
					$status = 0;
				}
				$query2 = "UPDATE #__registrationpro_usersconfig SET published = ".$status." WHERE user_id = ".$userid;
				$this->_db->setQuery($query2);
				if (!$this->_db->query()) {
					echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
					exit();
				}
			}
		}
	}

	function savemoderation($userids = array(), $status = 0) {
		$count = 0;
		foreach($userids as $userid) {
			$result = "";
			$strquery = "SELECT id FROM #__registrationpro_usersconfig WHERE user_id =".$userid;
			$this->_db->setQuery($strquery);
			$result = $this->_db->loadResult();

			if(!$result) {
				$query1 = "INSERT INTO #__registrationpro_usersconfig SET moderator = ".$status.", user_id = ".$userid;
				$this->_db->setQuery($query1);
				if (!$this->_db->query()) {
					echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
					exit();
				}
			}else{
				$query2 = "UPDATE #__registrationpro_usersconfig SET moderator = ".$status." WHERE user_id = ".$userid;
				$this->_db->setQuery($query2);
				if (!$this->_db->query()) {
					echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
					exit();
				}
			}
		}
	}
	function getSymbol($code){
		$db = JFactory::getDBO();
		$db->setQuery("SELECT currency_symbol FROM #__registrationpro_currency WHERE currency_code LIKE '%".$code."%'");
		if(!$this->_db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}else{
			return $db->loadResult();
		}
	}
}

?>