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

class RegistrationproInstallHelper {
	var $dbo;
	var $backendPath;
	var $frontendPath;
	var $tmpPath;
	var $currentMessages;
	var $errorList;
	var $errorResult;
	var $messageResult;
	var $hasErrors;
	var $status;
	var $joomlaAddons;
	var $archiveExt;
	var $compareExt;
	var $excludedModules;

	function test($var) {
		return 'TEST_FUNC_'.$var;
	}
	
	function RegistrationproInstallHelper () {
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.archive');
		$this->dbo = JFactory::getDBO();
		$this->dbo->debug(0);
		$this->backendPath  = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_registrationpro' . DS;
		$this->frontendPath = JPATH_ROOT . DS . 'components' . DS . 'com_registrationpro' . DS;
		$this->tmpPath  	= JPATH_ROOT . DS . 'tmp' . DS;
		$this->errorList = array();
		$this->currentMessages = array();
		$this->errorResult = '';
		$this->messageResult = '';
		$this->hasErrors = 0;
		$this->status = 1;
		//$this->joomlaAddons = array('plugins' => JText::_('REGPRO_INSTALLER_PLUGINS'), 'modules' => JText::_('REGPRO_INSTALLER_MODULES'));
		$this->joomlaAddons = array('plugins' => JText::_('REGPRO_INSTALLER_PLUGINS'));
		$this->archiveExt = '.zip';
		$this->compareExt = 'zip';
		$this->excludedModules = array('Registration Pro Event By Category','Registration Pro Latest Events','Registration Pro Mini Calendar','Registration Pro Event Map','Registration Pro Quick Icon');
	}

	function _prepareJsonResponse($status, $error, $message) {
		$jsonDATA = array(
			'status' => (int)$status,
			'error' => $error,
			'message' => $message,
			'done' => ' ... ' . JText::_('REGPRO_INSTALLER_DONE'),
		);
		return json_encode($jsonDATA);
	}

	function _testErrors() {
		$jerrors = JError::getErrors();
		if (is_array($jerrors) && count($jerrors)) {
			foreach($jerrors as $error) $this->errorList[] = $error->getMessage();
		}
		if (count($this->errorList)) {
			$this->hasErrors = 1;
		} else $this->hasErrors = 0;
	}

	function _setStatus() {
		if ($this->hasErrors) {
			$this->status = 0;
			$this->errorResult = $this->errorList;
		} else {
			$this->status = 1;
			$this->errorResult = '';
		}
	}

	function setError($error) {
		$this->errorList[] = $error;
	}

	function setMessage($msg) {
		$this->currentMessages[] = $msg;
	}

	function _buildMessages($step, $sep, $prefix = '') {
		if (is_array($this->currentMessages) && count($this->currentMessages)) {
			$this->messageResult = $prefix . implode($sep, $this->currentMessages);
		}
	}

	function _getJoomlaAddonName($dir) {
		$name = '';
		$files = JFolder::files($dir, '\.xml$', 1, true);
		if (count($files) > 0) {
			foreach ($files as $file) {
				$name = '';
				$xml = JFactory::getXML($file);
				$name = $xml->name;
				$xml = null;
				if (!$name) continue;
				return $name;
			}
		}
		return $name;
	}

	function _activateJoomlaAddon($type, $name) {
		if($type == 'module' && $name == "Registration Pro Quick Icon") {
			$publish = '1';
			$this->dbo->setQuery("UPDATE #__modules SET " . $this->dbo->nameQuote('published') . " = '" . $publish . "', position = 'icon' WHERE " . $this->dbo->nameQuote('title') . " = " . $this->dbo->Quote($name));
			$this->dbo->query();

			$this->dbo->setQuery("SELECT id FROM #__modules WHERE " . $this->dbo->nameQuote('title') . " = " . $this->dbo->Quote($name));
			$modid = $this->dbo->loadResult();

			$query = "INSERT INTO #__modules_menu SET " . $this->dbo->nameQuote('moduleid') . " = " . $modid . ", menuid = 0 ";
			$this->dbo->setQuery($query);
			$this->dbo->query();
		}
	}

	function extractArchive($source, $destination) {
		$destination = JPath::clean( $destination );
		$source = JPath::clean( $source );
		$result = JArchive::extract( $source , $destination );
		return $result;
	}

