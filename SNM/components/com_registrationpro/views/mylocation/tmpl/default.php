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
?>
<div id="regpro">
<?php
$regpro_header_footer = new regpro_header_footer;
$regpro_header_footer->regpro_header($this->regpro_config);

// user toolbar
$regpro_html = new regpro_html;
$regpro_html->user_toolbar();

// backbutton toolbar
$regpro_html->backbutton_toolbar();
?>

<script language="javascript" type="text/javascript">
		
		function submitbutton(pressbutton) {
			var form = document.adminForm;

			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if(!validateForm(form,false,false,false,false)){
					
			} else {
				submitform( pressbutton );
			}
		}	
</script>
<!--code for google address cordinates By JoomlaShowroom-->
<script>

	function cordinate()
	{
		geocoder = new google.maps.Geocoder(); // creating a new geocode object

		address1 = document.getElementById("address").value;

		if (geocoder)
		{
			geocoder.geocode( { 'address': address1}, function(results, status)
			{
			if (status == google.maps.GeocoderStatus.OK)
			{
				document.getElementById("latitude").value = results[0].geometry.location.lat();
				document.getElementById("longitude").value = results[0].geometry.location.lng();
			} else
			{
				alert("Geocode was not successful for the following reason: " + status);
				document.getElementById("latitude").value = '';
				document.getElementById("longitude").value = '';
			}
			});

		}//end of If

	}
