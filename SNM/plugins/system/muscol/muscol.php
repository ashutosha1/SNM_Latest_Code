<?php


// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

//new for Joomla 3.0
if(!defined('DS')){
define('DS',DIRECTORY_SEPARATOR);
}

class  plgSystemMuscol extends JPlugin
{
	

	function plgSystemMuscol(& $subject, $config)
	{
		parent::__construct($subject, $config);

	}
	
	function onAfterInitialise()
	{
		$document =& JFactory::getDocument();
		$uri =& JFactory::getURI();
		
		//$document->addScriptDeclaration("var base = \"".$uri->base()."\";");
		$document->addScriptDeclaration("var base = \"".$uri->getScheme()."://".$uri->getHost()."\";");
		$document->addScriptDeclaration("var extrabase = \"".$this->params->get('extrabase')."\";");
	}

	
}


