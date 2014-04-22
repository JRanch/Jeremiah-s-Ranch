<?php
/****************************************************************
 * @version			2.5.5											
 * @package			ticketmaster									
 * @copyright		Copyright © 2009 - All rights reserved.			
 * @license			GNU/GPL											
 * @author			Robert Dam										
 * @author mail		info@rd-media.org								
 * @website			http://www.rd-media.org							
 ***************************************************************/

## no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

class ticketmasterViewEvents extends JViewLegacy {

    function display($tpl = null) {

        ## If we want the add/edit form..
        if($this->getLayout() == 'form') {
                $this->_displayForm($tpl);
                return;
        }

        ## If we want the add/edit form..
        if($this->getLayout() == 'modal') {
                $this->_displayModal($tpl);
                return;
        }			

        $db    = JFactory::getDBO();	

        ## Model is defined in the controller
        $model	= $this->getModel('events');

        ## Getting the items into a variable
        $items      = $this->get('list');
        $sold       = $this->get('sold');
		$added      = $this->get('added');
        $pagination = $this->get('pagination');

        $this->assignRef('items', $items);
        $this->assignRef('sold', $sold);
		$this->assignRef('added', $added);
        $this->assignRef('pagination', $pagination);
        parent::display($tpl);		


    }

    function _displayModal($tpl = null) {

        $db    = JFactory::getDBO();	

        ## prepare list array
        $lists = array();

        ## table ordering
        $lists['order_Dir'] = $filter_order_Dir;
        $lists['order']     = $filter_order;

        ## Model is defined in the controller
        $model	=& $this->getModel();

        ## Getting the items into a variable
        $items	=& $this->get('list');

        $this->assignRef('items', $items);
        $this->assignRef('lists', $lists);
        parent::display($tpl);

    }
	
	function _displayForm($tpl=null) {
		
		## Connecting the Database
		$db     = JFactory::getDBO();
		$model	= $this->getModel();
		$data	= $this->get('data');

		$publish = array(
			'1' => array('value' => '1', 'text' => JText::_( 'COM_TICKETMASTER_YES' )),
			'0' => array('value' => '0', 'text' => JText::_( 'COM_TICKETMASTER_NO' )),
		);	
		$lists['published'] = JHTML::_('select.genericList', $publish, 'published', ' class="inputbox" '. '', 
		'value', 'text', $data->published );		

		$this->assignRef('data', $data);
		$this->assignRef('lists', $lists);
		parent::display($tpl);
		
	}    


}
?>