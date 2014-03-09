<?php

## Check if the file is included in the Joomla Framework
defined('_JEXEC') or die ('No Acces to this file!');

$editor =& JFactory::getEditor();

## Include the toolbars for saving.
JToolBarHelper::title( JText::_( 'MAIL CONFIG' ), 'config.png');	
## Make sure the user is authorized to click the save button
$user = & JFactory::getUser();
if ($user->gid == 25) {
	JToolBarHelper::save();
}
JToolBarHelper::back();

## Require specific menu-file.
$path = JPATH_COMPONENT.DS.'assets'.DS.'config.php';
if (file_exists($path)) {
	include_once $path;
}

?>
<form action = "index.php" method="POST" name="adminForm" id="adminForm" enctype="multipart/form-data">
<table width="100%" border="0">
  <tr>
    <td colspan="2" align="left" valign="top"> 
    <fieldset class = "adminForm">
        <legend><?php echo JText::_( 'ORDER CONFIRMATION' ); ?></legend>
        <table width="100%" border="0">
          <tr>
            <td width="50%" align="left" valign="top"><?php
                echo $editor->display('sendpdfmail', $this->config->sendpdfmail, '98%', '200', '70', '15', false);
            ?></td>
            <td width="1%" align="left" valign="top">&nbsp;</td>
            <td width="49%" align="left" valign="top"><?php echo JText::_( 'DESCRIPTION 1' ); ?>
            <br /><?php echo JText::_( 'HTML ALLOWED' ); ?><br />
            <br /><?php echo JText::_( 'REPLACABLE TEXT' ); ?><br />
            <a href="http://www.rd-media.org/rd-ticketmaster/rd-ticketmaster-faq/191-tm-109-mail-template-when-sending-the-tickets-to-clients.html"
                     target="_blank"><?php echo JText::_( 'EXAMPLE MAIL TEMPLATE' ); ?></a>            
            
            <ul>
            <li>%%NAME%% = <?php echo JText::_( 'CLIENTS NAME' ); ?></li>
            </ul>
            
            </td>
          </tr>
        </table>
        </fieldset>
        <fieldset class = "adminForm">
        <legend><?php echo JText::_( 'SEND REGISTRATION' ); ?></legend>
        <table width="100%" border="0">
          <tr>
            <td width="50%" align="left" valign="top">
			<?php
                echo $editor->display('sendconfirm', $this->config->sendconfirm, '98%', '200', '70', '15', false);
            ?>            </td>
            <td width="1%" align="left" valign="top">&nbsp;</td>
            <td width="49%" align="left" valign="top"><?php echo JText::_( 'DESCRIPTION 2' ); ?>
            <br /><?php echo JText::_( 'HTML ALLOWED' ); ?><br />
            <br /><?php echo JText::_( 'REPLACABLE TEXT' ); ?><br />
            <a href="http://www.rd-media.org/rd-ticketmaster/rd-ticketmaster-faq/192-tm-109-mail-template-when-sending-email-confirmations-to-clients.html"
                     target="_blank"><?php echo JText::_( 'EXAMPLE MAIL TEMPLATE' ); ?></a>            
            
            <ul>
            <li>%%NAME%% = <?php echo JText::_( 'CLIENTS NAME' ); ?></li>
            <li>%%USERNAME%% = <?php echo JText::_( 'NEW CREATED USERNAME' ); ?></li>
            <li>%%PASSWORD%% = <?php echo JText::_( 'PASSWOD FOR USER' ); ?></li>
            </ul>
          </tr>
        </table>
        </fieldset>	 
        <fieldset class = "adminForm">
        <legend><?php echo JText::_( 'SEND PDF CONFIRMATION' ); ?></legend>
        <table width="100%" border="0">
          <tr>
            <td width="50%" align="left" valign="top">
			<?php
                echo $editor->display('sendconfirmid', $this->config->sendconfirmid, '98%', '200', '70', '15', false);
            ?>            </td>
            <td width="1%" align="left" valign="top">&nbsp;</td>
            <td width="49%" align="left" valign="top"><?php echo JText::_( 'DESCRIPTION 2' ); ?><br />
            <br /><?php echo JText::_( 'HTML ALLOWED' ); ?><br />
            <br /><?php echo JText::_( 'REPLACABLE TEXT' ); ?><br />
            <a href="http://www.rd-media.org/rd-ticketmaster/rd-ticketmaster-faq/193-email-confirmation-when-logged-in.html"
                     target="_blank"><?php echo JText::_( 'EXAMPLE MAIL TEMPLATE' ); ?></a>            
            
            <ul>
                <li>%%NAME%% = <?php echo JText::_( 'CLIENTS NAME' ); ?></li>
              </ul>
          </tr>
        </table>
        </fieldset>	          	        
        <fieldset class = "adminForm">
        
        <legend><?php echo JText::_( 'VALIDATION OK PAGE' ); ?></legend>
        <table width="100%" border="0">
          <tr>
            <td width="50%" align="left" valign="top">
			<?php
                echo $editor->display('validation_ok', $this->config->validation_ok, '98%', '200', '70', '15', false);
            ?>            </td>
            <td width="1%" align="left" valign="top">&nbsp;</td>
            <td width="49%" align="left" valign="top">
            <br /><?php echo JText::_( 'HTML ALLOWED' ); ?><br />
            <br /><?php echo JText::_( 'REPLACABLE TEXT' ); ?><br />
            <a href="http://www.rd-media.org/rd-ticketmaster/rd-ticketmaster-faq/190-tm-109-validation-ok-mail-manual-payments.html"
                     target="_blank"><?php echo JText::_( 'EXAMPLE MAIL TEMPLATE' ); ?></a>
            <ul>
            <li>%%NAME%% = <?php echo JText::_( 'CLIENTS NAME' ); ?> </li>
            <li>%%EVENTNAME%% = <?php echo JText::_( 'NAME OF THE EVENT' ); ?></li>
            <li>%%EVENTDATE%% = <?php echo JText::_( 'DATE OF THE EVENT' ); ?></li>
            <li>%%FEES%% = <?php echo JText::_( 'FEES FOR THE ORDER' ); ?></li>
			<li>%%PRICE%% = <?php echo JText::_( 'TICKETPRICE' ); ?></li>
            <li>%%TOTALTICKETS%% = <?php echo JText::_( 'TOTAL TICKETS' ); ?></li>
            <li>%%TOTALAMOUNT%% = <?php echo JText::_( 'TOTAL PRICE TICKETS' ); ?></li>
            </ul>            
            </td>
          </tr>
        </table>
        </fieldset>	          	        
		
        <fieldset class = "adminForm">
        <legend><?php echo JText::_( 'PAYMENT CAME IN MSG' ); ?></legend>
        <table width="100%" border="0">
          <tr>
            <td width="50%" align="left" valign="top">
			<?php
                echo $editor->display('payment_received', $this->config->payment_received, '98%', '200', '70', '15', false);
            ?>            </td>
            <td width="1%" align="left" valign="top">&nbsp;</td>
            <td width="49%" align="left" valign="top">
            <br /><?php echo JText::_( 'HTML ALLOWED' ); ?><br />
            <br /><?php echo JText::_( 'REPLACABLE TEXT' ); ?><br />
            <a href="http://www.rd-media.org/rd-ticketmaster/rd-ticketmaster-faq/194-payment-came-in-confirmation-mail.html"
                     target="_blank"><?php echo JText::_( 'EXAMPLE MAIL TEMPLATE' ); ?></a>            
            
            <ul>
                <li>%%PPEMAIL%% = <?php echo JText::_( 'PAYPAL EMAIL' ); ?> </li>
                <li>%%PPT%% = <?php echo JText::_( 'PP TRANSACTION NR' ); ?></li>
                <li>%%AMOUNT%% = <?php echo JText::_( 'AMOUNT OF PAYMENT' ); ?></li>
                <li>%%ORDERID%% = <?php echo JText::_( 'ORDERIDTRANSACTION' ); ?></li>
            </ul>            
            </td>
          </tr>
        </table>
        </fieldset>
        <fieldset class = "adminForm">
        <legend><?php echo JText::_( 'MANUAL PAYMENT CAME IN MSG' ); ?></legend>
        <table width="100%" border="0">
          <tr>
            <td width="50%" align="left" valign="top">
			<?php
                echo $editor->display('payment_email_manual', $this->config->payment_email_manual, '98%', '200', '70', '15', false);
            ?>            </td>
            <td width="1%" align="left" valign="top">&nbsp;</td>
            <td width="49%" align="left" valign="top">
            <br /><?php echo JText::_( 'HTML ALLOWED' ); ?><br />
            <br /><?php echo JText::_( 'WHEN SENDDING THIS EMAIL' ); ?><br />
            <br /><?php echo JText::_( 'REPLACABLE TEXT' ); ?><br />           
            
            <ul>
                <li>%%NAME%% = <?php echo JText::_( 'CUSTOMER NAME' ); ?> </li>
            </ul>            
            </td>
          </tr>
        </table>
        </fieldset>        
        <fieldset class = "adminForm">	 
        <legend><?php echo JText::_( 'ORDER COMPLETED MESSAGE' ); ?></legend>
        <table width="100%" border="0">
          <tr>
            <td width="50%" align="left" valign="top">
			<?php
                echo $editor->display('ordercomplete_msg', $this->config->ordercomplete_msg, '98%', '200', '70', '15', false);
            ?>            </td>
            <td width="1%" align="left" valign="top">&nbsp;</td>
            <td width="49%" align="left" valign="top">&nbsp;</td>
          </tr>
        </table>
        </fieldset>	                 	        
        </td>
    </tr>
  <tr>
    <td colspan="2" align="left" valign="top">&nbsp;</td>
    </tr>  
</table>
	
</td>
</tr>
</table>
<input name="option" type="hidden" value="com_ticketmaster" />
<input name="configid" type="hidden" value="1" />
<input name="task" type="hidden" value="" />
<input name="boxchecked" type="hidden" value="0"/>
<input name="controller" type="hidden" value="configuration"/>
<input name="paypal_email" type="hidden" value="<?php echo $this->config->paypal_email; ?>"/>
<input name="layout" type="hidden" value="email"/>
</form>
