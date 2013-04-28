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

## Check if the file is included in the Joomla Framework
defined('_JEXEC') or die ('No Acces to this file!');

## Obtain user information.
$user   =  JFactory::getUser();
$userid = $user->id;
## getting the Joomla API Platform
$app     = JFactory::getApplication();
## Getting the global DB session
$session =& JFactory::getSession();

$pathway = $app->getPathway();
$pathway->addItem(JText::_( 'COM_TICKETMASTER_EVENT_OVERVIEW' ), 'index.php?option=com_ticketmaster');
$pathway->addItem(JText::_( 'COM_TICKETMASTER_YOUR_CART' ));

## Check if this is Joomla 2.5 or 3.0.+
$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');

if(!$isJ30) {
	JHTML::_( 'behavior.mootools' );
}

## Get document type and add it.
$document = JFactory::getDocument();
$document->addStyleSheet( 'components/com_ticketmaster/assets/css/component.css' );

if($this->config->load_bootstrap == 1){
	## Adding mootools for J!2.5
	JHTML::_('behavior.modal');
	## Include the tooltip behaviour.
	JHTML::_('behavior.tooltip', '.hasTip');
	$document->addScript('http://code.jquery.com/jquery-latest.js');
	$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' ); 
	$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/js/bootstrap.js');
	$button = 'btn btn-small';
}else{	
	$document->addStyleSheet( 'components/com_ticketmaster/assets/css/bootstrap.css' );
	$button = 'button_rdticketmaster';
}

$cssfile = 'components/com_ticketmaster/assets/css-overrides/cart.css';

## Check if there is a css override.
if (file_exists($cssfile)) {
    $document->addStyleSheet( $cssfile );
} else {
    $document->addStyleSheet( 'components/com_ticketmaster/assets/css/cart.css' );
}

$document->addScript('http://code.jquery.com/jquery-latest.js');
$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/lightbox/mediabox.js');
$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/lightbox/mediabox.css' );

## Including required paths to calculator.
$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
include_once( $path_include );	

## Total for this order:
$total = _getAmount($session->get('ordercode'));
$fees = _getFees($session->get('ordercode'));
$ordertotal = $total-$fees;

if ($this->config->pro_installed == 1){
	if ($this->required->total == 0){
		$link = JRoute::_('index.php?option=com_ticketmaster&view=checkout');
	}else{
		$link = JRoute::_('index.php?option=com_ticketmaster&view=cart');
	}
}else{
	$link = JRoute::_('index.php?option=com_ticketmaster&view=checkout');
}	

$shop_on = JRoute::_('index.php?option=com_ticketmaster&view=upcoming');


include_once( 'components/com_ticketmaster/assets/functions.php' );

$count = count($this->items);
$failds = count($this->failed);

?>

<script language="javascript">

var JQ = jQuery.noConflict();

JQ(document).ready(function() {
  
  JQ('a.delete').click(function(e) {
    e.preventDefault();
    
	var parent = JQ(this).parent();
	var orderid = parent.attr('id').replace('tm-cart-price-','');
	var container = parent.attr('id').replace('tm-cart-price-','tm-cart-container');
	var data = 'cid=' + orderid;

    JQ.ajax({
      type: 'get',
      url: 'index.php?option=com_ticketmaster&controller=order&task=remove&format=raw',
      data: 'orderid=' + parent.attr('id').replace('tm-cart-price-',''),
      beforeSend: function() {
        // The old way to show removed divs.
		//JQ("#row-"+orderid).css("border-color", "#fb6c6c");
		JQ('#tm-loader').show();
		JQ("#row-"+orderid).addClass("error");
      },
      success: function(result) {
			JQ("#row-"+orderid).remove();
			JQ("#tm-cart-total-price").html(result);
			JQ('#tm-loader').hide();
      }
    });

  });
  
});

</script>

