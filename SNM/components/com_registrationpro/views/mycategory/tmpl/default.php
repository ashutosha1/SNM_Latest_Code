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

			if (pressbutton == 'remove' || pressbutton == 'publish' || pressbutton == 'unpublish') {	
				if(form.boxchecked.value > 0){			
					submitform( pressbutton );
					return;
				}else{
					alert("<?php echo JText::_('MY_EVENTS_SELECT_RECORD_FIRST'); ?>");
				}
			}else if(!validateForm(form,false,false,false,false)){
					
			} else {
				submitform( pressbutton );
			}
		}

		//var cp = new ColorPicker();
		
		// Create a new ColorPicker object using Window Popup
		var cp = new ColorPicker('window');

		function pickColor(color) {
			document.getElementById('background').value = color;
			document.getElementById('background').style.background = color;
		}
	
</script>

<script language="javascript">cp.writeDiv();</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table cellpadding="4" cellspacing="1" border="0" class="adminform">    	
      	<tr>
      		<td valign="top" align="left" width="100%">
      			<table border="0" width="100%">
  					<tr>
    					<td valign="top">
							<?php echo JText::_('ADMIN_MANDATORY_SYMBOL'); ?>
							<?php echo JText::_('ADMIN_CATEGORIES_NAME')." "; ?>
						</td>
    					<td valign="top"><input name="catname" alt="blank" emsg="<?PHP echo JText::_('ADMIN_SCRIPT_CATEGORY_NAME_EMPTY'); ?>" value="<?php echo $this->row->catname; ?>" size="55" maxlength="50" width="100%"></td>
  					</tr>
      				
					<tr>
						<td valign="top">&nbsp;</td>
  						<td valign="top" colspan="2"><?php echo JText::_('ADMIN_CATEGORIES_DESCR_LO')." "; ?><br />
						<?php 
						// parameters : areaname, content, width, height, rows, cols
						echo $this->editor->display('catdescription', stripslashes($this->row->catdescription),'80%;', '200', '20', '40',array('pagebreak', 'readmore'));
						?>
						</td>
  					</tr>
				</table>

			</td>
		</tr>
		<tr>
			<td valign="top" align="right" width="96%">
				<table  width="98%">
					<tr>
						<td valign="top" width="98%">   
							<?php
							/* $tabs = JPane::getInstance('sliders', array('allowAllClose' => true));
							echo $tabs->startPane("elcategory-pane");
							echo $tabs->startPanel("Basic","elcatbasic-page");	 */		
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
								
			  					<tr>
			    					<td><?php echo JText::_('ADMIN_CATEGORIES_BACKGROUND'); ?></td>
			    					<td><input type="text" id="background" name="background" value="#<?php echo $this->row->background; ?>" size="8" maxlength="7" style="background:#<?php echo $this->row->background; ?>" /> <A HREF="#" onClick="cp.show('pick');return false;" NAME="pick" ID="pick">Pick</A></td>
			  					</tr>
								
								<tr>
          	  						<td><?php echo JText::_('ADMIN_CATEGORIES_ACCESS'); ?></td>
          	  						<td>
        						<?php 
        						echo $this->Lists['access'];
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
			<td> <input type="button" value="<?php echo JText::_('MY_CATEGORIES_SAVE'); ?>" onclick="return submitbutton('save');" /> </td>
		</tr>	
	</table>	
		
	
	<?php echo JHTML::_( 'form.token' ); ?>	
	<input type="hidden" name="option" value="com_registrationpro" />
	<input type="hidden" name="controller" value="mycategories" />	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="boxchecked" value="0" />			
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
</form>		

<?php
$regpro_header_footer->regpro_footer($this->regpro_config);
?>														
</div>