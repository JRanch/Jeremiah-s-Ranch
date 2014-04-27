<?php

/************************************************************
 * @version			ticketmaster 2.5.5
 * @package			com_ticketmaster
 * @copyright		Copyright © 2009 - All rights reserved.
 * @license			GNU/GPL
 * @author			Robert Dam
 * @author mail		info@rd-media.org
 * @website			http://www.rd-media.org
 *************************************************************/

defined('_JEXEC') or die ('No Access to this file!');

jimport('joomla.application.component.controller');

class TicketmasterControllerCart extends JControllerLegacy {

	function __construct() {
		parent::__construct();
		
		$this->ordercode 	= JRequest::getInt('ordercode', 0);
		$this->remark		= JRequest::getVar('content', '');

	}
	
	function showTos(){
		
		$db = JFactory::getDBO();
		
		$sql = 'SELECT * FROM #__ticketmaster_emails WHERE emailid = 50';
	 
		$db->setQuery($sql);
		$data = $db->loadObject();	
		
		$content= '<h3>'.$data->mailsubject.'</h3>';
		$content.= '<p>'.$data->mailbody.'</p>';
		
		echo $content; 

		exit();
		
	}
	
	function saveRemark(){
		
		$db = JFactory::getDBO();
		$post = JRequest::get('post');

		## Getting the global DB session
		$session = JFactory::getSession();
		## Gettig the orderid if there is one.
		$ordercode = $session->get('ordercode');
		
		if($ordercode != $this->ordercode){
			
			$msg = '<font color="#FF0000">'.JText::_( 'COM_TICKETMASTER_SAVING_CONTENT_FAILED' ).'</font>';
			
			### Odercodes are not the same 
			$arr = array('status' => '666', 
						 'msg' => $msg);
			
			echo json_encode($arr);						
			
		}else{
			
			$model	 = $this->getModel('cart');		
	
			## Making the query for showing all the cars in list function
			$sql = 'SELECT id FROM #__ticketmaster_remarks WHERE ordercode = "'.$ordercode.'"';
		 
			$db->setQuery($sql);
			$remarks = $db->loadObject();
			
			if($remarks->id != 0) {
				$post['id'] = $remarks->id;
			}
			
			$post['remarks'] = $new_string = filter_var($this->remark, FILTER_SANITIZE_STRING); 
			$post['ordercode'] = $ordercode;			
			
			if(!$model->storeRemark($post)){
				
				$msg = '<font color="#FF0000">'.JText::_( 'COM_TICKETMASTER_SAVING_CONTENT_FAILED' ).'</font>';
				
				### Odercodes are not the same 
				$arr = array('status' => '666', 
							 'msg' => $msg);
				
				echo json_encode($arr);						
			
			}else{
				
				$arr = array('status' => '200', 
							 'msg' => JText::_( 'COM_TICKETMASTER_CONTENT_SAVED' ));
				
				echo json_encode($arr);		
								
			}
		}
	}
	
	
}	
?>
