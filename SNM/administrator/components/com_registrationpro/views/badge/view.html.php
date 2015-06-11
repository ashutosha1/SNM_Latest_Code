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
jimport( 'joomla.application.component.view' );

class registrationproViewBadge extends JViewLegacy
{
	function display($tpl = null) {
		global $mainframe, $option;

		$option = "com_registrationpro";

		jimport('joomla.html.pane');
		JHtmlBehavior::framework();
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.calendar');

		$db = JFactory::getDBO();
		$editor = JFactory::getEditor();
		$user = JFactory::getUser();
		$document = JFactory::getDocument();
		$registrationproAdmin = new  registrationproAdmin; $regpro_config = $registrationproAdmin->config();
		$regpro_config['joomlabase'] = JPATH_SITE;

		// get filter vlaues
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'.search_filter_order', 'filter_order', '', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'.search_filter_order_Dir', 'filter_order_Dir', '', 'word' );
		$search 			= $mainframe->getUserStateFromRequest( $option.'.search', 'search', '', 'string' );
		$search 			= $db->escape( trim(JString::strtolower( $search ) ) );
		$event	 			= $mainframe->getUserStateFromRequest( $option.'.search_event', 'event', '0', 'int' );
		$start_date 		= $mainframe->getUserStateFromRequest( $option.'.search_start_date', 'start_date', '', 'string' );
		$end_date 			= $mainframe->getUserStateFromRequest( $option.'.search_end_date', 'end_date', '', 'string' );
		$firstname 			= $mainframe->getUserStateFromRequest( $option.'.search_firstname', 'firstname', '', 'string' );
		$firstname 			= $db->escape( trim(JString::strtolower( $firstname ) ) );
		$lastname 			= $mainframe->getUserStateFromRequest( $option.'.search_lastname', 'lastname', '', 'string' );
		$lastname 			= $db->escape( trim(JString::strtolower( $lastname ) ) );
		$email	 			= $mainframe->getUserStateFromRequest( $option.'.search_email', 'email', '', 'string' );
		$email 				= $db->escape( trim(JString::strtolower( $email ) ) );

		$data 				= array ();
		$data['search'] 	= $search;
		$data['firstname']	= $firstname;
		$data['lastname'] 	= $lastname;
		$data['email'] 		= $email;
		$data['event'] 		= $event;
		$data['start_date'] = $start_date;
		$data['end_date'] 	= $end_date;

		$reset = JRequest::getVar('reset','POST');

		if($reset == 1){
			$location = $mainframe->getUserStateFromRequest( $option.'.search_location', 'l', '', 'array' );
			$category = $mainframe->getUserStateFromRequest( $option.'.search_category','c', '', 'array' );
		}else{
			$location = $mainframe->getUserStateFromRequest( $option.'.search_location', 'location', '', 'array' );
			$category = $mainframe->getUserStateFromRequest( $option.'.search_category', 'category', '', 'array' );
		}

		//get vars
		$task = JRequest::getVar( 'task' );

		//add css and js to document
		$registrationproHelper = new registrationproHelper;
		$registrationproHelper->add_regpro_scripts();

		$layout = JRequest::getCmd('layout');

