<?php

## Check if the file is included in the Joomla Framework
defined('_JEXEC') or die ('No Acces to this file!');

## Helper file for what you can do.
require_once JPATH_COMPONENT.'/helpers/ticketmaster.php';
$canDo	= ticketmasterHelper::getActions($empty=0);
$user	= JFactory::getUser();


## Only super admin can save this :)
if ($canDo->get('core.admin')) {
	JToolBarHelper::save();
	JToolBarHelper::divider();
}

JToolBarHelper::back();

## Include the toolbars for saving.
JToolBarHelper::title( JText::_( 'COM_TICKETMASTER_CONFIG' ), 'config.png');	
## Make sure the user is authorized to click the save button

## Get document type and add it.
$document = JFactory::getDocument();
$document->addScript('https://code.jquery.com/jquery-latest.js');
## Add the fancy lightbox for information fields.
$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/lightbox/mediabox.js');
$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/lightbox/mediabox.css' );

## Check if this is Joomla 2.5 or 3.0.+
$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');

if(!$isJ30) {
	## Adding mootools for J!2.5
	JHTML::_('behavior.modal');
	## Include the tooltip behaviour.
	JHTML::_('behavior.tooltip', '.hasTip');
	$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' ); 
	$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/js/bootstrap.js');
}	

## Require specific menu-file.
$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmasterext'.DS.'assets'.DS.'menu.php';
if (file_exists($path)) {
	include_once $path;
}else{
	$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'menu.php';
	include_once $path;
}
?>



<form action = "index.php" method="POST" name="adminForm" id="adminForm" enctype="multipart/form-data">

