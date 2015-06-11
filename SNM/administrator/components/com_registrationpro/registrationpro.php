<?php
/**
 * @version		v.3.2 registrationpro $
 * @package		registrationpro
 * @copyright	Copyright © 2009 - All rights reserved.
 * @license  GNU/GPL
 * @author		JoomlaShowroom.com
 * @author mail	info@JoomlaShowroom.com
 * @website		www.JoomlaShowroom.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
if(!defined('DS')) define("DS","/");
ob_clean();
global $regpro_mail;

$regpro_mail = new JMail;
if (!JFactory::getUser()->authorise('core.manage', 'com_registrationpro')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);

$jlang =JFactory::getLanguage();
$jlang->load('com_registrationpro', JPATH_ADMINISTRATOR, 'en-GB', true);
$jlang->load('com_registrationpro', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
$jlang->load('com_registrationpro', JPATH_ADMINISTRATOR, null, true);

$install = isset($_REQUEST['install']) ? (int)$_REQUEST['install'] : 0;

if ((int)$install) {
	full_installation();
} else {
	$version = "";
	$version = substr(JVERSION,0,3);

	if($version == "1.5"){
		echo "<font color='#FF0000' size='+1'>This version of Event Registration Pro is not compatible with Joomla 1.5. Please visit www.JoomlaShowroom.com for information regarding the correct version for Joomla 1.5.</font>";
	}else{
		global $option, $mainframe;

		$option 	= "com_registrationpro";
		$mainframe 	= JFactory::getApplication();

		$document =JFactory::getDocument();
		$componentPath = 'components/com_registrationpro/';
		$document->addStyleSheet($componentPath . 'assets/css/ace-fonts.css');
		$document->addStyleSheet($componentPath . 'assets/css/ace-ie.min.css');
		$document->addStyleSheet($componentPath . 'assets/css/ace.min.css');
		$document->addStyleSheet($componentPath . 'assets/css/font-awesome.min.css');
		//$document->addStyleSheet($componentPath . 'assets/css/general.css');

		$lay = strtolower(JRequest::getWord('layout'));
		$tsk = strtolower(JRequest::getWord('task'));
		
		//echo $tsk; exit;
		
		$mnu = false;
		if (($lay !== 'transaction')&&
			($lay !== 'selectusers')&&
			($tsk !== 'print_report')&&
			($tsk !== 'getfieldvalues')&&
			($tsk !== 'event_report')&&
			($tsk !== 'excel_report')&&
			($tsk !== 'generate_badge')&&
			($tsk !== 'add_session')&&
			($tsk !== 'edit_session')&&
			($tsk !== 'remove_session')&&
			($tsk !== 'add_groupdiscount')&&
			($tsk !== 'edit_groupdiscount')&&
			($tsk !== 'edit_earlydiscount')&&
			($tsk !== 'remove_earlydiscount')&&
			($tsk !== 'add_earlydiscount')&&
			($tsk !== 'remove_groupdiscount')&&
			($tsk !== 'print_report')&&
			($tsk !== 'currencysymbol')&&
			($tsk !== 'orderuppayments')&&
			($tsk !== 'orderdownpayments')&&
			($tsk !== 'orderupsessions')&&
			($tsk !== 'orderdownsessions')&&
			($tsk !== 'add_ticket')&&
			($tsk !== 'edit_ticket')&&
			($tsk !== 'add_ticket_add')&&
			($tsk !== 'edit_ticket_add')&&
			($tsk !== 'remove_ticket_add')&&
			($tsk !== 'remove_ticket')) { ?>

		<script type="text/javascript">
		/* function hideDiv(matchClass) {
			var elems = document.getElementsByTagName('div'), i;
			for (i in elems) {
				if((' ' + elems[i].className + ' ').indexOf(' ' + matchClass + ' ')	> -1) {
					elems[i].innerHTML = '';
					elems[i].hide();
				}
			}
		}

		function SetAttr(matchClass, attr, value) {
			var elems = document.getElementsByTagName('*'), i;
			for (i in elems) {
				if((' ' + elems[i].className + ' ').indexOf(' ' + matchClass + ' ')	> -1) {
					elems[i].style.width = "100%";
				}
			}
		}

		hideDiv('span2');
		SetAttr('span2', 'style', 'margin:0px;padding:0px;visibility:collapse;display:none;')
		SetAttr('span10', 'style', 'margin:0px;padding:0px;') */
		</script>

		<?php
		$mnu = true;
		//echo "<table style=\"width:100%;border:none;margin:0px;padding:0px;\"><tr><td valign=top style=\"width:auto:border:none;margin:0px;padding:0px;\">\n";
		require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/sidebar.php');
		//echo "</td><td valign=top style=\"width:100%;border:none;margin:0px;padding:10px;padding-left:15px;padding-right:0px;\">\n";
		}

		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'registrationpro_constant.php'); 			// add constant file
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'admin.class.php'); 			// add admin class file
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'plugins.regpro.class.php'); 	// add plugins class file
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'form.class.php'); 				// add forms and fields class file
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'registration.class.php'); 		// save event registration class file
		$debug = true;

		// Require the base controller
		require_once (JPATH_COMPONENT.DS.'controller.php');
		require_once( JPATH_COMPONENT.DS.'helper.php' );

		// Require specific controller if requested
		if($controller = JRequest::getWord('controller')) {
			$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
			if (file_exists($path)) {
				require_once $path;
			} else $controller = '';
		}

		// Create the controller
		$classname  = 'registrationproController'.$controller;
		$controller = new $classname();

		// Perform the Request task
		$controller->execute( JRequest::getVar( 'task' ) );
		$controller->redirect();

		//if ($mnu) echo "</td></tr></table>";
	}
}

