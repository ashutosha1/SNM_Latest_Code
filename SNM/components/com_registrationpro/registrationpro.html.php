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
Class regpro_header_footer
{
	function regpro_header($regproConfig) {
		if($regproConfig['showhead']){
		$user	= JFactory::getUser();
		global $Itemid, $mainframe, $option;
?>
		<script language="javascript" type="text/javascript">
			function onclick_searchevent(){
				if(document.getElementById("searchevent").style.display == ''){
					document.getElementById("searchevent").style.display = 'none';
				} else document.getElementById("searchevent").style.display = '';
			}

			function form_reset(){
				var myfrm = document.eventsearch;
				myfrm.txtEventName.value		= "";
				myfrm.txtEventLocation.value	= "";
				myfrm.txtEventStartDate.value	= "";
				myfrm.txtEventEndDate.value		= "";
			}
		</script>

	   	<div class="btn-toolbar" id="regpro-header-toolbar">
				
				<?php
					$link = JRoute::_("index.php?option=com_registrationpro&view=calendar&Itemid=".$Itemid);
					$font_color = "";
					$class = "btn";
					if($_GET['view'] == 'calendar' ) $class = "btn active";
				?>
				<div class="top-left">
				<a href="<?php echo $link;?>"><button type="button" class="<?php echo $class;?>"><i class="icon-calendar"></i><?php echo JText::_('EVENTS_FRONT_HEADER_LBL_CALENDAR');?></button></a>

				<?php
					$link = JRoute::_("index.php?option=com_registrationpro&view=events&Itemid=".$Itemid);
					if($regproConfig['listing_button'] == 2) $link = JRoute::_("index.php?option=com_registrationpro&view=events&layout=listing&Itemid=".$Itemid);
					$font_color = "";
					$class = "btn";
					if($_GET['view'] == 'events' ) $class = "btn active";
				?>
				<a href="<?php echo $link;?>"><button type="button" class="<?php echo $class;?>"><i class="icon-list"></i><?php echo JText::_('EVENTS_FRONT_HEADER_LBL_LIST');?></button></a>
				<a href="#" id="regpro_search" onclick="onclick_searchevent(); return false;"><button type="button" class="btn"><i class="icon-search"></i><?php echo JText::_('EVENTS_FRONT_HEADER_LBL_SEARCH'); ?></button></a>
				</div>
				<div class="share-btn">
					<?php
					//print_r("sdlkjdhslkjh".$row); die;
						if(JRequest::getVar('view') == 'event')
						{
							$plugin_handler = new regProPlugins;				
			$res = $plugin_handler->getSocialsettings('event','top');
			//print_r($res);
			$pageurl = $_SERVER['REQUEST_URI'];
			$pageurl[0] = '';
			$pageurl = trim($pageurl);
			$pageurl = urlencode(JURI::root().$pageurl);
			$leftText = $res['share_text'];
			if(count($res) >0)
			{
				if(isset($res["l_facebook"]) || isset($res["l_twitter"]) || isset($res["l_linkedin"]) || isset($res["l_googlePlus"]) )
				echo (empty($leftText)) ? "" : '<span>'.$leftText.'</span><br>';
				include_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/tools.php';
				$imgPrefixSystem = JURI::root() . "images/regpro/system/";
				$imgPrefixEvents = JURI::root() . "images/regpro/events/";
				 $imgCurr = getImageName($row->id, $row->user_id);
				
					$imgName = $imgPrefixEvents . $imgCurr . getUniqFck();;					
				
				$s_desc = strip_tags($regproConfig['event_description']);
				$title = $regproConfig['event_title'];
				foreach($res as $k=>$v)
				{
					switch($k){
						case "l_facebook" :
						// Facebook
							echo sprintf($v,$pageurl,urlencode($title), urlencode($imgName),$pageurl,urlencode($s_desc));
							
						break;
						case "l_twitter" :
						// Twitter
							echo sprintf($v,$pageurl,urlencode($title), urlencode($imgName),$pageurl,urlencode($s_desc) );
						break;
						case "l_linkedin" :
							// Linkedin
							echo sprintf($v,$pageurl,urlencode($title),urlencode($s_desc));
						break;
						case "l_googlePlus" :
						// Google +
							echo sprintf($v,$pageurl,urlencode($title), urlencode($imgName),$pageurl,urlencode($s_desc));
						break;
						case "share_text" :
						
						break;
						default  :
							echo sprintf($v,$pageurl,urlencode($title), urlencode($imgName),$pageurl,urlencode($s_desc));
						break;
					}
						
						
					
				}
			}
						}
					?>
				</div>
				
				
				<?php
					$searchcolspan = 3;
					$session = JFactory::getSession();
					$cart 	 = $session->get('cart');
					if(@$cart['ticktes']) {
						$searchcolspan = 4;
						$link = JRoute::_("index.php?option=com_registrationpro&controller=cart&task=cart&Itemid=".$Itemid);
						$font_color = "";
						$class = "btn";
						if($_GET['view'] == 'cart' ||  $_GET['controller'] == 'cart') $class = "btn active";
					?>
					<a href="<?php echo $link ; ?>"><button type="button" class="<?php echo $class; ?>"><i class="icon-cart"></i><?php echo JText::_('EVENTS_FRONT_HEADER_LBL_CART'); ?></button></a>

					<?php
					}
																																		$registrationproHelper = new registrationproHelper;
					if($registrationproHelper->checkUserAccount()) {
						$link = JRoute::_("index.php?option=com_registrationpro&view=myevents&Itemid=".$Itemid);
						$font_color = "";
						$class = "btn";
						if($_GET['view'] == 'myevents' ) $class = "btn active";
					?>
					<a href="<?php echo $link ; ?>"><button type="button" class="<?php echo $class; ?>"><i class="icon-user"></i><?php echo JText::_('EVENTS_FRONT_HEADER_LBL_MYACCOUNTS'); ?></button></a>

					<?php
					}
					$action  = JRoute::_("index.php?option=com_registrationpro&view=events&Itemid=$Itemid"); ?>

					<?php
						$event_title 		= $mainframe->getUserStateFromRequest( $option.'.txtEventName', 'txtEventName', '', 'string' );
						$event_start_date 	= $mainframe->getUserStateFromRequest( $option.'.txtEventStartDate', 'txtEventStartDate', '', 'string' );
						$event_end_date 	= $mainframe->getUserStateFromRequest( $option.'.txtEventEndDate', 'txtEventEndDate', '', 'string' );
						$event_location 	= $mainframe->getUserStateFromRequest( $option.'.txtEventLocation', 'txtEventLocation', '', 'string' );
						$event_category		= $mainframe->getUserStateFromRequest( $option.'.selCategory', 'selCategory', '', 'int' );

						// All Category list
						$Lists = array();
						$categories		= array();
						$categories[] 	= JHTML::_('select.option',  '0', JText::_('EVENTS_SEARCH_EVENT_SELECT_CATEGORY'));
						$registrationproHelper = new registrationproHelper;
						$all_categories	=  $registrationproHelper->getAllCategory();
						$categories		= array_merge( $categories, $all_categories);
						$Lists['categories'] =  JHTML::_('select.genericlist', $categories, 'selCategory', 'class="regpro_inputbox"','value', 'text', $event_category);

					?>
			</div>
			<div id="searchevent" style="display:none;">
				<form name="eventsearch" action="<?php echo $action; ?>"  method="post">
					<table border='0' cellspacing="0" cellpadding="2" width="100%" align="center" class="eventlisting">
						<tr>
							<td align="left"><?php echo JText::_('EVENTS_SEARCH_EVENT_NAME');?></td>
							<td align="left"><input type='text' name='txtEventName' value="<?php echo $event_title;?>" class="regpro_inputbox" /></td>
						</tr>
						<tr>
							<td align="left"> <?php echo JText::_('EVENTS_SEARCH_EVENT_LOCATION');?></td>
							<td align="left"> <input type='text' name='txtEventLocation' value="<?php echo $event_location;?>" class="regpro_inputbox" /></td>
						</tr>
						<tr>
							<td align="left"> <?php echo JText::_('EVENTS_SEARCH_EVENT_START_DATE'); ?></td>
							<td align="left"> <input type='text' name='txtEventStartDate' value='<?php echo  $event_start_date;?>' class="regpro_inputbox" /><?php echo JText::_('EVENTS_SEARCH_EVENT_START_DATE_FORMAT'); ?></td>
						</tr>
						<tr>
							<td align="left"> <?php echo JText::_('EVENTS_SEARCH_EVENT_END_DATE');?></td>
							<td align="left"> <input type='text' name='txtEventEndDate' value='<?php echo  $event_end_date;?>' class="regpro_inputbox" /><?php echo JText::_('EVENTS_SEARCH_EVENT_END_DATE_FORMAT'); ?></td>
						</tr>
						<tr>
							<td align="left"> <?php echo JText::_('EVENTS_SEARCH_EVENT_CATEGORY');?></td>
							<td align="left"> <?php echo $Lists['categories']; ?></td>
						</tr>
						<tr>
							<td align="left">&nbsp;</td>
							<td align="left">
								<input type='submit' value="<?php echo JText::_('EVENTS_SEARCH_BUTTON'); ?>" class="btn regpro_button" />
								<input type='button' name="reset" value="<?php echo JText::_('EVENTS_RESET_BUTTON'); ?>" class="btn regpro_button" onclick="return form_reset();"/>
							</td>
						</tr>
					</table>
				</form>
			</div>
<?php
		}
	}

	function regpro_footer($regproConfig) {
		if($regproConfig['show_footer']){
?>
		<div style="height:4px"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border=0 /></div>
		<div class="regpro_outline" id="regpro_outline">
		<table border=0 cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<?php if($regproConfig['rss_enable']){ ?>
				<td class="regpro_footer-regpro" style="text-align:left"><a href="<?php echo REGPRO_SITE_URL ?>/index.php?option=com_registrationpro&controller=events&task=rssfeed" target="_blank"><img src="<?php echo REGPRO_IMG_PATH; ?>/rss.png" border=0 align="absmiddle" title="<?php echo JText::_('REGPRO_RSS_FEED'); ?>" alt="<?php echo JText::_('REGPRO_RSS_FEED'); ?>" /></a><?php echo JText::_('REGPRO_RSS_FEED'); ?></td>
				<?php } ?>
				<td class="regpro_footer-regpro" style="text-align:right"><?php echo JText::_('EVENTS_FRONT_FOOTER_TXT_POWEREDBY'); ?><a href="http://www.joomlashowroom.com" target="_blank"> <?php echo JText::_('EVENTS_FRONT_FOOTER_TXT'); ?> </a></td>
			</tr>
		</table>
		</div>
		<div style="height:4px"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border=0 /></div>
<?php
		}
	}

	function regpro_finalcheckout_header($regproConfig) {
		$user	= JFactory::getUser();
		global $Itemid, $mainframe, $option;
?>
			<div class="btn-toolbar" id="regpro-header-toolbar" style="width:80%;">
				<?php
					if($regproConfig['multiple_registration_button'] == 1) {
						$link = JRoute::_("index.php?option=com_registrationpro&view=events&Itemid=".$Itemid);
				?>
					<a href="<?php echo $link ; ?>"><button type="button" class="btn"><i class="icon-plus-sign"></i><?php echo JText::_('FINAL_CHECKOUT_CONTINUE_SHOPPING'); ?></button></a>
				<?php
					}
				?>
					<a href="<?php echo JRoute::_("index.php?option=com_registrationpro&controller=cart&task=cart&Itemid=".$Itemid); ?>"><button type="button" class="btn"><i class="icon-shopping-cart"></i><?php echo JText::_('FINAL_CHECKOUT_BACK_TO_CART'); ?></button></a>
			</div>
<?php
	}
}