	function extractRecursive($source, $destination, $extension) {
		if (is_dir($source)) {
			if ($dh = opendir($source)) {
				while (($file = readdir($dh)) !== false) {
					if ($file != '.' && $file != '..') {
						$pathInfo = pathinfo($source . $file);
						if ($pathInfo['extension'] == $extension) {
							if(!$this->extractArchive($source . $file, $destination)) {
								$this->setError(JText::sprintf('REGPRO_INSTALLER_ERR_EXTRACT',$source . $file));
							} else {
								$upgradeScript = $destination . $pathInfo['filename'] .'.upgrade.php';
								if (is_file($upgradeScript)) {
									ob_start();
									include($upgradeScript);
									ob_end_clean();
									unlink($upgradeScript);
								}
								$this->setMessage($pathInfo['filename']);
							}
						}
					}
				}
				closedir($dh);
			} else $this->setError(JText::sprintf('REGPRO_INSTALLER_ERR_OPENDIR',$source));
		} else $this->setError(JText::sprintf('REGPRO_INSTALLER_ERR_NODIR',$source));
	}

	function installRecursive($source, &$installer, $extension) {
		if (is_dir($source)) {
			if ($dh = opendir($source)) {
				while (($file = readdir($dh)) !== false) {
					if ($file != '.' && $file != '..') {
						$pathInfo = pathinfo($source . $file);
						if (isset($pathInfo['extension']) && $pathInfo['extension'] == $extension) {

							$package = JInstallerHelper::unpack($source . $file);
							if(!$installer->install($package['dir'])) {
								$this->setError(JText::sprintf('REGPRO_INSTALLER_ERR_INSTALL',$source . $file));
							} else {
								$name = $this->_getJoomlaAddonName($package['extractdir']);
								$this->setMessage($name);
								if (!is_file($package['packagefile'])) $package['packagefile'] = $source . $package['packagefile'];
								JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
							}
						}
					}
				}
				closedir($dh);
			} else { $this->setError(JText::sprintf('REGPRO_INSTALLER_ERR_OPENDIR',$source));}
		} else { $this->setError(JText::sprintf('REGPRO_INSTALLER_ERR_NODIR',$source)); }
	}
	
	
	
	
 

	function installBackend() {
		$zip = $this->backendPath . 'backend' . $this->archiveExt;
		$destination = $this->backendPath;
	
		if(!$this->extractArchive($zip,$destination ))
		{
			$this->setError(JText::_('REGPRO_INSTALLER_ERR_BACKEND'));
		} 
	
		$dir = '../images/regpro/system';
		if (!JFolder::exists($dir))
		{
			if(!mkdir($dir, 0755, true))
			{ 
				$fp = fopen('errorlog.txt', 'w');
				fwrite($fp, 'Error in making system directory');
				fclose($fp);  
			}
        }
		
		$dir = '../images/regpro/events';
		if (!JFolder::exists($dir))
		{
			if(!mkdir($dir, 0755, true))
			{ 
				$fp = fopen('errorlog.txt', 'w');
				fwrite($fp, 'Error in making events directory');
				fclose($fp);  
			}
        }
		$dir = '../images/regpro/system/noimage_200x200.jpg';
		if(!file_exists($dir))
		{
			if (!copy('components/com_registrationpro/assets/images/noimage_200x200.jpg', $dir)) 
			{ 
				$fp = fopen('errorlog.txt', 'w');
				fwrite($fp, 'Error in copy the image');
				fclose($fp); 
			}
		}
		
		$dir = '../images/regpro/system/nopdfimage_720x240.jpg';
		if(!file_exists($dir))
		{
			if (!copy('components/com_registrationpro/assets/images/nopdfimage_720x240.jpg', $dir)) 
			{ 
				$fp = fopen('errorlog.txt', 'w');
				fwrite($fp, 'Error in copy the image');
				fclose($fp); 
			}
		}
		
	}
	

	function initDatabase() {
		require_once (JPATH_COMPONENT . DS . 'install.registrationpro.php');
		$installer = new RegproInstaller();
		$installer->runSQLFile('install.mysql.utf8.sql');
		if (is_array($installer->logList) && count($installer->logList)) {
			$this->setError(JText::_('REGPRO_INSTALLER_ERR_DB'));
			foreach ($installer->logList as $logs) { $this->setError(JText::_($logs->message)); }
		}
		$installer->run();
	}

	function installFrontend() {
		$zip = $this->frontendPath . 'frontend' . $this->archiveExt;
		$destination = $this->frontendPath;
		if(!$this->extractArchive($zip,$destination )) $this->setError(JText::_('REGPRO_INSTALLER_ERR_FRONTEND'));
	}

	function installJoomlaAddons() {
		jimport('joomla.installer.installer');
		jimport('joomla.installer.helper');
		$installer = JInstaller::getInstance();

		foreach($this->joomlaAddons as $addon => $addon_name) {
			$zip = $this->frontendPath . 'regpro_' . $addon . $this->archiveExt;
			$destination = $this->tmpPath . 'regpro_joomla/' . $addon . '/';
			if(!$this->extractArchive($zip,$destination )) {
				$this->setError(JText::sprintf('REGPRO_INSTALLER_ERR_EXTRACT',$addon_name));
			} else {
				$this->setMessage(JText::sprintf('REGPRO_INSTALLER_INSTALLED',$addon_name));
				$this->installRecursive($destination, $installer, $this->compareExt);
			}
		}
	}

