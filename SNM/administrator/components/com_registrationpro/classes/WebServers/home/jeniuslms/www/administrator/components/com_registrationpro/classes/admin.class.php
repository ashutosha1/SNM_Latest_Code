<?php
/**
 * @version		v.3.2 registrationpro $
 * @package		registrationpro
 * @copyright	Copyright � 2009 - All rights reserved.
 * @license  	GNU/GPL		
 * @author		JoomlaShowroom.com
 * @author mail	info@JoomlaShowroom.com
 * @website		www.JoomlaShowroom.com
 *
*/

defined('_JEXEC') or die('Restricted access');


class registrationproAdmin {


	 function _getversion()
	{
		
		$xmlFile = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_registrationpro'.DS.'registrationpro.xml';

		$xml=simplexml_load_file($xmlFile);
		$version = $xml->version;
		return $version;
	}
	
	 function footer()
	{
	 ?>
	 	<table cellpadding="0" cellspacing="0" class="adminFooter" border="0" width="100%" style="line-height:18px;">
			<tr><td colspan=3 height=20></td></tr>
			<tr>
				<td style=" text-align:left; vertical-align:top;">
					<a href="http://joomlashowroom.com/documentation" target="_blank" title="<?php echo JText::_('LBLUSER_MANUAL'); ?> "> 
						<?php echo JText::_('LBLUSER_MANUAL'); ?> 
					</a> <br />
					<a href="http://www.joomlashowroom.com" target="_blank" title="<?php echo JText::_('PRODUCT_SUPPORT_CENTER'); ?>"> 
						<?php echo JText::_('PRODUCT_SUPPORT_CENTER'); ?> 
					</a> <br />
					<a href="http://twitter.com/joomlashowroom" target="_blank" title="<?php echo JText::_('PRODUCT_FOLLOW_US_TWITTER'); ?>"> 
						<?php echo JText::_('PRODUCT_FOLLOW_US_TWITTER'); ?> </a> <br />
					<a href="http://extensions.joomla.org/extensions/owner/JoomlaShowroom" target="_blank" title="<?php echo JText::_('PRODUCT_JED_FEEDBACK'); ?>"> 
						<?php echo JText::_('PRODUCT_JED_FEEDBACK'); ?> 
					</a>
				</td>
				
				<td style="text-align:center; vertical-align:top;"> 
					<?php echo JText::_('PRODUCT_NAME').": ".JText::_('PRODUCT_DESCRIPTION'); ?> <br/> 
					<?php $registrationproAdmin = new registrationproAdmin; echo JText::_('LBLCOPYRIGHT').": ".JText::_('COPYRIGHT');?> <br/> 
					<?php echo JText::_('LBLINSTALLED_VERSION').": ".$registrationproAdmin->_getversion();?> <br/> 
					<?php echo JText::_('LBLPHPVERSION').": (".JText::_('PRODUCT_PHP_VERSION_REQUIRED').") (".JText::_('PRODUCT_PHP_VERSION_CURRENT')." ".@phpversion().")";?> 
				</td>
				
				<td style="text-align:right; vertical-align:top;">
					<a href="http://www.joomlashowroom.com" target="_blank">
						<img src="<?php echo REGPRO_ADMIN_IMG_PATH;?>/Joomla_Showroom_logo.png" border="0" />
					</a>
				</td>
			</tr>
		</table>		
	<?php
	}

	 function config()
	{
		$db = JFactory::getDBO();
		$regpro2Config = array();	

		$db->setQuery("SELECT * FROM #__registrationpro_config");	
		$result = $db->loadObjectList();
	
		foreach ($result as $each){
			$regpro2Config[$each->setting_name] = stripslashes($each->setting_value);
		}
		return $regpro2Config;
	}
}

?>