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

defined('JPATH_PLATFORM') or die;

$fileName = JPATH_ADMINISTRATOR . '/components/com_sh404sef/pagination_' . Sh404sefHelperGeneral::getJoomlaVersionPrefix() . '.php';

if(JFile::exists($fileName))
{
	include_once $fileName;
}