class regpro_html 
{
	function user_toolbar()	{
		global $Itemid, $mainframe, $option;

		// get component config settings
		$registrationproAdmin =new registrationproAdmin;
		$regpro_config	= $registrationproAdmin->config();
?>
		<div style="height:4px"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border=0 /></div>
			<div style="text-align:left;">
				<table class="user-toolbar">
					<tr>
						<td><img src="<?php echo REGPRO_IMG_PATH; ?>/myevents.png" border=0 align="absmiddle"/><a href="<?php echo JRoute::_("index.php?option=com_registrationpro&view=myevents&Itemid=".$Itemid,false); ?>"><?php echo JText::_( 'MY_EVENTS' );?></a></td>
						<td><img src="<?php echo REGPRO_IMG_PATH; ?>/myforms.png" border=0 align="absmiddle"/><a href="<?php echo JRoute::_("index.php?option=com_registrationpro&view=forms&Itemid=".$Itemid,false); ?>"><?php echo JText::_( 'MY_FORMS' );?></a></td>
						<td><img src="<?php echo REGPRO_IMG_PATH; ?>/mycategories.png" border=0 align="absmiddle"/><a href="<?php echo JRoute::_("index.php?option=com_registrationpro&view=mycategories&Itemid=".$Itemid,false); ?>"><?php echo JText::_( 'MY_CATEGORIES' );?></a></td><td><img src="<?php echo REGPRO_IMG_PATH; ?>/mylocations.png" border=0 align="absmiddle"/><a href="<?php echo JRoute::_("index.php?option=com_registrationpro&view=mylocations&Itemid=".$Itemid,false); ?>"><?php echo JText::_( 'MY_LOCATIONS' );?></a></td>
					<?php
						if($regpro_config['frontend_help_link'] == 1) {
					?>
					<td><img src="<?php echo REGPRO_IMG_PATH; ?>/help.png" border=0 align="absmiddle"/><a href="#" target="_blank" onclick="window.open('<?php echo REGPRO_FRONT_MANUAL_LINK; ?>','popup','width=600,height=600,scrollbars=no,resizable=no,toolbar=no,directories=no,location=no,menubar=no,status=no,left=0,top=0'); return false"><?php echo JText::_( 'MY_ACCOUNT_HELP' );?></a></td>
						<?php
						}
					?>
					</tr>
				</table>
			</div>
		<div style="height:4px"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border=0 /></div>
<?php
	}