<div class="row-fluid">
  <div class="span6">
 
    <h3 style="color:#009; font-size:110%;"><?php echo JText::_( 'COM_TICKETMASTER_TPL_SETTINGS' ); ?></h3>
  
    <table class="table table-striped">
      <tr>
        <td width="50%" class="key"><?php echo JText::_( 'COM_TICKETMASTER_LOAD_BOOTSTRAP_TPL' ); ?></td>
        <td width="50%"><?php echo $this->lists['load_bootstrap_tpl']; ?>
            <a href="#load_bootstrap_tpl_desc" role="button" class="btn pull-right" data-toggle="modal">
            	<img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>          
        </td>
      </tr>
      <tr>
        <td width="50%" class="key"><?php echo JText::_( 'COM_TICKETMASTER_LOAD_BOOTSTRAP' ); ?></td>
        <td width="50%"><?php echo $this->lists['load_bootstrap']; ?>
            <a href="#load_bootstrap_desc" role="button" class="btn pull-right" data-toggle="modal">
            	<img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>          
        </td>
      </tr>
      <tr>
        <td width="50%" class="key"><?php echo JText::_( 'COM_TICKETMASTER_LOAD_JQUERY' ); ?></td>
        <td width="50%"><?php echo $this->lists['load_jquery']; ?>
            <a href="#load_jquery_desc" role="button" class="btn pull-right" data-toggle="modal">
            	<img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>          
        </td>
      </tr>              
     </table> 
  	
    <h3 style="color:#009; font-size:110%;"><?php echo JText::_( 'COM_TICKETMASTER_SETTINGS' ); ?></h3>
  
    <table class="table table-striped">
      <tr>
        <td width="50%" class="key"><?php echo JText::_( 'COM_TICKETMASTER_PRO_INSTALLED' ); ?></td>
        <td width="50%"><?php echo $this->lists['pro_installed']; ?>
            <a href="#mb_inline_pro" role="button" class="btn pull-right" data-toggle="modal">
            	<img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>          
        </td>
      </tr>
      <tr>
        <td class="key"><?php echo JHTML::_('tooltip', JText::_( 'COM_TICKETMASTER_INFO_CURRENCY' ), null , null, 
										JText::_( 'COM_TICKETMASTER_CURRENCY' )); ?></td>
        <td ><input name="valuta" type="text" id="valuta" size="3" value="<?php echo $this->config->valuta; ?>" /></td>
      </tr>
      <tr>
        <td  class="key"><?php echo JHTML::_('tooltip', JText::_( 'COM_TICKETMASTER_DATEFORMAT_INFO' ), null , null, 
		JText::_( 'COM_TICKETMASTER_DATEFORMAT' )); ?></td>
        <td><input name="dateformat" type="text" id="dateformat" size="3" value="<?php echo $this->config->dateformat; ?>" /></td>
      </tr>
      <tr>
        <td  class="key"><?php echo JHTML::_('tooltip', JText::_( 'COM_TICKETMASTER_SHOWPRICES' ), null , null, 
		JText::_( 'COM_TICKETMASTER_PRICES' )); ?></td>
        <td><?php echo $this->lists['placeholder']; ?></td>
      </tr>             
      <tr>
        <td class="key"><?php echo JHTML::_('tooltip', JText::_( 'COM_TICKETMASTER_TXTSHOWNOTE' ), null ,null, 
		JText::_( 'COM_TICKETMASTER_SHOW_NOTE_EVENT' )); ?></td>
        <td><?php echo $this->lists['show_eventlistnote']; ?></td>
      </tr> 
      <tr>
        <td class="key"><?php echo JHTML::_('tooltip', JText::_( 'COM_TICKETMASTER_TXTSHOWAVAILABLE' ), null ,null, 
		JText::_( 'COM_TICKETMASTER_SHOWAVAILABILLITY' )); ?></td>
        <td><?php echo $this->lists['show_available_tickets']; ?></td>
      </tr> 
      <tr>
        <td class="key"><?php echo JHTML::_('tooltip', JText::_( 'COM_TICKETMASTER_TXTSHOWQUANTITY' ), null ,null, 
		JText::_( 'COM_TICKETMASTER_SHOWQUANTITY' )); ?></td>
        <td><?php echo $this->lists['show_quantity_eventlist']; ?></td>
      </tr> 
      <tr>
        <td class="key"><?php echo JHTML::_('tooltip', JText::_( 'COM_TICKETMASTER_TXTSHOWPRICE' ),null ,null,
		JText::_( 'COM_TICKETMASTER_SHOWPRICEEVENT' )); ?></td>
        <td><?php echo $this->lists['show_price_eventlist']; ?></td>
      </tr> 
      <tr>
        <td class="key"><?php echo JText::_( 'COM_TICKETMASTER_SHOW_COUPON_SYSTEM' ); ?></td>
        <td><?php echo $this->lists['use_coupons']; ?></td>
      </tr>  
      <tr>
        <td class="key"><?php echo JText::_( 'COM_TICKETMASTER_SHOW_REMARKS_IN_CART' ); ?></td>
        <td><?php echo $this->lists['show_remark_field']; ?>
            <a href="#mb_inline_remarks_on" role="button" class="btn pull-right" data-toggle="modal">
            	<img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>         
        </td>
      </tr>   
      <tr>
        <td class="key"><?php echo JText::_( 'COM_TICKETMASTER_SHOW_WAITING_LIST' ); ?></td>
        <td><?php echo $this->lists['show_waitinglist']; ?></td>
      </tr>                                           
     </table>  
     
     <h3 style="color:#009; font-size:110%;"><?php echo JText::_( 'COM_TICKETMASTER_ESETTINGS' ); ?></h3>
     
    <table width="99%" class="table table-striped">
      <tr>
        <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_UPDATE_ON_PAYMENT' ); ?></td>
        <td width="50%"><?php echo $this->lists['payment_email_send']; ?></td>
      </tr>       
      <tr>
        <td class="key">
		<?php echo JHTML::_('tooltip', JText::_( 'COM_TICKETMASTER_PER_SENDING' ), null , null, JText::_( 'COM_TICKETMASTER_BATCH' )); ?></td>
        <td><input name="persending" type="text" id="persending" size="3" value="<?php echo $this->config->persending; ?>" /></td>
      </tr> 
      <tr>
        <td class="key">
		<?php echo JText::_( 'COM_TICKETMASTER_REMOVE_AFTER_X_DAYS' ); ?></td>
        <td>
        	<input name="removal_days" type="text" id="removal_days" size="3" value="<?php echo $this->config->removal_days; ?>" />
            <a href="#mb_inline_remove" role="button" class="btn pull-right" data-toggle="modal">
            	<img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>                        
        </td>
      </tr>  
      <tr>
        <td class="key">
		<?php echo JText::_( 'COM_TICKETMASTER_AUTO_REMOVE_AFTER_X_HOURS' ); ?></td>
        <td>
        	<?php echo $this->lists['remove_unfinished']; ?>
            <a href="#mb_inline_remove" role="button" class="btn pull-right" data-toggle="modal">
            	<img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>                        
        </td>
      </tr>
      <tr>
        <td class="key">
		<?php echo JText::_( 'COM_TICKETMASTER_AUTO_REMOVE_X_HOURS' ); ?></td>
        <td><?php echo $this->lists['removal_hours']; ?></td>
      </tr>                          
   </table>  
   
   <h3 style="color:#009; font-size:110%;"><?php echo JText::_( 'COM_TICKETMASTER_PSETTINGS' ); ?></h3>

    <table width="99%" class="table table-striped">
      <tr>
        <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_CHOOSE_TOS_PAGE' ); ?></td>
        <td width="50%">
			<?php echo $this->lists['tos_tpl']; ?>
            <a href="#mb_inline_termsofservice" role="button" class="btn pull-right" data-toggle="modal">
            	<img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>                      
        </td>
      </tr>
      <tr>
        <td width="32%"  class="key">
		<?php echo JHTML::_('tooltip', JText::_( 'COM_TICKETMASTER_TXTVARCOST' ), null , null, 
		JText::_( 'COM_TICKETMASTER_VARTRANSACTIONCOSTS' )); ?></td>
        <td width="68%"><?php echo $this->lists['variable_transcosts']; ?></td>
      </tr>        
      <tr>
        <td  class="key"><?php echo JHTML::_('tooltip', JText::_( 'COM_TICKETMASTER_TXTPERCENTAGE' ), null , null, 
		JText::_( 'COM_TICKETMASTER_VARCOSTS' )); ?></td>
        <td><input name="transcosts" type="text" id="transcosts" size="5" 
        value="<?php echo $this->config->transcosts; ?>" /> 
          %</td>
      </tr>
      <tr>
        <td  class="key">
		<?php echo JHTML::_('tooltip', JText::_( 'COM_TICKETMASTER_TXTTRANSACTIONCOSTS' ), null , null, 
		JText::_( 'COM_TICKETMASTER_TRANSACTIONCOSTS' )); ?></td>
        <td><input name="transactioncosts" type="text" id="transactioncosts" size="5" 
        value="<?php echo $this->config->transactioncosts; ?>" /></td>
      </tr>
      <tr>
        <td  class="key">
		<?php echo JText::_( 'COM_TICKETMASTER_SPECIALCHAR_IN_PDF' ); ?></td>
        <td><?php echo $this->lists['use_euros_in_pdf']; ?></td>
      </tr>
    </table> 
    
    <h3 style="color:#009; font-size:110%;"><?php echo JText::_( 'COM_TICKETMASTER_EVENTPAGE_SETTINGS' ); ?></h3>
    
    <table width="99%" class="table table-striped">
      <tr>
        <td width="50%"  class="key">
			<?php echo JText::_( 'COM_TICKETMASTER_SHOW_VENUEBOX' ); ?>
        </td>
        <td width="50%">
        	<?php echo $this->lists['show_venuebox']; ?>  
            <a href="#mb_inline_venuebox" role="button" class="btn pull-right" data-toggle="modal">
            	<img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>                                     
        </td>
      </tr>
      <tr>
        <td>
			<?php echo JText::_( 'COM_TICKETMASTER_SHOW_GOOGLE_MAPS' ); ?>
        </td>
        <td>
        	<?php echo $this->lists['show_google_maps']; ?>              
        </td>
      </tr> 
      <tr>
        <td>
			<?php echo JText::_( 'COM_TICKETMASTER_SHOW_GOOGLE_MAPS_API_KEY' ); ?>
        </td>
        <td>
        	<input name="google_maps_key" type="text" id="google_maps_key" size="5" 
        value="<?php echo $this->config->google_maps_key; ?>" />  
            <a href="#mb_inline_google_api" role="button" class="btn pull-right" data-toggle="modal">
            	<img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>                    
        </td>
      </tr>            
    </table> 

  </div>
  <div class="span6">
  
	<h3 style="color:#009; font-size:110%;"><?php echo JText::_( 'COM_TICKETMASTER_CHECKOUT_SETTINGS' ); ?></h3>  

    <table width="99%" class="table table-striped">
      <tr>
        <td width="50%"  class="key">
		<?php echo JText::_( 'COM_TICKETMASTER_CHOOSE_REDIRECTION_LOGIN' ); ?></td>
        <td width="50%">
			<?php echo $this->lists['redirect_after_login']; ?>
            <a href="#mb_inline_login" role="button" class="btn pull-right" data-toggle="modal">
            	<img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>                      
        </td>
      </tr>
      <tr>
        <td>
		<?php echo JText::_( 'COM_TICKETMASTER_CONFIRMATIONMAIL_AFTER_PROFILECHECK' ); ?></td>
        <td>
			<?php echo $this->lists['send_profile_mail']; ?>
            <a href="#mb_inline_mail_after_profile_check" role="button" class="btn pull-right" data-toggle="modal">
            	<img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>                       
        </td>
      </tr> 
      <tr>
        <td>
		<?php echo JText::_( 'COM_TICKETMASTER_AUTOLOGIN_ON_OFF' ); ?></td>
        <td>
			<?php echo $this->lists['use_automatic_login']; ?>
            <a href="#mb_inline_use_automatic_login" role="button" class="btn pull-right" data-toggle="modal">
            	<img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>                      
        </td>
      </tr>
      <tr>
        <td>
		<?php echo JText::_( 'COM_TICKETMASTER_AUTO_USERNAME' ); ?></td>
        <td>
			<?php echo $this->lists['auto_username']; ?>
            <a href="#mb_inline_auto_username" role="button" class="btn pull-right" data-toggle="modal">
            	<img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>                       
        </td>
      </tr>                   
      <tr>
        <td>
		<?php echo JText::_( 'COM_TICKETMASTER_CHOOSE_RACTIVATION_EMAIL' ); ?></td>
        <td>
			<?php echo $this->lists['activation_email']; ?>
            <a href="#mb_inline_activation_email" role="button" class="btn pull-right" data-toggle="modal">
            	<img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>                     
        </td>
      </tr> 
      <tr>
        <td  class="key"><?php echo JText::_( 'COM_TICKETMASTER_SHOW_2ND_ADDRESS' ); ?></td>
        <td><?php echo $this->lists['show_secondaddress']; ?></td>
      </tr>
      <tr>
        <td  class="key"><?php echo JText::_( 'COM_TICKETMASTER_SHOW_3RD_ADDRESS' ); ?></td>
        <td><?php echo $this->lists['show_thirdaddress']; ?></td>
      </tr>  
      <tr>
        <td  class="key"><?php echo JText::_( 'COM_TICKETMASTER_SHOW_ZIPCODE' ); ?></td>
        <td><?php echo $this->lists['show_zipcode']; ?></td>
      </tr>            
      <tr>
        <td  class="key"><?php echo JText::_( 'COM_TICKETMASTER_SHOW_COUNTRY' ); ?></td>
        <td><?php echo $this->lists['show_country']; ?></td>
      </tr> 
      <tr>
        <td  class="key"><?php echo JText::_( 'COM_TICKETMASTER_SHOW_PHONE' ); ?></td>
        <td><?php echo $this->lists['show_phone']; ?></td>
      </tr>           
      <tr>
        <td  class="key"><?php echo JText::_( 'COM_TICKETMASTER_SHOW_DAYOFBIRTH' ); ?></td>
        <td><?php echo $this->lists['show_birthday']; ?></td>
      </tr>                      
    </table>
    
    <h3 style="color:#009; font-size:110%;"><?php echo JText::_( 'COM_TICKETMASTER_NEWSLETTER_SIGNUP_SETTINGS' ); ?></h3>
    
    <table width="99%" class="table table-striped">
      <tr>
        <td width="50%"  class="key">
			<?php echo JText::_( 'COM_TICKETMASTER_SHOW_MAILCHIMP' ); ?>
        </td>
        <td width="50%">
        	<?php echo $this->lists['show_mailchimp_signup']; ?>              
        </td>
      </tr>
      <tr>
        <td>
			<?php echo JText::_( 'COM_TICKETMASTER_MAILCHIMP_APIKEY' ); ?>
        </td>
        <td>
        	<input name="mailchimp_api" type="text" id="mailchimp_api" size="40" value="<?php echo $this->config->mailchimp_api; ?>" />
            <a href="#mb_inline_mailchimp" role="button" class="btn pull-right" data-toggle="modal">
            	<img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>                           
        </td>
      </tr>
      <tr>
        <td>
			<?php echo JText::_( 'COM_TICKETMASTER_MAILCHIMP_LISTID' ); ?>
        </td>
        <td>
        	<input name="mailchimp_listid" type="text" id="mailchimp_listid" size="40" value="<?php echo $this->config->mailchimp_listid; ?>" />
            <a href="#mb_inline_mailchimp_list" role="button" class="btn pull-right" data-toggle="modal">
            	<img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>                           
        </td>
      </tr>      
    </table> 
    
    <h3 style="color:#009; font-size:110%;"><?php echo JText::_( 'COM_TICKETMASTER_PDF_SETTINGS_NEW' ); ?></h3>
    
    <table width="99%" class="table table-striped">
      <tr>
        <td width="50%"  class="key"><?php echo JText::_( 'COM_TICKETMASTER_SEND_PDF_IN_CONFIRMATION' ); ?></td>
        <td width="50%"><?php echo $this->lists['send_confirmation_pdf']; ?>
            <a href="#mb_inline_pdfinconfirmation" role="button" class="btn pull-right" data-toggle="modal">
            	<img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>                        
        </td>
      </tr>
      <tr>
        <td><?php echo JText::_( 'COM_TICKETMASTER_SEND_MULTI_TICKET_ONLY' ); ?></td>
        <td><?php echo $this->lists['send_multi_ticket_only']; ?>
            <a href="#mb_send_multi_ticket_only" role="button" class="btn pull-right" data-toggle="modal">
            	<img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>          
        </td>
      </tr>
      <tr>
        <td><?php echo JText::_( 'COM_TICKETMASTER_TURN_OFF_PDF_TICKETS_EMAIL' ); ?></td>
        <td><?php echo $this->lists['send_pdf_tickets']; ?>
            <a href="#mb_send_pdf_tickets" role="button" class="btn pull-right" data-toggle="modal">
            	<img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>          
        </td>
      </tr>
      <tr>
        <td><?php echo JText::_( 'COM_TICKETMASTER_SEND_MULTI_TICKET_TO_ADMIN' ); ?></td>
        <td><?php echo $this->lists['send_multi_ticket_admin']; ?>
            <a href="#mb_send_multi_ticket_admin" role="button" class="btn pull-right" data-toggle="modal">
            	<img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>          
        </td>
      </tr>
      </table>
      
	<div class="accordion" id="accordion2">
	  <div class="accordion-group">
	    <div class="accordion-heading">
	      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
	        <?php echo JText::_( 'COM_TICKETMASTER_SETTING_UP_DEFAULT_TICKET_POSITIONS' ); ?>
	      </a>
	    </div>
	    <div id="collapseOne" class="accordion-body collapse">
	      <div class="accordion-inner">
	      
		    <table width="99%" class="table table-striped">
		      <tr>
		        <td width="50%">
					<?php echo JText::_( 'COM_TICKETMASTER_EVENTNAME_POSITION' ); ?>
		        </td>
		        <td width="50%">
		        	<input name="eventname_position" type="text" id="eventname_position" size="5" 
		        			value="<?php echo $this->config->eventname_position; ?>" />                   
		        </td>
		      </tr>  
		        <td>
					<?php echo JText::_( 'COM_TICKETMASTER_DATE_POSITION' ); ?>
		        </td>
		        <td>
		        	<input name="date_position" type="text" id="date_position" size="5" 
		        			value="<?php echo $this->config->date_position; ?>" />                   
		        </td>
		      </tr>  
		      <tr>
		        <td>
					<?php echo JText::_( 'COM_TICKETMASTER_LOCATION_POSITION' ); ?>
		        </td>
		        <td>
		        	<input name="location_position" type="text" id="location_position" size="5" 
		        			value="<?php echo $this->config->location_position; ?>" />                   
		        </td>
		      </tr> 
		      <tr>
		        <td>
					<?php echo JText::_( 'COM_TICKETMASTER_ORDERID_POSITION' ); ?>
		        </td>
		        <td>
		        	<input name="orderid_position" type="text" id="orderid_position" size="5" 
		        			value="<?php echo $this->config->orderid_position; ?>" />                   
		        </td>
		      </tr> 
		      <tr>
		        <td>
					<?php echo JText::_( 'COM_TICKETMASTER_ORDERNUMBER_POSITION' ); ?>
		        </td>
		        <td>
		        	<input name="ordernumber_position" type="text" id="ordernumber_position" size="5" 
		        			value="<?php echo $this->config->ordernumber_position; ?>" />                   
		        </td>
		      </tr> 
		      <tr>
		        <td>
					<?php echo JText::_( 'COM_TICKETMASTER_PRICE_POSITION' ); ?>
		        </td>
		        <td>
		        	<input name="price_position" type="text" id="price_position" size="5" 
		        			value="<?php echo $this->config->price_position; ?>" />                   
		        </td>
		      </tr> 
		      <tr>
		        <td>
					<?php echo JText::_( 'COM_TICKETMASTER_BARCODE_POSITION' ); ?>
		        </td>
		        <td>
		        	<input name="bar_position" type="text" id="bar_position" size="5" 
		        			value="<?php echo $this->config->bar_position; ?>" />                   
		        </td>
		      </tr> 
		      <tr>
		        <td>
					<?php echo JText::_( 'COM_TICKETMASTER_CLIENTNAME_POSITION' ); ?>
		        </td>
		        <td>
		        	<input name="name_position" type="text" id="name_position" size="5" 
		        			value="<?php echo $this->config->name_position; ?>" />                   
		        </td>
		      </tr> 
		      <tr>
		        <td>
					<?php echo JText::_( 'COM_TICKETMASTER_ORDERDATE_POSITION' ); ?>
		        </td>
		        <td>
		        	<input name="orderdate_position" type="text" id="orderdate_position" size="5" 
		        			value="<?php echo $this->config->orderdate_position; ?>" />                   
		        </td>
		      </tr> 
		      <tr>
		        <td>
					<?php echo JText::_( 'COM_TICKETMASTER_SEATNUMBER_POSITION' ); ?>
		        </td>
		        <td>
		        	<input name="position_seatnumber" type="text" id="position_seatnumber" size="5" 
		        			value="<?php echo $this->config->position_seatnumber; ?>" />                   
		        </td>
		      </tr> 
		      <tr>
		        <td>
					<?php echo JText::_( 'COM_TICKETMASTER_FREETEXT1_POSITION' ); ?>
		        </td>
		        <td>
		        	<input name="free_text1_position" type="text" id="free_text1_position" size="5" 
		        			value="<?php echo $this->config->free_text1_position; ?>" />                   
		        </td>
		      </tr> 	
		      <tr>
		        <td>
					<?php echo JText::_( 'COM_TICKETMASTER_FREETEXT2_POSITION' ); ?>
		        </td>
		        <td>
		        	<input name="free_text2_position" type="text" id="free_text2_position" size="5" 
		        			value="<?php echo $this->config->free_text2_position; ?>" />                   
		        </td>
		      </tr> 		      		      		       		                 
		    </table> 
		    
	      </div>
	    </div>
	  </div>
	  
	  
	  <div class="accordion-group">
	    <div class="accordion-heading">
	      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseThree">
	        <?php echo JText::_( 'COM_TICKETMASTER_ADDRESS_FORMAT_COMPANY_DATA' ); ?>
	      </a>
	    </div>
	    <div id="collapseThree" class="accordion-body collapse">
	      <div class="accordion-inner">
	      
				<div class="row-fluid">
				  <div class="span6">
				  	 <textarea rows="7" name="address_format_company" id="address_format_company" style="width:100%;"><?php echo $this->config->address_format_company; ?></textarea>
				  </div>
				  <div class="span6" style="padding-left:5px;">
					 
					 
					 <div class="alert alert-info">
					    <h4 style="margin-bottom:8px;"><?php echo JText::_( 'COM_TICKETMASTER_INFORMATION' ); ?></h4>
					    <?php echo JText::_( 'COM_TICKETMASTER_ADDRESS_FORMAT_COMPANY_DATA_DESC' ); ?>	
					    <?php echo JText::_( 'COM_TICKETMASTER_ADDRESS_FORMAT_COMPANY_DATA_NO_FILLED' ); ?>
					</div>
					 			  	
				  </div>
				</div> 
		    
	      </div>
	    </div>
	  </div>	  
	  
	  
	  <div class="accordion-group">
	    <div class="accordion-heading">
	      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
	        <?php echo JText::_( 'COM_TICKETMASTER_ADDRESS_FORMAT' ); ?>
	      </a>
	    </div>
	    <div id="collapseTwo" class="accordion-body collapse">
	      <div class="accordion-inner">

				<div class="row-fluid">
				  <div class="span6">
				  	 <textarea rows="9" name="address_format_client" id="address_format_client" style="width:100%;"><?php echo $this->config->address_format_client; ?></textarea>
				  </div>
				  <div class="span6" style="padding-left:5px;">

				  	<table width="99%" class="table">
				  		<tr>
				  			<td width="50%">%%FIRSTNAME%%</td>
				  			<td width="50%" align="right">%%LASTNAME%%</td>
				  		</tr>
				  		<tr>
				  			<td width="50%">%%ADDRESS1%%</td>
				  			<td width="50%" align="right">%%ADDRESS2%%</td>
				  		</tr>
				  		<tr>
				  			<td width="50%">%%ZIPCODE%%</td>
				  			<td width="50%" align="right">%%CITY%%</td>
				  		</tr>
				  		<tr>
				  			<td width="50%">%%COUNTRY_FULL%%</td>
				  			<td width="50%" align="right">%%EMAIL%%</td>
				  		</tr>
				  		<tr>
				  			<td width="50%">%%COUNTRY_2D%%</td>
				  			<td width="50%" align="right">%%COUNTRY_3D%%</td>
				  		</tr>				  									  						  		
				  	</table>
				  	
				  </div>
				</div>  
	      </div>	      
	      
	    </div>
	  </div>
	</div>        
      
      <h3 style="color:#009; font-size:110%;"><?php echo JText::_( 'COM_TICKETMASTER_COMPANY_SETTINGS' ); ?></h3>
      
      <table width="99%" class="table table-striped">            
      <tr>
        <td><?php echo JText::_( 'COM_TICKETMASTER_COMPANYNAME' ); ?></td>
        <td><input name="companyname" type="text" id="companyname" size="40" 
        value="<?php echo $this->config->companyname; ?>" /></td>
      </tr>
      <tr>
        <td><?php echo JText::_( 'COM_TICKETMASTER_ADDRESS1' ); ?></td>
        <td><input name="address1" type="text" id="address1" size="40" 
        value="<?php echo $this->config->address1; ?>" /></td>
      </tr>
      <tr>
        <td><?php echo JText::_( 'COM_TICKETMASTER_ZIPCODE' ); ?></td>
        <td><input name="zipcode" type="text" id="zipcode" size="40" 
        value="<?php echo $this->config->zipcode; ?>" /></td>
      </tr>
      <tr>
        <td><?php echo JText::_( 'COM_TICKETMASTER_CITY' ); ?></td>
        <td><input name="city" type="text" id="city" size="40" 
        value="<?php echo $this->config->city; ?>" /></td>
      </tr>
      <tr>
        <td><?php echo JText::_( 'COM_TICKETMASTER_PHONE' ); ?></td>
        <td><input name="phone" type="text" id="phone" size="40" 
        value="<?php echo $this->config->phone; ?>" /></td>
      </tr>
      <tr>
        <td><?php echo JText::_( 'COM_TICKETMASTER_EMAIL' ); ?></td>
        <td><input name="email" type="text" id="email" size="40" 
        value="<?php echo $this->config->email; ?>" /></td>
      </tr>
      <tr>
        <td><?php echo JText::_( 'COM_TICKETMASTER_WEBSITE' ); ?></td>
        <td><input name="website" type="text" id="website" size="40" 
        value="<?php echo $this->config->website; ?>" /></td>
      </tr>
      <tr>
        <td><?php echo JText::_( 'COM_TICKETMASTER_LOGO_POSITION' ); ?></td>
        <td><input name="position_logo_confirmation" type="text" id="position_logo_confirmation" size="10" 
        value="<?php echo $this->config->position_logo_confirmation; ?>" />           
            <a href="#mb_inline_pos" role="button" class="btn pull-right" data-toggle="modal">
            	<img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>                     
        </td>
      </tr>      
      <tr>
        <td><?php echo JText::_( 'COM_TICKETMASTER_UPLOAD_COMPANY_LOGO' ); ?></td>
        <td>
        	<input name="company_logo" type="file" />
            <a href="#logo_desc" role="button" class="btn pull-right" data-toggle="modal">
            	<img src="../administrator/components/com_ticketmaster/assets/images/help16x16.png" width="16px" />
            </a>           
        </td>
      </tr>      
    </table>
    
   	<h3 style="color:#009; font-size:125%;"><?php echo JText::_( 'COM_TICKETMASTER_PDF_CURRENT_LOGO' ); ?></h3>
    <img src="../administrator/components/com_ticketmaster/assets/images/confirmation_logo.jpg" />	    
       
  
  </div>
