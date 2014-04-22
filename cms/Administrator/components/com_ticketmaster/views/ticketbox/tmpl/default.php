<?php
/****************************************************************
 * @version				Ticketmaster 2.5.5						
 * @package				ticketmaster								
 * @copyright           Copyright © 2009 - All rights reserved.			
 * @license				GNU/GPL										
 * @author				Robert Dam									
 * @author mail         info@rd-media.org								
 * @website				http://www.rd-media.org						
 ***************************************************************/

## no direct access
defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$document->addStyleSheet('components/com_ticketmaster/assets/component_css.css');

## Check if this is Joomla 2.5 or 3.0.+
$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');

if(!$isJ30) {
	$paid_button 	= 'paid';
	$refund_button 	= 'refund';
	$pdf_button 	= 'pdf';
	$send_button 	= 'send';
	$confirm_button = 'confirm';
	$process_button = 'process';
}else{
	$paid_button 	= 'thumbs-up';
	$refund_button 	= 'minus-sign';
	$pdf_button 	= 'file';
	$send_button 	= 'envelope';
	$confirm_button = 'chevron-down';
	$process_button = 'refresh';
}

## Setup the toolbars and functionality.
JToolBarHelper::title( JText::_( 'COM_TICKETMASTER_TICKETBOX' ), 'generic.png' );
JToolbarHelper::custom( 'allpayments', $paid_button, '', JText::_( 'COM_TICKETMASTER_TOOLBAR_PAID' ), true, false);
JToolbarHelper::custom( 'refund', $refund_button, '', JText::_( 'COM_TICKETMASTER_TOOLBAR_REFUND' ), true, false);
JToolBarHelper::divider();
JToolbarHelper::custom( 'processticket', $pdf_button, '', JText::_( 'COM_TICKETMASTER_TOOLBAR_PDF_CREATE' ), true, false);
JToolbarHelper::custom( 'sendingticket', $send_button, '', JText::_( 'COM_TICKETMASTER_TOOLBAR_SEND_TICKETS' ), true, false);
JToolbarHelper::custom( 'sendconfirmation', $confirm_button, '', JText::_( 'COM_TICKETMASTER_TOOLBAR_CONFIRM' ), true, false);
JToolbarHelper::custom( 'resendpayment', $process_button, '', JText::_( 'COM_TICKETMASTER_RESEND_PAYMENT' ), true, false);
JToolBarHelper::publish();
JToolBarHelper::divider();
JToolbarHelper::custom( 'edit', $process_button, '', JText::_( 'COM_TICKETMASTER_TOOLBAR_PROCESS' ), true, false);
JToolBarHelper::deleteList();

$option = 'com_ticketmaster';

## Require specific menu-file.
$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmasterext'.DS.'assets'.DS.'menu.php';
if (file_exists($path)) {
	include_once $path;
}else{
	$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'menu.php';
	include_once $path;
}

## Including required paths to calculator.
$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
include_once( $path_include );

if(!$isJ30) {
	$document = JFactory::getDocument();
	## Adding mootools for J!2.5
	JHTML::_('behavior.modal');
	## Include the tooltip behaviour.
	JHTML::_('behavior.tooltip', '.hasTip');
	$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' ); 
	$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/js/bootstrap.js');
}else{
	$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' );
	$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/js/bootstrap.js');
}	

?>

<script language="javascript">

jQuery.noConflict();
  jQuery(document).ready(function(jQuery) {
		var oldSrc = '<?php echo JURI::root(true); ?>/administrator/templates/isis/images/admin/tick.png';
		var newSrc = '<?php echo JURI::root(true); ?>/administrator/components/com_ticketmaster/assets/images/tick.png';
		jQuery('img[src="' + oldSrc + '"]').attr('src', newSrc);
  });



</script>

<form action = "index.php" method="POST" name="adminForm" id="adminForm" class="form-inline">
<table class="table">
<tr>
	<td align="left" width="100%">

	</td>
	<td nowrap="nowrap">
        <input type="text" name="searchbox" id="searchbox" value="<?php echo $this->lists['search'];?>" class="input-medium" placeholder="Search Ordercode.."/> 
		<?php echo $this->lists['eventcurrent'];?>
		<?php echo $this->lists['eventid'];?>
        <?php echo $this->lists['sent'];?>
        <?php echo $this->lists['pdf_created'];?>
        <?php echo $this->lists['paid'];?>
        <button onclick="this.form.submit();" class="btn"><?php echo JText::_( 'COM_TICKETMASTER_SEARCH' ); ?></button>
	</td>