function full_installation() {
	require_once (JPATH_COMPONENT.DS.'controllers'.DS.'default.php');

	$lang =JFactory::getLanguage();
	$lang->load( 'com_registrationpro', JPATH_ROOT . DS . 'administrator' );

	$controller = new jssController();
	$task = JRequest::getVar('task');
	if ($task) {
		$controller->execute($task);
		$controller->redirect();
	} else {
		JHTML::_('behavior.tooltip');

		$basePath = JPATH_ROOT . DS . 'administrator' . DS;
		$componentPath = 'components/com_registrationpro/';
		$success_link = JRoute::_('index.php?option=com_registrationpro');

		$document =JFactory::getDocument();
		$rpconstants  = 'var REGPRO_AJAX_FAILURE = \'' . JText::_('REGPRO_INSTALLER_AJAX_FAILURE') . '\';' . "\n";
		$rpconstants .= 'var REGPRO_JSON_FAILURE = \'' . JText::_('REGPRO_INSTALLER_JSON_FAILURE') . '\';' . "\n";
		$rpconstants .= 'var REGPRO_AJAX_ERRORS = \'' . JText::_('REGPRO_INSTALLER_AJAX_ERRORS') . '\';';
		$rpconstants .= 'var REGPRO_SUCCESS_LINK = \'' . $success_link . '\';';
		$document->addScriptDeclaration($rpconstants);

		$document->addScript($componentPath . 'install/install.js');
		$document->addStyleSheet($componentPath . 'install/install.css');

		JRequest::setVar('hidemainmenu', 1);
		JToolBarHelper::title(JText::_('REGPRO_INSTALLER_HEADER'), 'registrationpro.png');

		$menuArr = array(
			1 => array(
				'title' => JText::_('REGPRO_INSTALLER_STEP1_TITLE'),
				'description' => JText::_('REGPRO_INSTALLER_STEP1_DESC'),
			),
			2 => array(
				'title' => JText::_('REGPRO_INSTALLER_STEP2_TITLE'),
				'description' => JText::_('REGPRO_INSTALLER_STEP2_DESC'),
			),
			3 => array(
				'title' => JText::_('REGPRO_INSTALLER_STEP3_TITLE'),
				'description' => JText::_('REGPRO_INSTALLER_STEP3_DESC'),
			),
			4 => array(
				'title' => JText::_('REGPRO_INSTALLER_STEP4_TITLE'),
				'description' => JText::_('REGPRO_INSTALLER_STEP4_DESC'),
			),
			5 => array(
				'title' => JText::_('REGPRO_INSTALLER_STEP7_TITLE'),
				'description' => JText::_('REGPRO_INSTALLER_STEP7_DESC'),
			),
			6 => array(
				'title' => JText::_('REGPRO_INSTALLER_STEP8_TITLE'),
				'description' => JText::_('REGPRO_INSTALLER_STEP8_DESC'),
			),
			7 => array(
				'title' => JText::_('REGPRO_INSTALLER_STEP9_TITLE'),
				'description' => JText::sprintf('REGPRO_INSTALLER_STEP9_DESC', $success_link),
			),
		);

		$html = '
		<div class="regpro_install_steps">
		';

		foreach ($menuArr as $step_num => $step) {
			$html .= '
			<div id="install_step_' . $step_num . '" ' . ($step_num == 1 ? 'class="regpro_install_steps_active"' : '') . '>' . $step_num . '. ' . $step['title'] . '</div>
			<div id="install_step_desc_' . $step_num . '" style="display: none;">' . $step['description'] . '</div>
			';
		}

		//?stat_me=1&stat_date=TEST_DATETIME&stat_erpv=TEST_REGPROVERSION&stat_host=TEST_HOST&stat_phpv=TEST_PHPVERSION&stat_joom=TEST_JOOMLAVER&stat_serv=TEST_SERVERINFO&stat_else=TEST_ELSE
		//$query = "SELECT `version_id` FROM ".$db_dbprefix."schemas WHERE `extension_id`=700";
		//$result = mysql_query($query);
		//$row = mysql_fetch_row($result);
		//$joomla_version  = $row[0];
		//echo "stat_date = " . date('Y-m-d H:i:s') . "<br />";	
		//echo "stat_erpv = " . $regpro_version . "<br />";	
		//echo "stat_host = " . $_SERVER['HTTP_HOST'] . "<br />";
		//echo "stat_phpv = " . phpversion() . "<br />";	
		//echo "stat_joom = " . $joomla_version . "<br />";
		//echo "stat_serv = " . json_encode($_SERVER) . "<br />";
		//echo "stat_else = " . $stat_else . "<br />";	
		////$arr = json_decode($str, true);
		////echo "<pre>"; print_r($arr); echo "</pre>";
		
		$html .= "<script>alert('REQUEST');</script>\n";
		
		$html .= '
		</div>
		<div class="regpro_install_info">
			<fieldset>
				<legend id="step_label">1. ' . $menuArr[1]['title'] . '</legend>
				<div id="error_holder">
				<dl id="system-message">
					<dd class="error message fade">
						<ul id="regpro_install_error">
						</ul>
					</dd>
				</dl>
				</div>
				<div class="unfloat"></div>
				<div id="regpro_install_title">' . $menuArr[1]['description'] . '</div>
				<div id="regpro_install_loading">
					<img src="components/com_registrationpro/assets/images/icon_ajax.gif"/>
				</div>
				<div class="unfloat"></div>
				<div id="regpro_install_feedback"></div>
			</fieldset>
		</div>
		<script>
		initInstall();
		</script>
		';
		echo $html;
	}
}
?>