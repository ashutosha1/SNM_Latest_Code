<?php
/**
 * @version          $Id: default.php 55 2014-01-22 03:50:36Z thongta $
 * @package          foobla RSS Feed Creator for Joomla.
 * @copyright    (C) 2007-2012 foobla.com. All rights reserved.
 * @author           : foobla.com
 * @license          : GNU/GPL, see LICENSE
 */
// ensure a valid entry point
defined( '_JEXEC' ) or die( 'Restricted Access' );
global $option, $obRootSite, $mainframe, $isJ25;
$mainframe = JFactory::getApplication();
JHtml::addIncludePath( JPATH_COMPONENT . '/helpers/html' );
if ( ! $isJ25 ) :
	JHtml::_( 'bootstrap.tooltip' );
else :
	JHTML::_( 'behavior.tooltip' );
endif;
JHtml::_( 'behavior.multiselect' );
JHtml::_( 'dropdown.init' );
JHtml::_( 'formbehavior.chosen', 'select' );
JHTML::_( 'behavior.modal' );
// endif;

$task = JRequest::getVar( 'task' );
$controller = JRequest::getVar( 'controller' );
$ordering = ( $this->lists['order'] == 'j.ordering' );

$listOrder = $mainframe->getUserStateFromRequest( "$option.filter_order", 'filter_order', 'j.id', 'cmd' );
$listDirn = $mainframe->getUserStateFromRequest( "$option.filter_order_Dir", 'filter_order_Dir', 'DESC', 'word' );

$saveOrder = $listOrder == 'j.ordering';
if ( $saveOrder ) {
	$saveOrderingUrl = 'index.php?option=com_obrss&controller=feed&task=saveOrderAjax&tmpl=component';
	JHtml::_( 'sortablelist.sortable', 'articleList', 'adminForm', strtolower( $listDirn ), $saveOrderingUrl );
}