	function events_toolbar() {
		global $Itemid, $mainframe, $option;
?>
		<div style="height:4px"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border=0 /></div>
			<div style="text-align:right;">
				<table class="events_toolbar">
					<tr>
						<td><img src="<?php echo REGPRO_IMG_PATH; ?>/add.png" border=0 align="absmiddle"/><a href="#" onclick="submitbutton('add');return false;"><?php echo JText::_( 'MY_EVENT' );?></a></td>
						<td><img src="<?php echo REGPRO_IMG_PATH; ?>/copy.png" border=0 align="absmiddle"/><a href="#" onclick="submitbutton('copy');return false;"><?php echo JText::_( 'MY_COPY_EVENT' );?></a></td>
						<td><img src="<?php echo REGPRO_IMG_PATH; ?>/delete.png" border=0 align="absmiddle"/><a href="#" onclick="submitbutton('remove');return false;"><?php echo JText::_( 'MY_DELETE_EVENT' );?></a></td>
						<td><img src="<?php echo REGPRO_IMG_PATH; ?>/ball_green.png" border=0 align="absmiddle"/><a href="#" onclick="submitbutton('publish');return false;"><?php echo JText::_( 'MY_PUBLISH_EVENT' );?></a></td>
						<td><img src="<?php echo REGPRO_IMG_PATH; ?>/ball_red.png" border=0 align="absmiddle"/><a href="#" onclick="submitbutton('unpublish');return false;"><?php echo JText::_( 'MY_UNPUBLISH_EVENT' );?></a></td>
					</tr>
				</table>
			</div>
		<div style="height:4px"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border=0 /></div>
<?php
	}

