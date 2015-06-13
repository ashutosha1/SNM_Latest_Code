<script type="text/javascript">

		// for mootools 1.2 version
		window.addEvent('domready', function() {						
			$$('a.Tips4').each(function(element,index) {
				var content = element.get('title').split('::');
				element.store('tip:title', content[0]);
				element.store('tip:text', content[1]);
			});
			
			//create the tooltips
			var tipz = new Tips('.Tips4',{
				className: 'Tips4',
				fixed: false,
				hideDelay: 50,
				showDelay: 50
			});
			
			//customize			
			tipz.addEvents({
				'show': function(tip) {
					tip.fade('in');
				},
				'hide': function(tip) {
					tip.fade('out');
				}
			});						
		});
		// end

function cal_month_change(month, strurl) {
	var finalurl = strurl;
	finalurl1 = finalurl.replace("monthnumber",month);
	window.location = finalurl.replace("monthnumber",month);
}

function cal_year_change(year, strurl) {
	var finalurl = strurl;
	finalurl1 = finalurl.replace("yearnumber",year);
	window.location = finalurl.replace("yearnumber",year);
}

function cal_category_change(catid, strurl) {
	var finalurl = strurl;
	finalurl1 = finalurl.replace("categoryid",catid);
	window.location = finalurl.replace("categoryid",catid);
}

</script>

<LINK REL=StyleSheet HREF="<?php echo JURI::root().'/components/com_registrationpro/assets/css/regpro.css'; ?>" TYPE="text/css" MEDIA=screen>
<LINK REL=StyleSheet HREF="<?php echo JURI::root().'/components/com_registrationpro/assets/css/regpro_calendar.css'; ?>" TYPE="text/css" MEDIA=screen>
<style>
.tip { Z-INDEX: 13000; WIDTH: 350px; COLOR: #000; left top repeat-y;margin-left: -180px; margin-top: 20px; }
.tip-top {}
.tip-title { PADDING-RIGHT: 4px; PADDING-LEFT: 4px; FONT-WEIGHT: bold; FONT-SIZE: 12px; BACKGROUND: #333333; PADDING-BOTTOM: 4px; MARGIN: 0px; COLOR: #ffffff; PADDING-TOP: 4px; BORDER-BOTTOM: #135cae 1px solid;}
.tip-text { PADDING-RIGHT: 4px; PADDING-LEFT: 4px; FONT-SIZE: 11px; BACKGROUND: #efefef; PADDING-BOTTOM: 4px; PADDING-TOP: 4px;}
.tip-bottom {}
</style>

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

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<!--  CODE TO DISPLAY PAGE HEADING -- -->
<?php if ($this->params->get('show_page_heading')) : ?>
<h1>
	<?php if ($this->escape($this->params->get('page_heading'))) : ?>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	<?php else : ?>
		<?php echo $this->escape($this->params->get('page_title')); ?>
	<?php endif; ?>
</h1>
<?php endif; ?>
<!-- ********************************************************************** -->
<div id="regpro">
<?php 
$regpro_header_footer = new regpro_header_footer;
$regpro_header_footer->regpro_header($this->regproConfig); 

// Event tickets
$month = JRequest::getVar('month');		
$year  = JRequest::getVar('year');

global $mainframe;

$catid = $mainframe->getUserStateFromRequest('com_registrationpro_calender_categoryid', 'categoryid', 0, 'int' );

$cal = new WebCamCalendar($this->regproConfig, $catid);
$start_year = $this->regproConfig['cal_start_year'];
$end_year = $this->regproConfig['cal_end_year'];
if(!empty($month) && !empty($year)){
	echo $cal->getMonthView($month, $year,$start_year, $end_year);
} else echo $cal->getCurrentMonthView($start_year, $end_year);

$regpro_header_footer->regpro_footer($this->regproConfig);

?>
</div>