</div>


<input name="option" type="hidden" value="com_ticketmaster" />
<input name="configid" type="hidden" value="1" />
<input name="task" type="hidden" value="" />
<input name="boxchecked" type="hidden" value="0"/>
<input name="controller" type="hidden" value="configuration"/>
</form>

 
<!-- Modals to be used in this page :) (Nice Bootstrap modals) -->
<div id="logo_desc" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_LOGO_INFORMATION' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_LOGO_DESC' ); ?><br/><br/>
        <?php echo JText::_( 'COM_TICKETMASTER_LOGO_SIZE_DEFAULT' ); ?>: 65x20 mm.<br/>
        <?php echo JText::_( 'COM_TICKETMASTER_LOGO_SIZE_POSITION' ); ?>: 65x20 mm.    
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<!-- Modal to show logo position information :) (Nice Bootstrap modals) -->
<div id="mb_inline_pos" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_POS_EXPLANATION_HEADER' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_POS_EXPLANATION' ); ?>  
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<!-- Modal to show logo position PDF Information :) (Nice Bootstrap modals) -->
<div id="mb_inline_pdfinconfirmation" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_CONFIRMATIONPDF_PROFILECHECK' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_CONFIRMATIONPDF_PROFILECHECK_DESC' ); ?>  
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<!-- Modal to show logo position PDF Information :) (Nice Bootstrap modals) -->
<div id="mb_inline_login" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_LOGIN_INFORMATION' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_LOGIN_DESC' ); ?> 
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<!-- Modal to show logo position PDF Information :) (Nice Bootstrap modals) -->
<div id="mb_inline_logo" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_LOGO_INFORMATION' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_LOGO_DESC' ); ?><br/><br/>
        <?php echo JText::_( 'COM_TICKETMASTER_LOGO_SIZE_DEFAULT' ); ?>: 65x20 mm.<br/>
        <?php echo JText::_( 'COM_TICKETMASTER_LOGO_SIZE_POSITION' ); ?>: 65x20 mm.
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<!-- Modal to show logo position PDF Information :) (Nice Bootstrap modals) -->
<div id="mb_inline_remove" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_REMOVE_TICKETS' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_REMOVE_TICKETS_DESC' ); ?>
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<!-- Modal to show logo position PDF Information :) (Nice Bootstrap modals) -->
<div id="mb_inline_activation_email" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_CHOOSE_RACTIVATION_EMAIL' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_CHOOSE_RACTIVATION_EMAIL_DESC' ); ?>
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<!-- Modal to show logo position PDF Information :) (Nice Bootstrap modals) -->
<div id="mb_inline_mailchimp" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_MAILCHIMP_API' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_MAILCHIMP_API_DESC' ); ?>
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>
  
