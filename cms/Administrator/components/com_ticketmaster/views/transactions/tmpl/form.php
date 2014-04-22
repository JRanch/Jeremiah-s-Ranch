<?php

## Check if the file is included in the Joomla Framework
defined('_JEXEC') or die ('No Acces to this file!');

## Setting the toolbars up here..
JToolBarHelper::title(JText::_( 'COM_TICKETMASTER_PAYMENT_DETAILS' ), 'generic.png');
## For existing items the button is renamed `close`
JToolBarHelper::cancel( 'cancel', 'Close' );


$editor 	= JFactory::getEditor();
$document 	= JFactory::getDocument();

$document->addScript('https://code.jquery.com/jquery-latest.js');
$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/js/jquery.json-2.2.min.js');

## Check if this is Joomla 2.5 or 3.0.+
$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');

if(!$isJ30) {
	$document = JFactory::getDocument();
	## Adding mootools for J!2.5
	JHTML::_('behavior.modal');
	## Include the tooltip behaviour.
	JHTML::_('behavior.tooltip', '.hasTip');
	$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' ); 
	$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/js/bootstrap.js');
}


$amount =  $this->config->valuta.' '.number_format($this->data->amount, 2, ',', ' ');
?>



<form action = "index.php" method="POST" name="adminForm" id="adminForm" enctype="multipart/form-data">

<div class="row-fluid">
  <div class="span5">
  
    <table class="table table-striped">
        <tr>
            <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_PID' ); ?></td>
            <td width="50%"><input class="text_area" type="text" name="transid" id="transid" size="30" maxlength="50" 
                                    value="<?php echo $this->data->transid; ?>" /></td>
        </tr>
        <tr>
            <td width="50%">
            <?php echo JText::_( 'COM_TICKETMASTER_DATE' ); ?></td>
            <td width="50%"><input class="text_area" type="text" name="date" id="date" size="30" maxlength="50"
                            value="<?php echo $this->data->date; ?>" /></td>
        </tr>
        <tr>
            <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_PAYMENT_TYPE' ); ?></td>
            <td width="50%"><input class="text_area" type="text" name="type" id="type" size="30" maxlength="50"
                        value="<?php echo $this->data->type; ?>" /></td>
        </tr>
        <tr>
            <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_PAYPAL_EMAIL' ); ?></td>
            <td width="50%"><input class="text_area" type="text" name="email_paypal" id="email_paypal" size="30" maxlength="50"
                            value="<?php echo $this->data->email_paypal; ?>" /></td>
        </tr>
        <tr>
            <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_RELATION_WITH_USER' ); ?></td>
            <td width="50%"><input class="text_area" type="text" name="useless" id="useless" size="30" maxlength="50"
                    value="<?php echo $this->data->name; ?> [ <?php echo $this->data->userid; ?> ]" /></td>
        </tr>
        <tr>
            <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_AMOUNT' ); ?></td>
            <td width="50%"><input class="text_area" type="text" name="amount-fixed" id="amount-fixed" size="30" maxlength="50"
                    value="<?php echo $amount; ?>" /></td>
        </tr>
        <tr>
          <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_ORDERCODE' ); ?></td>
          <td width="50%"><input class="text_area" type="text" name="amount-fixed2" id="amount-fixed2" size="30" maxlength="50"
                    value="<?php echo $this->data->orderid; ?>" /></td>
        </tr>
    </table>
    
    <a href="#mb_inline_payment_notes" role="button" class="btn btn-large btn-block" data-toggle="modal">
        <?php echo JText::_( 'COM_TICKETMASTER_PAYMENT_INFORMATION'); ?>
    </a> 
  
  </div>
  <div class="span7"></div>
</div>

<input type="hidden" name="catid" value="<?php echo $this->data->pid; ?>" />
<input type="hidden" name="option" value="com_ticketmaster" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="transactions" />
</form>
		
<div id="mb_inline_payment_notes" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_( 'COM_TICKETMASTER_NOTES_BY_PAYMENTPROCESSOR' ); ?></h3>
  </div>
  <div class="modal-body">
    <p style="font-size:105%; padding:0px;">
		<?php echo $this->data->details; ?>      
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>