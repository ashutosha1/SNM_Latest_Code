<?php
/**
 * Shlib - Db query cache and programming library
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2012
 * @package     shlib
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     0.2.5.357
 * @date		2013-11-01
 */

// Security check to ensure this file is being included by a parent file.
defined('_JEXEC') or die;

class ShlHtmlBs_Helper
{

	/**
	 *
	 */
	public static function addBootstrapCss($document, $root = '')
	{
		$root = empty($root) ? JURI::root(true) : $root;
		$document->addStyleSheet($root . '/media/plg_shlib/css/bs.css');
		$document->addStyleSheet($root . '/media/plg_shlib/css/modal.css');
	}

	public static function addBootstrapModalFixCss($document, $root = '')
	{
		$root = empty($root) ? JURI::root(true) : $root;
		$document->addStyleSheet($root . '/media/plg_shlib/css/modalfix.css');
	}

	public static function addBootstrapJs($document, $root = '')
	{
		$root = empty($root) ? JURI::root(true) : $root;
		$document->addScript($root . '/media/plg_shlib/js/bs.js');
	}

	public static function badge($text, $type = '', $title = '', $extraClass = '')
	{
		if (empty($text))
		{
			return '';
		}
		$badged = '<span ' . (empty($title) ? '' : 'title="' . htmlspecialchars($title, ENT_COMPAT, 'UTF-8') . '" ') . 'class="badge'
			. (empty($type) ? '' : ' badge-' . strtolower($type)) . (empty($extraClass) ? '' : ' ' . strtolower($extraClass)) . '">' . $text
			. '</span>';
		return $badged;
	}

	public static function label($text, $type = '', $title = '', $extraClass = '')
	{
		if (empty($text))
		{
			return '';
		}
		$label = '<span ' . (empty($title) ? '' : 'title="' . htmlspecialchars($title, ENT_COMPAT, 'UTF-8') . '" ') . 'class="label'
			. (empty($type) ? '' : ' label-' . strtolower($type)) . (empty($extraClass) ? '' : ' ' . strtolower($extraClass)) . '">' . $text
			. '</span>';
		return $label;
	}

	public static function iconglyph($text, $type, $title = '', $prefix = "shl-")
	{
		$glyph = '<i ' . (empty($title) ? '' : 'title="' . htmlspecialchars($title, ENT_COMPAT, 'UTF-8') . '" ') . 'class="' . $prefix . 'icon-'
			. strtolower($type) . '"></i>' . $text;
		return $glyph;
	}

	/**
	 * Creates markup for an alert area, with optional classes
	 * and dismiss button
	 *
	 * @param string $text text to be displayed in alert area
	 * @param string $type bootstrap alert type: '',info,success,error
	 * @param string $dismiss if true, a dismiss button is added
	 * @param string $extraClass additional class added to the div
	 * @return string the html
	 */
	public static function alert($text, $type = '', $dismiss = false, $extraClass = '')
	{
		$alert = array();
		$alert[] = '<div class="alert' . (empty($type) ? '' : ' alert-' . strtolower($type))
			. (empty($extraClass) ? '' : ' ' . strtolower($extraClass)) . '">';
		if ($dismiss)
		{
			$alert[] = '<button type="button" class="close" data-dismiss="alert">&times;</button>';
		}
		$alert[] = $text . '</div>';
		return implode("\n", $alert);
	}

	/**
	 * Creates markup for a button, with optional type, size and disabled state
	 *
	 * @param string $text text to be displayed in alert area
	 * @param string $type bootstrap type: '', primary, info, success, warning,danger, inverse, link
	 * @param string $size bootstrap size: large, '', small, mini
	 * @param string $onclick an optional onclick event
	 * @param string $disabled if true, button is shown as disabled (though still active)
	 * @return string
	 */
	public static function button($text, $type = '', $size = '', $onclick = '', $disabled = false)
	{
		$button = array();
		$class = empty($type) ? 'btn' : ' btn-' . strtolower($type);
		$class .= empty($size) ? '' : ' btn-' . strtolower($size);
		$class .= empty($disabled) ? '' : ' disabled';

		$button[] = '<button class="' . $class . '"';
		if (!empty($onclick))
		{
			$button[] = ' onclick="' . $onclick . '"';
		}
		$button[] = '>' . $text . '</button>';
		return implode("", $button);
	}

	public static function buttonsGroup($buttons)
	{
		$group = array();
		$group[] = '<div class="btn-group">';
		foreach ($buttons as $button)
		{
			$button['text'] = empty($button['text']) ? '' : $button['text'];
			$button['type'] = empty($button['type']) ? '' : $button['type'];
			$button['size'] = empty($button['size']) ? '' : $button['size'];
			$button['onclick'] = empty($button['onclick']) ? '' : $button['onclick'];
			$button['disabled'] = empty($button['disabled']) ? '' : $button['disabled'];

			$group[] = self::button($button['text'], $button['type'], $button['size'], $button['onclick'], $button['disabled']);
		}
		$group[] = '</div>';
		return implode("\n", $group);
	}

	public static function buttonsToolbar($buttonsGroups)
	{
		$toolbar = array();
		$toolbar[] = '<div class="btn-toolbar">';
		foreach ($buttonsGroups as $group)
		{
			$toolbar[] = self::buttonsGroup($group);
		}
		$toolbar[] = '</div>';
		return implode("\n", $toolbar);
	}

	/**
	 * Method to render a Bootstrap modal
	 *
	 * @param   string  $selector  The ID selector for the modal.
	 * @param   array   $params    An array of options for the modal.
	 *
	 * @return  string  HTML markup for a modal
	 *
	 * @since   3.0
	 */
	public static function renderInputCounter($selector = 'counter', $params = array())
	{
		// Ensure the behavior is loaded
		JHtml::_('bootstrap.framework');

		$params['selector'] = $selector;
		if(!empty($params['title'])) {
			$params['title'] = JText::_($params['title'], $jssafe = true);
		}
		$paramsString = JHtml::getJSObject($params);
		$js = '
			<script>
			(function() {
						var params = ' . $paramsString . ';
						shlBootstrap.registerInputCounter(params);
						})();
			</script>';

		return $js;
	}

}