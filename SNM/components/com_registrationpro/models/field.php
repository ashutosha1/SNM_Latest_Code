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

class registrationproModelField extends JModelLegacy
{
	var $_id = null;
	var $_data = null;

	function __construct()
	{
		parent::__construct();

		/*$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);*/
		$id = JRequest::getVar('id',0,'','int');
		$this->setId((int)$id);
		
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
			$query = "SELECT * FROM #__registrationpro_fields WHERE id=".$this->_id;
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}
	
	function _initData()
	{
		if (empty($this->_data))
		{
			$field = new stdClass();			
			$field->id 				= null;
			$field->form_id 		= null;
			$field->name 			= null;
			$field->title 			= null;			
			$field->description 	= null;
			$field->inputtype 		= null;
			$field->values	 		= null;	
			$field->default_value 	= null;	
			$field->params 			= null;
			$field->validation_rule	= null;
			$field->confirm			= null;
			$field->ordering 		= null;
			$field->published 		= 1;
			$field->batch_display 	= null;	
			$field->groupid 		= null;	
			$field->display_type	= null;		
			$this->_data			= $field;
			return (boolean) $this->_data;
		}
		return true;
	}	
	
	function store($data)
	{
		$repgrosettings = registrationproAdmin::config();
		$user		=  JFactory::getUser();
		$config 	=  JFactory::getConfig();

		$tzoffset 	= $config->get('config.offset');

		$row  =& $this->getTable('registrationpro_fields', '');

		if (!$row->bind($data)) {
			JError::raiseError(500, $this->_db->getErrorMsg() );
			return false;
		}
				
		$row->id 		= (int) $row->id;
		//$row->ordering 	= $row->getNextOrder();
		$nullDate		= $this->_db->getNullDate();
						
		// Store it in the db
		if (!$row->store()) {
			JError::raiseError(500, $this->_db->getErrorMsg() );
			return false;
		}
		
		// add groupid with groups records
		if($row->inputtype == 'groups'){
			$strqry = "UPDATE #__registrationpro_fields SET groupid = ".$row->id." WHERE id = ".$row->id;
			$this->_db->setQuery($strqry);
			$this->_db->query();
		}
		
		// Check the fields and update item order
		$row->checkin();
		$row->reorder('form_id = '.(int) $row->form_id.' AND groupid ='.(int) $row->groupid);
				
		return $row->id;
	}
	
	function delete($cid = array(), $form_id)
	{
		global $mainframe;
		
		$result = false;
		
		$total 	= count( $cid );
		$fields = implode( ',', $cid );
						
		//Delete field
		$query = "DELETE FROM #__registrationpro_fields WHERE id IN ($fields) AND (name NOT IN ('firstname','lastname','email') AND params != 'default')";
		$this->_db->setQuery($query);
		//$this->_db->query();
								
		if ( !$this->_db->query() ) {				
			echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";	
			exit();
		}

		$cnt = $this->_db->getAffectedRows; //mysql_affected_rows();
		
		$total = $cnt;
		if($cnt > 0){
			$msg = $total .JText::_('ADMIN_FIELDS_DEL');
		}else{			
			$msg = JText::_('ADMIN_FIELDS_CANT_DELETE');			
		}

		return $msg;
	}	
	
	function move($inc, $cid){	
		//echo $cid; exit;
		$row  =& $this->getTable('registrationpro_fields', '');
		$row->load( $cid );
		$row->move( $inc, "");				
	}
		