	function myusers_toolbar() {
		global $Itemid, $mainframe, $option;
?>
		<div style="height:4px"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border=0 /></div>
			<div style="text-align:right;">
				<img src="<?php echo REGPRO_IMG_PATH; ?>/add.png" border=0 align="absmiddle"/><a href="#" onclick="submitbutton('add_user');return false;"><?php echo JText::_( 'MY_ADD_USER' );?></a>
				&nbsp;&nbsp; <img src="<?php echo REGPRO_IMG_PATH; ?>/delete.png" border=0 align="absmiddle"/><a href="#" onclick="submitbutton('remove');return false;"><?php echo JText::_( 'MY_DELETE_USER' );?></a>
				&nbsp;&nbsp; <img src="<?php echo REGPRO_IMG_PATH; ?>/ball_green.png" border=0 align="absmiddle"/><a href="#" onclick="submitbutton('accept_user');return false;"><?php echo JText::_( 'MY_ACCEPTED_USER' );?></a>
				&nbsp;&nbsp; <img src="<?php echo REGPRO_IMG_PATH; ?>/ball_red.png" border=0 align="absmiddle"/><a href="#" onclick="submitbutton('pending_user');return false;"><?php echo JText::_( 'MY_PENDING_USER' );?></a>
				&nbsp;&nbsp; <img src="<?php echo REGPRO_IMG_PATH; ?>/report.png" border=0 align="absmiddle"/><a href="#" onclick="submitbutton('event_report');return false;"><?php echo JText::_( 'MY_REPORT_USER' );?></a>
				&nbsp;&nbsp; <img src="<?php echo REGPRO_IMG_PATH; ?>/excel.png" border=0 align="absmiddle"/><a href="#" onclick="submitbutton('excel_report');return false;"><?php echo JText::_( 'MY_EXCELREPORT_USER' );?></a>
			</div>
		<div style="height:4px"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border=0 /></div>
<?php
	}

