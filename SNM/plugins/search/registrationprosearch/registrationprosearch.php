<?php
/**
* @version		v.3.1.2 registrationprosearch $
* @package		registrationprosearch
* @copyright	Copyright @ 2011 - All rights reserved.
* @license  	GNU/GPL
* @author		JoomlaShowroom.com
* @author mail	info@JoomlaShowroom.com
* @website		www.JoomlaShowroom.com
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.plugin.plugin');

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
require_once JPATH_SITE.DS.'components'.DS.'com_registrationpro'.DS.'router.php';

error_reporting(E_ALL ^ E_NOTICE); // show all error execpt notice errors

class plgSearchRegistrationprosearch extends JPlugin
{
	/**
	 * Constructor
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config) {
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * @return array An array of search areas
	 */
	function onContentSearchAreas() {
		static $areas = array(
		'events' => 'Events'
		);
		return $areas;
	}

	/**
	 * Categories Search method
	 *
	 * The sql must return the following fields that are
	 * used in a common display routine: href, title, section, created, text,
	 * browsernav
	 * @param string Target search string
	 * @param string mathcing option, exact|any|all
	 * @param string ordering option, newest|oldest|popular|alpha|category
	 * @param mixed An array if restricted to areas, null if search all
	 */
	function onContentSearch($text, $phrase='', $ordering='', $areas=null) {
		$db		= JFactory::getDbo();
		$user	= JFactory::getUser();
		$app	= JFactory::getApplication();
		$groups	= implode(',', $user->getAuthorisedViewLevels());
		$searchText = $text;

		if (is_array($areas)) {
			if (!array_intersect($areas, array_keys($this->onContentSearchAreas()))) return array();
		}

		$limit = $this->params->def('search_limit',	50);
		
		$state = array();
		if ($sContent)  $state[]=1;
		if ($sArchived) $state[]=2;

		$text = trim($text);
		if ($text == '') return array();

		switch ( $ordering ) {

		//alphabetic, ascending
			case 'alpha':
				$order = 'a.titel ASC';
				break;

			case 'category':
				$order = 'c.catname ASC';
				break;

		//oldest first
			case 'oldest':

		//popular first
			case 'popular':

		//newest first
			case 'newest':

		//default setting: alphabetic, ascending
			default:
				$order = 'a.titel ASC';
		}

		$return = array();
		$text = $db->Quote( '%'.$db->escape( $text, true ).'%', false );
		$Itemid = 0;

		// get itemid
		$query = "SELECT id FROM #__menu WHERE link LIKE ('%index.php?option=com_registrationpro%')";
		$db->setQuery($query);
		$Itemid = $db->loadResult();

		$query = "SELECT a.id, a.dates, a.enddates, a.shortdescription as text, a.max_attendance, a.times, a.endtimes, a.titel as title, a.locid, a.status,a.shw_attendees, l.club , l.url, l.street, l.plz, l.city, l.country, l.locdescription, c.catname as section, c.id AS catid"
					. "\nFROM #__registrationpro_dates AS a"
					. "\nLEFT JOIN #__registrationpro_locate AS l ON l.id = a.locid"
					. "\nLEFT JOIN #__registrationpro_categories AS c ON c.id = a.catsid"
					. " WHERE ( a.titel LIKE ".$text
					. " OR a.shortdescription LIKE ".$text
					. " OR l.club LIKE ".$text
					. " OR l.street LIKE ".$text
					. " OR l.city LIKE ".$text
					. " OR l.country LIKE ".$text
					. " OR c.catname LIKE ".$text." )"
					. " AND a.published = 1"
					. " AND c.publishedcat = 1"
					. " AND a.access IN (". $groups .")"
					. " AND c.access IN (". $groups .")"
					. " GROUP BY a.id"
					. " ORDER BY ". $order;

		$db->setQuery( $query, 0, $limit );
		$rows = $db->loadObjectList();

		foreach($rows AS $key => $row) $rows[$key]->href = JRoute::_('index.php?option=com_registrationpro&view=event&did='.$row->id.'&Itemid='.$Itemid);

		return $rows;
	}
}
?>