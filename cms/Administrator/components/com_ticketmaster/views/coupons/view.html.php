<?php
/****************************************************************
 * @version			2.5.5											
 * @package			com_ticketmaster									
 * @copyright		Copyright © 2009 - All rights reserved.			
 * @license			GNU/GPL											
 * @author			Robert Dam										
 * @author mail		info@rd-media.org								
 * @website			http://www.rd-media.org							
 ***************************************************************/

## no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

class TicketmasterViewCoupons extends JViewLegacy {
	

	function display($tpl = null) {

		## If we want the add/edit form..
		if($this->getLayout() == 'form') {
			$this->_displayForm($tpl);
			return;
		}
	
		$db    		= JFactory::getDBO();
		$app	 	= JFactory::getApplication();			
		$model		= $this->getModel('coupons');
		
		$search		= $app->getUserStateFromRequest( 'searchbox', 'searchbox', '', 'string' );
		$search		= JString::strtolower( $search );	
		
		$lists['search']= $search;		
		
		## Getting the items into a variable
		$items	= $this->get('list');
		$config	= $this->get('config');
		$pagination = $this->get('Pagination');

		$this->assignRef('items', $items);
		$this->assignRef('config', $config);
		$this->assignRef('pagination', $pagination);
		
		parent::display($tpl);	

	
	}
		
	function _displayForm($tpl) {
		
		## Connecting the Database
		$db     = JFactory::getDBO();
		
		$app     = JFactory::getApplication();
		
		## Only perform this is error reporting is on max!
		if ($app->getCfg('error_reporting') == 'maximum'){
			error_reporting(0);
		}		
		
		$model	= $this->getModel('coupons');
		
		## Get the data for the product
		$data	= $this->get('data');
		## Get the configuration
		$config	= $this->get('config');	
									 
		$state = array(
			'0' => array('value' => '0', 'text' => JText::_( 'COM_TICKETMASTER_UNPUBLISHED' )),
			'1' => array('value' => '1', 'text' => JText::_( 'COM_TICKETMASTER_PUBLISHED' )),
		);
		$lists['published'] = JHTML::_('select.genericList', $state, 'published', ' class="inputbox" '. '', 
										'value', 'text', $data->published );							 

		$type = array(
			'0' => array('value' => '1', 'text' => JText::_( 'COM_TICKETMASTER_PERCENT' )),
			'1' => array('value' => '0', 'text' => JText::_( 'COM_TICKETMASTER_AMOUNT' )),
		);
		
		$lists['coupon_type'] = JHTML::_('select.genericList', $type, 'coupon_type', ' class="inputbox" '. '', 
										'value', 'text', $data->coupon_type );		
		
		
		$this->assignRef('data', $data);
		$this->assignRef('config', $config);
		$this->assignRef('lists', $lists);
		
		parent::display($tpl);
		
	}    

}
?>