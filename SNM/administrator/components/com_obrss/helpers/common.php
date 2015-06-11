<?php
/**
 * @version          $Id: common.php 55 2014-01-22 03:50:36Z thongta $
 * @package          obRSS Feed Creator for Joomla.
 * @copyright    (C) 2007-2012 foobla.com. All rights reserved.
 * @author           foobla.com
 * @license          GNU/GPL, see LICENSE
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.filesystem.folder' );

class ObRssCommon {
	public static function loadElement( $addon ) {
		$path = JPATH_COMPONENT_SITE . DS . 'addons' . DS . $addon . DS . 'elements' . DS;
		if ( ! is_dir( $path ) ) {
			return;
		}
		$elements = JFolder::files( $path, '.php$' );
		if ( ! is_array( $elements ) && count( $elements ) < 1 ) {
			return;
		}
		foreach ( $elements as $el ) {
			include_once $path . $el;
		}
	}

	public static function getUrlContent( $url, $dates = null ) {
		if ( function_exists( 'curl_init' ) ) {
			$curl = curl_init();
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $curl, CURLOPT_URL, $url );
			curl_setopt( $curl, CURLOPT_TIMEOUT, 20 );
			curl_setopt( $curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );
			$content = curl_exec( $curl );
			curl_close( $curl );
		} else {
			$content = file_get_contents( $url );
		}

		return $content;
	}

	public static function repair_url_img( $body ) {
		$nbody = $body;
		# repair img url
		preg_match_all( '/<img\s.*?[*>]*>/', $body, $matches_img );
		if ( isset( $matches_img[0] ) ) {
			$imgtags = $matches_img[0];
			foreach ( $imgtags as $imgtag ) {
				preg_match( '/(src)=("([^"]*)")/', $imgtag, $result );
				$img       = isset( $result[3] ) ? $result[3] : '';
				$nimg      = str_replace( '../', '', $img );
				$parse_img = parse_url( $img );
				if ( ! key_exists( 'scheme', $parse_img ) && ! key_exists( 'host', $parse_img ) && $nimg != '' ) {
					$nimg  = JURI::root() . trim( $nimg, "/ " );
					$nbody = str_replace( '"' . $img . '"', '"' . $nimg . '"', $nbody );
				}
			}
		}

		return $nbody;
	}

	public static function repair_href_link( $body ) {
		$nbody = $body;
		preg_match_all( '/(href)=("([^"]*)")/', $nbody, $matches );
		if ( isset( $matches[3] ) ) {
			$hrefs = isset( $matches[3] ) ? $matches[3] : '';
			foreach ( $hrefs as $href ) {
				$nhref = str_replace( '../', '', $href );
				$parse_href = parse_url( $href );
				if ( ! key_exists( 'scheme', $parse_href ) && ! key_exists( 'host', $parse_href ) && $nhref != '' ) {
					$nhref = JURI::root() . trim( $nhref, "/ " );
					$nbody = str_replace( '"' . $href . '"', '"' . $nhref . '"', $nbody );
				}
			}
		}

		return $nbody;
	}
}