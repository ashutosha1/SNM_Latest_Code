<?php
/**
* @package Access-Manager (com_accessmanager)
* @version 2.2.1
* @copyright Copyright (C) 2012 - 2014 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class accessmanagerModelFrontend extends JModelList{
	//bogus empty model just for some crazy 3rd party extensions (k2 system plugin) which throw errors if class does not exist	
	//http://www.pages-and-items.com/forum/38-redirect-on-login/8307-model-class-redirectonloginmodelusergroup-not-found-in-file
}
?>