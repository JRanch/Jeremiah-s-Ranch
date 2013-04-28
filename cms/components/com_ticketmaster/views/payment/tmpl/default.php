<?php 
/**
 * @version		1.0.0 ticketmaster $
 * @package		ticketmaster
 * @copyright	Copyright © 2009 - All rights reserved.
 * @license		GNU/GPL
 * @author		Robert Dam
 * @author mail	info@rd-media.org
 * @website		http://www.rd-media.org
 */

## Check if the file is included in the Joomla Framework
defined('_JEXEC') or die ('No Acces to this file!');

$app =& JFactory::getApplication();
$pathway =& $app->getPathway();
$pathway->addItem(JText::_( 'COM_TICKETMASTER_EVENTS' ), 'index.php?option=com_ticketmaster');
$pathway->addItem(JText::_( 'COM_TICKETMASTER_CART' ), 'index.php?option=com_ticketmaster&view=cart');
$pathway->addItem(JText::_( 'COM_TICKETMASTER_PAYMENTPAGE' ));

## Add the tooltip behaviour.
JHTML::_('behavior.tooltip');
JHTML::_( 'behavior.mootools' );

## Obtain user information.
$user = & JFactory::getUser();
$userid = $user->id;
## Get document type and add it.
$document = &JFactory::getDocument();
$document->addStyleSheet( 'components/com_ticketmaster/assets/component.css' );

$cssfile = 'components/com_ticketmaster/assets/css-overrides/cart.css';

## Check if there is a css override.
if (file_exists($cssfile)) {
    $document->addStyleSheet( $cssfile );
} else {
    $document->addStyleSheet( 'components/com_ticketmaster/assets/css/cart.css' );
}

$document->setTitle( JText::_('COM_TICKETMASTER_PAYMENTPAGE') );
## Adding the lightbox functionality
$document->addScript('/jquery/jquery-1.9.0.min.js');
## Adding the mediabox functions.
$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/lightbox/mediabox.js');
$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/lightbox/mediabox.css' );

## Getting the global DB session
$session =& JFactory::getSession();
## Gettig the orderid if there is one.
$ordercode = $session->get('ordercode');

include_once( 'components/com_ticketmaster/assets/functions.php' );

## Including required paths to calculator.
$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
include_once( $path_include );	

## Getting the total to pay :)
$ordertotal = _getAmount($ordercode);
$fees = _getFees($ordercode);
$discount = _getDiscount($ordercode);

$count = count($this->items);

if($this->config->load_bootstrap == 1){
	## Adding mootools for J!2.5
	JHTML::_('behavior.modal');
	## Include the tooltip behaviour.
	JHTML::_('behavior.tooltip', '.hasTip');
	$document->addScript('/jquery/jquery-1.9.0.min.js');
	$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' ); 
	$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/js/bootstrap.js');
	$button = 'btn btn-small';
}else{	
	$document->addStyleSheet( 'components/com_ticketmaster/assets/css/bootstrap.css' );
	$button = 'button_rdticketmaster';
}

?>

<script language="javascript">

var JQ = jQuery.noConflict();

JQ(document).ready(function(){
		
	//Hide div w/id extra
   JQ("#paymentmethods").css("display","none");

	// Add onclick handler to checkbox w/id checkme
   JQ("#checkme").click(function(){

		// If checked
		if (JQ("#checkme").is(":checked")){
			//show the hidden div
			JQ("#paymentmethods").show("slow");
		}else{
			//otherwise, hide it
			JQ("#paymentmethods").hide("slow");
		}
	
  });

});
	 

</script>

