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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class registrationpro_fields extends JTable{
	var $id 				= null;
	var $form_id			= null;
	var $name 				= null;
	var $title 				= null;	
	var $description 		= null;
	var $inputtype 			= null;
	var $values				= null;
	var $default_value 		= null;	
	var $params 			= null;
	var $validation_rule	= null;
	var $confirm			= null;
	var $ordering 			= null;
	var $published 			= 1;	
	var $batch_display 		= null;	
	var $groupid 			= null;	
	var $display_type		= null;	
	var $conditional_field	= null;
	var $conditional_field_values	= null;
	var $conditional_field_name	= null;
	var $fees_field			= null;
	var $fees 				= null;
	var $fees_type 			= null;

	function __construct( &$db )
	{
		parent::__construct( '#__registrationpro_fields', 'id', $db );
	}
	
	 function moveFields( $dirn, $where='',$form_id ){
	 $db= JFactory::getDBO();
        if (!in_array( 'ordering',  array_keys($this->getProperties())))
        {
                $this->setError( get_class( $this ).' does not support ordering' );
                return false;
        }
 
        $k = $this->_tbl_key;
 
        $sql = "SELECT $this->_tbl_key, ordering FROM $this->_tbl";
 
        if ($dirn < 0)
        {
                $sql .= ' WHERE ordering = '.(int) $this->ordering;
                $sql .= ($where ? ' AND '.$where : '');
                $sql .= ' ORDER BY ordering DESC';
        }
        else if ($dirn > 0)
        {
                $sql .= ' WHERE ordering > '.(int) $this->ordering;
                $sql .= ($where ? ' AND '. $where : '');
                $sql .= ' ORDER BY ordering';
        }
        else
        {
                $sql .= ' WHERE ordering = '.(int) $this->ordering;
                $sql .= ($where ? ' AND '.$where : '');
                $sql .= ' ORDER BY ordering';
        }
 
        $this->_db->setQuery( $sql);
 //echo $sql;
        $row = null;
        $row = $this->_db->loadObject();
		//echo '<pre>'; print_r($row);//die;
		
		 $sql1 = "SELECT $this->_tbl_key, ordering FROM $this->_tbl WHERE ordering < ".$row->ordering." AND published=1  AND form_id=".$form_id." AND inputtype='groups' ";
		  $this->_db->setQuery( $sql1);
		  $new_row = $this->_db->loadObjectList();
		 //echo '<pre>'; print_r($new_row);
		   $sql2 = "SELECT $this->_tbl_key, ordering FROM $this->_tbl WHERE groupid= ".$row->id." AND form_id=".$form_id." ORDER BY ordering";
		  $this->_db->setQuery( $sql2);
		  $grp_row = $this->_db->loadObjectList();
		 // echo '<pre>'; print_r($grp_row);//die;
		 $count = count($grp_row);
		 $ordr = $new_row[0]->ordering;
		  foreach($grp_row as $val){
                $query = 'UPDATE '. $this->_tbl
                . ' SET ordering = '. (int) ($ordr)
                . ' WHERE id = '. $val->id.' AND form_id='.$form_id;
                ;
				
				
                $this->_db->setQuery( $query );
 
                if (!$this->_db->query())
                {
                        $err = $this->_db->getErrorMsg();
                        JError::raiseError( 500, $err );
                } 
				$ordern = $ordr++;
			}
          $sql4 = "SELECT $this->_tbl_key, ordering FROM $this->_tbl WHERE groupid= ".$new_row[0]->id." AND form_id=".$form_id." ORDER BY ordering";
		  $this->_db->setQuery( $sql4);
		  $grp_row1 = $this->_db->loadObjectList();
		  $count1 = count($grp_row1);
		  $forder = $ordern+1;;
		  foreach($grp_row1 as $valu){
		  
              $query = 'UPDATE '. $this->_tbl
                . ' SET ordering = '. (int) ($forder)
                . ' WHERE id = '. $valu->id.' AND form_id='.$form_id;
                ;
				
				
                $this->_db->setQuery( $query );
 
                if (!$this->_db->query())
                {
                        $err = $this->_db->getErrorMsg();
                        JError::raiseError( 500, $err );
                }
				$forder++; 
			}
    
	  $sql5 = "SELECT $this->_tbl_key, ordering FROM $this->_tbl WHERE form_id=".$form_id." ORDER BY ordering";
		  $this->_db->setQuery( $sql5);
		  $grp_row2 = $this->_db->loadObjectList();
		  $i = 1;
		  foreach($grp_row2 as $value){
		  
                 $query = 'UPDATE '. $this->_tbl
                . ' SET ordering = '. (int) ($i)
                . ' WHERE id = '. $value->id.' AND form_id='.$form_id;
                ;
				
				
                $this->_db->setQuery( $query );
 
                if (!$this->_db->query())
                {
                        $err = $this->_db->getErrorMsg();
                        JError::raiseError( 500, $err );
                }
				$i++;
			}
	 
        return true;
} 
	 function moveFieldsInter( $dirn, $where='',$form_id ){
	 $db= JFactory::getDBO();
        if (!in_array( 'ordering',  array_keys($this->getProperties())))
        {
                $this->setError( get_class( $this ).' does not support ordering' );
                return false;
        }
 
        $k = $this->_tbl_key;
 
        $sql = "SELECT $this->_tbl_key, ordering, groupid FROM $this->_tbl";
 
        if ($dirn < 0)
        {
                $sql .= ' WHERE ordering = '.(int) $this->ordering.' AND form_id='.$form_id;
                $sql .= ($where ? ' AND '.$where : '');
                $sql .= ' ORDER BY ordering DESC LIMIT 1';
        }
        else if ($dirn > 0)
        {
                $sql .= ' WHERE ordering > '.(int) $this->ordering.' AND form_id='.$form_id;
                $sql .= ($where ? ' AND '. $where : '');
                $sql .= ' ORDER BY ordering';
        }
        else
        {
                $sql .= ' WHERE ordering = '.(int) $this->ordering.' AND form_id='.$form_id;
                $sql .= ($where ? ' AND '.$where : '');
                $sql .= ' ORDER BY ordering';
        }
 
        $this->_db->setQuery( $sql);
 //echo $sql;
        $row = null;
        $row = $this->_db->loadObjectList();
		//echo '<pre>'; print_r($row);
		$sql2 = "SELECT $this->_tbl_key, ordering,groupid FROM $this->_tbl WHERE id <>".$row[0]->groupid." AND ordering < ".$row[0]->ordering." AND form_id=".$form_id." AND published=1 ORDER BY ordering DESC LIMIT 1";
		  $this->_db->setQuery( $sql2);
		  $grp_row = $this->_db->loadObjectList();
	if(!empty($grp_row)){
			if($grp_row[0]->groupid == $row[0]->groupid){
							$query = 'UPDATE '. $this->_tbl
							. ' SET ordering = '. (int) ($grp_row[0]->ordering)
							. ' WHERE id = '. $row[0]->id.' AND form_id='.$form_id;
							;
							
							
							$this->_db->setQuery( $query );
			 
							if (!$this->_db->query())
							{
									$err = $this->_db->getErrorMsg();
									JError::raiseError( 500, $err );
							} 
						  $query1 = 'UPDATE '. $this->_tbl
							. ' SET ordering = '. (int) ($row[0]->ordering)
							. ' WHERE id = '. $grp_row[0]->id.' AND form_id='.$form_id;
							;
							
							
						 	$this->_db->setQuery( $query1 );
			 
							if (!$this->_db->query())
							{
									$err = $this->_db->getErrorMsg();
									JError::raiseError( 500, $err );
							} 
					}else{
							 $sqlx = "SELECT $this->_tbl_key, ordering,groupid FROM $this->_tbl WHERE ordering < ".$row[0]->ordering." AND form_id=".$form_id." AND published=1 ORDER BY ordering DESC LIMIT 1";
							$this->_db->setQuery( $sqlx);
							$prev_grp = $this->_db->loadObjectList();
					//echo '<pre>'; print_r($grp_row);
					
							$query = 'UPDATE '. $this->_tbl
							. ' SET ordering = '. (int) ($prev_grp[0]->ordering).' , groupid ='.$grp_row[0]->groupid
							. ' WHERE id = '. $row[0]->id.' AND form_id='.$form_id;
							;
							
							
							$this->_db->setQuery( $query );
			 
							if (!$this->_db->query())
							{
									$err = $this->_db->getErrorMsg();
									JError::raiseError( 500, $err );
							}  
						 $query1 = 'UPDATE '. $this->_tbl
							. ' SET ordering = '. (int) ($row[0]->ordering)
							. ' WHERE id = '. $prev_grp[0]->id;
							;
							
							
						 	$this->_db->setQuery( $query1 );
			 
							if (!$this->_db->query())
							{
									$err = $this->_db->getErrorMsg();
									JError::raiseError( 500, $err );
							}  
					}
				}
			

        return true;
} 
 
// down order functions

	 function moveDownFields( $dirn, $where='',$form_id ){
	 $db= JFactory::getDBO();
        if (!in_array( 'ordering',  array_keys($this->getProperties())))
        {
                $this->setError( get_class( $this ).' does not support ordering' );
                return false;
        }
 
        $k = $this->_tbl_key;
 
        $sql = "SELECT $this->_tbl_key, ordering FROM $this->_tbl";
 
        if ($dirn < 0)
        {
                $sql .= ' WHERE ordering = '.(int) $this->ordering;
                $sql .= ($where ? ' AND '.$where : '');
                $sql .= ' ORDER BY ordering';
        }
        else if ($dirn > 0)
        {
                $sql .= ' WHERE ordering > '.(int) $this->ordering;
                $sql .= ($where ? ' AND '. $where : '');
                $sql .= ' ORDER BY ordering';
        }
        else
        {
                $sql .= ' WHERE ordering = '.(int) $this->ordering;
                $sql .= ($where ? ' AND '.$where : '');
                $sql .= ' ORDER BY ordering';
        }
 
        $this->_db->setQuery( $sql);
 //echo $sql;
        $row = null;
        $row = $this->_db->loadObject();
		//echo '<pre>'; print_r($row);
		
		 $sql1 = "SELECT $this->_tbl_key, ordering FROM $this->_tbl WHERE ordering > ".$row->ordering." AND published=1  AND form_id=".$form_id." AND inputtype='groups' ";
		  $this->_db->setQuery( $sql1);
		  $new_row = $this->_db->loadObjectList();
		 //echo '<pre>'; print_r($new_row);
		 $sql2 = "SELECT $this->_tbl_key, ordering FROM $this->_tbl WHERE groupid= ".$row->id." AND form_id=".$form_id." ORDER BY ordering";
		  $this->_db->setQuery( $sql2);
		  $grp_row = $this->_db->loadObjectList();
		  //echo '<pre>'; print_r($grp_row);//die;
		 $count = count($grp_row);
		 $sql4 = "SELECT $this->_tbl_key, ordering FROM $this->_tbl WHERE groupid= ".$new_row[0]->id." AND form_id=".$form_id." ORDER BY ordering ASC";
		  $this->_db->setQuery( $sql4);
		  $grp_row1 = $this->_db->loadObjectList();
		  $count1 = count($grp_row1);
		  //echo '<pre>'; print_r($grp_row1);
		  
		  foreach($grp_row as $val){
                 echo '<br/>  '.$query = 'UPDATE '. $this->_tbl
                . ' SET ordering = '. (int) ($val->ordering+$count1)
                . ' WHERE id = '. $val->id.' AND form_id='.$form_id;
                ;
				
				
                $this->_db->setQuery( $query );
 
                if (!$this->_db->query())
                {
                        $err = $this->_db->getErrorMsg();
                        JError::raiseError( 500, $err );
                }
				$last_order = $val->ordering+$count1;
			}
          $new_order = $grp_row[0]->ordering;
		  foreach($grp_row1 as $valu){
		  
                $query = 'UPDATE '. $this->_tbl
                . ' SET ordering = '. (int) ($new_order)
                . ' WHERE id = '. $valu->id.' AND form_id='.$form_id;
                ;
				
				
                $this->_db->setQuery( $query );
 
                if (!$this->_db->query())
                {
                        $err = $this->_db->getErrorMsg();
                        JError::raiseError( 500, $err );
                }
				$new_order++;
			}
     // atlast update all
	  $sql5 = "SELECT $this->_tbl_key, ordering FROM $this->_tbl WHERE form_id=".$form_id." ORDER BY ordering";
		  $this->_db->setQuery( $sql5);
		  $grp_row2 = $this->_db->loadObjectList();
		  $i = 1;
		  foreach($grp_row2 as $value){
		  
                 $query = 'UPDATE '. $this->_tbl
                . ' SET ordering = '. (int) ($i)
                . ' WHERE id = '. $value->id.' AND form_id='.$form_id;
                ;
				
				
                $this->_db->setQuery( $query );
 
                if (!$this->_db->query())
                {
                        $err = $this->_db->getErrorMsg();
                        JError::raiseError( 500, $err );
                }
				$i++;
			}
	 
        return true;
} 
	 function moveFieldsDownInter( $dirn, $where='',$form_id ){
	 $db= JFactory::getDBO();
        if (!in_array( 'ordering',  array_keys($this->getProperties())))
        {
                $this->setError( get_class( $this ).' does not support ordering' );
                return false;
        }
 
        $k = $this->_tbl_key;
 
        $sql = "SELECT $this->_tbl_key, ordering, groupid FROM $this->_tbl";
 
        if ($dirn < 0)
        {
                $sql .= ' WHERE ordering = '.(int) $this->ordering;
                $sql .= ($where ? ' AND '.$where : '');
                $sql .= ' ORDER BY ordering DESC LIMIT 1';
        }
        else if ($dirn > 0)
        {
                $sql .= ' WHERE ordering > '.(int) $this->ordering;
                $sql .= ($where ? ' AND '. $where : '');
                $sql .= ' ORDER BY ordering';
        }
        else
        {
                $sql .= ' WHERE ordering = '.(int) $this->ordering;
                $sql .= ($where ? ' AND '.$where : '');
                $sql .= ' ORDER BY ordering';
        }
// echo $sql;
        $this->_db->setQuery( $sql);
 //echo $sql;
        $row = null;
        $row = $this->_db->loadObjectList();
		//echo '<pre>'; print_r($row);
		  $sql2 = "SELECT $this->_tbl_key, ordering,groupid FROM $this->_tbl WHERE ordering > ".$row[0]->ordering." AND form_id=".$form_id." AND published=1 ORDER BY ordering ASC LIMIT 1";
		  $this->_db->setQuery( $sql2);
		  $grp_row = $this->_db->loadObjectList();
		 // echo '<pre>'; print_r($grp_row);die;
	if(!empty($grp_row)){
			if($grp_row[0]->groupid == $row[0]->groupid){
							echo $query = 'UPDATE '. $this->_tbl
							. ' SET ordering = '. (int) ($grp_row[0]->ordering)
							. ' WHERE id = '. $row[0]->id.' AND form_id='.$form_id;
							;
							
						 	
							$this->_db->setQuery( $query );
			 
							if (!$this->_db->query())
							{
									$err = $this->_db->getErrorMsg();
									JError::raiseError( 500, $err );
							}  
						echo  $query1 = 'UPDATE '. $this->_tbl
							. ' SET ordering = '. (int) ($row[0]->ordering)
							. ' WHERE id = '. $grp_row[0]->id.' AND form_id='.$form_id;
							;
							
							
						 	 $this->_db->setQuery( $query1 );
			 
							if (!$this->_db->query())
							{
									$err = $this->_db->getErrorMsg();
									JError::raiseError( 500, $err );
							}  
					}else{
							 $sqlx = "SELECT $this->_tbl_key, ordering,groupid FROM $this->_tbl WHERE ordering > ".$row[0]->ordering." AND form_id=".$form_id." AND published=1 ORDER BY ordering ASC LIMIT 1";
							$this->_db->setQuery( $sqlx);
							$prev_grp = $this->_db->loadObjectList();
					//echo '<pre>'; print_r($grp_row);
					
							$query = 'UPDATE '. $this->_tbl
							. ' SET ordering = '. (int) ($prev_grp[0]->ordering).' , groupid ='.$grp_row[0]->groupid
							. ' WHERE id = '. $row[0]->id.' AND form_id='.$form_id;
							;
							
							
							$this->_db->setQuery( $query );
			 
							if (!$this->_db->query())
							{
									$err = $this->_db->getErrorMsg();
									JError::raiseError( 500, $err );
							}  
						 $query1 = 'UPDATE '. $this->_tbl
							. ' SET ordering = '. (int) ($row[0]->ordering)
							. ' WHERE id = '. $prev_grp[0]->id;
							;
							
							
						 	$this->_db->setQuery( $query1 );
			 
							if (!$this->_db->query())
							{
									$err = $this->_db->getErrorMsg();
									JError::raiseError( 500, $err );
							}  
					}
				}
			

        return true;
} 

//ends down order functions
}
?>