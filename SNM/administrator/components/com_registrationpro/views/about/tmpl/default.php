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

//create the toolbar
JToolBarHelper::title( JText::_( 'ADMIN_LBL_CONTROPANEL_LIST_EVENTS' ), 'events' );
//JToolBarHelper::help( 'screen.registrationpro', true );

?>

<?php echo JText::_('PRODUCT_NAME').": ".JText::_('PRODUCT_DESCRIPTION'); ?> <br/> 
<?php $registrationproAdmin = new registrationproAdmin; echo JText::_('LBLCOPYRIGHT').": ".JText::_('COPYRIGHT');?> <br/> 
<?php echo JText::_('LBLINSTALLED_VERSION').": ".$registrationproAdmin->_getversion();?> <br/> 
<?php echo JText::_('LBLPHPVERSION').": (".JText::_('PRODUCT_PHP_VERSION_REQUIRED').") (".JText::_('PRODUCT_PHP_VERSION_CURRENT')." ".@phpversion().")";?> 