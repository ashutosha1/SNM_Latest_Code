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

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
	die('Direct Access to this location is not allowed.');
if (is_readable(JPATH_ADMINISTRATOR . '/components/com_sh404sef/sh404sef.php'))
{
	require_once 'sh404sef.php';
}
else
{
	echo 'Missing main sh404SEF file: probably corrupted installation.';
}