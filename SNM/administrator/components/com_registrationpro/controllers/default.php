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
 
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class jssController extends JControllerLegacy {	
	function __constuct($config = array()) {
		parent::__construct($config);
	}
	
	function debug() {
		$config =JFactory::getConfig();
		$logfile = $config->get('config.tmp_path').DS.'converter.log';
		
		$content = file_get_contents($logfile);
		echo '<pre>';
		echo $content;
		echo '</pre>';
		exit;
	}
	
	function intro() {
		$document =JFactory::getDocument();
		$viewType = $document->getType();
		$model = $this->getModel('Settings');
		$view =$this->getView('intro', $viewType);
		$view->setModel($model, true);
		$view->setLayout('default');
		$view->display();
	}
	
	function fields() {
		$document =JFactory::getDocument();
		$viewType = $document->getType();
		$model = $this->getModel('Fields');
		$view =$this->getView('fields', $viewType);
		$view->setModel($model, true);
		$view->setLayout('default');
		$view->display();
	}
	
	function fieldsform() {
		$mode = JRequest::getVar('mode', '');
		$document =JFactory::getDocument();
		$viewType = $document->getType();
		$model = $this->getModel('Fields');
		
		$view =$this->getView('fields', $viewType);
		$view->setModel($model, true);
		
		if ($mode == 'paramsonly') {
			JRequest::setVar('tmpl', 'component');
			$view->setLayout('formview2');
		} else {
			$model->fieldSave();
			$view->setLayout('formview');
		}
		$view->display();
	}
	
	function fieldsformnew() {
		$this->fieldsform();
	}
	
	function fieldopts() {
		$mode = JRequest::getVar('mode', '');
		if ($mode == 'ajax') {
			JRequest::setVar('tmpl', 'component');
		}
		
		$document =JFactory::getDocument();
		$viewType = $document->getType();
		$model = $this->getModel('Fields');
		
		$view =$this->getView('fieldopts', $viewType);
		$view->setModel($model, true);
		$view->setLayout('default');
		$view->display();
	}
	
	function fieldoptsform() {
		$mode = JRequest::getVar('mode', '');
		if ($mode == 'ajax') {
			JRequest::setVar('tmpl', 'component');
		}
		
		$document =JFactory::getDocument();
		$viewType = $document->getType();
		$model = $this->getModel('Fields');
		
		$model->fieldoptsSave();
		
		$view =$this->getView('fieldopts', $viewType);
		$view->setModel($model, true);
		$view->setLayout('formview');
		$view->display();
	}
	
	function fieldoptsformnew() {
		$this->fieldoptsform();
	}
	
	function fieldoptsdelete() {
		$model = $this->getModel('Fields');
		$model->fieldoptsDelete();
	}
	
	function fieldsPublishYes() {
		$model = $this->getModel('Fields');
		$model->fldPublish(1);
	}
	
	function fieldsPublishNo() {
		$model = $this->getModel('Fields');
		$model->fldPublish(0);
	}
	
	function fieldsRequiredYes() {
		$model = $this->getModel('Fields');
		$model->fldRequired(1);
	}
	
	function fieldsRequiredNo() {
		$model = $this->getModel('Fields');
		$model->fldRequired(0);
	}
	
	function fieldsAtregYes() {
		$model = $this->getModel('Fields');
		$model->fldAtreg(1);
	}
	
	function fieldsAtregNo() {
		$model = $this->getModel('Fields');
		$model->fldAtreg(0);
	}
	
	function deletefield() {
		$model = $this->getModel('Fields');
		$model->fldDelete();
	}
	
	function fieldgroups() {
		$document =JFactory::getDocument();
		$viewType = $document->getType();
		$model = $this->getModel('FieldGroups');
		$view =$this->getView('fieldgroups', $viewType);
		$view->setModel($model, true);
		$view->setLayout('default');
		$view->display();
	}
	
	function fieldgroupsnew() {
		$this->fieldgroupsform();
	}
	function fieldgroupsform() {
		$document =JFactory::getDocument();
		$viewType = $document->getType();
		$model = $this->getModel('FieldGroups');
		
		$view =$this->getView('fieldgroups', $viewType);
		$view->setModel($model, true);
		$view->setLayout('formview');
		
		$view->display();
	}
	
	function fieldgroupsave() {
		$document =JFactory::getDocument();
		$viewType = $document->getType();
		$model = $this->getModel('FieldGroups');
		$view =$this->getView('fieldgroups', $viewType);
		$model->fieldGroupSave();
		$view->setModel($model, true);
		$view->setLayout('default');
		$view->display();
	}
	
	function deletefieldgroup() {
		$model = $this->getModel('FieldGroups');
		$model->fieldGroupDelete();
	}
	
	function fieldgroupscancel() {
		JRequest::setVar('id',0);
		$this->fieldgroups();
	}
	
	function settings() {
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.modal');
		
		$document =JFactory::getDocument();
		$viewType = $document->getType();
		$model = $this->getModel('Settings');
		
		$model->configSave();
		
		$view =$this->getView('settings', $viewType);
		$view->setModel($model, true);
		$view->setLayout('default');
		$view->display();
	}

	function blacklist() {
		$document =JFactory::getDocument();
		$viewType = $document->getType();
		$model = $this->getModel('BlackList');		
		$view =$this->getView('blacklist', $viewType);
		$view->setModel($model, true);
		$view->setLayout('default');		
		$view->display();
	}

	function blacklistsave() {
		$document =JFactory::getDocument();
		$viewType = $document->getType();
		$model = $this->getModel('BlackList');
		$view =$this->getView('blacklist', $viewType);
		$model->blackListSave();
		$view->setModel($model, true);
		$view->setLayout('default');
		$view->display();
	}
	
	function install() {	
		$step = (int)$_REQUEST['step'];
		require_once (JPATH_COMPONENT . DS . 'install' . DS . 'install.helper.php');
		$helper = new RegistrationproInstallHelper();
		$result = $helper->install($step);
		
		JRequest::setVar('tmpl', 'component');
		JRequest::setVar('format', 'raw');
		$document = JFactory::getDocument();
		$document->setMimeEncoding('application/json');	
		echo $result;
	}
	
	function display($cachable = false, $urlparams = false) {
		parent::display();		
	}
}

?>
