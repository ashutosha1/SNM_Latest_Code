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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class registrationproModelForms extends JModelLegacy
{
	var $_data = null;
	var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();

		global $mainframe, $option;

		// set page limit from config setting of component
		$this->setState('limit', $mainframe->getUserStateFromRequest('com_registrationpro_forms.limit', 'limit', $regpro_config['eventslimit'], 'int'));
		$this->setState('limitstart', JRequest::getVar('limitstart', 0, '', 'int'));

		// In case limit has been changed, adjust limitstart accordingly
		$this->setState('limitstart', ($this->getState('limit') != 0 ? (floor($this->getState('limitstart') / $this->getState('limit')) * $this->getState('limit')) : 0));

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
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();
		
		$query = "SELECT * FROM #__registrationpro_forms AS f "							
				. $where
				. $orderby;
								
		return $query;
	}

	/**
	 * Build the order clause
	 *
	 * @access private
	 * @return string
	 */
	function _buildContentOrderBy()
	{
		global $mainframe, $option;

		$filter_order		= $mainframe->getUserStateFromRequest( $option.'.forms.filter_order', 'filter_order', 'f.id', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'.forms.filter_order_Dir', 'filter_order_Dir', '', 'word' );	
		
		if ($filter_order == ''){
			$orderby 	= ' ORDER BY '.$filter_order_Dir;
		} else {
			$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;
		}
		
		return $orderby;
	}

	function _buildContentWhere()
	{
		global $mainframe, $option;
		
		$user 		=  JFactory::getUser();

		$filter_state 		= $mainframe->getUserStateFromRequest( $option.'.filter_state', 'filter_state', '', 'word' );
		$filter 			= $mainframe->getUserStateFromRequest( $option.'.filter', 'filter', '', 'int' );
		$search 			= $mainframe->getUserStateFromRequest( $option.'.search', 'search', '', 'string' );
		$search 			= $this->_db->escape( trim(JString::strtolower( $search ) ) );

		$where = array();
		
		$where[] = "f.user_id = ".$user->id;	

		if ($filter_state) {
			if ($filter_state == 'P') {
				$where[] = 'f.published = 1';
			} else if ($filter_state == 'U') {
				$where[] = 'f.published = 0';
			} else {
				$where[] = 'f.published >= 0';
			}
		} else {
			$where[] = 'f.published >= 0';
		}

		if ($search) {
			$where[] = "LOWER(f.title) LIKE '%$search%'";				
		}		

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	function _additionals($rows)
	{		
		return $rows;
	}

	function publish($cid = array(), $publish = 1)
	{
		$user 	= JFactory::getUser();
		$userid = (int) $user->get('id');

		if (count( $cid ))
		{
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__registrationpro_forms'
				. ' SET published = '. (int) $publish
				. ' WHERE id IN ('. $cids .')'
				;
			$this->_db->setQuery( $query );

			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
	}

	function delete($cid = array())
	{
		global $mainframe;
		
		$result = false;
		
		$total 	= count( $cid );
		$forms = implode( ',', $cid );
						
		if (count( $cid ))
		{
			//Check if Form has fields
				$this->_db->setQuery("SELECT form_id FROM #__registrationpro_fields");			
				$result = $this->_db->query();
			
				while($row = mysql_fetch_assoc($result)) {			
					if ($row['form_id'] == $forms) {						
						$this->_db->setQuery("SELECT count(*) FROM #__registrationpro_fields where form_id = $forms");															
						$result1 = $this->_db->loadResult();
						
						if($result1 > 3){ // redirect if fields are more then three means (firstname, lastname, email and more...)
							$mainframe->redirect("index.php?option=com_registrationpro&view=forms", JText::_( 'ADMIN_EVENTS_DEL_FORM')); 
						}
					}
				}
			// End				
		
			// delete forms by change the status of form record
			$this->_db->setQuery("UPDATE #__registrationpro_forms SET published = -2 WHERE id IN ($forms)");
			$this->_db->query();
		
			if ( !$this->_db->query() ) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			// end									
		}
		return $total;
	}				
	
}//Class end
?>