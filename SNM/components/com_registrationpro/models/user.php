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

class registrationproModelUser extends JModelLegacy
{
	var $_id 		= null;
	var $_data 		= null;
	var $_eventid 	= null;

	function __construct()
	{
		parent::__construct();
		
		//echo "<pre>"; print_r($_REQUEST); exit;
		/*$array 		= JRequest::getVar('rcid',  0, '', 'array');
		$eventid 	= JRequest::getVar('rdid', 0);
		$this->setId((int)$array[0],(int)$eventid);*/
		
		$id 		= JRequest::getVar('rcid',0,'','int');
		$eventid 	= JRequest::getVar('rdid',0,'','int');
		$this->setId((int)$id,(int)$eventid);
		
	}
	function setId($id, $eventid)
	{
		$this->_id	    = $id;
		$this->_eventid	= $eventid;
		$this->_data	= null;		
	}

	function &getData()
	{
		if ($this->_loadData()){
			// nothing
		}else{
		  $this->_initData();
		}
		
		return $this->_data;
	}
	
	function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = "SELECT * FROM #__registrationpro_register WHERE rid =".$this->_id;				

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
			$user = new stdClass();
			
			$user->rid 			= null;
			$user->rdid 		= null;
			$user->uid 			= null;
			$user->urname 		= null;
			$user->uregdate 	= null;
			$user->uip 			= null;
			$user->status 		= null;	
			$user->temp_params	= null;
			$user->params 		= null;
			$user->notify 		= null;
			$user->notified 	= null;
			$user->products 	= null;
			$user->firstname	= null;
			$user->lastname 	= null;
			$user->email 		= null;
			$user->active 		= null;
			$user->added_by 	= null;					
			$this->_data		= $user;
			return (boolean) $this->_data;
		}
		return true;
	}
	
		
	function store($data)
	{
		$repgrosettings = registrationproAdmin::config();
		$user			= JFactory::getUser();
		$config 		= JFactory::getConfig();

		$tzoffset		= $config->getValue('config.offset');

		$form_data 		= $this->stripslashes_deep($data['form']);								
		$params			= serialize($form_data);		
		$params			= addslashes($params); // add slashes to solve single quoutes (') problem
		
		$firstname 		= trim($data['form']['firstname'][0][0]); 
		$lastname 		= trim($data['form']['lastname'][0][0]);
		$email	 		= trim($data['form']['email'][0][0]);
		
		$query = "UPDATE #__registrationpro_register SET params='".$params."', firstname='".$firstname."', lastname = '".$lastname."', email='".$email."' WHERE rid=".$data['rid']." AND rdid=".$data['rdid'];				
			
		$this->_db->setQuery($query);
		$this->_db->query();
		
		if ( !$this->_db->query() ) {
			echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n"; exit();
		}
		
		return $this->_db->getAffectedRows();		
	}
	
	function check_duplicate_email($data)
	{	
		//echo "<pre>"; print_r($data); exit;				
		$email	 	= trim($data['form']['email'][0][0]);
						
		$query = "SELECT email FROM #__registrationpro_register WHERE email='".$email."' AND rid!=".$data['rid']." AND rdid=".$data['rdid'];			
		$this->_db->setQuery($query);
		$checkemailflag = $this->_db->loadResult();
		
		if ( !$this->_db->query() ) {
			echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n"; exit();
		}
						
		return $checkemailflag;
	}
	
	function stripslashes_deep($value)
	{
    	$value = is_array($value) ?
                array_map($this->stripslashes_deep, $value) :
                stripslashes($value);

    	return $value;
	}
	
			
	function getRegistered($rdid){		
		$this->_db->setQuery("SELECT products,status FROM #__registrationpro_register as r, #__registrationpro_transactions as t  WHERE r.rdid = '$rdid' and r.rid = t.reg_id and r.active=1 GROUP BY r.rid");

		$products = $this->_db->loadObjectList();

		$prods = array(0=>0);

		foreach($products as $product){
			$expl = explode("¶\n",$product->products); 						
			
			foreach($expl as $rw){
				if($rw!=''){
					$expl2= explode("=",$rw);
					if(isset($expl2[1])){
							if(!isset($prods[$expl2[0]]))$prods[$expl2[0]]=0;
							if(!isset($prods['status'][$product->status]))$prods['status'][$product->status]=0;
							$prods[$expl2[0]] += $expl2[1];
							$prods['status'][$product->status] += $expl2[1];
					}else{
						$prods[0]+=1;
						$prods['status'][$product->status] += 1;
					}
				}
			}			
		}		
		return $prods;
	}			
	
	// Take back up of registered orignal information
	function orignal_values_backup($user_data)
	{
		// Insert params value into temp_params field for back up of orignal registered values of user
		if($user_data->params){
		
			// filter  single user record from params array and save
			foreach($user_data as $key=>$value)
			{					
				$arrF 		= unserialize($user_data->params);		
				$arrcount 	= count($arrF['firstname']);
				$arrFields 	= array_keys($arrF);
				$arrImpode	= array();
				
				//echo "<pre>";print_r($arrFields);exit;					
				foreach($arrFields as $k=>$v)
				{													
					for($i=0;$i<$arrcount;$i++)
					{		
						if(trim($arrF['firstname'][$i][$i]) == trim($user_data->firstname) && trim($arrF['lastname'][$i][$i]) == trim($user_data->lastname) && trim($arrF['email'][$i][$i]) == trim($user_data->email)){
								
							if(is_array($arrF[$v])){
								$z=0;
								for($y=0;$y<=count($arrF[$v]);$y++){
									if($arrF[$v][$y][$i]){																						
										$userdata[$v][0][$z] = $arrF[$v][$y][$i];
										$z++;
									}										
								}																											
							}																
						}	
					}	
				}
			}
			
			//echo "<pre>";print_r($userdata);exit;				
			$finalparams = serialize($userdata);						
			// end
					
			// check if temp_params is empty
			if(empty($user_data->temp_params)){
				$query = "UPDATE #__registrationpro_register SET temp_params = '".$finalparams."' WHERE rid =".$user_data->rid;
				$this->_db->setQuery($query);
				$this->_db->query();
				
				if ( !$this->_db->query() ) {
					echo "<script> alert('".$this->_db->getErrorMsg(true)."'); window.history.go(-1); </script>";								
					exit();
				}
			}						
		}
		// end
	}
	
	function get_user_params_values($row)
	{		
		$arrF 		= unserialize($row->params);		
		$arrcount 	= count($arrF['firstname']);
		$arrFields 	= array_keys($arrF);		
		//echo "<pre>";print_r($arrF); exit;
		//echo"<pre>";print_r($arrFields); exit;
		//echo $arrcount;	
			
			foreach($arrFields as $k=>$v)
			{													
				//for($i=0;$i<$arrcount;$i++)
				//{		
					if(trim($arrF['firstname'][0][0]) == trim($row->firstname) && trim($arrF['lastname'][0][0]) == trim($row->lastname) && trim($arrF['email'][0][0]) == trim($row->email)){
										
						$colname = $v;			
						// creat the columns title			
							$columns[] = $colname;
						// end
															
						$arrImpode = array();
						if(is_array($arrF[$v])){																								
							for($y=0;$y<=count($arrF[$v]);$y++){
								if($arrF[$v][$y][$y]){
									array_push($arrImpode,$arrF[$v][$y][$y]); // push in array for show comma seperated values
								}
							}							
							
							$Fieldvalue = implode(", ",$arrImpode);										
							// add data in orignal data array
								$data[0][$colname] = $Fieldvalue;							
							// end																													
						}
					}	
				//}			
			}		
		
		//$data = $data[0];	
		//echo "<pre>";print_r($data); exit;	
		foreach($data as $key=>$value)
		{
			$datas = $data[$key];
		}
		
		return $datas;
	}
	
	function getEmails($eventid = 0, $userids = array(), $emailto = 'A')				
	{
	
		if(count($userids) > 0 && $emailto == 'S'){
			$implode_user = implode(",", $userids); 
			$query = "SELECT email FROM #__registrationpro_register where rdid=".$eventid." AND rid in ($implode_user) AND active = 1";
		}else{	
			$query = "SELECT email FROM #__registrationpro_register where rdid=".$eventid." AND active = 1";
		}
		
		
		$this->_db->setQuery($query);
		$emailids = $this->_db->loadObjectList();
			
		if ( !$this->_db->query() ) {	
			echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";	
			exit();	
		}
		
		return $emailids;
	}
	
	// update email subject and body in database		
	function update_email_template($emailtoregistersubject, $emailtoregisterbody)
	{
		$query =  "UPDATE #__registrationpro_config SET setting_value = ".$this->_db->quote( $this->_db->escape($emailtoregistersubject), false )." WHERE setting_name = 'emailtoregistersubject'";
		$this->_db->setQuery($query);
		$this->_db->query();
		
		$query = "UPDATE #__registrationpro_config SET setting_value = ".$this->_db->quote( $this->_db->escape($emailtoregisterbody), false )." WHERE setting_name = 'emailtoregisterbody'";
		$this->_db->setQuery($query);
		$this->_db->query();
	}
	
	// get transaction details of user
	function getTransaction_details($rid = 0)
	{
		$query = "SELECT * FROM #__registrationpro_register r WHERE r.rid = $rid LIMIT 1";
		$this->_db->setQuery($query);
		$details = $this->_db->loadObjectList();
		$details = $details[0];		
		//echo "<pre>";print_r($details); exit;	
		
		$query = "SELECT t.*, edt.event_discount_amount, edt.event_discount_type FROM #__registrationpro_transactions t"
				. "\nLEFT JOIN #__registrationpro_event_discount_transactions AS edt ON t.id = edt.trans_id"
				. "\n WHERE t.reg_id = $rid";
		$this->_db->setQuery($query);
		$details->transaction = $this->_db->loadObjectList();
		
		//echo "<pre>";print_r($details); exit;		
		
		return $details;
	}
}

?>