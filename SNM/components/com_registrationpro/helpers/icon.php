<?php
/**
* @version		v.3.2 registrationpro $
* @package		registrationpro
* @copyright	Copyright © 2009 - All rights reserved.
* @license  	GNU/GPL		
* @author		JoomlaShowroom.com
* @author mail	info@JoomlaShowroom.com
* @website		www.JoomlaShowroom.com
*
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class JHTMLIcon
{
	function create($article, $params, $access, $attribs = array())
	{
		$uri = JFactory::getURI();
		$ret = $uri->toString();	
	
		$url = 'index.php?task=new&ret='.base64_encode($ret).'&id=0&sectionid='.$article->sectionid;

		if ($params->get('show_icons')) {
			$text = JHTML::_('image.site', 'new.png', '/images/M_images/', NULL, NULL, JText::_('New') );
		} else {
			$text = JText::_('New').'&nbsp;';
		}

		$attribs	= array( 'title' => JText::_( 'New' ));
		return JHTML::_('link', JRoute::_($url), $text, $attribs);
	}

	function pdf($article, $params, $access, $attribs = array())
	{
		//$url  = 'index.php?view=article';
		//$url .=  @$article->catslug ? '&catid='.$article->catslug : '';
		//$url .= '&id='.$article->slug.'&format=pdf';
		
		$url	= "index.php?option=com_registrationpro&controller=events&task=event_report&format=pdf&cid[]=1";

		$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';

		// checks template image directory for image, if non found default are loaded
		//if ($params->get('show_icons')) {
			$text = JHTML::_('image.site', 'pdf_button.png', '/images/M_images/', NULL, NULL, JText::_('PDF'));
		//} else {
			//$text = JText::_('PDF').'&nbsp;';
		//}

		$attribs['title']	= JText::_( 'PDF' );
		$attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";
		$attribs['rel']     = 'nofollow';

		return JHTML::_('link', JRoute::_($url), $text, $attribs);
	}

	function email($article, $params, $access, $attribs = array())
	{
		$uri	=& JURI::getInstance();
		$base	= $uri->toString( array('scheme', 'host', 'port'));
		//$link	= $base.JRoute::_( ContentHelperRoute::getArticleRoute($article->slug, $article->catslug, $article->sectionid) , false );
		//$url	= 'index.php?option=com_mailto&tmpl=component&link='.base64_encode( $link );
		
		$url	= "index.php?option=com_registrationpro&controller=events&task=event_report&tmpl=component&print=1&cid[]=1";

		$status = 'width=400,height=350,menubar=yes,resizable=yes';

		//if ($params->get('show_icons')) 	{
			$text = JHTML::_('image.site', 'emailButton.png', '/images/M_images/', NULL, NULL, JText::_('Email'));
		//} else {
			$text = '&nbsp;'.JText::_('Email');
		//}

		$attribs['title']	= JText::_( 'Email' );
		$attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";

		$output = JHTML::_('link', JRoute::_($url), $text, $attribs);
		return $output;
	}
	
	function print_popup($url, $params, $access, $attribs = array())
	{		
		$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=1200,height=800,directories=no,location=no';
		
		$text 	= JHTML::_('image.site', $params['imgname'], $params['imgpath'], NULL, NULL, $params['title'] );		

		$attribs['title']	= $params['title'];
		$attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";
		$attribs['rel']     = 'nofollow';

		return JHTML::_('link', JRoute::_($url), $text, $attribs);
	}	
}
