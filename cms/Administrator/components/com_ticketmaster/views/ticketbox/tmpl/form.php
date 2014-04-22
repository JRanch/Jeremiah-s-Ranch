<?php

/****************************************************************
 * @version			2.5.5											
 * @package			ticketmaster									
 * @copyright		Copyright � 2009 - All rights reserved.			
 * @license			GNU/GPL											
 * @author			Robert Dam										
 * @author mail		info@rd-media.org								
 * @website			http://www.rd-media.org							
 ***************************************************************/

## Check if the file is included in the Joomla Framework
defined('_JEXEC') or die ('No Acces to this file!');

$document = JFactory::getDocument();
$document->addStyleSheet('../administrator/components/com_ticketmaster/assets/component_css.css');

$counttickets = count($this->data);

## Check if this is Joomla 2.5 or 3.0.+
$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');

if(!$isJ30) {
	$paid_button 	= 'paid';
	$notpaid_button = 'notpaid';
	$blocked_button = 'blocked';
	$unlock_button  = 'unlock';
}else{
	$paid_button 	= 'thumbs-up';
	$notpaid_button = 'thumbs-down';
	$unlock_button  = 'ok';
	$blocked_button = 'lock';
}

## Setting the toolbars up here..
JToolBarHelper::title( JText::_( 'COM_TICKETMASTER_PROCESS' ), 'generic.png');
JToolbarHelper::custom( 'nopayment', $notpaid_button, '', JText::_( 'COM_TICKETMASTER_TOOLBAR_UNPAID' ), true, false);
JToolbarHelper::custom( 'payment', $paid_button, '', JText::_( 'COM_TICKETMASTER_TOOLBAR_PAID' ), false, false);
JToolbarHelper::custom( 'blocked', $blocked_button, '', JText::_( 'COM_TICKETMASTER_BLOCK' ), false, false);
JToolbarHelper::custom( 'unlock', $unlock_button, '', JText::_( 'COM_TICKETMASTER_UNBLOCK' ), false, false);

if ($counttickets < 1)  {
	## Cancel the operation
	JToolBarHelper::cancel();
} else {
	## For existing items the button is renamed `close`
	JToolBarHelper::cancel( 'cancel', 'Close' );
};

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

?>

