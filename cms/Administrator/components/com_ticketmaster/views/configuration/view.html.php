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

## No Direct Access - Kill this Script!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class TicketmasterViewConfiguration extends JViewLegacy {

function display($tpl = null) {

	## If we want the add/edit form..
	if($this->getLayout() == 'dbcheck') {
		$this->_dbcheck($tpl);
		return;
	}

	## Model is defined in the controller
	$model	=& $this->getModel();

	$config	=& $this->get('data');

	$yesno = array(
		'0' => array('value' => '0', 'text' => JText::_( 'COM_TICKETMASTER_NO' )),
		'1' => array('value' => '1', 'text' => JText::_( 'COM_TICKETMASTER_YES' )),
	);

	$lists = array();

	$lists['show_thirdaddress'] = JHTML::_('select.genericList', $yesno, 'show_thirdaddress', ' class="input" '. '', 
	'value', 'text', $config->show_thirdaddress );
	
	$lists['show_secondaddress'] = JHTML::_('select.genericList', $yesno, 'show_secondaddress', ' class="input" '. '', 
	'value', 'text', $config->show_secondaddress );

	$lists['show_eventlistnote'] = JHTML::_('select.genericList', $yesno, 'show_eventlistnote', ' class="input" '. '', 
	'value', 'text', $config->show_eventlistnote );

	$lists['payments_on'] = JHTML::_('select.genericList', $yesno, 'payments_on', ' class="input" '. '', 
	'value', 'text', $config->payments_on );	

	$lists['man_payment'] = JHTML::_('select.genericList', $yesno, 'man_payment', ' class="input" '. '', 
	'value', 'text', $config->man_payment );	

	$lists['show_cancel'] = JHTML::_('select.genericList', $yesno, 'show_cancel', ' class="input" '. '', 
	'value', 'text', $config->show_cancel );		

	$lists['variable_transcosts'] = JHTML::_('select.genericList', $yesno, 'variable_transcosts', ' class="input" '. '', 
	'value', 'text', $config->variable_transcosts );	

	$lists['payment_email_send'] = JHTML::_('select.genericList', $yesno, 'payment_email_send', ' class="input" '. '', 
	'value', 'text', $config->payment_email_send );			

	$lists['show_available_tickets'] = JHTML::_('select.genericList', $yesno, 'show_available_tickets', ' class="input" '. '', 
	'value', 'text', $config->show_available_tickets );			

	$lists['show_quantity_eventlist'] = JHTML::_('select.genericList', $yesno, 'show_quantity_eventlist', ' class="input" '. '', 
	'value', 'text', $config->show_quantity_eventlist );	

	$lists['show_price_eventlist'] = JHTML::_('select.genericList', $yesno, 'show_price_eventlist', ' class="input" '. '', 
	'value', 'text', $config->show_price_eventlist );	

	$lists['show_google_maps'] = JHTML::_('select.genericList', $yesno, 'show_google_maps', ' class="input" '. '', 
	'value', 'text', $config->show_google_maps );
	
	$lists['send_profile_mail'] = JHTML::_('select.genericList', $yesno, 'send_profile_mail', ' class="input" '. '', 
	'value', 'text', $config->send_profile_mail );	
	
	$lists['send_confirmation_pdf'] = JHTML::_('select.genericList', $yesno, 'send_confirmation_pdf', ' class="input" '. '', 
	'value', 'text', $config->send_confirmation_pdf );	
	
	$lists['show_venuebox'] = JHTML::_('select.genericList', $yesno, 'show_venuebox', ' class="input" '. '', 
	'value', 'text', $config->show_venuebox );										

	$lists['show_country'] = JHTML::_('select.genericList', $yesno, 'show_country', ' class="input" '. '', 
	'value', 'text', $config->show_country );
	
	$lists['show_birthday'] = JHTML::_('select.genericList', $yesno, 'show_birthday', ' class="input" '. '', 
	'value', 'text', $config->show_birthday );

	$lists['auto_username'] = JHTML::_('select.genericList', $yesno, 'auto_username', ' class="input" '. '', 
	'value', 'text', $config->auto_username );	
	
	$lists['show_mailchimp_signup'] = JHTML::_('select.genericList', $yesno, 'show_mailchimps', ' class="input" '. '', 
	'value', 'text', $config->show_mailchimps );		
	
	$lists['pro_installed'] = JHTML::_('select.genericList', $yesno, 'pro_installed', ' class="input" '. '', 
	'value', 'text', $config->pro_installed );	
	
	$lists['use_coupons'] = JHTML::_('select.genericList', $yesno, 'use_coupons', ' class="input" '. '', 
	'value', 'text', $config->use_coupons );	
	
	$lists['show_remark_field'] = JHTML::_('select.genericList', $yesno, 'show_remark_field', ' class="input" '. '', 
	'value', 'text', $config->show_remark_field );	
	
	$lists['show_waitinglist'] = JHTML::_('select.genericList', $yesno, 'show_waitinglist', ' class="input" '. '', 
	'value', 'text', $config->show_waitinglist );	

	$lists['show_phone'] = JHTML::_('select.genericList', $yesno, 'show_phone', ' class="input" '. '',
			'value', 'text', $config->show_phone );	

	$lists['show_zipcode'] = JHTML::_('select.genericList', $yesno, 'show_zipcode', ' class="input" '. '',
			'value', 'text', $config->show_zipcode );			
			
	$redirect_after_login = array(
		'0' => array('value' => '0', 'text' => JText::_( 'COM_TICKETMASTER_REDIRECT_PROFILE' )),
		'1' => array('value' => '1', 'text' => JText::_( 'COM_TICKETMASTER_REDIRECT_PAYMENT' )),
	);	
	

	$lists['redirect_after_login'] = JHTML::_('select.genericList', $redirect_after_login, 'redirect_after_login', ' class="input" '. '', 
	'value', 'text', $config->redirect_after_login );
	
	$lists['use_automatic_login'] = JHTML::_('select.genericList', $yesno, 'use_automatic_login', ' class="input" '. '', 
	'value', 'text', $config->use_automatic_login );
	
	$lists['remove_unfinished'] = JHTML::_('select.genericList', $yesno, 'remove_unfinished', ' class="input" '. '', 
	'value', 'text', $config->remove_unfinished );
	
	$lists['load_bootstrap_tpl'] = JHTML::_('select.genericList', $yesno, 'load_bootstrap_tpl', ' class="input" '. '', 
	'value', 'text', $config->load_bootstrap_tpl );	

	$lists['load_bootstrap'] = JHTML::_('select.genericList', $yesno, 'load_bootstrap', ' class="input" '. '', 
	'value', 'text', $config->load_bootstrap );	
	
	$lists['send_multi_ticket_admin'] = JHTML::_('select.genericList', $yesno, 'send_multi_ticket_admin', ' class="input" '. '', 
	'value', 'text', $config->send_multi_ticket_admin );	
	
	$lists['send_multi_ticket_only'] = JHTML::_('select.genericList', $yesno, 'send_multi_ticket_only', ' class="input" '. '', 
	'value', 'text', $config->send_multi_ticket_only );
	
	$lists['send_pdf_tickets'] = JHTML::_('select.genericList', $yesno, 'send_pdf_tickets', ' class="input" '. '', 
	'value', 'text', $config->send_pdf_tickets );						

	## Filling the Array() for a dropdown list.
	$jquery = array(
		'1' => array('value' => '1', 'text' => JText::_( 'COM_TICKETMASTER_JQ_LOAD_FROM_CDN_JQUERY' )),
		'2' => array('value' => '2', 'text' => JText::_( 'COM_TICKETMASTER_JQ_LOAD_LOCALLY' )),
		'3' => array('value' => '3', 'text' => JText::_( 'COM_TICKETMASTER_JQ_LOAD_IN_TEMPLATE' )),
	);
	$lists['load_jquery'] = JHTML::_('select.genericList', $jquery, 'load_jquery', 'class="input" '. '', 'value',
	'text', $config->load_jquery );

	## Filling the Array() for a dropdown list.
	$hours = array(
		'1' => array('value' => '1', 'text' => '1 '.JText::_( 'COM_TICKETMASTER_HOUR' )),
		'2' => array('value' => '2', 'text' => '2 '.JText::_( 'COM_TICKETMASTER_HOURS' )),
		'3' => array('value' => '3', 'text' => '3 '.JText::_( 'COM_TICKETMASTER_HOURS' )),
		'4' => array('value' => '4', 'text' => '4 '.JText::_( 'COM_TICKETMASTER_HOURS' )),
		'5' => array('value' => '5', 'text' => '5 '.JText::_( 'COM_TICKETMASTER_HOURS' )),
	);
	$lists['removal_hours'] = JHTML::_('select.genericList', $hours, 'removal_hours', 'class="input" ="1"'. '', 'value',
	'text', $config->removal_hours );

	## Filling the Array() for a dropdown list.
	$placeholder = array(
		'1' => array('value' => '1', 'text' => ''.JText::_( 'COM_TICKETMASTER_PLACEHOLDER1' )),
		'2' => array('value' => '2', 'text' => ''.JText::_( 'COM_TICKETMASTER_PLACEHOLDER2' )),
		'3' => array('value' => '3', 'text' => ''.JText::_( 'COM_TICKETMASTER_PLACEHOLDER3' )),
		'4' => array('value' => '4', 'text' => ''.JText::_( 'COM_TICKETMASTER_PLACEHOLDER4' )),
		'5' => array('value' => '5', 'text' => ''.JText::_( 'COM_TICKETMASTER_PLACEHOLDER5' )),
		'6' => array('value' => '6', 'text' => ''.JText::_( 'COM_TICKETMASTER_PLACEHOLDER6' )),
		'7' => array('value' => '7', 'text' => ''.JText::_( 'COM_TICKETMASTER_PLACEHOLDER7' )),
		'8' => array('value' => '8', 'text' => ''.JText::_( 'COM_TICKETMASTER_PLACEHOLDER8' )),
		'9' => array('value' => '9', 'text' => ''.JText::_( 'COM_TICKETMASTER_PLACEHOLDER9' )),
		'10' => array('value' => '10', 'text' => ''.JText::_( 'COM_TICKETMASTER_PLACEHOLDER10' )),
		'11' => array('value' => '11', 'text' => ''.JText::_( 'COM_TICKETMASTER_PLACEHOLDER11' )),
		'12' => array('value' => '12', 'text' => ''.JText::_( 'COM_TICKETMASTER_PLACEHOLDER12' )),
	);
	$lists['placeholder'] = JHTML::_('select.genericList', $placeholder, 'priceformat', 'class="input" ="1"'. '', 'value',
	'text', $config->priceformat );

	## Filling the Array() for a dropdown list.
	$currencyholder = array(
		'0' => array('value' => '1', 'text' => ''.JText::_( 'COM_TICKETMASTER_NO' )),
		'1' => array('value' => '2', 'text' => ''.JText::_( 'COM_TICKETMASTER_EURO' )),
		'2' => array('value' => '3', 'text' => ''.JText::_( 'COM_TICKETMASTER_POUND' )),
	);
	$lists['use_euros_in_pdf'] = JHTML::_('select.genericList', $currencyholder, 'use_euros_in_pdf', 'class="input" ="1"'. '', 'value',
	'text', $config->use_euros_in_pdf );

	$db    = JFactory::getDBO();
	
	$query = "SELECT emailid AS id, mailsubject AS name FROM #__ticketmaster_emails"; 
	$db->setQuery($query);
	
	$email[] = JHTML::_('select.option',  '0', JText::_( 'COM_TICKETMASTER_PLS_SELECT' ), 'id', 'name' );
	$email	 = array_merge( $email, $db->loadObjectList() );
	
	## Creating a list for the activation email.
	$lists['activation_email']  = JHTML::_('select.genericlist',  $email, 'activation_email', 'class="input" ="1" ','id', 'name', intval($config->activation_email) );	
	
	## Creating the list for terms of service page.
	$lists['tos_tpl']  = JHTML::_('select.genericlist',  $email, 'tos_tpl', 'class="input" ="1" ','id', 'name', intval($config->tos_tpl) );		
	
	$this->assignRef('config', $config);
	$this->assignRef('lists', $lists);
	parent::display($tpl);
	
	}
 
	function _dbcheck($tpl = null) {
		
		$db  = JFactory::getDBO();
		$app = JFactory::getApplication();
		
		$filter_table = $app->getUserStateFromRequest( 'table', 'table','ticketmaster_clients','cmd' );
		
		$table = '#__'.$filter_table;
		
		$sql = 'SHOW CREATE TABLE '.$table;
		
		$db->setQuery($sql);
		$rows = $db->loadAssocList();
		
		### CREATE DROPDOWN NOW ###
		
		$tables = array(
			'0' => array('value' => 'none', 'text' => JText::_( 'COM_TICKETMASTER_PLEASE_SELECT' )),
			'1' => array('value' => 'ticketmaster_clients', 'text' => JText::_( 'COM_TICKETMASTER_CLIENT_TABLE' )),
			'2' => array('value' => 'ticketmaster_config', 'text' => JText::_( 'COM_TICKETMASTER_CONFIG_TABLE' )),
			'3' => array('value' => 'ticketmaster_country', 'text' => JText::_( 'COM_TICKETMASTER_COUNTRY_TABLE' )),
			'4' => array('value' => 'ticketmaster_coupons', 'text' => JText::_( 'COM_TICKETMASTER_COUPON_TABLE' )),
			'5' => array('value' => 'ticketmaster_emails', 'text' => JText::_( 'COM_TICKETMASTER_EMAILS_TABLE' )),
			'6' => array('value' => 'ticketmaster_events', 'text' => JText::_( 'COM_TICKETMASTER_EVENTS_TABLE' )),
			'7' => array('value' => 'ticketmaster_orders', 'text' => JText::_( 'COM_TICKETMASTER_ORDERS_TABLE' )),
			'8' => array('value' => 'ticketmaster_remarks', 'text' => JText::_( 'COM_TICKETMASTER_REMARKS_TABLE' )),
			'9' => array('value' => 'ticketmaster_scans', 'text' => JText::_( 'COM_TICKETMASTER_SCANS_TABLE' )),
			'10' => array('value' => 'ticketmaster_tickets', 'text' => JText::_( 'COM_TICKETMASTER_TICKETS_TABLE' )),
			'11' => array('value' => 'ticketmaster_transactions', 'text' => JText::_( 'COM_TICKETMASTER_TRANSACTIONS_TABLE' )),
			'12' => array('value' => 'ticketmaster_transactions_temp', 'text' => JText::_( 'COM_TICKETMASTER_TEMPTRANSACTIONS_TABLE' )),
			'13' => array('value' => 'ticketmaster_venues', 'text' => JText::_( 'COM_TICKETMASTER_VENUES_TABLE' )),
			'14' => array('value' => 'ticketmaster_waitinglist', 'text' => JText::_( 'COM_TICKETMASTER_WAITINGLIST_TABLE' )),
		);
		$lists['tables'] = JHTML::_('select.genericList', $tables, 'table', 'class="input" '. '', 'value','text', $filter_table);		
		
		$remote_db["database"] = file_get_contents('http://rd-media.org/tablechecker/'.$filter_table.'.txt', true);		
		
		$this->assignRef('remote', $remote_db);
		$this->assignRef('local', $rows);
		$this->assignRef('lists', $lists);	
		
		parent::display($tpl);
	}
	
}
?>
