<?php
/**
 * @package          obRSS Feed Creator for Joomla.
 * @copyright    (C) 2007-2012 foobla.com. All rights reserved.
 * @author           foobla.com
 * @license          GNU/GPL, see LICENSE
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class addonRss_weblinks {
	/**
	 * Get Items for the Content plugin
	 *
	 * @param $itemCf
	 *
	 * @return mixed
	 */
	function getItems( $itemCf ) {
		$db           = JFactory::getDBO();
		$orderby_date = $itemCf->orderby_date == 'created' ? 'a.created' : 'a.modified';
		$orderby      = $orderby_date . ' ASC';

		$itemsTime = $itemCf->hidden_time == 1 ? 'h' : '';

		// AUTHORS
		$aus = $itemCf->author;
		if ( ! is_array( $aus ) ) {
			$aus = array( $aus );
		}
		if ( ! in_array( 0, $aus ) ) {
			$aus    = implode( ',', $aus );
			$qryAut = " AND a.created_by IN ($aus)";
		} else {
			$qryAut = '';
		}

		$qry = '
			SELECT
				a.id,
				a.title AS title,
				a.description as description,
				UNIX_TIMESTAMP( a.created ) AS ' . $itemsTime . 's4rss_created,
				a.created AS `created`,
				u.name AS author,
				a.created_by_alias AS author_alias,
				`images`,
				CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,
				CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as catslug
				FROM #__weblinks AS a
					LEFT JOIN #__users AS u ON u.id = a.created_by
					LEFT JOIN `#__categories` as c on c.id = a.catid
				WHERE
					a.state=1 ' . $qryAut;

		// CATEGORIES
		$cats = $itemCf->categories;
		if ( ! is_array( $cats ) ) {
			$cats = array( $cats );
		}
		if ( ! in_array( 0, $cats ) ) {
			$cats = implode( ',', $cats );
			$qry .= " AND a.catid IN ($cats) ";
		}
		$now      = $itemCf->now;
		$nullDate = $db->getNullDate();
		$limit    = intval( $itemCf->limit );
		if ( $limit < 1 ) {
			$limit = 30;
		}

		$qry_keywordsArr = '';

		$qry_access = '';
		if ( $itemCf->access != - 1 ) {
			$qry_access = " AND a.access <= " . $itemCf->access . " AND (c.access <= " . $itemCf->access . ")";
		}
		$qry .= $qry_access . $qry_keywordsArr . "
			AND (a.publish_up = " . $db->Quote( $nullDate ) . " OR a.publish_up <= " . $db->Quote( $now ) . ")
			AND (a.publish_down = " . $db->Quote( $nullDate ) . " OR a.publish_down >= " . $db->Quote( $now ) . ")
			ORDER BY $orderby
			LIMIT $limit
		";
		$db->setQuery( $qry );

		$rows = $db->loadObjectList();

		if ( isset( $_GET['x'] ) ) {
			echo '<pre>' . $qry . '<br>';
			#print_r($itemCf);
			echo count( $rows );
			print_r( $rows );
			echo '</pre>';
			exit();
		}

		return $rows;
	}

	/**
	 * Get Link for each Feed item
	 *
	 * @param $row
	 *
	 * @return mixed
	 */
	function getLink( $row ) {
		$idslug  = $row->slug;
		$catslug = $row->catslug;
		//$sectionlug = $row->sectionid;
		require_once( JPATH_SITE . DS . 'components' . DS . 'com_weblinks' . DS . 'helpers' . DS . 'route.php' );
		//$link = ContentHelperRoute::getArticleRoute($idslug, $catslug, $sectionlug);
		$link = WeblinksHelperRoute::getWeblinkRoute( $idslug, $catslug );

		return $link;
	}

	function getDesc( $row, $itemCf ) {
		// com_weblinks global configuration
		$app                   = JFactory::getApplication();
		$weblinks_options      = $app->getParams( 'com_weblinks' );
		$global_float_intro    = $weblinks_options->get( 'float_intro' );
		$global_float_fulltext = $weblinks_options->get( 'float_fulltext' );
		$images                = $row->images;
		$images_array          = json_decode( $images );
		$float_intro           = '';
		$float_fulltext        = '';
		if ( isset( $images_array->image_intro ) ) {
			$image_intro = $images_array->image_intro;
			$float_intro = ( $images_array->float_intro == '' ) ? $global_float_intro : $images_array->float_intro;
		} else {
			$image_intro = '';
		}
		if ( isset( $images_array->image_fulltext ) ) {
			$image_fulltext = $images_array->image_fulltext;
			$float_fulltext = ( $images_array->float_fulltext == '' ) ? $global_float_fulltext : $images_array->float_fulltext;
		} else {
			$image_fulltext = '';
		}
		// image floating for intro image
		if ( $float_intro == 'left' ) {
			$margin_intro = 'margin-right: 10px;';
		} elseif ( $float_intro == 'right' ) {
			$margin_intro = 'margin-left: 10px;';
		} else {
			$margin_intro = '';
		}
		// image floating for fulltext image
		if ( $float_fulltext == 'left' ) {
			$margin_fulltext = 'margin-right: 10px;';
		} elseif ( $float_fulltext == 'right' ) {
			$margin_fulltext = 'margin-left: 10px;';
		} else {
			$margin_fulltext = '';
		}
		$desc = '';
//		if ( $itemCf->text == 3 ) { # show only fulltext
//			if ( $image_fulltext ) :
//				$desc .= '<img src="' . JURI::root() . $image_fulltext . '" align="' . $float_fulltext . '" style="border: 5px solid #595E62;margin-bottom:10px;' . $margin_fulltext . '" />';
//			endif;
//			$desc .= $row->ftext;
//		} else {
//			if ( $image_intro ) :
//				$desc .= '<img src="' . JURI::root() . $image_intro . '" align="' . $float_intro . '" style="border: 5px solid #595E62;margin-bottom:10px;' . $margin_intro . '" />';
//			endif;
//			$desc .= $row->itext;
//			if ( $itemCf->text == 1 ) { # intro + readmore
//				$link = JRoute::_( $this->getLink( $row ), true, 2 );
//				$desc .= '<br /><a href="' . $link . '" target="_blank">Read more ...</a>';
//			} elseif ( $itemCf->text == 2 ) { # intro + fulltext
//				if ( $image_fulltext ) :
//					$desc .= '<img src="' . JURI::root() . $image_fulltext . '" align="' . $float_fulltext . '" style="border: 5px solid #595E62;margin-bottom:10px;' . $margin_fulltext . '" />';
//				endif;
//				$desc .= $row->ftext;
//			}
//		}
		$desc .= '<img src="' . JURI::root() . $image_fulltext . '" align="' . $float_fulltext . '" style="border: 5px solid #595E62;margin-bottom:10px;' . $margin_fulltext . '" />';
		$desc .= $row->description;

		return $desc;
	}
}
