<?php

/**
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'helpers.php');

class RatingsViewRatings extends JViewLegacy
{

	function display($tpl = null)
	{
		JToolBarHelper::title(   JText::_( 'Rating Manager' ), 'rating' );
		JToolBarHelper::deleteList(JText::_( 'Are you sure you want to delete the selected ratings' ));
		
		//cridem el CSS
		$document	= JFactory::getDocument();
		$document->addStyleSheet('components/com_muscol/assets/albums.css');

		// Get data from the model
		
		$items		= $this->get( 'Data');
		$pagination = $this->get('Pagination');
		
		$lists['order_Dir'] = $this->get('FilterOrderDir') ;
		$lists['order']     = $this->get('FilterOrder') ;
		
		$this->assignRef('lists', $lists);
		
		// push data into the template

		$this->assignRef('pagination', $pagination);
		$this->assignRef('items',		$items);

		parent::display($tpl);
	}
}