<div class="row-fluid">
  <div class="span5">
  	
    <h3 style="size:115%; color:#000099;"><?php echo JText::_( 'COM_TICKETMASTER_CLIENT_INFORMATION' ); ?></h3>
    <table class="table table-striped">
      <tr>
        <td><?php echo JText::_( 'COM_TICKETMASTER_NAME' ); ?>
            </label></td>
        <td><?php echo $this->items->name; ?>, <?php echo $this->items->firstname; ?></td>
      </tr>
      <tr>
        <td class="key"><?php echo JText::_( 'COM_TICKETMASTER_ADDRESS' ); ?> 1</td>
        <td><?php echo $this->items->address; ?></td>
      </tr>
      <tr>
        <td class="key"><?php echo JText::_( 'COM_TICKETMASTER_ADDRESS' ); ?> 2</td>
        <td colspan="5"><?php echo $this->items->address2; ?></td>
      </tr>
      <tr>
        <td class="key"><?php echo JText::_( 'COM_TICKETMASTER_ADDRESS' ); ?> 3</td>
        <td colspan="5"><?php echo $this->items->address3; ?></td>
      </tr>            
      <tr>
        <td class="key"><?php echo JText::_( 'COM_TICKETMASTER_CITY' ); ?></td>
        <td colspan="5"><?php echo $this->items->zipcode; ?> <?php echo $this->items->city; ?></td>
      </tr>
      <tr>
        <td width="213" class="key"><?php echo JText::_( 'COM_TICKETMASTER_PHONE' ); ?></td>
        <td><?php echo $this->items->phonenumber; ?></td>
      </tr>
      <tr>
        <td width="213" class="key"><?php echo JText::_( 'COM_TICKETMASTER_EMAIL' ); ?></td>
        <td><?php echo $this->items->emailaddress; ?></td>
      </tr>    
    </table>  
    
    <?php if ($this->remark->id != '') { ?>
        <div class="alert">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <strong><?php echo JText::_( 'COM_TICKETMASTER_REMARKS' ); ?>:</strong><br/>
		  <?php echo $this->remark->remarks; ?>
        </div>    	
    <?php } ?>
  
  </div>
  <div class="span7">
  
     <h3 style="size:115%; color:#000099;"><?php echo JText::_( 'COM_TICKETMASTER_ORDER_INFORMATION' ); ?></h3>
     
        <div id = "ticketmaster-moneybox" style="height: 175px;">
         	
            <div id = "ticketmaster-paymentinfo" style="font-size:120%;">
				<?php echo JText::_( 'COM_TICKETMASTER_ORDERCODE' ); ?>: <?php echo $this->items->ordercode; ?> || 
                <?php echo date ("d-m-Y", strtotime($this->items->orderdate)); ?> || 
                <?php echo JText::_( 'COM_TICKETMASTER_TOTAL_PRICE2' ); ?>: 
				
                <?php if ($this->price->coupon_type == 1){ $type = '%'; }else{ $type = ''; } ?>
				<?php if( $this->price->coupon_discount != '') {
					if($type == '%') {
							$discounted = '<small><em>&nbsp;('. JText::_( 'COM_TICKETMASTER_DISCOUNT' ).': '.
								$this->price->coupon_discount.$type.')</em></small>';
								$discount = ($this->price->orderprice/100)*$this->price->coupon_discount;
								$ordering_price = $this->price->orderprice-$discount;
					}else{
						$discounted = '<small><em>&nbsp;('. JText::_( 'COM_TICKETMASTER_DISCOUNT' ).': '.
							number_format($this->price->coupon_discount, 2, ',', '').')</em></small>';
							$ordering_price = $this->price->orderprice-$this->price->coupon_discount;
					}
                }else{
					$discounted = '';
					$ordering_price = $this->price->orderprice;
				}?>
                
				
                <?php echo $this->config->valuta; ?> <?php echo number_format($ordering_price, 2, ',', ''); ?>
                <?php echo $discounted; ?>

            </div>
            
            <?php if($this->items->paid == 0){ ?>
            
                <div class="alert" style="margin-top:25px; width:88%;">
                  <strong>Warning!</strong><br/>
                  <?php echo JText::_( 'COM_TICKETMASTER_PROCESS_INFO' ); ?>
                </div>                       
            
            <?php }else{ ?>
				
                <div class="alert alert-info"  style="margin-top:10px; width:88%;">
					<?php echo JText::_( 'COM_TICKETMASTER_TO_RESEND_ORDER' ); ?>
            	</div>
                
                <form action = "index.php" method="POST" name="adminFormular" class="form-inline">
                    
                    <input name="cid" type="text" style="text-align:center" value="<?php echo $this->items->ordercode; ?>" size="10" 
                    	READONLY  class="input-medium" />
                    <input name="submit" type="submit" class="btn" value="<?php echo JText::_( 'COM_TICKETMASTER_RESEND' ); ?>" /></td>
                      
                    <input name = "option" type="hidden" value="com_ticketmaster" />
                    <input name = "task" type="hidden" value="sendticketcopy" />
                    <input name = "boxchecked" type="hidden" value="0"/>
                    <input name = "controller" type="hidden" value="ticketbox"/>
                
                </form>            

                <form action = "index.php" method="POST" name="adminFormular" class="form-inline">
                    
                    <input name="cid[]" type="text" style="text-align:center" value="<?php echo $this->items->ordercode; ?>" size="10" 
                    	READONLY  class="input-medium" />
                    <input name="submit" type="submit" class="btn" value="<?php echo JText::_( 'COM_TICKETMASTER_CREATE_TICKETS' ); ?>" /></td>
                      
                    <input name = "option" type="hidden" value="com_ticketmaster" />
                    <input name = "task" type="hidden" value="processticket" />
                    <input name = "boxchecked" type="hidden" value="0"/>
                    <input name = "controller" type="hidden" value="ticketbox"/>
                
                </form>                  
                
            <?php } ?>
        </div>
  
  </div>
</div>

<hr  />
<h3 style="size:115%; color:#000099;"><?php echo JText::_( 'COM_TICKETMASTER_ORDER_DETAILS' ); ?></h3>

