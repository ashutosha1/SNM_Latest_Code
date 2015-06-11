<?php
/**
 * @package          obRSS - Joomla! Anything Grabber
 * @version          $Id: listfeed.php 216 2014-02-17 03:35:04Z tung $
 * @author           Tung Pham - foobla.com
 * @copyright    (c) 2007-2014 foobla.com. All rights reserved.
 * @license          GNU/GPL, see LICENSE
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once JPATH_SITE . DS . 'libraries' . DS . 'joomla' . DS . 'form' . DS . 'fields' . DS . 'list.php';

//jimport('joomla.form.form.fields.list');
class JFormFieldListfeed extends JFormFieldList {
	public $type = 'Listfeed';

	protected function getInput() {
		$html = parent::getInput();

		return $html;
	}

	protected function getOptions() {
		$lists = $this->getFeedList();

		return $lists;
	}

	public static function getFeedList() {
		$feedlist = array();
		$db       = JFactory::getDBO();
		$qr       = '
			SELECT
				`id` AS `value`, `name` AS `text`
			FROM
				`#__obrss`
			WHERE
				`published` = 1
			ORDER BY `id` ASC
		';
		$db->setQuery( $qr );
		$feeds = $db->loadObjectList();
		if ( ! $feeds ) {
			$feed        = new stdClass();
			$feed->value = '';
			$feed->text  = JText::_( 'OBRSS_SETIINGS_NONE_OPTIONS' );
			$feedlist[]  = $feed;

			return $feedlist;
		}
		foreach ( $feeds as $f ) {
			$feed        = new stdClass();
			$feed->value = $f->value;
			$feed->text  = $f->text;
			$feedlist[]  = $feed;
		}

		return $feedlist;
	}
}