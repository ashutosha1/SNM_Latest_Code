<?php
/**
 * @package          foobla RSS Feed Creator for Joomla.
 * @subpackage       : install.jlord_rss.php
 * @created          : Setember 2008.
 * @updated          : 2009/06/30
 * @copyright    (C) 2007-2012 foobla.com. All rights reserved.
 * @author           foobla
 * @license          GNU/GPL, see LICENSE
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

global $option, $obRootSite;
$models = $this->getModel( 'cpanel' );
$rows = $models->getLatestItem();
?>
<div class="well well-small">
	<div class="module-title nav-header"><?php echo JText::_( 'OBRSS_LATEST_FEEDS' ); ?></div>
	<table class="table table-striped table-condensed">
		<?php
		require_once( JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'itemshelper.php' );
		foreach ( $rows as $row ) {
			?>
			<tr>
				<td>
					<?php
					$link = JFilterOutput::ampReplace( 'index.php?option=' . $option . '&controller=feed&task=edit&cid[]=' . $row->id );
					$linkURL = $obRootSite . 'index.php?option=' . $option . '&amp;task=feed&amp;id=' . $row->id . ':' . $row->alias;
					$format = itemsHelper::getFeedTypePrefix( $row->feed_type );
					$Itemid = obRSSUrl::getItemid();
					if ( $Itemid ) {
						$linkURL = "index.php?option=com_obrss&task=feed&id=$row->id" . ':' . $row->alias . '&format=' . $format . '&Itemid=' . $Itemid;
					} else {
						$linkURL = "index.php?option=com_obrss&task=feed&id=$row->id" . ':' . $row->alias . '&format=' . $format;
					}
					$app = JFactory::getApplication();
					$params = JComponentHelper::getParams('com_obrss');

					if ( $params->get( 'admin_link_sef', 0 ) ) {
						$app_route = $app->getInstance( 'site' );

						$router = $app_route->getRouter();

						$newUrl  = $router->build( $linkURL );
						$linkURL = $newUrl->toString();

						$linkURL = preg_replace( '/[\s\S]*?administrator\//i', '', $linkURL );
					}

					$linkURL = JUri::root() . $linkURL;

					?>
					<strong class="row-title"><a href="<?php echo $link; ?>" class="hasTooltip" data-original-title="<?php echo JText::_( 'OBRSS_EDIT_JLORDRSS' ); ?> <?php echo $row->name; ?>"><?php echo $row->name; ?></a></strong>
					&nbsp;<a href="<?php echo $linkURL ?>" target="_blank" title="<?php echo JText::_( 'PREVIEW' ); ?>" class="hasTooltip"><i class="icon-out-2 small" title="<?php echo JText::_( 'PREVIEW' ); ?>"></i></a>
					<span class="small">(<?php echo JText::_( 'OBRSS_ALIAS' ) ?>: <?php echo $row->alias; ?>)</span>
					<?php

					?>
				</td>
				<td>
					<span class="small label"><?php echo $row->addon; ?></span>
				</td>
				<td>
				<span class="small"><i class="icon-calendar"></i> <?php
					$created = $row->created;
					$created = explode( ' ', $created );
					echo $created[0];
					?></span>
				</td>
			</tr>
		<?php
		}
		?>
	</table>
</div>