		if($layout == 'users'){
			$model = $this->getModel('badge');
			$model->setState('limit', 0);
			$model->setState('limitstart', 0);
			$rows = $this->get('Data');
			$this->users($rows); // show print report window
		}else{
			$model = $this->getModel('badge');
			$model->setState('limit', 0);
			$model->setState('limitstart', 0);
			$rows = $this->get('Data');

			$Lists = array();
			$Lists['events'] = JHTML::_('grid.state', $filter_state );

			// Locations list
			$events = array();
			$events[] = JHTML::_('select.option', 0, JText::_('ADMIN_BADGES_EVENT_SELECT_ONE'));
			$all_events = $this->get('Events');
			$events = array_merge( $events, $all_events);
			$Lists['events'] = JHTML::_('select.genericlist', $events, 'event', 'class="inputbox" style="width:500px;" onchange="return event_change();"','value', 'text', $event);

			// sortby list
			$sortby = array();
			$sortby[] = JHTML::_('select.option',  'firstname','First Name');
			$sortby[] = JHTML::_('select.option',  'lastname','Last Name');
			$sortby[] = JHTML::_('select.option',  'email','Email Address');
			$Lists['sortby']=  JHTML::_('select.genericlist', $sortby, 'sortby', 'class="inputbox"','value', 'text', $sortby );

			// badgestyles Lists
			$badgestyles 	= array();
			$badgestyles[] 		= JHTML::_('select.option',  '86x55','Avery L7418 - Name Badges (86 x 55 mm)');
			$Lists['badgestyles'] = JHTML::_('select.genericlist', $badgestyles, 'badgestyles', 'class="inputbox"','value', 'text', $badgestyle );

			// Get form fields
			$FormFelds = $model->getFormFields($event);

			// fields Lists
			$fields = array();
			$fields[] = JHTML::_('select.option',0, JText::_('ADMIN_BADGES_FIELD_SELECT_ONE'));
			$fields[] = JHTML::_('select.option',  'event','Event Name');
			$fields[] = JHTML::_('select.option',  'event_start','Event Date');
			$fields[] = JHTML::_('select.option',  'location','Event Location');
			$fields[] = JHTML::_('select.option',  'fullname','Firstname Lastname');
			$fields[] = JHTML::_('select.option',  'firstname','First Name');
			$fields[] = JHTML::_('select.option',  'lastname','Last Name');
			$fields[] = JHTML::_('select.option',  'email','Email');
			//echo '<pre>';print_r($FormFelds);echo '</pre>';die;
			if(is_array($FormFelds) && count($FormFelds)>0) {
				foreach($FormFelds as $fkey => $fvalue)
				{
					/*		OLD CODE
					if(strtolower(str_replace(" ","",$fvalue[0])) != "firstname" && strtolower(str_replace(" ","",$fvalue[0])) != "lastname" && strtolower(str_replace(" ","",$fvalue[0])) != "email") {
						$fields[] = JHTML::_('select.option', $fvalue[0], $fvalue[0]);
					}
					*/
					
					/* MODIFIED CODE ADDED BY SUSHIL ON 13-02-2015 TO FIX BADGE GENERATION */
					if(strtolower(str_replace(" ","",$fvalue[1])) != "firstname" && strtolower(str_replace(" ","",$fvalue[1])) != "lastname" && strtolower(str_replace(" ","",$fvalue[1])) != "email") {
						$fields[] = JHTML::_('select.option', $fvalue[1], $fvalue[1]);
					}
				}
			}

			$Lists['fields'] = JHTML::_('select.genericlist', $fields, 'fields[]', 'class="inputbox"','value', 'text', $field );

			// font name Lists
			$fonts = array();
			$fonts[] = JHTML::_('select.option',  'Courier','Courier');
			$fonts[] = JHTML::_('select.option',  'Helvetica','Helvetica');
			$Lists['fonts'] = JHTML::_('select.genericlist', $fonts, 'fonts[]', 'class="inputbox"','value', 'text', $font );

			// font size Lists
			$fontsizes = array();
			$fontsizes[] = JHTML::_('select.option',  '8pt','8pt');
			$fontsizes[] = JHTML::_('select.option',  '9pt','9pt');
			$fontsizes[] = JHTML::_('select.option',  '10pt','10pt');
			$fontsizes[] = JHTML::_('select.option',  '11pt','11pt');
			$fontsizes[] = JHTML::_('select.option',  '12pt','12pt');
			$fontsizes[] = JHTML::_('select.option',  '14pt','14pt');
			$fontsizes[] = JHTML::_('select.option',  '16pt','16pt');
			$fontsizes[] = JHTML::_('select.option',  '18pt','18pt');
			$fontsizes[] = JHTML::_('select.option',  '20pt','20pt');
			$fontsizes[] = JHTML::_('select.option',  '24pt','24pt');
			$fontsizes[] = JHTML::_('select.option',  '28pt','28pt');
			$fontsizes[] = JHTML::_('select.option',  '32pt','32pt');
			$fontsizes[] = JHTML::_('select.option',  '36pt','36pt');
			$fontsizes[] = JHTML::_('select.option',  '40pt','40pt');
			$fontsizes[] = JHTML::_('select.option',  '45pt','45pt');
			$fontsizes[] = JHTML::_('select.option',  '50pt','50pt');
			$Lists['fontsizes'] = JHTML::_('select.genericlist', $fontsizes, 'fontsizes[]', 'class="inputbox"','value', 'text', $fontsize );

			//assign data to template
			$this->assignRef('data'      	, $data);
			$this->assignRef('Lists'      	, $Lists);
			$this->assignRef('rows'      	, $rows);
			$this->assignRef('pageNav' 		, $pageNav);
			$this->assignRef('user'			, $user);
			$this->assignRef('template'		, $template);
			$this->assignRef('editor'      	, $editor);
			$this->assignRef('regpro_config' , $regpro_config);
			$this->assignRef('task' 		, $task);

			parent::display($tpl);
		}
	}

	function users($rows) {
		global $mainframe;
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config	= $registrationproAdmin->config();
		$this->assignRef('regpro_config' , $regpro_config);
		$this->assignRef('rows' , $rows);
		parent::display($tpl);
		exit;
	}
}
?>