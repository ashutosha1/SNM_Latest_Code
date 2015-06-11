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
JHtml::_('formbehavior.chosen', 'select');
?>

<script language="javascript" type="text/javascript">
	Joomla.submitbutton = function(pressbutton)	{
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		if(!validateForm(form,false,false,false,false)){
		} else submitform( pressbutton );
	}
</script>

<!--code for google address cordinates By JoomlaShowroom-->
<script>
	function cordinate() {
		geocoder = new google.maps.Geocoder(); // creating a new geocode object
		address1 = document.getElementById("address").value;
		if (geocoder) {
			geocoder.geocode( { 'address': address1}, function(results, status)	{
			if (status == google.maps.GeocoderStatus.OK) {
				document.getElementById("latitude").value = results[0].geometry.location.lat();
				document.getElementById("longitude").value = results[0].geometry.location.lng();
			} else {
				alert("Geocode was not successful for the following reason: " + status);
				document.getElementById("latitude").value = '';
				document.getElementById("longitude").value = '';
			}
			});
		}
	}
</script>
<div class="span10">
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<span class="span12 no-gutter y-offset">
		<b class="pull-right"><?php echo JText::_('ADMIN_MANDATORY_FIELDS_NOTE');?></b>
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<b><?php echo JText::_( 'ADMIN_MANDATORY_SYMBOL').JText::_( 'ADMIN_EVENTS_CLUB_LO');?></b>
	</span>
	<span class="span6 y-offset no-gutter">
		<input  type="text" name="club" alt="blank" emsg="<?php echo JText::_( 'ADMIN_SCRIPT_LOCATION_EMPTY');?>" value="<?php echo $this->row->club;?>" size="55" maxlength="50">
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<b><?php echo JText::_('ADMIN_CATEGORIES_PUBLI');?></b>
	</span>
	<span class="span6 y-offset no-gutter">
		<fieldset class="radio btn-group btn-group-yesno">
			<?php echo $this->Lists['published'];?>
		</fieldset>
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<b><?php echo JText::_( 'ADMIN_EVENTS_CLUBHOME_LO');?></b>
	</span>
	<span class="span6 y-offset no-gutter">
		<input type="text" name="url" value="<?php echo $this->row->url;?>" size="55">&nbsp;&nbsp;<b><?php echo JText::_( 'ADMIN_EVENTS_CLUBHOME_NOTICE_LO');?>
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_( 'ADMIN_EVENTS_CLUBSTREET_LO');?>
	</span>
	<span class="span6 y-offset no-gutter">
		<input type="text" name="street" value="<?php echo $this->row->street;?>" size="35" maxlength="50">
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_( 'ADMIN_MANDATORY_SYMBOL').JText::_( 'ADMIN_EVENTS_CITY_LO');?>
	</span>
	<span class="span6 y-offset no-gutter">
		<input type="text" name="city" alt="blank" emsg="<?php echo JText::_( 'ADMIN_SCRIPT_LOCATION_CITYSTATE_EMPTY');?>" value="<?php echo $this->row->city;?>" size="35" maxlength="50"  id='address' onblur="cordinate()" >
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_( 'ADMIN_EVENTS_CLUBPLZ_LO');?>
	</span>
	<span class="span6 y-offset no-gutter">
		<input type="text" name="plz" value="<?php echo $this->row->plz;?>" size="15" maxlength="10">
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_( 'ADMIN_EVENTS_CLUBCOUNTRY_LO');?>
	</span>
	<span class="span6 y-offset no-gutter">
		<input type="text" name="country" value="<?php echo $this->row->country;?>" size="4" maxlength="3">
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_( 'ADMIN_MANDATORY_SYMBOL').JText::_( 'ADMIN_EVENTS_LAT');?>
	</span>
	<span class="span6 y-offset no-gutter">
		<input type="text" name="latitude" alt="blank" emsg="<?php echo JText::_( 'ADMIN_SCRIPT_LOCATION_LATITUDE_EMPTY');?>" value="<?php echo $this->row->latitude;?>" id='latitude' size="35" maxlength="50">
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_( 'ADMIN_MANDATORY_SYMBOL').JText::_( 'ADMIN_EVENTS_LNG');?>
	</span>
	<span class="span6 y-offset no-gutter">
		<input type="text" name="longitude" alt="blank" emsg="<?php echo JText::_( 'ADMIN_SCRIPT_LOCATION_LONGITUDE_EMPTY');?>" value="<?php echo $this->row->longitude;?>" id='longitude' size="35" maxlength="50">
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_( 'ADMIN_EVENTS_DESCR_LO');?>
	</span>
	<br/>
	<span class="span7 y-offset no-gutter">
		<?php echo $this->editor->display( 'locdescription',  $this->row->locdescription , '100%', '250', '75', '60', array('pagebreak', 'readmore'));?>
	</span>
	<br/>
	<?php echo JHTML::_( 'form.token' );?>
	<input type="hidden" name="option" value="com_registrationpro" />
	<input type="hidden" name="controller" value="locations" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->row->id;?>" />
	<input type="hidden" name="boxchecked" value="0" />
</form>
</div>