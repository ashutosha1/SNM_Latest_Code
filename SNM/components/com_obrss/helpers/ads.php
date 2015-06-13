<?php
/**
 * @version          $Id: ads.php 55 2014-01-22 03:50:36Z thongta $
 * @package          obRSS Feed Creator for Joomla.
 * @copyright    (C) 2007-2012 foobla.com. All rights reserved.
 * @author           foobla.com
 * @license          GNU/GPL, see LICENSE
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.utilities.utility' );

class obRssAds {
	public static function getAds_banner( $config ) {
		global $isJ25;
		$db  = JFactory::getDBO();
		$ads = new stdClass();
		$order = '';
		if( $config->ads_order != 0 ){
			$order = ( $config->ads_order == 1 ) ? ' ORDER BY `created` DESC' : ' ORDER BY `created` ASC';
		}
		if ( $isJ25 ) {
			$qry = "
				SELECT
					`bid` AS `id`, `name` as title, `custombannercode`, `type` as banner_type, `imageurl` as img
				FROM
					`#__banner`
				WHERE
					`showBanner` = 1
					AND (`publish_up` = " . $db->Quote( $db->getNullDate() ) . " OR `publish_up` <= NOW())
					AND (`publish_down` = " . $db->Quote( $db->getNullDate() ) . " OR `publish_down` >= NOW())
			" . $order;
		} else {
			$qry = "
				SELECT
					`id`, `name` as title, `custombannercode`, `type` as banner_type, `params` as img
				FROM
					`#__banners`
				WHERE
					`state` = 1
					AND (`publish_up` = " . $db->Quote( $db->getNullDate() ) . " OR `publish_up` <= NOW())
					AND (`publish_down` = " . $db->Quote( $db->getNullDate() ) . " OR `publish_down` >= NOW())
			" . $order;
		}
		$db->setQuery( $qry );
		$items  = $db->loadObjectList();
		$srcDir = JURI::base() . 'images/banners/';
		for ( $i = 0; $i < count( $items ); $i ++ ) {
			$link            = ( $isJ25 ) ? 'index.php?option=com_banners&task=click&bid=' . $items[$i]->id : 'index.php?option=com_banners&task=click&id=' . $items[$i]->id;
			$items[$i]->link = JRoute::_( $link );
			if ( ! $isJ25 ) {
				$image_array    = json_decode( $items[$i]->img );
				$items[$i]->img = JURI::base() . $image_array->imageurl;
			} else {
				$items[$i]->img = $srcDir . $items[$i]->img;
			}
		}
		$ads->items = $items;
		$ads->type = 'com_banners';

		return $ads;
	}

	public static function getAds_obbaner( $config ) {
		$ads             = new stdClass();
		$ad              = new stdClass();
		$ad->obrssCustom = '<h3>{obbanner}</h3>';
		$items           = array();
		$items[0]        = $ad;
		$ads->items      = $items;

		return $ads;

		return array();
	}

	public static function getAds_flexbanner( $config ) {
		$ads             = new stdClass();
		$ad              = new stdClass();
		$ad->obrssCustom = '<h3>{flexbanner}</h3>';
		$items           = array();
		$items[0]        = $ad;
		$ads->items      = $items;

		return $ads;

		return array();
	}

	public static function getAds_rsbanners( $config ) {
		$db  = JFactory::getDBO();
		$qry = "SELECT b.`ad_code` as obrssCustom FROM `#__rsbanners_ad` as b WHERE `status` = 1";
		$db->setQuery( $qry );
		$items = $db->LoadObjectList();
		$ads   = new stdClass();
		if ( ! $items ) {
			//return array();
			$ad              = new stdClass();
			$ad->obrssCustom = '<h3>{rsbanners}</h3>';
			$items           = array();
			$items[0]        = $ad;
		}
		$ads->items = $items;

		return $ads;
	}

	public static function getAds( $config ) {
		$ad_name = $config->ads_from;
		if ( $ad_name == '' ) {
			return array();
		}
		$method = 'getAds_' . $ad_name;
		$ads    = obRssAds::$method( $config );

		return $ads;
	}

	public static function addAds( $html, $ads = array() ) {
		if ( count( $ads ) < 1 ) {
			return $html;
		}
		$items = $ads->items;
		if ( count( $items ) < 1 ) {
			return $html;
		}
		$n = rand( 0, ( count( $items ) - 1 ) );
		$html .= obRssAds::renderAds( $items[$n] );

		return $html;
	}

	public static function addAds_com_banners( $html, $ads = array(), $index ) {
		$items = $ads->items;
		if ( count( $items ) < 1 || ! isset($items[$index])) {
			return $html;
		}
		$html .= obRssAds::renderAds( $items[$index] );

		return $html;
	}

	public static function renderAds( $ads ) {
		if ( isset( $ads->obrssCustom ) ) {
			return $ads->obrssCustom;
		}//echo'<pre>';print_r($ads);die;
		$html = '<div style=\"clear: both\"><a href="' . $ads->link . '"><img alt="' . $ads->title . '" src="' . $ads->img . '"/></a></div>';
		if ( isset( $ads->custombannercode ) && isset( $ads->banner_type ) && $ads->custombannercode != '' && $ads->banner_type == 1 ) {
			$html = $ads->custombannercode;
		}

		return $html;
	}
}