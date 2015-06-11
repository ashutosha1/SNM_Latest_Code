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

jimport('joomla.application.component.model');

class registrationproModelForm extends JModelLegacy
{
	var $_id = null;
	var $_data = null;

	function __construct()
	{
		global $mainframe, $option;
		parent::__construct();

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
		// Get the pagination request variables
		$limit		= $mainframe->getUserStateFromRequest( $option.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( $option.'coupons_limitstart', 'limitstart', 0, 'int' );
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}
	function setId($id)
	{
		$this->_id	    = $id;
		$this->_data	= null;
	}

	function &getData()
	{
		if ($this->_loadData())
		{

		}
		else  $this->_initData();

		return $this->_data;
	}
	
	function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = "SELECT * FROM #__registrationpro_forms "
				. "\nWHERE id = ".$this->_id;				

			$this->_db->setQuery($query);

			$this->_data = $this->_db->loadObject();

			return (boolean) $this->_data;
		}
		return true;
	}
	
	function _initData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$forms = new stdClass();
			
			$forms->id 					= null;
			$forms->name 				= null;
			$forms->title 				= null;
			$forms->thankyou 			= null;
			$forms->published 			= 1;
			$forms->checked_out 		= null;
			$forms->checked_out_time 	= null;
													
			$this->_data				= $forms;
			return (boolean) $this->_data;
		}
		return true;
	}
	
	
	function store($data)
	{
		$registrationproAdmin = new registrationproAdmin;		
		$repgrosettings	= $registrationproAdmin->config();
		$user		= JFactory::getUser();
		$config 	= JFactory::getConfig();

		$tzoffset 	= $config->get('config.offset');

		$row  =$this->getTable('registrationpro_forms', '');

		if (!$row->bind($data)) {
			JError::raiseError(500, $this->_db->getErrorMsg() );
			return false;
		}
				
		$row->id = (int) $row->id;
		
		//$row->ordering = $row->getNextOrder();

		$nullDate	= $this->_db->getNullDate();
						
		// Store it in the db
		if (!$row->store()) {
			JError::raiseError(500, $this->_db->getErrorMsg() );
			return false;
		}

		return $row->id;
	}
	
	
	function getDefaultFieldsCount($formid)
	{
		//$query = "SELECT count(*) as field_cnt FROM #__registrationpro_fields WHERE name in ('firstname','lastname','email') and params='default' and form_id = $formid";
		$query = "SELECT count(*) as field_cnt FROM #__registrationpro_fields WHERE name in ('firstname','lastname','email') and form_id = $formid";
		$this->_db->setQuery($query);
		$count = $this->_db->loadResult($count);
		//echo $count; exit;
		return $count;
	}
					
	function getFields($formid)
	{		
		$query = "SELECT *"
		. "\nFROM #__registrationpro_fields"
		. "\nWHERE form_id = '$formid'"	
		. "\nORDER BY groupid, ordering";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	
	function getCBFields($formid)
	{		
		$cb_fields = array();		
		//$this->_db->setQuery("SELECT * FROM #__comprofiler_fields WHERE published=1 ORDER BY tabid, ordering");
		
		$query = "SELECT f.* FROM #__comprofiler_fields AS f "		
				. "\n INNER JOIN #__comprofiler_tabs AS t ON ( (f.tabid = t.tabid) AND (t.fields = 1) ) "
				. "\n WHERE f.published=1"
				. "\n ORDER BY t.ordering, f.ordering";
		
		$this->_db->setQuery($query);
		
		$cb_fields = $this->_db->loadObjectList();
		
		//echo "<pre>"; print_r($cb_fields); exit;
		
		//check if cb_fields are assigned to regpro
		foreach($cb_fields as $i=>$cb_field){
			$query = "SELECT count(*) cnt FROM #__registrationpro_cbfields WHERE form_id = $formid AND cbfield_id = ".$cb_field->fieldid;
			$this->_db->setQuery($query);
			$is_published = $this->_db->loadResult();
			$cb_fields[$i]->is_regpro = ($is_published) ? $is_published : 0;
		}
		
		//echo "<pre>"; print_r($cb_fields); exit;
				
		return $cb_fields;
	}
	
	function getJoomsocialFields($formid)
	{		
		$joomsocial_fields = array();
		
		$query = "SELECT f.* FROM #__community_fields AS f "				
				. "\n WHERE f.published=1"
				. "\n ORDER BY f.ordering";
		
		$this->_db->setQuery($query);
		
		$joomsocial_fields = $this->_db->loadObjectList();
		
		//echo "<pre>"; print_r($joomsocial_fields); exit;
		
		//check if cb_fields are assigned to regpro
		foreach($joomsocial_fields as $i=>$joomsocial_field){
			$query = "SELECT count(*) cnt FROM #__registrationpro_cbfields WHERE form_id = $formid AND joomfishfield_id = ".$joomsocial_field->id;
			$this->_db->setQuery($query);
			$is_published = $this->_db->loadResult();
			$joomsocial_fields[$i]->is_regpro = ($is_published) ? $is_published : 0;
		}
		
		
		//echo "<pre>"; print_r($joomsocial_fields); exit;
				
		return $joomsocial_fields;
	}
	
	function getCorefields($formid)
	{
		$plugin =& JPluginHelper::getPlugin('user', 'profile');
		
		$pluginParams = new JRegistry();
		$pluginParams->loadString($plugin->params);
		
		
		$corefields = array();	
		
		$i = 0;
		
		if ($pluginParams->get('register-require_address1', 1) > 0) {
					
			$corefields[$i]['title'] = 'Address 1';		
			$corefields[$i]['identification'] = 'address1';			
			$corefields[$i]['inputtype'] = 'text';
			$corefields[$i]['form_id'] = $formId;									
			$i++;
		}
		
		if ($pluginParams->get('register-require_address2', 1) > 0) {			
			$corefields[$i]['title'] = 'Address 2';			
			$corefields[$i]['identification'] = 'address2';			
			$corefields[$i]['inputtype'] = 'text';
			$corefields[$i]['form_id'] = $formId;	
			$i++;				
		}
		
		if ($pluginParams->get('register-require_city', 1) > 0) {			
			$corefields[$i]['title'] = 'City';			
			$corefields[$i]['identification'] = 'city';			
			$corefields[$i]['inputtype'] = 'text';
			$corefields[$i]['form_id'] = $formId;
			$i++;					
		}
		
		if ($pluginParams->get('register-require_region', 1) > 0) {			
			$corefields[$i]['title'] = 'Region';
			$corefields[$i]['identification'] = 'region';			
			$corefields[$i]['inputtype'] = 'text';
			$corefields[$i]['form_id'] = $formId;	
			$i++;					
		}
		
		if ($pluginParams->get('register-require_country', 1) > 0) {			
			$corefields[$i]['title'] = 'Country';			
			$corefields[$i]['identification'] = 'country';			
			$corefields[$i]['inputtype'] = 'text';
			$corefields[$i]['form_id'] = $formId;
			$i++;		
		}
		
		if ($pluginParams->get('register-require_postal_code', 1) > 0) {			
			$corefields[$i]['title'] = 'Postal / ZIP Code';			
			$corefields[$i]['identification'] = 'postal_code';			
			$corefields[$i]['inputtype'] = 'text';
			$corefields[$i]['form_id'] = $formId;
			$i++;		
		}
		
		if ($pluginParams->get('register-require_phone', 1) > 0) {			
			$corefields[$i]['title'] = 'Phone';		
			$corefields[$i]['identification'] = 'phone';			
			$corefields[$i]['inputtype'] = 'text';
			$corefields[$i]['form_id'] = $formId;	
			$i++;					
		}
		
		if ($pluginParams->get('register-require_website', 1) > 0) {			
			$corefields[$i]['title'] = 'Website';		
			$corefields[$i]['identification'] = 'website';			
			$corefields[$i]['inputtype'] = 'text';
			$corefields[$i]['form_id'] = $formId;
			$i++;		
		}
		
		if ($pluginParams->get('register-require_favoritebook', 1) > 0) {			
			$corefields[$i]['title'] = 'Favourite Book';			
			$corefields[$i]['identification'] = 'favoritebook';			
			$corefields[$i]['inputtype'] = 'text';
			$corefields[$i]['form_id'] = $formId;
			$i++;						
		}
				
		if ($pluginParams->get('register-require_aboutme', 1) > 0) {			
			$corefields[$i]['title'] = 'About Me';			
			$corefields[$i]['identification'] = 'aboutme';			
			$corefields[$i]['inputtype'] = 'textarea';
			$corefields[$i]['form_id'] = $formId;	
			$i++;					
		}
		
		if ($pluginParams->get('register-require_tos', 1) > 0) {			
			$corefields[$i]['title'] = 'Terms of Service';
			$corefields[$i]['identification'] = 'tos';			
			$corefields[$i]['inputtype'] = 'radio';
			$corefields[$i]['form_id'] = $formId;
			$i++;									
		}
		
		if ($pluginParams->get('register-require_dob', 1) > 0) {			
			$corefields[$i]['title'] = 'Date of Birth';			
			$corefields[$i]['identification'] = 'dob';			
			$corefields[$i]['inputtype'] = 'calendar';
			$corefields[$i]['form_id'] = $formId;
			$i++;						
		}
		
		//echo "<pre>"; print_r($corefields); exit;
		
		//check if core_fields are assigned to regpro
		foreach($corefields as $i=>$corefield){
			$query = "SELECT count(*) cnt FROM #__registrationpro_cbfields WHERE form_id = $formid AND corefield_id = '".$corefield['identification']."'";
			$this->_db->setQuery($query);
			$is_published = $this->_db->loadResult();
			$corefields[$i]['is_regpro'] = ($is_published) ? $is_published : 0;
		}				
	
		//echo "<pre>"; print_r($corefields); exit;
				
		return $corefields;
	}
	
	function getGroupName($id)
	{
		// get all fields groups
		$query = "SELECT title FROM #__registrationpro_fields WHERE published = 1 AND inputtype ='groups' AND id = $id";
		$this->_db->setQuery($query);
		$groupname  = $this->_db->loadResult();
		return $groupname;
	}
	/**
	 * Retrieves the JPagination object
	 *
	 * @return object	JPagination object
	 **/
	public function getPagination($formid)
	{
		if ($this->_pagination == null)
		{
			$this->getFieldsNew($formid);
		}
		return $this->_pagination;
	}

	/**
	 * Returns the Fields
	 *
	 * @return object	JParameter object
	 **/
	public function getFieldsNew( $formid, $usePagination = false )
	{
		$mainframe	= JFactory::getApplication();

		static $fields;

		if( isset( $fields ) )
		{
			return $fields;
		}

		// Initialize variables
		$db			= JFactory::getDBO();

		// Get the limit / limitstart
		$limit		= $mainframe->getUserStateFromRequest( $option.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( $option.'coupons_limitstart', 'limitstart', 0, 'int' );

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart	= ($limit != 0) ? ($limitstart / $limit ) * $limit : 0;

		// Get the total number of records for pagination
		$query	= 'SELECT COUNT(*) FROM ' . $db->quoteName( '#__registrationpro_fields' );
		$db->setQuery( $query );
		$total	= $db->loadResult();

		jimport('joomla.html.pagination');

		// Get the pagination object
		$this->_pagination	= new JPagination( $total , $limitstart , $limit );

		$query	= 'SELECT * FROM ' . $db->quoteName( '#__registrationpro_fields' ) . ' '
				. ' WHERE form_id = "'.$formid.'" '
				. 'ORDER BY groupid,ordering';

		if( $usePagination )
		{
			$db->setQuery( $query , $this->_pagination->limitstart , $this->_pagination->limit );
		}
		else
		{
			$db->setQuery( $query );
		}

		$fields	= $db->loadObjectList();

		return $fields;
	}	
}

?>