?>
<div id="foobla">
	<script type="text/javascript">
		Joomla.orderTable = function () {
			table = document.getElementById("sortTable");
			direction = document.getElementById("directionTable");
			order = table.options[table.selectedIndex].value;
			if (order != '<?php echo $listOrder; ?>') {
				dirn = 'asc';
			} else {
				dirn = direction.options[direction.selectedIndex].value;
			}
			Joomla.tableOrdering(order, dirn, '');
		}
	</script>
	<form action="index.php?option=com_obrss&controller=feed" method="post" name="adminForm" id="adminForm">
		<div class="row-fluid">
			<div id="j-sidebar-container" class="span2">
				<?php if ( 1 == 1 ) {
					echo $this->sidebar;
				} ?>
			</div>
			<div id="j-main-container" class="span10 pull-right">
				<div id="filter-bar" class="btn-toolbar">
					<div class="filter-search btn-group pull-left">
						<label for="filter_search" class="element-invisible"><?php echo JText::_( 'COM_OBRSS_SEARCH_IN_TITLE' ); ?></label>
						<input type="text" name="search" id="filter_search" placeholder="<?php echo JText::_( 'COM_OBRSS_SEARCH_IN_TITLE' ); ?>" value="" title="<?php echo JText::_( 'COM_OBRSS_SEARCH_IN_TITLE' ); ?>">
					</div>
					<div class="btn-group pull-left hidden-phone">
						<button class="btn hasTooltip" type="submit" data-original-title="<?php echo JText::_( 'JSEARCH' ); ?>">
							<i class="icon-search"></i></button>
						<button class="btn hasTooltip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" data-original-title="<?php echo JText::_( 'JCLEAR' ) ?>">
							<i class="icon-remove"></i></button>
					</div>
				</div>
				<table class="table table-striped" id="articleList">
					<thead>
					<tr>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_( 'grid.sort', '<i class="icon-menu-2"></i>', 'j.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING' ); ?>
						</th>
						<th width="1%" class="hidden-phone">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_( 'JGLOBAL_CHECK_ALL' ); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th class="nowrap center" width="5%"><?php echo JText::_( 'JSTATUS' ); ?></th>
						<th>
							<?php echo JHTML::_( 'grid.sort', 'JGLOBAL_TITLE', 'j.name', @$this->lists['order_Dir'], $this->lists['order'] ); ?>
						</th>
						<th class="title nowrap hidden-phone" width="5%">
							<?php echo JHTML::_( 'grid.sort', 'OBRSS_ADDONS', 'j.components', @$this->lists['order_Dir'], $this->lists['order'] ); ?>
						</th>
						<th class="title nowrap hidden-phone" width="5%" align="left">
							<?php echo JHTML::_( 'grid.sort', 'TYPE', 'j.feed_type', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
						</th>
						<th class="title nowrap hidden-phone" width="1%" nowrap="nowrap">
							<?php echo JHTML::_( 'grid.sort', 'ID', 'j.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
						</th>
					</tr>
					</thead>
					<tfoot>
					<tr>
						<td colspan="7">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
					</tfoot>
					<tbody>
					<?php
					$k = 0;
					require_once( JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'itemshelper.php' );

					for ( $i = 0, $n = count( $this->items ); $i < $n; $i ++ ) {
						$row = $this->items[$i];
//				$checked 			= JHTML::_('grid.checkedout', $row, $i );
						$published    = JHTML::_( 'grid.published', $row, $i );
						$link         = JFilterOutput::ampReplace( 'index.php?option=' . $option . '&controller=' . $controller . '&task=edit&cid[]=' . $row->id );
						$link_add_fb  = JFilterOutput::ampReplace( 'index.php?option=' . $option . '&controller=' . $controller . '&task=add_fb&cid=' . $row->id . '&tmpl=component' );
						$link_edit_fb = JFilterOutput::ampReplace( 'index.php?option=' . $option . '&controller=' . $controller . '&task=edit_fb&cid=' . $row->id . '&tmpl=component' );
						$link_stats   = JFilterOutput::ampReplace( 'index.php?option=' . $option . '&controller=' . $controller . '&task=view_stats_fb&cid=' . $row->id . '&tmpl=component' );

						$linkURLFB = $row->uri;

						$format = itemsHelper::getFeedTypePrefix( $row->feed_type );
						$Itemid = obRSSUrl::getItemid();
						if ( $Itemid ) {
							$linkURL = "index.php?option=com_obrss&task=feed&id=$row->id" . ':' . $row->alias . '&format=' . $format . '&Itemid=' . $Itemid;
						} else {
							$linkURL = "index.php?option=com_obrss&task=feed&id=$row->id" . ':' . $row->alias . '&format=' . $format;
						}
						$app    = JFactory::getApplication();
						$params = JComponentHelper::getParams( 'com_obrss' );

						if ( $params->get( 'admin_link_sef', 0 ) ) {
							$app_route = $app->getInstance( 'site' );

							$router = $app_route->getRouter();

							$newUrl  = $router->build( $linkURL );
							$linkURL = $newUrl->toString();

							$linkURL = preg_replace( '/[\s\S]*?administrator\//i', '', $linkURL );
						}

						$linkURL         = JUri::root() . $linkURL;
						$link_preview_fb = "http://feeds.feedburner.com/" . $row->uri;
						if ( $row->display_feed_module ) {
							$img_display  = 'publish_g.png';
							$alt_display  = JText::_( "OBRSS_DISPLAY" );
							$todo_display = 'undisplay_feed_module';
						} else {
							$img_display  = 'publish_x.png';
							$alt_display  = JText::_( "OBRSS_UNDISPLAY" );
							$todo_display = 'display_feed_module';
						}
						if ( $row->feeded ) {
							$img  = 'publish_g.png';
							$alt  = JText::_( "OBRSS_FEED" );
							$todo = 'unfeeded';
						} else {
							$img  = 'publish_x.png';
							$alt  = JText::_( "OBRSS_UNFEED" );
							$todo = 'feeded';
						}
						?>
						<tr class="row<?php echo $i % 2; ?>" sortable-group-id="1">
							<td class="order nowrap center hidden-phone">
								<?php
								$disableClassName = '';
								$disabledLabel = '';

								if ( ! $saveOrder ) :
									$disabledLabel    = JText::_( 'JORDERINGDISABLED' );
									$disableClassName = 'inactive tip-top';
								endif; ?>
								<span class="sortable-handler hasTooltip <?php echo $disableClassName ?>" title="<?php echo $disabledLabel ?>">
							<i class="icon-menu"></i>
						</span>
								<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="width-20 text-area-order " />
							</td>
							<td class="hidden-phone"><?php echo JHtml::_( 'grid.id', $i, $row->id ); ?></td>
							<td class="center">
								<div class="btn-group">
									<?php //echo JHtml::_('jgrid.published', $row->published, $i, '', true, 'cb'); ?>
									<?php echo JHtml::_( 'obrssadmin.published', $row->published, $i ); ?>
									<?php echo JHtml::_( 'obrssadmin.displaymodule', $row->display_feed_module, $i ); ?>
									<?php echo JHtml::_( 'obrssadmin.feeded', $row->feeded, $i ); ?>
								</div>
							</td>
							<td>
								<!-- Edit link -->
								<a href="<?php echo $link ?>" class="hasTip hasTooltip" data-original-title="<?php echo JText::_( 'OBRSS_EDIT_JLORDRSS' ); ?> <?php echo $row->name; ?>" rel="<?php echo JText::_( 'OBRSS_EDIT_JLORDRSS' ); ?> <?php echo $row->name; ?>"><?php echo htmlspecialchars( $row->name, ENT_QUOTES ) ?></a>
								<!-- Preview link -->
								<a href="<?php echo $linkURL ?>" target="_blank" class="hasTip hasTooltip" data-original-title="<?php echo JText::_( 'PREVIEW' ); ?>" rel="<?php echo JText::_( 'PREVIEW' ); ?>"><i class="fa fa-external-link" title="<?php echo JText::_( 'PREVIEW' ); ?>"></i></a>
								<?php if ( $linkURLFB ) : ?>
									<!-- Feedburner Preview link -->
									<a href="http://feeds.feedburner.com/<?php echo $linkURLFB ?>" target="_blank" class="hasTip hasTooltip" data-original-title="<?php echo JText::_( 'PREVIEWFB' ); ?>" rel="<?php echo JText::_( 'PREVIEWFB' ); ?>"><i class="fa fa-fire" title="<?php echo JText::_( 'PREVIEWFB' ); ?>"></i></a>
								<?php endif; ?>
								<!-- Alias (hide on phone devices) -->
								<span class="small hidden-phone">(<?php echo JText::_( 'OBRSS_ALIAS' ) ?>: <?php echo $row->alias; ?>)</span>
								<!-- Description (hide on phone devices) -->
								<div class="small hidden-phone"><?php echo $row->description; ?></div>
							</td>
							<td nowrap="nowrap" class="hidden-phone">
								<span class="label"><?php $plugin = explode( ".xml", $row->components );
									echo $plugin[0]; ?></span>
							</td>
							<td align="left" class="hidden-phone">
						<span class="label label-warning">
						<?php
						$feed_button = explode( ".png", $row->feed_button );
						$feed_button = $feed_button[0];
						$feed_button = str_replace( "_", " ", $feed_button );
						echo strtoupper( $feed_button );
						?>
						</span>
								<!-- <img src="<?php if ( $row->feed_button != "" ) {
									echo( JURI::root() . "components/com_obrss/images/buttons/" . $row->feed_button );
								} ?>" /> -->
							</td>
							<td align="center" class="hidden-phone">
								<?php echo $row->id; ?>
							</td>
						</tr>
						<?php $k = 1 - $k;
					}?>
					</tbody>
				</table>
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="hidemainmenu" value="0" />
				<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
				<?php echo JHTML::_( 'form.token' ); ?>
			</div>
		</div>
	</form>
</div>