	function myforms_toolbar() {
		global $Itemid, $mainframe, $option;
?>
		<div style="height:4px"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border=0 /></div>
			<div style="text-align:right;">
				<table class="myforms-toolbar">
					<tr>
						<td><img src="<?php echo REGPRO_IMG_PATH; ?>/add.png" border=0 align="absmiddle"/><a href="#" onclick="submitbutton('add');return false;"><?php echo JText::_( 'MY_FROMS_ADD' );?></a></td>
						<td><img src="<?php echo REGPRO_IMG_PATH; ?>/copy.png" border=0 align="absmiddle"/><a href="#" onclick="submitbutton('copy');return false;"><?php echo JText::_( 'MY_COPY_FORM' );?></a></td>
						<td><img src="<?php echo REGPRO_IMG_PATH; ?>/delete.png" border=0 align="absmiddle"/><a href="#" onclick="submitbutton('remove');return false;"><?php echo JText::_( 'MY_DELETE_FORM' );?></a></td>
						<td><img src="<?php echo REGPRO_IMG_PATH; ?>/ball_green.png" border=0 align="absmiddle"/><a href="#" onclick="submitbutton('publish');return false;"><?php echo JText::_( 'MY_PUBLISH_FORM' );?></a></td>
						<td><img src="<?php echo REGPRO_IMG_PATH; ?>/ball_red.png" border=0 align="absmiddle"/><a href="#" onclick="submitbutton('unpublish');return false;"><?php echo JText::_( 'MY_UNPUBLISH_FORM' );?></a></td>
					</tr>
				</table>
			</div>
		<div style="height:4px"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border=0 /></div>
<?php
	}

