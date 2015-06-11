<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2012
 * @package     sh404sef
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.2.1.1586
 * @date		2013-11-02
 */

defined('JPATH_BASE') or die;

/**
 * Displays a search input box with a search and a clear button
 */

?>

<div id="shl-main-searchbar-right-block" class="span12">
	<?php
		echo ShlMvcLayout_Helper::render('com_sh404sef.filters.search_all', $displayData->options);
		echo ShlMvcLayout_Helper::render('com_sh404sef.filters.limit_box', $displayData->pagination);
	?>
</div>