	function publish($cid = array(), $publish = 1)
	{
		if (count( $cid ))
		{
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__registrationpro_fields'
				. ' SET published = '. (int) $publish
				. ' WHERE name NOT IN ("firstname","lastname","email") AND id IN ('. $cids .')';
				
			$this->_db->setQuery( $query );

			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
	}
		
	/**
	 * Method to move a events
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function saveorder($cid = array(), $order)
	{
		$row  =& $this->getTable('registrationpro_fields', '');
		$total		= count( $cid );		
		
		$conditions = array();
		
		// update ordering values
		for( $i=0; $i < $total; $i++ ) {
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
					exit();
				} // if
				// remember to updateOrder this group
				$condition = 'form_id = '.(int) $row->form_id.' AND groupid ='.(int) $row->groupid; //"form_id = " . (int) $row->form_id;
				$found = false;
				foreach ( $conditions as $cond )
					if ($cond[1]==$condition) {
						$found = true;
						break;
					} // if
				if (!$found) $conditions[] = array($row->id, $condition);
			} // if
		} // for			
	
		// execute updateOrder for each group
		foreach ( $conditions as $cond ) {
			$row->load( $cond[0] );
		} // foreach
	
		// clean any existing cache files
		$cache = JFactory::getCache('com_registrationpro');
		$cache->clean('com_registrationpro');

		return true;
	}
	
	function adddefaultfields($formid)
	{		
		$arr_data = array(
			1=>array("name"=>"firstname","title"=>"First Name","validation_rule"=>"mandatory","ordering"=>1),
			2=>array("name"=>"lastname","title"=>"Last Name","validation_rule"=>"mandatory","ordering"=>2),
			3=>array("name"=>"email","title"=>"Email","validation_rule"=>"email","ordering"=>3));
			//echo "<pre>"; print_r($arr_data);exit;
											
		foreach($arr_data as $key=>$value){
			$row  =& $this->getTable('registrationpro_fields', '');
			
			$row->form_id		=	$formid;
			$row->name			=	$arr_data[$key]['name'];
			$row->title			=	$arr_data[$key]['title'];
			$row->description	=	'';
			$row->inputtype		=	'text';
			$row->default_value	=	'';
			$row->params		=	'default';
			$row->validation_rule =	$arr_data[$key]['validation_rule'];
			$row->published		=	1;
			$row->batch_display	=	1;
			$row->ordering		=	$arr_data[$key]['ordering'];
					
			// Store it in the db
			if (!$row->store()) {
				JError::raiseError(500, $this->_db->getErrorMsg() );
				return false;
			}
		}	
				
		return true; //$row->id;						
	}
	
	
	function copyfields($copied_formid, $assign_formid)
	{
		// get all forms fields
		$query = "SELECT * FROM #__registrationpro_fields WHERE form_id=".$copied_formid;
		$this->_db->setQuery($query);
		$fields  = $this->_db->loadObjectList();
						
		//echo "<pre>"; print_r($fields);exit;													
		foreach($fields as $key=>$value){
			$row  =& $this->getTable('registrationpro_fields', '');
			
			$row->form_id		=	$assign_formid;
			$row->name			=	$fields[$key]->name;
			$row->title			=	$fields[$key]->title;
			$row->description	=	$fields[$key]->description;
			$row->inputtype		=	$fields[$key]->inputtype;
			$row->default_value	=	$fields[$key]->default_value;
			$row->params		=	$fields[$key]->params;
			$row->validation_rule =	$fields[$key]->validation_rule;
			$row->published		=	$fields[$key]->published;
			$row->batch_display	=	$fields[$key]->batch_display;
			$row->ordering		=	$fields[$key]->ordering;
					
			// Store it in the db
			if (!$row->store()) {
				JError::raiseError(500, $this->_db->getErrorMsg() );
				return false;
			}
		}
				
		return true; //$row->id;						
	}
	
	function copycbfields($copied_formid, $assign_formid)
	{
		// get all cb forms fields
		$query = "SELECT * FROM #__registrationpro_cbfields WHERE form_id=".$copied_formid;
		$this->_db->setQuery($query);
		$fields  = $this->_db->loadObjectList();
						
		//echo "<pre>"; print_r($fields);exit;													
		foreach($fields as $key=>$value){
			$row  =& $this->getTable('registrationpro_cbfields', '');
			
			$row->form_id		=	$assign_formid;
			$row->cbfield_id	=	$fields[$key]->cbfield_id;					
			// Store it in the db
			if (!$row->store()) {
				JError::raiseError(500, $this->_db->getErrorMsg() );
				return false;
			}
		}
				
		return true;
	}
	
	
	function getfields_groups($form_id)
	{
		// get all fields groups
		$query = "SELECT id AS value, title AS text FROM #__registrationpro_fields WHERE published = 1 AND inputtype ='groups' AND form_id =".$form_id;
		$this->_db->setQuery($query);
		$field_groups  = $this->_db->loadObjectList();
		return $field_groups;
	}
	
	function getAllfields($form_id)
	{
		// get all fields groups
		$query = "SELECT name AS value, title AS text FROM #__registrationpro_fields WHERE published = 1 AND inputtype = 'text' AND form_id =".$form_id;
		$this->_db->setQuery($query);
		$all_fields  = $this->_db->loadObjectList();
		return $all_fields;
	}	
	
	// **************************************** Cb Fields functions ***************************************************
	
	function publishcbfield($cid, $form_id,$publishfield=1)
	{		
		//delete from registrationpro_cbfields where cbfields = cid and form_id = get[form_id]
		$query = "DELETE FROM #__registrationpro_cbfields WHERE form_id = $form_id AND cbfield_id = $cid";
		$this->_db->setQuery($query);
		$this->_db->query();
		
		if($publishfield){
			//insert
			$query = "INSERT INTO #__registrationpro_cbfields (form_id, cbfield_id) VALUES ($form_id,$cid)";
			$this->_db->setQuery($query);
			$this->_db->query();			
		}		
	}
	
	// **************************************** Joomfish Fields functions ***************************************************
	
	function publishjoosocialfield($cid, $form_id,$publishfield=1)
	{		
		//delete from registrationpro_cbfields where cbfields = cid and form_id = get[form_id]
		$query = "DELETE FROM #__registrationpro_cbfields WHERE form_id = $form_id AND joomfishfield_id = $cid";
		$this->_db->setQuery($query);
		$this->_db->query();
		
		if($publishfield){
			//insert
			$query = "INSERT INTO #__registrationpro_cbfields (form_id, joomfishfield_id) VALUES ($form_id,$cid)";
			$this->_db->setQuery($query);
			$this->_db->query();			
		}		
	}
		
}

?>