</tr>
</table><br/>

  <table class="table table-striped" width="100%">
    <thead>
      <tr>
        <th width="33" height="24"><div align="center">
          <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?> ) ;" />
        </div></th>
        <th width="32" class="title"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_ORDERID' ); ?></div></th>
        <th width="173" class="title">
        <div align="left"><?php echo JText::_( 'COM_TICKETMASTER_EVENTINFORMATION' ); ?></div></th>
        <th class="title" width="261"><div align="left"><?php echo JText::_( 'COM_TICKETMASTER_ORDER_INFO' ); ?></div></th>
        <th class="title" width="75"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_BLACKLIST' ); ?></div></th>
        <th class="title" width="75"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_SCANNED' ); ?></div></th>
        <th class="title" width="75"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_TOTAL_TICKETS_2' ); ?></div></th>
        <th class="title" width="95"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_TOTAL_REGULAR_PRICE' ); ?></div></th>
        <th class="title" width="70"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_TOTAL_TICKET_SENT' ); ?></div></th>
        <th class="title" width="70"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_PDF_CREATED' ); ?></div></th>
        <th class="title" width="70"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_PAYMENT_STATUS' ); ?></div></th>
        <th class="title" width="70"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_ACTIVATED' ); ?></div></th>
      </tr>
    </thead>
    <?php 
	   
	   $k = 0;
	   for ($i = 0, $n = count($this->items); $i < $n; $i++ ){
		
		## Give give $row the this->item[$i]
		$row        = &$this->items[$i];
		$published 	= JHTML::_('grid.published', $row, $i );
		$checked    = JHTML::_('grid.id', $i, $row->ordercode );
		$link       = 'index.php?option=' .$option. '&controller=ticketbox&task=edit&cid[]='.$row->ordercode;
		$link2      = 'index.php?option=' .$option. '&controller=tickets&task=edit&cid[]='.$row->ticketid;

	?>
    <tr class="<?php echo "row$k"; ?>">
      <td><div align="center"><?php echo $checked; ?></div></td>
      <td><div align="center"><?php echo $row->orderid; ?></div></td>
      <td><div align="left"><strong><?php echo $row->eventname; ?></strong><br />
      <?php  if ($row->parentname != $row->ticketname) { 
	  	echo $row->parentname.' <em>[ '.$row->ticketname.' ]</em>'; }else{  echo $row->ticketname; 
	   } ?>
        </div></td>
      <td><div align="left">
      <?php if ($row->coupon != '') { ?><span class="label label-info">C</span><?php } ?>      
      <strong><?php echo JText::_( 'COM_TICKETMASTER_ORDERCODE' ); ?> <?php echo $row->ordercode; ?></strong>
      &nbsp;<em>[&nbsp;<a href="<?php echo $link;?>"><?php echo JText::_( 'COM_TICKETMASTER_SHOW_ORDER' ); ?></a>&nbsp;]</em><br />
	  <?php echo $row->firstname; ?> <?php echo $row->name; ?> - <?php echo $row->address; ?> - <?php echo $row->city; ?></div></td>
      <td><div align="center">
			<?php if ($row->blacklisted == 1) { ?>
            <img src="components/com_ticketmaster/assets/images/tick.png" width="16" height="16" />
            <?php } ?>       
      </div></td>
      <td><div align="center">
			<?php if ($row->scanned == 1) { ?>
            <img src="components/com_ticketmaster/assets/images/tick.png" width="16" height="16" />
            <?php } ?>      
      </div></td>
      <td><div align="center"><?php echo $row->totaltickets; ?></div></td>
      <td><div align="center"><?php echo $this->config->valuta; ?> <?php echo number_format($row->orderprice, 2, ',', ''); ?></div></td>
      <td width="75"><div align="center">
        <?php if ($row->pdfsent == 1) { ?>
        <img src="components/com_ticketmaster/assets/images/tick.png" width="16" height="16" />
        <?php } ?>
      </div></td>
      <td width="75"><div align="center">
        <?php if ($row->pdfcreated == 1) { ?>
        <img src="components/com_ticketmaster/assets/images/tick.png" width="16" height="16" />
        <?php } ?>
		</div></td>
      <td width="75"><div align="center">
           <?php if ($row->paid == 1){ ?>
            	<span class="label label-success"><?php echo JText::_( 'COM_TICKETMASTER_PAID' ); ?></span>
            <?php }elseif($row->paid == 2){ ?>
            	<span class="label label-inverse"><?php echo JText::_( 'COM_TICKETMASTER_REFUNDED' ); ?></span>
            <?php }elseif($row->paid == 3){ ?>
            	<span class="label label-warning"><?php echo JText::_( 'COM_TICKETMASTER_PENDING' ); ?></span>
            <?php }else{ ?>
            	<span class="label label-important"><?php echo JText::_( 'COM_TICKETMASTER_UNPAID_OVERVIEW' ); ?></span>
            <?php } ?>	            
      </div></td>
      <td width="81"><div align="center"><?php echo $published; ?></div></td>
    </tr>
    <?php
	  $k=1 - $k;
	  }
	  ?>
  </table>

  <table width="100%" align="center" class="adminlist">
    <tfoot>
        <tr>
            <td colspan="7"><div align="center"><?php echo $this->pagination->getListFooter(); ?></div></td>
        </tr>  
    </tfoot>   
  </table>  
  
  <input name = "option" type="hidden" value="com_ticketmaster" />
  
  <input name = "task" type="hidden" value="" />
  <input name = "boxchecked" type="hidden" value="0"/>
  <input name = "controller" type="hidden" value="ticketbox"/>
</form>