	function myforms_fields_toolbar() {
		global $Itemid, $mainframe, $option;
?>
		<div style="height:4px"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border=0 /></div>
			<div style="text-align:right;">
				<table class="myforms-fields-toolbar">
					<tr>
						<td><img src="<?php echo REGPRO_IMG_PATH; ?>/add.png" border=0 align="absmiddle"/><a href="#" onclick="submitbutton('edit_field');return false;"><?php echo JText::_( 'MY_FROMS_FIELD_ADD' );?></a></td>
						<td><img src="<?php echo REGPRO_IMG_PATH; ?>/delete.png" border=0 align="absmiddle"/><a href="#" onclick="submitbutton('remove_field');return false;"><?php echo JText::_( 'MY_DELETE_FORM_FIELD' );?></a></td>
						<td><img src="<?php echo REGPRO_IMG_PATH; ?>/ball_green.png" border=0 align="absmiddle"/><a href="#" onclick="submitbutton('publishfield');return false;"><?php echo JText::_( 'MY_PUBLISH_FORM_FIELD' );?></a></td>
						<td><img src="<?php echo REGPRO_IMG_PATH; ?>/ball_red.png" border=0 align="absmiddle"/><a href="#" onclick="submitbutton('unpublishfield');return false;"><?php echo JText::_( 'MY_UNPUBLISH_FORM_FIELD' );?></a></td>
						<td><img src="<?php echo REGPRO_IMG_PATH; ?>/back.png" border=0 align="absmiddle"/><a href="javascript:history.back();" ><?php echo JText::_( 'MY_BACK' );?></a></td>
					</tr>
				</table>
			</div>
		<div style="height:4px"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border=0 /></div>
<?php
	}

	function mycategories_toolbar()	{
		global $Itemid, $mainframe, $option;
?>
		<div style="height:4px"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border=0 /></div>
			<div style="text-align:right;">
				<table class="mycategories-toolbar">
					<tr>
						<td><img src="<?php echo REGPRO_IMG_PATH; ?>/add.png" border=0 align="absmiddle"/><a href="#" onclick="submitbutton('add');return false;"><?php echo JText::_( 'MY_CATEGORIES_ADD' );?></a></td>
						<td><img src="<?php echo REGPRO_IMG_PATH; ?>/delete.png" border=0 align="absmiddle"/><a href="#" onclick="submitbutton('remove');return false;"><?php echo JText::_( 'MY_CATEGORIES_REMOVE' );?></a></td>
						<td><img src="<?php echo REGPRO_IMG_PATH; ?>/ball_green.png" border=0 align="absmiddle"/><a href="#" onclick="submitbutton('publish');return false;"><?php echo JText::_( 'MY_CATEGORIES_PUBLISH' );?></a></td>
						<td><img src="<?php echo REGPRO_IMG_PATH; ?>/ball_red.png" border=0 align="absmiddle"/><a href="#" onclick="submitbutton('unpublish');return false;"><?php echo JText::_( 'MY_CATEGORIES_UNPUBLISH' );?></a></td>
					</tr>
				</table>
			</div>
		<div style="height:4px"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border=0 /></div>
<?php
	}

	function backbutton_toolbar() {
		global $Itemid, $mainframe, $option;
?>
		<div style="height:4px"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border=0 /></div>
			<div style="text-align:right;">
				<img src="<?php echo REGPRO_IMG_PATH; ?>/back.png" border=0 align="absmiddle"/><a href="javascript:history.back();" ><?php echo JText::_( 'MY_BACK' );?></a>
			</div>
		<div style="height:4px"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border=0 /></div>
<?php
	}