<?php if ($count == 0 ){ ?>

    <h2><?php echo JText::_('COM_TICKETMASTER_YOUR_CART_EMPTY'); ?></h2>

<?php }else{  ?>    

    <h2><?php echo JText::_('COM_TICKETMASTER_PAYMENT_ORDER'); ?></h2>
    
    <div id = "tm-cart-text">
    	<?php echo JText::_( 'COM_TICKETMASTER_YOUR_PAYMENT_TEXT' ); ?>
    </div> 
    
        <table class="table table-striped" id="cart">               
            
            <thead>
                <th width="10%"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_ORDERID' ); ?></div></th>
                <th width="70%"><?php echo JText::_( 'COM_TICKETMASTER_EVENT_INFORMATION' ); ?></th>
                <th width="15%"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_PRICE' ); ?></div></th>                  
            </thead> 
            
            <?php 
               
               $k = 0;
               for ($i = 0, $n = count($this->items); $i < $n; $i++ ){
                
                ## Give give $row the this->item[$i]
                $row        = &$this->items[$i];
                $published 	= JHTML::_('grid.published', $row, $i );
                $checked    = JHTML::_('grid.id', $i, $row->orderid );
                
        
            ?>                 
            
            <tr id="row-<?php echo $row->orderid; ?>">
                <td><div align="center"><?php echo $row->orderid; ?></div></td>
                <td>
					<?php echo $row->eventname; ?></strong> - <?php echo $row->ticketname; ?>
                    <?php if($row->seat_sector != 0){ echo ' - '.JText::_( 'COM_TICKETMASTER_SEATNUMBER' ).': '.checkSeat($row->orderid, $this->coords); } ?>
                    <br/><?php echo JText::_( 'COM_TICKETMASTER_DATE' ); ?>: <?php echo date ($this->config->dateformat, strtotime($row->ticketdate)); ?> - 
                    <?php echo JText::_( 'COM_TICKETMASTER_START' ); ?>: <?php echo $row->starttime; ?><br/>                    
                </td>
                <td><div align="center"><?php echo showprice($this->config->priceformat ,$row->ticketprice,$this->config->valuta); ?></div></td>                   
            </tr>
            
            <?php
              $k=1 - $k;
              }
            ?>                             
            
        </table>     

    
    <div style="clear:both; margin-top: 10px;"></div>
    
    <div id = "tm-total-container" style="height:25px; line-height:25px; font-size:110%;">
        
        <div style="float:right; width:18%; text-align:center;">
        	<?php echo showprice($this->config->priceformat , ($ordertotal+$discount)-$fees, $this->config->valuta); ?>
        </div> 	
        
        <div style="float:right; width:45%; text-align:right;"><?php echo JText::_('COM_TICKETMASTER_SUBTOTAL'); ?></div>
        
        <div style="clear:both; margin-top: 10px;"></div>   
        
    </div>
    
    <?php if($discount != 0){ ?>
        <div id = "tm-total-container" style="height:25px; line-height:25px; font-size:110%;">
            
            <div style="float:right; width:18%; text-align:center;">
                <?php echo showprice($this->config->priceformat , $discount, $this->config->valuta); ?>
            </div> 	
            
            <div style="float:right; width:45%; text-align:right;"><?php echo JText::_('COM_TICKETMASTER_DISCOUNT'); ?></div>
            
            <div style="clear:both; margin-top: 10px;"></div>   
            
        </div>
    <?php } ?> 

    <div id = "tm-total-container" style="height:25px; line-height:25px; font-size:110%;">
        
        <div style="float:right; width:18%; text-align:center;">
        	<?php echo showprice($this->config->priceformat , $fees, $this->config->valuta); ?>
        </div> 	
        
        <div style="float:right; width:45%; text-align:right;"><?php echo JText::_('COM_TICKETMASTER_FEES'); ?></div>
        
        <div style="clear:both; margin-top: 10px;"></div>   
        
    </div>    
  
      <div id = "tm-total-container" style="height:25px; line-height:25px; font-size:110%;">
        
        <div style="float:right; width:18%; text-align:center;">
        	<?php echo showprice($this->config->priceformat , $ordertotal, $this->config->valuta); ?>
        </div> 	
        
        <div style="float:right; width:45%; text-align:right;"><?php echo JText::_('COM_TICKETMASTER_ORDERTOTAL'); ?></div>
        
        <div style="clear:both; margin-top: 10px;"></div>   
        
    </div> 
    
<?php } ?> 

<div style="height: 25px; line-height:25px; width: 3%; float:left;">
	<input name="checkme" type="checkbox" value="checkme" id="checkme" />
</div>

<div style="height: 25px; line-height:11px; width: 97%; float:left; font-size:95%;">
	<?php echo JText::_('COM_TICKETMASTER_ACCEPT_YES_I_DO'); ?> <a href="#mb_inline" rel="lightbox[inline 800 600]" title="">
		<?php echo JText::_('COM_TICKETMASTER_TOS_READ'); ?></a> 
	<em><?php echo JText::_('COM_TICKETMASTER_ACCEPT_TOS_BEFORE_PROCEED'); ?></em>
</div>



<div style="padding-top:2px; clear:both"></div>

<div id = "paymentmethods">
	
    <h2 class="contentheading">
        <?php echo JText::_('COM_TICKETMASTER_PAYMENT'); ?>
    </h2>

	<?php 
    JPluginHelper::importPlugin( 'rdmedia' );
    $dispatcher =& JDispatcher::getInstance();
    $results = $dispatcher->trigger( 'display' );
	?>

</div>


<div id="mb_inline" style="display: none;">
    
    <h2 class="contentheading">
        <?php echo $this->tos->mailsubject; ?>
    </h2>
    
    <?php echo $this->tos->mailbody; ?>
    <a href="" onclick="Mediabox.close();return false;"><?php echo JText::_('COM_TICKETMASTER_CLOSE_TOS'); ?></a></span>
    
</div>

<?php function checkSeat($value, $seat) {
	
   for ($i = 0, $n = count($seat); $i < $n; $i++ ){

		if ($value == $seat[$i]->orderid) {
			$seat = $seat[$i]->seatid;
		}

	}	
		
	return $seat; 
} 

?>




