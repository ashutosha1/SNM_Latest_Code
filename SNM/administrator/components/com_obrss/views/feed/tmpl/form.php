<?php
/**
 * @version          $Id: form.php 55 2014-01-22 03:50:36Z thongta $
 * @package          obRSS Feed Creator for Joomla.
 * @copyright    (C) 2007-2012 foobla.com. All rights reserved.
 * @author           foobla.com
 * @license          GNU/GPL, see LICENSE
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

global $isJ25, $boolean_array;
JHtml::addIncludePath( JPATH_COMPONENT . '/helpers/html' );
JHTML::_( 'behavior.tooltip' );
jimport( 'joomla.html.pane' );
JHtml::_( 'behavior.multiselect' );
JHtml::_( 'dropdown.init' );
JHtml::_( 'formbehavior.chosen', 'select' );

require_once( JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'itemshelper.php' );
$Itemid = obRSSUrl::getItemid();
$format = itemsHelper::getFeedTypePrefix( $this->feed->feed_type );

if ( $Itemid ) {
	$link_feed = "index.php?option=com_obrss&task=feed&id={$this->feed->id}" . ':' . $this->feed->alias . '&format=' . $format . '&Itemid=' . $Itemid;
} else {
	$link_feed = "index.php?option=com_obrss&task=feed&id={$this->feed->id}" . ':' . $this->feed->alias . '&format=' . $format;
}
$app = JFactory::getApplication();
$params = JComponentHelper::getParams( 'com_obrss' );

if ( $params->get( 'admin_link_sef', 0 ) ) {
	$app_route = $app->getInstance( 'site' );

	$router = $app_route->getRouter();

	$newUrl    = $router->build( $link_feed );
	$link_feed = $newUrl->toString();

	$link_feed = preg_replace( '/[\s\S]*?administrator\//i', '', $link_feed );
}

$link_feed = JUri::root() . $link_feed;
$boolean_array = array(
	JHTML::_( 'select.option', '0', JText::_( 'OBRSS_SETTINGS_NO' ) ),
	JHTML::_( 'select.option', '1', JText::_( 'OBRSS_SETTINGS_YES' ) )
);
JFilterOutput::objectHTMLSafe( $this->feed, ENT_QUOTES );
$detail = $this->feed->components;
echo "<script type=\"text/javascript\"> var addons = Array('" . implode( '\',\'', $this->addons->lists ) . "');</script>";
?>
<script type="text/javascript">
	<!--
	if (typeof(Joomla) === 'undefined') {
		var Joomla = {};
	}
	function submitbutton(pressbutton) {
		obSubmitbutton(pressbutton, true);
	}
	Joomla.submitbutton = function (pressbutton) {
		if (pressbutton == 'preview') {
			var obrssid = document.adminForm.id.value;
			var obrss_alias = document.adminForm.alias.value;
			window.open('<?php echo $link_feed ?>');
			return false;
		}
		var form = document.adminForm;
		if (pressbutton != '') {
			if (pressbutton == 'cancel') {
				Joomla.submitform(pressbutton);
			} else {
				if (form.name.value == "") {
					form.name.focus();
					alert("Item must have a name");
				} else {
					Joomla.submitform(pressbutton);
				}
			}
		}
	}
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton != '') {
			if (pressbutton == 'preview') {
				var obrssid = document.adminForm.id.value;
				window.open('<?php echo JURI::root(); ?>index.php?option=com_obrss&controller=feed&task=feed&id=' + obrssid);
				return false;
			}
			if (pressbutton == 'cancel') {
				submitform(pressbutton);
			} else {
				if (form.name.value == "") {
					form.name.focus();
					alert("Item must have a name");
				} else {
					submitform(pressbutton);
				}
			}
		}
	}
	function loadButton(elem) {
		document.getElementById("feedButton").src = '<?php echo JURI::root(); ?>components/com_obrss/images/buttons/' + elem.value;
	}
	function showComParamater() {
		var catNamelist = document.getElementById('components').value;
		var arrCatName = new Array();
		var length = catNamelist.length;
		catName = 'obrss_addon_' + catNamelist;
		for (i = 0; i < addons.length; i++) {
			if (catName == addons[i]) {
				document.getElementById(catName).style.display = "block";
				document.getElementById('detail').value = catName;
			} else {
				document.getElementById(addons[i]).style.display = "none";
			}
		}
	}
	function HSFeedParam(me, el) {
		var gid = function (id) {
			return document.getElementById(id);
		}
		var fpr = gid(el).style;
		if (fpr.display == 'none') {
			fpr.display = 'block';
			me.className = 'title pane-toggler-down';
		} else {
			fpr.display = 'none';
			me.className = 'title pane-toggler';
		}
	}
	function obRssResetHits() {
		var obrssid = document.adminForm.id.value;
		document.location = 'index.php?option=com_obrss&controller=feed&task=resethits&id=' + obrssid;
	}
	function obDoSwitch(switcher) {
		var el = switcher.getParent().getFirst(".ob_switch");
		//var vl = el.getParent().getFirst(".ob_switch").value;
		if (el.value == 1) {
			switcher.setProperty("class", "switcher-off");
			el.value = 0;
		} else {
			switcher.setProperty("class", "switcher-on");
			el.value = 1;
		}
	}

	// Bootstrap nav-tabs
	jQuery(document).ready(function ($) {
		$('#myTab a').click(function (e) {
			e.preventDefault();
			$(this).tab('show');
		});
		(function ($) {

			// Turn radios into btn-group
			$('.radio.btn-group label').addClass('btn');
			$(".btn-group label:not(.active)").click(function () {
				var label = $(this);
				var input = $('#' + label.attr('for'));

				if (!input.prop('checked')) {
					label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
					if (input.val() == '') {
						label.addClass('active btn-primary');
					} else if (input.val() == 0) {
						label.addClass('active btn-danger');
					} else {
						label.addClass('active btn-success');
					}
					input.prop('checked', true);
				}
			});
			$(".btn-group input[checked=checked]").each(function () {
				if ($(this).val() == '') {
					$("label[for=" + $(this).attr('id') + "]").addClass('active btn-primary');
				} else if ($(this).val() == 0) {
					$("label[for=" + $(this).attr('id') + "]").addClass('active btn-danger');
				} else {
					$("label[for=" + $(this).attr('id') + "]").addClass('active btn-success');
				}
			});
		})(jQuery);
	});


	//-->
</script>
<?php if ( ! $isJ25 ) { ?>
	<style type="text/css">
		fieldset.adminform label.radiobtn-no {
			width: 40px;
		}

		label.radiobtn-no,
		label.radiobtn-yes {
			clear:   none;
			display: inline;
		}
	</style>
<?php } ?>
<div id="foobla">
	<form id="adminForm" name="adminForm" action="index.php?option=com_obrss&controller=feed" method="post" class="form-horizontal">
		<div class="row-fluid">
			<!-- Begin Content -->
			<div class="span9">
				<ul class="nav nav-tabs" id="myTab">
					<li class="active">
						<a href="#general" data-toggle="tab"><i class="fa fa-rss"></i> <?php echo JText::_( 'OBRSS_FEED_DETAILS' ); ?>
						</a>
					</li>
					<li>
						<a href="#feedparams" data-toggle="tab"><i class="fa fa-gears"></i> <?php echo JText::_( 'OBRSS_FEED_PARAMATERS' ); ?>
						</a></li>
					<li>
						<a href="#feedburner" data-toggle="tab"><i class="fa fa-fire"></i> <?php echo JText::_( 'OBRSS_FEEDBURNER' ); ?>
						</a></li>
					<li>
						<a href="#itunes" data-toggle="tab"><i class="fa fa-microphone"></i> <?php echo JText::_( 'OBRSS_ITUNES_PODCAST' ); ?>
						</a></li>
				</ul>

				<div class="tab-content">
					<!-- Begin Tabs -->
					<div class="tab-pane active" id="general">
						<div class="control-group">
							<div class="control-label">
								<label id="name-lbl" for="name" class="hasTip required">
									<?php echo JText::_( 'OBRSS_NAME' ); ?>
									<span class="star">&nbsp*</span>
								</label>
							</div>
							<div class="controls">
								<input type="text" class="inputbox" name="name" size="40" value="<?php echo $this->feed->name; ?>" />
							</div>
						</div>

						<div class="control-group">
							<div class="control-label">
								<label>
									<?php echo JText::_( 'OBRSS_ALIAS' ); ?>
								</label>
							</div>
							<div class="controls">
								<input class="text_area" class="inputbox" type="text" name="alias" id="alias" size="40" maxlength="250" value="<?php echo $this->feed->alias; ?>" />
							</div>
						</div>


						<div class="control-group">
							<div class="control-label">
								<label>
									<?php echo JText::_( 'OBRSS_DESCRIPTION' ); ?>
								</label>
							</div>
							<div class="controls">
								<textarea rows="5" cols="40" name="description"><?php echo $this->feed->description; ?></textarea>
							</div>
						</div>

						<div class="">
							<span class="hasTip" title="<?php echo JText::_( 'OBRSS_CONTENT_DATA_SOURCE' ) . '::' . JText::_( 'OBRSS_CONTENT_DATA_SOURCE_DESC' ) ?>"><?php echo $this->lists1['components']; ?></span>
							<span class="hasTip" title="<?php echo JText::_( 'OBRSS_CONTENT_DATA_SOURCE_DESC_EXTRA' ) ?>"><a href="http://foob.la/obRSSstore" target="_blank"><img src="components/com_obrss/assets/images/obstore_48.png" width="48" style="vertical-align: top;" /></a></span>
						</div>

						<div class="">
							<div class="accordion-inner"><?php echo $this->addons->params; ?></div>
						</div>
					</div>
					<!-- End Tabs -->

					<!-- Feed Options -->
					<div class="tab-pane" id="feedparams">
						<?php echo $this->params->render(); ?>
					</div>
					<!-- End Feed Options -->

					<!-- Feedburner -->
					<div class="tab-pane" id="feedburner">
						<?php echo $this->loadTemplate( 'feedburner' ); ?>
					</div>
					<!-- End Feedburner -->

					<!-- iTunes Podcast -->
					<div class="tab-pane" id="itunes">
						<?php echo $this->loadTemplate( 'itunes' ); ?>
					</div>
					<!-- End iTunes Podcast -->
				</div>
			</div>
			<!-- End Content -->
			<!-- Begin Sidebar -->
			<div class="span3">
				<?php echo $this->loadTemplate( 'sidebar' ); ?>
			</div>
			<!-- End Sidebar -->
		</div>
		<input type="hidden" name="id" value="<?php echo $this->feed->id; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="detail" id="detail" value="<?php echo $detail; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div>