<?php if ($count == 0 ){ ?>

    <h2 class="contentheading">
        <?php echo JText::_('COM_TICKETMASTER_YOUR_CART_EMPTY'); ?>
    </h2>
    
    <div style="float:left;">
    	
		<br/><?php echo JText::_('COM_TICKETMASTER_GO_TO_UPCOMING'); ?><br/><br/><br/>
        
        <a class="button_rdticketmaster" style="float:left;" onclick="document.location.href='<?php echo $shop_on; ?>'">
            <span><?php echo JText::_('COM_TICKETMASTER_AVAILABLE_EVENTS'); ?></span>                      
        </a>
        
    </div>

<?php }else{  ?>        

    <h2><?php echo JText::_('COM_TICKETMASTER_YOUR_CART'); ?></h2>
    
    <div id = "tm-cart-text">
    	<?php echo JText::_( 'COM_TICKETMASTER_YOUR_CART_TEXT' ); ?>
    </div>
    
     <?php if ($failds > 0) { ?>
     
     <div style="width:100%; padding:0px; margin-bottom: 10px;">
     
     	<div style="width:100%; padding:0px; margin-bottom:8px; color:#F00; font-size:110%;">
        	<strong><?php echo JText::_( 'COM_TICKETMASTER_PLEASE_CHOOSE_SEATS_NOW' ); ?></strong>
        </div>
     
		<?php $k = 0;
           for ($i = 0, $n = count($this->failed); $i < $n; $i++ ){ 
		   
		   $row = $this->failed[$i];
		   		   
		   $image = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmasterext'.DS.'assets'.DS.'seatcharts'.DS.$row->seat_chart;
		   ## Get the image size
		   list($width, $height, $type, $attr) = getimagesize($image);	
		   $link_seat = 'index.php?option=com_ticketmasterext&&tmpl=component&cid[]='.$row->parent;	   
		   
		?>
        
            <a class="button_rdticketmaster" id="seatselection" href="<?php echo $link_seat; ?>" 
            	rel="lightbox[external <?php echo $width+300; ?> <?php echo $height+190; ?>]">
                <span><?php echo JText::_('COM_TICKETMASTER_CHOOSE_SEAT_FOR'); ?> <?php echo $row->ticketname; ?></span>                      
            </a>             
		   
		<?php  $k=1 - $k; } ?> 
     
     </div>      		     
	 
	 <?php } ?>     
    
        <div class="failed" style="display: none;">
            <?php echo JText::_('COM_TICKETMASTER_CART_FAILED'); ?>
        </div>  
        
        <div id="ticketmaster-loading" align="center" style="margin-bottom:3px; height:20px;">
            <div id = "tm-loader" style=" display: none; ">
                <img src="components/com_ticketmaster/assets/images/ajaxloader.gif" height="20px" />
            </div>
        </div>    
        
    
        <table class="table table-striped" id="cart">               
            
            <thead>
                <th width="10%"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_ORDERID' ); ?></div></th>
                <th width="60%"><?php echo JText::_( 'COM_TICKETMASTER_EVENT_INFORMATION' ); ?></th>
                <th width="15%"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_PRICE' ); ?></div></th>
                <th width="15%"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_REMOVE' ); ?></div></th>                    
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
                <td><div align="center">
                        <div id = "tm-cart-price-<?php echo $row->orderid; ?>" class="tm-cart-price">
                            <a href="#" class="delete">
                                <img src="components/com_ticketmaster/assets/images/trash-icon-32x32.png" />
                            </a>
                        </div>                 
                	</div>
                </td>                    
            </tr>
            
            <?php
              $k=1 - $k;
              }
            ?>                             
            
        </table>     

        <div style="clear:both; margin-top: 15px;"></div>
        
        <div id = "tm-total-container" class="tm-total-container">
        
            <div id = "tm-cart-total-text"><?php echo JText::_( 'COM_TICKETMASTER_CART_TOTAL' ); ?></div>
            <div id = "tm-cart-total-price" class="tm-cart-total-price">
                <?php echo showprice($this->config->priceformat ,$ordertotal, $this->config->valuta); ?>
            </div>
                   
        </div>
    
        
        <div style="clear:both;text-align:left; float:right; margin-left:20px;">
                     
            <a class="button_rdticketmaster"  onclick="document.location.href='<?php echo $link; ?>'">
                <span><?php echo JText::_('COM_TICKETMASTER_CHECKOUT_NOW'); ?></span>                      
            </a>           
            
        </div>
        
        <div style="text-align:left;">
            <a class="button_rdticketmaster" onclick="document.location.href='<?php echo $shop_on; ?>'">
                <span><?php echo JText::_('COM_TICKETMASTER_SHOP_ON'); ?></span>                      
            </a>              
        </div>    
    
        <div style="clear:both; margin-top: 35px;"></div>
        
        <?php if( $this->config->use_coupons ){ ?> 
        
            <h2><?php echo JText::_('COM_TICKETMASTER_COUPON_CODE'); ?></h2> 
        	
            <div style="margin-bottom:12px;">
				<?php echo JText::_('COM_TICKETMASTER_COUPON_CODE_DESC'); ?>
            </div>

            <form action = "index.php" method="POST" name="adminForm" id="adminForm" class="form-inline">
         
                <input name="couponcode" id="couponcode" type="text" size="25" maxlength="50" />
                <input type="hidden" name="task" id="coupon" value="coupon" />
                <input type="hidden" name="controller" id="cart" value="checkout" />
                <input type="hidden" name="option" id="option" value="com_ticketmaster" /> 
                
                <input name="button" type="submit" value="<?php echo JText::_('COM_TICKETMASTER_COUPON_SUBMIT'); ?>" class="button_rdticketmaster"/>
             
            </form>    
        
        <?php } ?>           
        
    
<?php } ?> 

<?php function checkSeat($value, $seat) {
	
   for ($i = 0, $n = count($seat); $i < $n; $i++ ){

		if ($value == $seat[$i]->orderid) {
			$seat = $seat[$i]->seatid;
		}

	}	
		
	return $seat; 
} 

?>