<!-- Modal to show logo position PDF Information :) (Nice Bootstrap modals) -->
<div id="mb_inline_mailchimp_list" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_MAILCHIMP_LISTID' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_MAILCHIMP_LISTID_DESC' ); ?>
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<!-- Modal to show logo position PDF Information :) (Nice Bootstrap modals) -->
<div id="mb_inline_venuebox" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_SHOW_VENUEBOX' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_SHOW_VENUEBOX_DESC' ); ?>
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<div id="mb_inline_use_automatic_login" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_AUTOLOGIN_ON_OFF' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_AUTOLOGIN_ON_OFF_DESC' ); ?>
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<div id="mb_inline_auto_username" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_AUTO_USERNAME' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_AUTO_USERNAME_DESC' ); ?>
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<div id="mb_inline_pro" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_PRO_INSTALLED' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_PRO_INSTALLED_DESC' ); ?><br/><br/>
        <a href="http://rd-media.org" target="_blank" class="btn btn-large btn-block">Buy Ticketmaster PRO Now!!</a>
         
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<div id="mb_inline_termsofservice" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_CHOOSE_TOS_PAGE' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_CHOOSE_TOS_PAGE_DESC' ); ?><br/><br/>       
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<div id="mb_inline_mail_after_profile_check" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_SEND_MAIL_AFTER_PROFILE_CHECK' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_CHOOSE_TOS_PAGE_DESC' ); ?><br/><br/>       
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<div id="load_bootstrap_desc" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_LOAD_BOOTSTRAP' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_LOAD_BOOTSTRAP_DESC' ); ?><br/><br/>       
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<div id="load_bootstrap_tpl_desc" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_LOAD_BOOTSTRAP_TPL' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_LOAD_BOOTSTRAP_TPL_DESC' ); ?><br/><br/>       
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<div id="load_jquery_desc" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_LOAD_JQUERY' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_LOAD_JQUERY_DESC' ); ?><br/>
        <div class="alert">
          <strong>Warning!</strong><br/><?php echo JText::_( 'COM_TICKETMASTER_LOAD_JQUERY_NOTE' ); ?>
        </div>             
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<!-- Modals to be used in this page :) (Nice Bootstrap modals) -->
<div id="mb_send_multi_ticket_only" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_SEND_MULTI_TICKET_ONLY' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_SEND_MULTI_TICKET_ONLY_DESC' ); ?> 
        <a href="http://www.rd-media.org/support/knowledgebase/view-article/66-send-multi-ticket-only-functionality-3-0-2.html" target="_blank">FAQ</a>
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<!-- Modals to be used in this page :) (Nice Bootstrap modals) -->
<div id="mb_send_pdf_tickets" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_TURN_OFF_PDF_TICKETS_EMAIL' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_TURN_OFF_PDF_TICKETS_EMAIL_DESC' ); ?>  
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<!-- Modals to be used in this page :) (Nice Bootstrap modals) -->
<div id="mb_send_multi_ticket_admin" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_SEND_MULTI_TICKET_TO_ADMIN' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_SEND_MULTI_TICKET_TO_ADMIN_DESC' ); ?>  
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<!-- Modals to be used in this page :) (Nice Bootstrap modals) -->
<div id="mb_inline_google_api" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_SHOW_GOOGLE_MAPS_API_KEY' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_SHOW_GOOGLE_MAPS_API_KEY_DESC' ); ?> <a href="http://rd-media.org/support/knowledgebase/view-article/67-sign-up-for-a-google-maps-api-key-3-0-2.html" target="_blank"><strong>( FAQ )</strong></a>
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<!-- Modals to be used in this page :) (Nice Bootstrap modals) -->
<div id="mb_inline_remarks_on" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_SHOW_REMARKS_IN_CART' ); ?>?</h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo JText::_( 'COM_TICKETMASTER_SHOW_REMARKS_IN_CART_DESC' ); ?>
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>