</script>
<form action="index.php?option=com_registrationpro&controller=locations&task=save" method="post" name="adminForm" id="adminForm" class="my-location-form">
<table cellpadding="4" cellspacing="1" border="0" class="adminform">
	<tr>
		<td width="100%">
			<div id="location-form">
			<table cellpadding="4" cellspacing="1" border="0" class="my-location-form">
				<tr>
					<td valign="top">
						<?php echo JText::_( 'ADMIN_MANDATORY_SYMBOL'); ?>
						<?php echo JText::_( 'ADMIN_EVENTS_CLUB_LO')." "; ?>
					</td>
					<td valign="top">
						<input name="club" alt="blank" emsg="<?php echo JText::_( 'ADMIN_SCRIPT_LOCATION_EMPTY'); ?>" value="<?php echo $this->row->club; ?>" size="55" maxlength="50" class="regpro_inputbox">
					</td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top"><?php echo JText::_( 'ADMIN_EVENTS_CLUBHOME_LO')." "; ?></td>
					<td valign="top"><input name="url" value="<?php echo $this->row->url; ?>" size="55" class="regpro_inputbox"> 
					<b><?php echo JText::_( 'ADMIN_EVENTS_CLUBHOME_NOTICE_LO')." "; ?></b> </td>
				</tr>
				
				<!--<tr>
					<td valign="top">&nbsp;</td>
					<td colspan="2"><b><?php //echo JText::_( 'ADMIN_EVENTS_ADRESSDET')." "; ?></b></td>
				</tr>
			-->
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top"><?php echo JText::_( 'ADMIN_EVENTS_CLUBSTREET_LO')." "; ?></td>
					<td valign="top"><input name="street" value="<?php echo $this->row->street; ?>" size="35" maxlength="50" class="regpro_inputbox"></td>
				</tr>
			
				<tr>
					<td valign="top"></td>
					<td valign="top">
						<?php echo JText::_( 'ADMIN_MANDATORY_SYMBOL'); ?>
						<?php echo JText::_( 'ADMIN_EVENTS_CITY_LO')." "; ?>
					</td>
					<td valign="top"><input name="city" alt="blank" emsg="<?php echo JText::_( 'ADMIN_SCRIPT_LOCATION_CITYSTATE_EMPTY'); ?>" id="address" value="<?php echo $this->row->city; ?>" size="35" maxlength="50" class="regpro_inputbox"  onblur="cordinate()"></td>
				</tr>
			
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top"><?php echo JText::_( 'ADMIN_EVENTS_CLUBPLZ_LO')." "; ?></td>
					<td valign="top"><input name="plz" value="<?php echo $this->row->plz; ?>" size="15" maxlength="10" class="regpro_inputbox"></td>
				</tr>
			
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">
						<?php echo JText::_( 'ADMIN_MANDATORY_SYMBOL'); ?>
						<?php echo JText::_( 'ADMIN_EVENTS_CLUBCOUNTRY_LO')." "; ?>
					</td>
					<td valign="top"><input name="country" alt="blank" emsg="<?php echo JText::_( 'ADMIN_SCRIPT_LOCATION_COUNTRY_EMPTY'); ?>" value="<?php echo $this->row->country; ?>" size="4" maxlength="3" class="regpro_inputbox">
					</td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">
						<?php echo JText::_( 'ADMIN_MANDATORY_SYMBOL'); ?>
						<?php echo JText::_( 'ADMIN_EVENTS_LAT')." "; ?>
					</td>
					<td valign="top"><input name="latitude" alt="blank" emsg="<?php echo JText::_( 'ADMIN_SCRIPT_LOCATION_LATITUDE_EMPTY'); ?>" value="<?php echo $this->row->latitude; ?>" id='latitude' size="35" maxlength="50"></td>
				</tr>
				
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">
						<?php echo JText::_( 'ADMIN_MANDATORY_SYMBOL'); ?>
						<?php echo JText::_( 'ADMIN_EVENTS_LNG')." "; ?>
					</td>
					<td valign="top"><input name="longitude" alt="blank" emsg="<?php echo JText::_( 'ADMIN_SCRIPT_LOCATION_LONGITUDE_EMPTY'); ?>" value="<?php echo $this->row->longitude; ?>" id='longitude' size="35" maxlength="50"></td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top"><?php echo JText::_( 'ADMIN_EVENTS_DESCR_LO')." "; ?></td>
					<td valign="top">
					<?php 
					// parameters : areaname, content, hidden field, width, height, rows, cols			
					echo $this->editor->display( 'locdescription',  $this->row->locdescription , '100%', '250', '75', '60', array('pagebreak', 'readmore') ) ;
					?> </td>
				</tr>		
			</table>	
			</div>
		</td>
		</tr>
		<tr>
			<td valign="top" align="right" width='97%'>
					<table cellpadding="4" cellspacing="1" border="0" class="adminform">
						<tr>
							<td width='97%'>   
								<?php
								/* $tabs = JPane::getInstance('sliders', array('allowAllClose' => true));
								echo $tabs->startPane("elcategory-pane");
								echo $tabs->startPanel("Basic","elcatbasic-page"); */	
							echo JHtml::_('sliders.start', 'content-sliders-location', array('useCookie'=>1));		
							echo JHtml::_('sliders.panel', 'Basic', 'elcatbasic-page');								
								?>
								<table class="adminform">							
									<tr>
										<td><?php echo JText::_('ADMIN_CATEGORIES_PUBLI'); ?></td>
										<td> <?php												
												echo $this->Lists['published']; 
											?> 
										</td>
									</tr>							
								</table>									
							<?php
							/* echo $tabs->endPanel();
							echo $tabs->endPane(); */
							echo JHtml::_('sliders.end');
							?>
							</td>
						</tr>
						<tr><td><?php echo JText::_('ADMIN_MANDATORY_FIELDS_NOTE'); ?></td></tr>
					</table>
			</td>
		</tr>
	<tr>
		<td> <input type="button" value="<?php echo JText::_('MY_LOCATION_SAVE'); ?>" onclick="return submitbutton('save');" /> </td>
	</tr>
</table>

	
	<?php echo JHTML::_( 'form.token' ); ?>	
	<input type="hidden" name="option" value="com_registrationpro" />
	<input type="hidden" name="controller" value="locations" />	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="boxchecked" value="0" />	
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />					
</form>	
<?php
$regpro_header_footer->regpro_footer($this->regpro_config);
?>
</div>