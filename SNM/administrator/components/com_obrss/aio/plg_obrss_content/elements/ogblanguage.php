<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;
require_once JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'form'.DS.'fields'.DS.'list.php';
//JFormHelper::loadFieldClass('list');
class JFormFieldOgblanguage extends JFormFieldList
{
	public $type = 'Ogblanguage';
	protected function getOptions(){
		$rows	= JLanguageHelper::createLanguageList($this->value, JPATH_SITE, true, true);
		$option	= JHTML::_('select.option', '*', JText::_('JALL_LANGUAGE'), 'value', 'text');
		array_unshift($rows, $option);		
		return $rows;
	}
}
