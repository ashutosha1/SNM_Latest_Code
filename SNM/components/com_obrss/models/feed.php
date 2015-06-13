<?php
/**
 * @package          obRSS Feed Creator for Joomla.
 * @copyright        2007-2014 foobla.com. All rights reserved.
 * @author           foobla.com
 * @license          GNU/GPL, see LICENSE
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.model' );
jimport( 'joomla.html.parameter' );
require_once( JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'ads.php' );

class obrssModelFeed extends JModelLegacy {
	function __construct() {
		parent::__construct();
	}

	function showFeed() {
		global $option, $mainframe, $isJ25;
		$params = $mainframe->getParams();
		$force_ssl_obrr = $params->get( 'force_ssl_obrss', 2 ) ? $params->get( 'force_ssl_obrss', 2 ) : 2;
//		$dispatcher = JDispatcher::getInstance();
		$fId = JRequest::getInt( 'id' );
		if ( ! $fId ) {
			return 1;
		}
		$db       = JFactory::getDBO();
		$glConfig = JFactory::getConfig();
		$qry      = "SELECT * FROM `#__" . OB_TABLE_RSS . "` WHERE `id` = $fId";
		$db->setQuery( $qry );
		$feed = $db->LoadObject();
		if ( $feed ) {
			$qry = "UPDATE `#__" . OB_TABLE_RSS . "` SET `hits` = (`hits`+1) WHERE id = $fId";
			$db->setQuery( $qry );
			$db->query();
		} else {
			return 2;
		}
//		echo '<pre>'.print_r($feed, true).'</pre>';exit();
		$adapter = $feed->components;
//		$pathAO  = JPATH_SITE . DS . 'plugins' . DS . 'obrss' . DS . $adapter . DS . $adapter . '.php';
		$pathAO               = JPATH_SITE . DS . 'plugins' . DS . 'obrss' . DS . $adapter;
		$path_to_adapter_file = $pathAO . DS . $adapter . '.php';
		if ( is_file( $path_to_adapter_file ) ) {
			$language  = JFactory::getLanguage();
			$extension = 'plg_obrss_' . $adapter;

			if ( isset( $_GET['x913'] ) ) {
				echo "\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n"; //exit();
				var_dump( $extension );
			}

			$abc = $language->load( $extension, $pathAO, $language->getTag() );
//			echo $extension.'<br />-'.$pathAO.'<br />-'.$language->getTag();
//			var_dump( $abc );
//			exit();
			require_once $path_to_adapter_file;
			$classAdapter = 'addonRss_' . $adapter;
			if ( class_exists( $classAdapter ) ) {
				$classAdapter = new $classAdapter;
			} else {
				return 4; # The addon class not exist.!
			}
			require_once( JPATH_COMPONENT . DS . 'helpers' . DS . 'itemshelper.php' );
		} else {
			return 3;
		}
		$config = $this->feedConfig( $feed );
		$rssCf  = $config->rss;
		if ( intval( $rssCf->published ) == 0 ) {
			return 5;
		}
		$lang        = substr( $glConfig->get( 'config.language' ), 0, 2 );
		$cacheFile   = $adapter . '_' . strtolower( str_replace( '.', '', $rssCf->feed ) ) . $lang . "_" . $fId . '.xml';
		$rssCf->file = JPATH_SITE . DS . 'cache' . DS . 'com_obrss' . DS . $cacheFile;
		if ( ! $this->RssFolder( $rssCf->file ) ) {
			return 6;
		}
		// @TODO: get SEF URL & Timezone, pass it to FeedCreator
		require_once( JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'feedcreator.php' );
		$rss = new UniversalFeedCreator();

		if ( $config->item->cache && is_file( $rssCf->file ) && ! isset( $_GET['x'] ) ) {
			$rss->useCached( $rssCf->feed, $rssCf->file, $config->item->cache_time );
		}
		$rss->title                     = $feed->name;
		$rss->description               = $feed->description;
		$rss->descriptionHtmlSyndicated = true;
		$rss->link                      = $rssCf->link;
		$rss->syndicationURL            = JURI::getInstance()->toString();
		//$rss->cssStyleSheet 			= JURI::base().'components'.DS.$option.DS.'assets'.DS.'xsl'.DS.'utility.css';
		$rss->xslStyleSheet = JURI::base() . 'components' . DS . $option . DS . 'assets' . DS . 'xsl' . DS . 'atom-to-html.xsl';
		$rss->language      = $rssCf->language;
		$rss->encoding      = $rssCf->encoding;
		$rss->params        = $config;
		if ( $rssCf->image ) {
			$image              = new FeedImage();
			$image->url         = $rssCf->image;
			$image->link        = $rssCf->link;
			$image->title       = $feed->name;
			$image->description = $feed->description;
			$rss->image         = $image;
		}
		$tz = 0;
		if ( ! $isJ25 ) {
			$app = JFactory::getApplication();
			// Gets and sets timezone offset from site configuration and convert it to seconds format
			$tz             = $app->getCfg( 'offset' );
			$serverTimezone = new DateTimeZone( $tz );
			$gmtTimeZone    = new DateTimeZone( 'GMT' );
			$myDateTime     = new DateTime( date( 'r' ), $gmtTimeZone );
			$tz             = $serverTimezone->getOffset( $myDateTime ); # seconds format
		} else {
			$tzoffset = $glConfig->getValue( 'config.offset' ); # in hours
			$tz       = 3600 * $tzoffset; # in seconds
		}
		$rows = $classAdapter->getItems( $config->item );
		$ads  = obRssAds::getAds( $config->item );
		// $config	 = JFactory::getConfig();
		for ( $i = 0; $i < count( $rows ); $i ++ ) {
			$row = $rows[ $i ];
			if ( method_exists( $classAdapter, 'getTitle' ) ) {
				$title = $classAdapter->getTitle( $row, $config->item );
			} else {
				$title = htmlspecialchars( $row->title );
			}
			$desc = $classAdapter->getDesc( $row, $config->item );
			if ( $rssCf->hideimages == 1 ) {
				$desc = itemsHelper::stripTags( $desc, 'img' );
			} elseif ( $rssCf->resize_img != '0x0' ) {
				$desc = itemsHelper::resizeImg( $desc, $rssCf->resize_img );
			}
			$desc                            = itemsHelper::filterDesc( $desc, $config->item );
			if( isset($ads->type) && $ads->type == 'com_banners' ){
				$desc                            = obRssAds::addAds_com_banners( $desc, $ads, $i );
			}else{
				$desc                            = obRssAds::addAds( $desc, $ads );
			}
			$item                            = new FeedItem();
			$item->title                     = html_entity_decode( $title );
			$item->link                      = str_replace( "amp;", "", JRoute::_( $classAdapter->getLink( $row, $config->item ), true, $force_ssl_obrr ) );
			$item->description               = $desc;
			$item->descriptionHtmlSyndicated = true;

// 			Timezone debug
// 			if (isset($_GET['k'])) {
// 				echo '<pre>';
// 				echo 'tz: '.$tz.'---';
// 				print_r($config);
// 				echo '</pre>';
// 				exit('<br />Stop');
// 			}

			// Author
			$author = $params->get( 'feed_author' );
			if ( $config->item->use_global == 1 ) {
				$item->authorEmail = '';
				$item->author      = $author->author;
			} else {
				if ( $config->item->override_feedauthor == 2 ) { // Override Feed Author > Force Override (2)
					$item->author      = $config->item->mail_author;
					$item->authorEmail = '';
				} elseif ( $config->item->override_feedauthor == 0 ) {
					// Override Feed Author > NO (0)
					// Do not override Author, get Author from $row, if doesn't exist, just return null
					if ( isset( $row->author ) OR isset( $row->author_alias ) ) {
						if ( isset( $row->author_alias ) && $row->author_alias != '' ) {
							$item->author = $row->author_alias;
						} else {
							$item->author = $row->author;
						}
//						$item->author = ($row->author_alias!='') ? $row->author_alias : $row->author;
						if ( isset( $row->authorEmail ) ) {
							$item->authorEmail = $row->authorEmail;
						} else {
							$item->authorEmail = '';
						}
					}
				} else { // Override Feed Author > If Empty (1)
					// check if $row > author is empty, if so > use mail_author, otherwise, just ignore
					if ( ! isset( $row->author ) AND ! isset( $row->author_alias ) ) {
						$item->author      = $config->item->mail_author;
						$item->authorEmail = '';
					} else {
						if ( isset( $row->author_alias ) && $row->author_alias != '' ) {
							$item->author = $row->author_alias;
						} else {
							$item->author = $row->author;
						}
//						$item->author = ($row->author_alias!='') ? $row->author_alias : $row->author;
						if ( isset( $row->authorEmail ) ) {
							$item->authorEmail = $row->authorEmail;
						} else {
							$item->authorEmail = '';
						}
					}
				}
				/*
								if (isset($row->author) OR isset($row->author_alias)) {
									if (isset($row->author_alias) && $row->author_alias!='') {
										$item->author = $row->author_alias;
									} else {
										$item->author = $row->author;
									}
									#$item->author = ($row->author_alias!='') ? $row->author_alias : $row->author;
									if (isset($row->authorEmail)) {
										$item->authorEmail = $row->authorEmail;
									} else {
										$item->authorEmail = '';
									}
								} else {
									$item->author = $config->item->mail_author;
									$item->authorEmail = '';
								}*/
			}
			if ( $config->item->hidden_time == 0 ) {
//				$item->date		= strtotime($row->created)+$tz;
//				var_dump($row->created);
				$created    = isset( $row->s4rss_created ) ? intval( $row->s4rss_created ) : 0;
				$item->date = $created > 0 ? ( $created + $tz ) : '';
			} else {
				$item->date = '';
			}
			// load Additional Elments
			if ( method_exists( $classAdapter, 'loadAdditionalElements' ) ) {
				$item->additionalElements = $classAdapter->loadAdditionalElements( $item, $row, $config->item );
			}
			// load Additional Markup
			if ( method_exists( $classAdapter, 'loadAdditionalMarkup' ) ) {
				$item->additionalMarkup = "			" . $classAdapter->loadAdditionalMarkup( $item, $row, $config->item );
			}
			// load Enclosure
			if ( method_exists( $classAdapter, 'getEnclosure' ) ) {
				$enclosure = $classAdapter->getEnclosure( $row, $config->item );
				if ( $enclosure != null && ! is_array( $enclosure ) ) {
					$item->enclosure         = new EnclosureItem();
					$item->enclosure->url    = $enclosure->url;
					$item->enclosure->length = $enclosure->length;
					$item->enclosure->type   = $enclosure->type;
				} elseif ( is_array( $enclosure ) ) {
					$item->enclosures = array();
					foreach ( $enclosure as $ec ) {
						$item_enclosure         = new EnclosureItem();
						$item_enclosure->url    = $ec->url;
						$item_enclosure->length = $ec->length;
						$item_enclosure->type   = $ec->type;
						$item->enclosures[]     = $item_enclosure;
					}
				}
			}
			$rss->addItem( $item );
		}
		// Debug
		if ( isset( $_GET['y'] ) ) {
			echo '<pre>';
			echo 'tz: ' . $tz . '---';
			print_r( $config );
			echo '</pre>';
			exit( '<br />Stop' );
		}
		if ( ! $rss->saveFeed( $rssCf->feed, $rssCf->file, true ) ) {
			return 7;
		}

		return 0;
	}

	function feedConfig( $feed ) {
		$params      = new JRegistry( $feed->params );
		$date        = JFactory::getDate();
		$feedType    = JRequest::getVar( 'format', $feed->feed_type );
		$feedType    = ($feedType != $feed->feed_type) ? $feed->feed_type : $feedType;
		$feedType    = strtoupper( $feedType );
		$feedType    = in_array( $feedType, array(
				'ATOM03',
				'ATOM',
				'RSS091',
				'RSS20',
				'HTML',
				'JSON',
				'RSS10',
				'SITEMAP'
			) ) ? $feedType : 'RSS20';
		$itLimit     = intval( $params->def( 'count', 20 ) );
		$adapterPrms = new JRegistry( $feed->paramsforowncomponent );
		//config for feed items;
//		$item                      = new stdClass();
		$item                      = $adapterPrms->toObject();
		$item->menu_itemid         = $params->def( 'menu_itemid', 0 );
		$item->limit               = $itLimit > 0 ? $itLimit : 20;
		$item->hidden_time         = $params->def( 'hidden_time', 0 );
		$item->strip               = $params->def( 'strip_tags', '' );
		$item->limit_text          = $params->def( 'limit_text', 1 );
		$item->text_length         = $params->def( 'text_length', 20 );
		$item->now                 = $date->toSql();
		$item->cache               = $params->def( 'cache', 1 );
		$item->cache_time          = $params->def( 'cache_time', 3600 );
		$item->ads_from            = $params->def( 'ads_from', '' );
		$item->ads_order            = $params->def( 'ads_order', 0 );
		$item->language            = $params->def( 'feed_lang', '' );
		$item->use_global          = $params->def( 'use_global' );
		$item->mail_author         = $params->def( 'mail_author' );
		$item->override_feedauthor = $params->def( 'override_feedauthor' );

		//Config sitemap
		$sitemap            = new stdClass();
		$sitemap->frequency = $params->def( 'frequency', '' );
		$sitemap->priority  = $params->def( 'priority', '' );
		//config for feed view;
		$rss            = new stdClass();
		$rss->encoding  = 'utf-8';
		$rss->published = $feed->published;
		$rss->link      = htmlspecialchars( JURI::root() );
		//$rss->link			= JURI::getInstance()->toString(); # return to the Feed URL instead of homepage
		$rss->feed       = $feedType;
		$rss->image_file = $params->def( 'image_file', '' );
		$rss->date_format = $params->def( 'date_format', 'rfc822' );
		$rss->hideimages = $params->def( 'hideimages', 0 );
		$rss->language   = $params->def( 'feed_lang', '' );
		$size            = $params->def( 'resize_img', '0x0' );
		if ( $size != '0x0' ) {
			$size = explode( 'x', $size );
			if ( is_array( $size ) && count( $size ) == 2 ) {
				$size = array( intval( $size[0] ), intval( $size[1] ) );
			} else {
				$size = '0x0';
			}
		}
		$rss->resize_img = $size;
		$rss->image      = $rss->image_file == - 1 ? '' : JURI::root() . 'images/' . $rss->image_file;
		$rss->offsetrss  = $params->def( 'offsetrss', 1 );
		$config          = new stdClass();
		$config->rss     = $rss;
		$config->item    = $item;
		$config->sitemap = $sitemap;

		return $config;
	}

	function RssFolder( $file ) {
		jimport( 'joomla.filesystem.folder' );
		jimport( 'joomla.filesystem.file' );
		$dir = dirname( $file );
		if ( ! JFolder::exists( $dir ) ) {
			$file = $dir . DS . 'index.html';
			$txt  = '<html><body bgcolor="#FFFFFF">&nbsp;</body></html><html>';
			if ( ! JFile::write( $file, $txt ) ) {
				return false;
			}
		}

		return true;
	}
}