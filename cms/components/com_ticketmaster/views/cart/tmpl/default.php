<?php 
/************************************************************
 * @version			ticketmaster 2.5.5
 * @package			com_ticketmaster
 * @copyright		Copyright � 2009 - All rights reserved.
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
$session = JFactory::getSession();
$document = JFactory::getDocument();

$pathway = $app->getPathway();
$pathway->addItem(JText::_( 'COM_TICKETMASTER_EVENT_OVERVIEW' ), 'index.php?option=com_ticketmaster');
$pathway->addItem(JText::_( 'COM_TICKETMASTER_YOUR_CART' ));

## Check if this is Joomla 2.5 or 3.0.+
$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');

if ($this->config->load_jquery == 1) {
	$document->addScript('https://code.jquery.com/jquery-latest.js');
}elseif ($this->config->load_jquery == 2) {
	$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/jquery/jquery.js');
}


if(!$isJ30) {
	
	if($this->config->load_bootstrap == 1){
		
		JHTML::_( 'behavior.mootools' );
		## Adding mootools for J!2.5
		JHTML::_('behavior.modal');
		## Include the tooltip behaviour.
		JHTML::_('behavior.tooltip', '.hasTip');

		$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' ); 
		$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/js/bootstrap.js');
		
		$document->addScript(JURI::root(true).'/components/com_ticketmaster/assets/j3-lightbox/js/jquery.colorbox.js');
		$document->addScript(JURI::root(true).'/components/com_ticketmaster/assets/j3-lightbox/js/colorbox.js');
		$document->addStyleSheet( JURI::root(true).'/components/com_ticketmaster/assets/j3-lightbox/css/colorbox.css' );	
		
		$button = 'btn btn-small';
		$btndanger = 'btn btn-small btn-danger';
		
	}else{	

		$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/lightbox/mediabox.js');
		$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/lightbox/mediabox.css' );
	
		$document->addStyleSheet( 'components/com_ticketmaster/assets/css/bootstrap.css' );
		$button = 'button_rdticketmaster';
	}	
	
}else{

	## We are in J3, load the bootstrap!

	JHtml::_('bootstrap.framework');
	jimport('joomla.html.html.bootstrap');
	
	$document->addScript(JURI::root(true).'/components/com_ticketmaster/assets/j3-lightbox/js/jquery.colorbox.js');
	$document->addScript(JURI::root(true).'/components/com_ticketmaster/assets/j3-lightbox/js/colorbox.js');
	$document->addStyleSheet( JURI::root(true).'/components/com_ticketmaster/assets/j3-lightbox/css/colorbox.css' );

	$button = 'btn';
	$btndanger = 'btn btn-small btn-danger';
	
}

## Get document type and add it.
$document->addStyleSheet( 'components/com_ticketmaster/assets/css/component.css' );

$cssfile = 'components/com_ticketmaster/assets/css-overrides/cart.css';

## Check if there is a css override.
if (file_exists($cssfile)) {
    $document->addStyleSheet( $cssfile );
} else {
    $document->addStyleSheet( 'components/com_ticketmaster/assets/css/cart.css' );
}

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

$items = count($this->items);
$waiters = count($this->waiters);
$failds = count($this->failed);


?>

<script language="javascript">

var JQ = jQuery.noConflict();

var max = 255;
JQ(document).ready(function() {
	JQ('#remarks').keyup(function() {
		if(JQ(this).val().length > max) {
			JQ(this).val(JQ(this).val().substr(0, max));
		}
		
		JQ('#chars-remaining').html('<?php echo JText::_( 'COM_TICKETMASTER_REMAINING' ); ?> ' + (max - JQ(this).val().length));
	});	
});

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

JQ(document).ready(function() {
  
  JQ('a.deletewaiting').click(function(e) {
    e.preventDefault();
    
	var parent = JQ(this).parent();
	var orderid = parent.attr('id').replace('tm-cart-waiting-','');
	var container = parent.attr('id').replace('tm-cart-waiting-','tm-cart-container');
	var data = 'cid=' + orderid;

    JQ.ajax({
      type: 'get',
      url: 'index.php?option=com_ticketmaster&controller=order&task=removeWaiting&format=raw',
      data: 'orderid=' + parent.attr('id').replace('tm-cart-waiting-',''),
      beforeSend: function() {
		JQ('#tm-loader').show();
		JQ("#wait-"+orderid).addClass("error");
      },
      success: function(result) {
			JQ("#wait-"+orderid).remove();
			JQ("#tm-cart-total-price").html(result);
			JQ('#tm-loader').hide();
      }
    });

  });
  
});

JQ(document).ready(function() {
  
  JQ('#checkout').click(function(e) {
    e.preventDefault();
    	
		// getting the remarks if available.
		var remarks = JQ( "#remarks" ).val();
		
		if(remarks == '') {
			
			// If remarks is empty, submit now.
			document.location.href='<?php echo $link; ?>';
		
		}else{
			
			// Please do AJAX call with data. -- Get post data first. 
			var data = 'content='+remarks+'&ordercode='+ <?php echo $session->get('ordercode'); ?> +'';

			JQ.ajax({
				//this is the php file that processes the data and send mail
				url: "index.php?option=com_ticketmaster&controller=cart&task=saveRemark&format=raw", 
				//POST method is used
				type: "POST",
				// data:
				data: data,
				// data type = json 
				dataType: 'json',				
				//Do not cache the page	
				cache: false,
				// Before sending the form
				beforeSend: function() {
					JQ( "#test" ).html(' <?php echo JText::_('COM_TICKETMASTER_PLEASE_WAIT'); ?>');
					JQ('<img class="inline" style="margin-right:5px;" src="components/com_ticketmaster/assets/images/loading.gif" />').prependTo("#test");
				},				
				// On Success trigger						
				success: function (html) {              

					if(html.status == 666){
						JQ( "#chars-remaining" ).html(html.msg);
						JQ( "#inner" ).html(html.msg);
					}else{
						JQ( "#chars-remaining" ).html(html.msg);
						JQ( "#chars-remaining" ).css('color', '#04B404');
						
						setTimeout(function() {
							  document.location.href='<?php echo $link; ?>';
						}, 1000);												
					}

				},
			   error:function (xhr, ajaxOptions, thrownError){
				 alert(xhr.status);
			  } 			 		
			});			  

		}

  });
  
});

</script>

<?php if ($items == 0 && $waiters == 0 ){ ?>

    <div style="min-height:250px;">
        <h2><?php echo JText::_('COM_TICKETMASTER_YOUR_CART_EMPTY'); ?></h2>
            
        <br/><?php echo JText::_('COM_TICKETMASTER_GO_TO_UPCOMING'); ?><br/><br/><br/>
        
        <a class="<?php echo $button; ?>" style="float:left;" onclick="document.location.href='<?php echo $shop_on; ?>'">
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
		   
		   if($row->parent != 0) {
		   	  $link_seat = 'index.php?option=com_ticketmasterext&&tmpl=component&cid[]='.$row->parent;
		   }else{
			   $link_seat = 'index.php?option=com_ticketmasterext&&tmpl=component&cid[]='.$row->ticketid;
		   }
		   
		?>
        <?php if (!$isJ30) { ?>
        
            <a class="<?php echo $btndanger; ?> " id="seatselection" href="<?php echo $link_seat; ?>" 
            	rel="lightbox[external <?php echo $width+300; ?> <?php echo $height+190; ?>]">
                <span><?php echo JText::_('COM_TICKETMASTER_CHOOSE_SEAT_FOR'); ?> <?php echo $row->ticketname; ?></span>                      
            </a> 
        
        <?php }else{ ?>

            <a class="iframe <?php echo $btndanger; ?>" id="seatselection" href="<?php echo $link_seat; ?>">
                <span><?php echo JText::_('COM_TICKETMASTER_CHOOSE_SEAT_FOR'); ?> <?php echo $row->ticketname; ?></span>                      
            </a>
                      
        <?php } ?>                
		   
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
        
        <table class="table" id="cart">               
            
            <?php if($items != 0) { ?>
            <thead>
                <th width="10%"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_ORDERID' ); ?></div></th>
                <th width="60%"><?php echo JText::_( 'COM_TICKETMASTER_EVENT_INFORMATION' ); ?></th>
                <th width="15%"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_PRICE' ); ?></div></th>
                <th width="15%"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_REMOVE' ); ?></div></th>                    
            </thead>
            <?php } ?> 
            
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
                    <br/><?php echo JText::_( 'COM_TICKETMASTER_DATE' ); ?>: <?php echo date ($this->config->dateformat, strtotime($row->ticketdate)); ?>
		              <?php if ( $row->show_end_date == 1 ){?>
		              - <?php echo date ($this->config->dateformat, strtotime($row->end_date)); ?>
		              <?php } ?>                   
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
            
			<?php if (count($this->waiters) != 0) { ?>
                <tr>
                    <td colspan="4"><div style="padding-left:10px; font-weight:bold;">
						<?php echo JText::_( 'COM_TICKETMASTER_ITEMS_ON_WAITINGLIST' ); ?><br/>
                        <?php echo JText::_( 'COM_TICKETMASTER_A PAYMENT_REQUEST_WILL BE_SENT' ); ?>
                    </div></td>
                </tr>
			<?php } ?>  
                      
			<?php 
               
               $k = 0;
               for ($i = 0, $n = count($this->waiters); $i < $n; $i++ ){
                
                ## Give give $row the this->item[$i]
                $row        = &$this->waiters[$i];
               
        
            ?>                 
            
            <tr id="wait-<?php echo $row->id; ?>">
                <td><div align="center"><?php echo $i+1; ?></div></td>
                <td>
					<?php echo $row->eventname; ?></strong> - <?php echo $row->ticketname; ?>
                    <?php if($row->seat_sector != 0){ echo ' - '.JText::_( 'COM_TICKETMASTER_SEATNUMBER' ).': '.checkSeat($row->orderid, $this->coords); } ?>
                    <br/><?php echo JText::_( 'COM_TICKETMASTER_DATE' ); ?>: <?php echo date ($this->config->dateformat, strtotime($row->ticketdate)); ?> 
                      <?php if ( $row->show_end_date == 1 ){?>
		                - <?php echo date ($this->config->dateformat, strtotime($row->end_date)); ?>
		              <?php } ?>                      
                </td>
                <td></td>
                <td><div align="center">
                        <div id = "tm-cart-waiting-<?php echo $row->id; ?>" class="tm-cart-price">
                            <a href="#" class="deletewaiting">
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
    	<?php if($this->config->show_remark_field == 1) { ?>
        <div style="clear:both;text-align:left; width:100%; float:right; margin:0 0 15px 0;">
        	
            <h2><?php echo JText::_('COM_TICKETMASTER_ENTER_REMARKS'); ?></h2>

            <textarea rows="3" style="width:98%;" id="remarks" name="remarks" maxlength="255"></textarea>
            <div id="chars-remaining" style="margin-top:-5px; width: 100%; text-align:right; height: 20px; font-size:90%; line-height:18px; font-weight:bold;">
                <?php echo JText::_( 'COM_TICKETMASTER_REMAINING' ); ?> 255
            </div>

        </div>
        <?php }else{ ?>
       	 	<input type="hidden" name="remarks" id="remarks" value="" />
        <?php } ?>
        	
        
        <div style="clear:both;text-align:left; float:right; margin-left:20px;">
                     
            <a class="<?php echo $button; ?>"  id="checkout">
                <span id="test"><?php echo JText::_('COM_TICKETMASTER_CHECKOUT_NOW'); ?></span>                      
            </a>           
            
        </div>
        
        <div style="text-align:left;">
            <a class="<?php echo $button; ?>" onclick="document.location.href='<?php echo $shop_on; ?>'">
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
         
                <input name="couponcode" id="couponcode" type="text" class="input-medium" size="25" maxlength="50" />
                <input type="hidden" name="task" id="coupon" value="coupon" />
                <input type="hidden" name="controller" id="cart" value="checkout" />
                <input type="hidden" name="option" id="option" value="com_ticketmaster" /> 
                
                <input name="button" type="submit" value="<?php echo JText::_('COM_TICKETMASTER_SUBMIT_COUPON'); ?>" class="<?php echo $button; ?>"/>
             
            </form>    
        
        <?php } ?>           
        
    
<?php } ?> 

<?php function checkSeat($value, $seat) {
	
   for ($i = 0, $n = count($seat); $i < $n; $i++ ){

		if ($value == $seat[$i]->orderid) {
			if($seat[$i]->row_name != ''){
				$seat = $seat[$i]->row_name.$seat[$i]->seatid;
			}else{
				$seat = $seat[$i]->seatid;
			}
		}

	}	
		
	return $seat; 
} 

?>