<form action = "index.php" method="POST" name="adminForm" id="adminForm">

  <table class="table table-striped" width="100%">
    <thead>
      <tr>
        <th width="55" height="24"><div align="center"></div></th>
        <th width="55" class="title"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_ORDERID' ); ?></div></th>
        <th width="269" class="title">
        <div align="left" width="400"><?php echo JText::_( 'COM_TICKETMASTER_EVENTINFORMATION' ); ?></div></th>
        <th width="120" align="center" class="title"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_PRESENT' ); ?></div></th>
        <th width="120" align="center" class="title"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_COUPON' ); ?></div></th>
        <th width="120" align="center" class="title"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_BLACKLIST' ); ?></div></th>
        <th width="120" align="center" class="title"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_SCANNED' ); ?></div></th>
        <th width="120" align="center" class="title"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_BARCODE' ); ?></div></th>
        <th width="120" align="center" class="title"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_TOTAL_PRICE2' ); ?></div></th>
        <th width="120" align="center" class="title"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_PAYMENT_STATUS' ); ?></div></th>
        </tr>
    </thead>
    <?php 
	   
	   $k = 0;
	   for ($i = 0, $n = count($this->data); $i < $n; $i++ ){
		
		## Give give $row the this->item[$i]
		$row        = &$this->data[$i];
		$published 	= JHTML::_('grid.published', $row, $i );
		$checked    = JHTML::_('grid.id', $i, $row->orderid );

	?>
    <tr class="<?php echo "row$k"; ?>">
      <td><div align="center"><?php echo $checked; ?></div></td>
      <td><div align="center"><?php echo $row->orderid; ?></div></td>
      <td><div align="left">
      <?php if ($row->parentname != $row->ticketname) { ?>
      	<?php echo $row->eventname; ?><br/><?php echo $row->ticketname; ?> 
        <?php if ($row->seat_sector != 0) { echo ' - Seat: '. checkSeat($row->orderid, $this->coords); } ?>
      <?php }else{ ?><strong><?php echo $row->eventname; ?></strong> <br /> <?php echo $row->ticketname; ?>
      	<?php if ($row->seat_sector != 0) { echo '<br/>'.JText::_( 'COM_TICKETMASTER_SEAT' ).': '. checkSeat($row->orderid, $this->coords); } ?> <?php } ?>
      </div></td>

      </td>
      <td align="center" valign="top">
          <div align="center">
		   <?php   
		   		## Path to a normal ticket is as below:
				$path = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_ticketmaster'.DS.'tickets'.DS.'eTicket-'.$row->orderid.'.pdf';
				$multi = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_ticketmaster'.DS.'tickets'.DS.'multi-'.$row->ordercode.'.pdf';
				## Remove single ticket
			if (!file_exists($path) && !file_exists($multi)) { ?>
					
					<span class="label label-important"><?php echo JText::_( 'COM_TICKETMASTER_NO' ); ?></span>
		   
		   	<?php }else{ 
		   		
		   		$multi_link = 'components/com_ticketmaster/tickets/multi-'.$row->ordercode.'.pdf';
		   		$ticket_link = 'components/com_ticketmaster/tickets/eTicket-'.$row->orderid.'.pdf';
		   		
			   	if (file_exists($multi)) { ?>
			   		<a href="<?php echo $multi_link; ?>" target="_blank" class="btn btn-small btn-success" type="button"><?php echo JText::_( 'COM_TICKETMASTER_SHOW' ); ?></a>
			   <?php }elseif(file_exists($path)){ ?>
			   		<a href="<?php echo $ticket_link; ?>" target="_blank" class="btn btn-small btn-success" type="button"><?php echo JText::_( 'COM_TICKETMASTER_SHOW' ); ?></a>
			   <?php } ?>
			   
		   <?php } ?>
          </div>
      </td>
      <td align="center" valign="top">
	  	<div align="center"><?php if ($row->coupon != '') { ?><span class="label label-info"><?php echo $row->coupon; ?></span><?php } ?></div>
      </td>
      <td align="center" valign="top">
          <div align="center">
            <?php
            if ($row->blacklisted == 1){ ?>
             	<span class="label label-important"><?php echo JText::_( 'COM_TICKETMASTER_NOT_LISTED' ); ?></span>
             <?php }else{ ?>
             	<span class="label label-success"><?php echo JText::_( 'COM_TICKETMASTER_YES_LISTED' ); ?></span>
             <?php } ?>	    
               </div></td>
    <td><div align="center">
                  <?php 
            if ($row->scanned == 1){ ?>
                  <span class="label label-success"><?php echo JText::_( 'COM_TICKETMASTER_YES_SCAN' ); ?></span>
                  <?php }else{ ?>
                  <span class="label label-important"><?php echo JText::_( 'COM_TICKETMASTER_NO_SCAN' ); ?></span>
                  <?php } ?>	      
        </div></td>
      <td align="center"><div align="center">
	  <?php echo $row->barcode; ?></strong><br />
      </div></td>
      <td><div align="center">
	    <?php echo $this->config->valuta; ?> <?php echo number_format($row->ticketprice, 2, ',', ''); ?>
      </div></td>
      <td width="120" align="center" valign="top">
            
          <div align="center">
            <?php 
            if ($row->paid == 1){ ?>
            	<span class="label label-success"><?php echo JText::_( 'COM_TICKETMASTER_PAID' ); ?></span>
            <?php }elseif($row->paid == 2){ ?>
            	<span class="label label-inverse"><?php echo JText::_( 'COM_TICKETMASTER_REFUNDED' ); ?></span>
            <?php }elseif($row->paid == 3){ ?>
            	<span class="label label-warning"><?php echo JText::_( 'COM_TICKETMASTER_PENDING' ); ?></span>
            <?php }else{ ?>
            	<span class="label label-important"><?php echo JText::_( 'COM_TICKETMASTER_UNPAID_OVERVIEW' ); ?></span>
            <?php } ?>		
        </div></td>
      </tr>
    <?php
	  $k=1 - $k;
	  }
	  ?>
  </table>  
  
  <input name = "option" type="hidden" value="com_ticketmaster" />
  <input name = "task" type="hidden" value="" />
  <input name = "boxchecked" type="hidden" value="0"/>
  <input name = "controller" type="hidden" value="ticketbox"/>

</form>
</fieldset>

<?php function checkSeat($value, $seat) {
	
   for ($i = 0, $n = count($seat); $i < $n; $i++ ){

		if ($value == $seat[$i]->orderid) {
			if($seat[$i]->row_name != ''){
			$seat = $seat[$i]->row_name.$seat[$i]->seatid;
			}else{
				$seat[$i]->seatid;
			}
		}

	}	
		
	return $seat; 
} 

?>