	function installLangs() {
		$langArr = array(
			array(
				'source' => $this->backendPath . 'languages-admin' . DS . 'en-GB' . DS,
				'dest' => JPATH_ADMINISTRATOR . DS . 'language',
			),
			array(
				'source' => $this->frontendPath . 'languages-front' . DS . 'en-GB' . DS,
				'dest' => JPATH_ROOT . DS . 'language',
			),
		);
		foreach ($langArr as $lang) {
			$source = $lang['source'];
			if (is_dir($source)) {
				if ($dh = opendir($source)) {
					while (($file = readdir($dh)) !== false) {
						if ($file != '.' && $file != '..') {
							$pathInfo = pathinfo($source . $file);
							if ($pathInfo['extension'] == 'ini') {
								$locale = substr($file,0,strpos($file,'.'));
								if (!JFile::copy($source . $file, $lang['dest'] . DS . $locale . DS . $file)) {
									$this->setError(JText::sprintf('REGPRO_INSTALLER_ERR_LANGFILE',$source . $file, $lang['dest'] . DS . $locale . DS . $file));
								}
							}
						}
					}
					closedir($dh);
				} else $this->setError(JText::sprintf('REGPRO_INSTALLER_ERR_OPENDIR',$source));
			} else $this->setError(JText::sprintf('REGPRO_INSTALLER_ERR_NODIR',$source));
		}
	}

	function copyJoomfishFiles() {
		$joomfishArr = array(
			array(
				'source' => $this->backendPath . 'joomfish' . DS,
				'dest' => JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_joomfish' . DS . 'contentelements',
			),
		);
		foreach ($joomfishArr as $joomfish) {
			$source = $joomfish['source'];
			$dest	= $joomfish['dest'];
			if (is_dir($source)) {
				if (is_dir($dest)) {
					if ($dh = opendir($source)) {
						while (($file = readdir($dh)) !== false) {
							if ($file != '.' && $file != '..') {
								$pathInfo = pathinfo($source . $file);
								if ($pathInfo['extension'] == 'xml') {
									if (!JFile::copy($source . $file, $joomfish['dest'] . DS . $file)) {
										$this->setError(JText::sprintf('REGPRO_INSTALLER_ERR_JOOMFISH',$source . $file, $joomfish['dest'] . DS . $file));
									}
								}
							}
						}
						closedir($dh);
					} else $this->setError(JText::sprintf('REGPRO_INSTALLER_ERR_OPENDIR',$source));
				}
			} else $this->setError(JText::sprintf('REGPRO_INSTALLER_ERR_NODIR',$source));
		}
	}

	function cleanup() {
		$this->installLangs();
		$this->copyJoomfishFiles();
		if(!JFolder::delete($this->tmpPath . 'regpro_joomla/'))
		{
			$fp = fopen('errorlog.txt', 'w');
			fwrite($fp, 'Error in deleting folder regpro_joomla');
			fclose($fp);
		}
		if(!JFile::delete($this->frontendPath . 'frontend' . $this->archiveExt))
		{
			$fp = fopen('errorlog.txt', 'w');
			fwrite($fp, 'Error in deleting folder frontend');
			fclose($fp);
		}
		
		if(!JFile::delete($this->backendPath . 'backend' . $this->archiveExt))
		{
			$fp = fopen('errorlog.txt', 'w');
			fwrite($fp, 'Error in deleting file');
			fclose($fp);
		}
		
		foreach($this->joomlaAddons as $addon => $addon_name)
		{
			if(!JFile::delete($this->frontendPath . 'regpro_' . $addon . $this->archiveExt))
			{
				$fp = fopen('errorlog.txt', 'w');
				fwrite($fp, 'Error in deleting file');
				fclose($fp);
			}
		}
		$db = JFactory::getDbo();
		$db->setQuery("UPDATE #__extensions SET `enabled` = 1 WHERE 'element'=('registrationprosearch' && 'payoffline') AND `type` = 'plugin'");
		$db->execute();
	}

	function finish() {}

	function install($step) {
		switch((int)$step) {
			case 1:
				break;
			case 2:
				$this->installBackend();
				break;
			case 3:
				$this->initDatabase();
				break;
			case 4:
				$this->installFrontend();
				break;
			case 5:
				$this->installJoomlaAddons();
				break;
			case 6:
				$this->cleanup();
				break;
			case 7:
				$this->finish();
				break;
		}
		$this->_testErrors();
		$this->_setStatus();
		$this->_buildMessages($step, '<br/>', '<br/><br/>');
		return $this->_prepareJsonResponse($this->status,$this->errorResult,$this->messageResult);
	}
}

?>