	// show payment methods select box
	function list_payment_methods() {
		jimport( 'joomla.html.parameter' );
		$plugin_handler = new regProPlugins;

		// get all active payment methods
		$payment_methods = $plugin_handler->payment_plugins;

		foreach($payment_methods as $key=>$value) {
			$pluginParams = new JRegistry ( $payment_methods[$key]->params);
			$payment_methods[$key]->params = $pluginParams;
		}

		if(is_array($payment_methods) && count($payment_methods)>0){
			?>

			<script language="javascript">
				function onchange_payment(selpayment) {
					if(selpayment.value == "payoffline"){
						document.getElementById("displayofflinedetails").style.display = "";
					} else document.getElementById("displayofflinedetails").style.display = "none";
				}
			</script>
			<tr>
				<td colspan="3" class="regpro_outline" id="regpro_outline">
					<table border=0 cellpadding="3" cellspacing="0" width="100%">

						<tr><td class="regpro_sectiontableheader" style="text-align:center" colspan="2"> <?php echo JText::_('EVENT_CART_PAYMENT_HEADING'); ?></td></tr>

						<tr style="height:25px;">
							<td width="20%"><b><?php echo JText::_('PAYMENT_OPTIONS'); ?><b/></td>
							<td>
								<select name="selPaymentOption" class="fValidate['required']" onchange="return onchange_payment(this);">
								<option value=""><?php echo JText::_('EVENTS_SELECT_PAYMENT_OPTION'); ?></option>
								<?php
									if(is_array($payment_methods) && count($payment_methods)>0){
										foreach($payment_methods as $key=>$value) {
											$pmethod_name = $payment_methods[$key]->name;
								?>
										<option value="<?php echo $pmethod_name; ?>"><?php echo ucfirst($payment_methods[$key]->params->get($pmethod_name.'_label',''));?></option>
								<?php
										}
									}
								?>
								</select>
							</td>
						</tr>

						<tr id="displayofflinedetails" style="display:none">
							<td colspan="2">
								<?php
									echo JText::_('EVENTS_OFFLINE_PAYMENT_INSTRUCTION'),"<br /><br />";
									// check if offline payment values
									if(is_array($payment_methods) && count($payment_methods) > 0) {
										foreach($payment_methods as $key=>$value) {
											if($payment_methods[$key]->name == strtolower("payoffline")) {
												echo $payment_methods[$key]->params->get(payoffline_body,"");
											}
										}
									}
								?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		<?php
		}
	}

	// show event payment methods select box
	function list_event_payment_methods($event_payment_method) {
		jimport( 'joomla.html.parameter' );
		$plugin_handler = new regProPlugins;

		// get all active payment methods
		$allpayment_methods = $plugin_handler->payment_plugins;

		foreach($allpayment_methods as $key=>$value) {
			$pluginParams    = new JRegistry( $allpayment_methods[$key]->params);
			foreach($event_payment_method as $eventkey => $eventvalue) {
				if(trim(strtolower($allpayment_methods[$key]->name)) == trim(strtolower($eventvalue))){
					$payment_methods[$key] = $allpayment_methods[$key];
					$payment_methods[$key]->params = $pluginParams;
				}
			}
		}

		if(is_array($payment_methods) && count($payment_methods)>0) {
			?>

			<script language="javascript">
				function onchange_payment(selpayment) {
					if(selpayment.value == "payoffline"){
						document.getElementById("displayofflinedetails").style.display = "";
					} else document.getElementById("displayofflinedetails").style.display = "none";
				}
			</script>
			<tr>
				<td colspan="3" class="regpro_outline" id="regpro_outline">
					<table border=0 cellpadding="3" cellspacing="0" width="100%">
						<tr> <td class="regpro_sectiontableheader" style="text-align:center" colspan="2"> <?php echo JText::_('EVENT_CART_PAYMENT_HEADING'); ?> </td> </tr>
						<tr>
							<td width="20%"><b><?php echo JText::_('PAYMENT_OPTIONS'); ?><b/></td>
							<td>
								<select name="selPaymentOption" class="fValidate['required']" onchange="return onchange_payment(this);">
								<option value=""><?php echo JText::_('EVENTS_SELECT_PAYMENT_OPTION'); ?></option>
								<?php
									if(is_array($payment_methods) && count($payment_methods)>0) {
										foreach($payment_methods as $key=>$value) {
											$pmethod_name = $payment_methods[$key]->name;
								?>
										<option value="<?php echo $pmethod_name; ?>"><?php echo ucfirst($payment_methods[$key]->params->get($pmethod_name.'_label',''));?></option>
								<?php
										}
									}
								?>
								</select>
							</td>
						</tr>

						<tr id="displayofflinedetails" style="display:none">
							<td colspan="2">
								<?php
									echo JText::_('EVENTS_OFFLINE_PAYMENT_INSTRUCTION'),"<br /><br />";
									// check if offline payment values
									if(is_array($payment_methods) && count($payment_methods) > 0) {
										foreach($payment_methods as $key=>$value) {
											if($payment_methods[$key]->name == strtolower("payoffline")) {
												echo $payment_methods[$key]->params->get(payoffline_body,"");
											}
										}
									}
								?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		<?php
		}
	}
}
?>