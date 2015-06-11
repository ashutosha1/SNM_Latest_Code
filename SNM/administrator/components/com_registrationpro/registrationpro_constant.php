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

/* 
 * Define constants for all pages 
 */

// site constants
define('REGPRO_SITE_URL',JURI::root());
define('REGPRO_SITE_BASE',JPATH_ROOT);

// front end constants
define( 'REGPRO_BASE_URL', JURI::root().'components/com_registrationpro');
define( 'REGPRO_IMG_PATH', REGPRO_BASE_URL.'/assets/images');
define( 'REGPRO_BASE_PATH', JPATH_ROOT.DS.'components'.DS.'com_registrationpro');

// form file field document uplaoding path
define('REGPRO_FORM_DOCUMENT_URL_PATH',JURI::root().'images/registrationpro/forms');
define('REGPRO_FORM_DOCUMENT_BASE_PATH',JPATH_ROOT.DS.'images'.DS.'registrationpro'.DS.'forms');
define('REGPRO_FORM_INVALID_EXTENSIONS','doc,xls,csv,txt,jpg,jpeg,bmp,gif,png,zip,rar,pdf,swf,html,htm,ppt');
define('REGPRO_FORM_MAX_UPLOAD_FILESIZE','2097152'); // size in bytes

// admin constants
define( 'REGPRO_ADMIN_BASE_URL', JURI::root().'administrator/components/com_registrationpro');
define( 'REGPRO_ADMIN_IMG_PATH', REGPRO_ADMIN_BASE_URL.'/assets/images');
define( 'REGPRO_ADMIN_IMG_BASE_PATH', JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_registrationpro'.DS.'assets'.DS.'images');
define( 'REGPRO_ADMIN_BASE_PATH', JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_registrationpro');

// medeia constants
define( 'REGPRO_MEDIA_INVOICE_PDF_BASE_PATH', JPATH_ROOT.DS.'media'.DS.'com_registrationpro'.DS.'invoicepdf');

// recurring feature constants
define('REGPRO_RECURRING_UNLIMITED_DATE', "2010-12-31");

// Rss feed constants
define('REGPRO_RSS_CHANNEL_URL',REGPRO_SITE_URL);
define('REGPRO_RSS_IMAGE_URL',REGPRO_SITE_URL."/images/joomla_logo_black.jpg");
define('REGPRO_RSS_ITEM_URL',REGPRO_SITE_URL);
define('REGPRO_RSS_CACHE_TIME',10);
define('REGPRO_RSS_ITEM_COUNT',20);

// frontend manual link
define('REGPRO_FRONT_MANUAL_LINK','http://joomlashowroom.com/images/user_manuals/registration_pro/Web_Help/Index.htm');

?>