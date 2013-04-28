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

## Add the tooltip behaviour.
JHTML::_('behavior.tooltip');
JHTML::_( 'behavior.mootools' );

$app =& JFactory::getApplication();
$pathway =& $app->getPathway();
$pathway->addItem(JText::_( 'COM_TICKETMASTER_EVENT_OVERVIEW' ), 'index.php?option=com_ticketmaster');
$pathway->addItem(JText::_( 'COM_TICKETMASTER_EVENT' ), 'index.php?option=com_ticketmaster&view=eventlist&id='.$this->items->eventid);
$pathway->addItem($this->items->eventname.' - '.$this->items->ticketname);

## Obtain user information.
$user = & JFactory::getUser();
$userid = $user->id;
## Get document type and add it.
$document = JFactory::getDocument();
$document->addStyleSheet( 'components/com_ticketmaster/assets/css/component.css' );

## Link to the lightbox:
$link = 'index.php?option=com_ticketmasterext&&tmpl=component&cid[]='.$this->items->ticketid;

$cssfile = 'components/com_ticketmaster/assets/css-overrides/event.css';

## Check if there is a css override.
if (file_exists($cssfile)) {
    $document->addStyleSheet( $cssfile );
} else {
    $document->addStyleSheet( 'components/com_ticketmaster/assets/css/event.css' );
}

## Adding the lightbox functionality
$document->addScript('/jquery/jquery-1.9.0.min.js');
$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/lightbox/mediabox.js');
$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/lightbox/mediabox.css' );

include_once( 'components/com_ticketmaster/assets/functions.php' );

## Getting the global DB session
$session =& JFactory::getSession();
## Gettig the orderid if there is one.
$ordercode = $session->get('ordercode');

## Including required paths to calculator.
$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
include_once( $path_include );	

## Total for this order:
$total = _getAmount($session->get('ordercode'));
$fees = _getFees($session->get('ordercode'));
$ordertotal = $total-$fees;

## Check if this is Joomla 2.5 or 3.0.+
$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');

if(!$isJ30) {
	
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
}

## Redirection link in JRoute:
$gotocart = JRoute::_('index.php?option=com_ticketmaster&view=cart');

if ($this->extended->seat_chart != '') {
	
	$image = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmasterext'.DS.'assets'.DS.'seatcharts'.DS.$this->extended->seat_chart;
	## Get the image size
	list($width, $height, $type, $attr) = getimagesize($image);
	
}

?>

<script type="text/javascript">
  
  function updateCart(){
  	
	var JQ = jQuery.noConflict();
  	JQ('#cart-information-loader').show();

	var order = 'ordercode=' + <?php echo $ordercode; ?> ;

	JQ.ajax({
		//this is the php file that processes the data and send mail
		url: "index.php?option=com_ticketmaster&controller=order&task=updatecart&format=raw", 
		//POST method is used
		type: "POST",
		//pass the data         
		data: order,     
		//Do not cache the page	
		cache: false,
		//success			
		success: function (html) {              
			//if process.php returned 1/true (send mail success)
			 JQ("#cart-information").html(html); 
			 JQ('#cart-information-loader').hide();
			 JQ("#seatselection").show(); 
		} 		
	});
	
  }
  
  function init(items,userId) {  
    
	// Getting the data we need for submission.
	var JQ = jQuery.noConflict();
    var inputdata = JQ("#qty_"+items).val();
	
	// Showing the loader image.	
	JQ('#tm-loader').show();
	
	//organize the data properly
	var data = 'amount=' + inputdata + '&ticketid=' + items +  '&ordercode='
	+ <?php echo $ordercode; ?> + '&togo='  + <?php echo $this->items->totaltickets; ?> + '&eventid=' + <?php echo $this->items->eventid; ?> +'';	

	JQ.ajax({
		//this is the php file that processes the data and send mail
		url: "index.php?option=com_ticketmaster&controller=order&task=addtocart&format=raw", 
		//POST method is used
		type: "POST",
		//pass the data         
		data: data,     
		//Do not cache the page	
		cache: false,
		//success			
		success: function (html) {              

			if (html==1) {                 
				//if process.php returned 0/false (send mail failed)
				JQ('#tm-loader').hide();
				JQ('div.success').slideToggle('slow').delay(2000).slideToggle(1000);
				JQ("#qty_"+items).val('1');	
				updateCart();
			} else if (html==2) {
				//if process.php returned 0/false (send mail failed)
				JQ('#tm-loader').hide('slow');
				JQ('div.failed').slideToggle('slow').delay(2000).slideToggle(1000);
				JQ("#qty_"+items).val(''); 
			} else if (html==3) {
				//if process.php returned 0/false (send mail failed)
				JQ('#tm-loader').hide('slow');
				JQ('div.soldout').slideToggle('slow').delay(2000).slideToggle(1000);
				JQ("#qty_"+items).val(''); 
			} else if (html==4) {
				//if process.php returned 0/false (send mail failed)
				JQ('#tm-loader').hide('slow');
				JQ('div.lowamount').slideToggle('slow').delay(2000).slideToggle(1000);
				JQ("#qty_"+items).val(''); 
			}   else if (html==7) {
				//if process.php returned 0/false (send mail failed)
				JQ('#tm-loader').hide('slow');
				JQ('div.maxorder').slideToggle('slow').delay(2000).slideToggle(1000);
				JQ("#qty_"+items).val(''); 
			}   else if (html==8) {
				//if process.php returned 0/false (send mail failed)
				JQ('#tm-loader').hide('slow');
				JQ('div.minorder').slideToggle('slow').delay(2000).slideToggle(1000);
				JQ("#qty_"+items).val(''); 
			}    
			    
		} 		
	});
	
};



</script>

<?php if ($this->items->eventname != $this->items->ticketname) { ?>
	<h2><?php echo $this->items->eventname; ?>  - <?php echo $this->items->ticketname; ?></h2>
<?php }else{ ?>    
	<h2><?php echo $this->items->eventname; ?></h2>
<?php } ?>

<div id="ticketmaster_left">

	<div id="ticketmaster_venue_header">
    	<span class="ticketmaster-header-text">
			<?php echo JText::_('COM_TICKETMASTER_EVENT_DETAILED'); ?>
        </span>
    </div>
    
    <div id="ticketmaster-eventdetails">
    	<?php 
		if ($this->items->ticketdescription != ''){
			echo $this->items->ticketdescription;
		}else{
			echo '<br/><br/><br/><br/>';
		}
		?>
    </div>
    
    <div id="ticketmaster-loading" align="center">
        <div id = "tm-loader" style="display: none;">
        	<img src="components/com_ticketmaster/assets/images/ajaxloader.gif" height="20px" />
        </div>
    </div>

    <div class="success" style="display: none;">
		<?php echo JText::_('COM_TICKETMASTER_EVENT_ADDED_TO_CART'); ?>
    </div>
    
    <div class="failed" style="display: none;">
		<?php echo JText::_('COM_TICKETMASTER_EVENT_FAILED_ADD_TO_CART'); ?>
    </div>
 
     <div class="soldout" style="display: none;">
		<?php echo JText::_('COM_TICKETMASTER_EVENT_SOLD_OUT'); ?>
    </div>

     <div class="lowamount" style="display: none;">
		<?php echo JText::_('COM_TICKETMASTER_EVENT_NO_AMOUNT'); ?>
    </div>
    
     <div class="maxorder" style="display: none;">
		<?php echo JText::_('COM_TICKETMASTER_TO_MANY_ORDERED'); ?>
    </div>  
    
     <div class="minorder" style="display: none;">
		<?php echo JText::_('COM_TICKETMASTER_TO_LESS_ORDERED'); ?>
    </div>    
    
	<?php if ($this->config->show_quantity_eventlist == 1) { ?>
        <?php if ($this->items->totaltickets < 25 && $this->items->totaltickets != 0) { ?>
            <div id = "ticketmaster-order-warning">
                <strong>
                <?php echo JText::_('COM_TICKETMASTER_ONLY').' '.$this->items->totaltickets.' '. JText::_('COM_TICKETMASTER_LEFT_MSG').' '.			
                $this->items->eventname; ?>
                </strong>
            </div>
        <?php } ?>
    <?php } ?>            
    
    <?php if (count($this->childs) != 0) { ?>

       <table class="table table-striped">               
    	
		<?php 
        
        $k = 0;
        for ($i = 0, $n = count($this->childs); $i < $n; $i++ ){
        
        
        ## Give give $row the this->item[$i]
        $row        = &$this->childs[$i];
        $item->odd	= $k; 
        
        ?>
        
        
            <tr>
                <td><div style="padding-top: 3px;">
                    <input id="qty_<?php echo $row->ticketid;?>" type="text" autocomplete="OFF" value="1"  style="text-align:center;" size="1" />
                    &nbsp; <?php echo $row->ticketname; ?> - <?php echo showprice($this->config->priceformat ,$row->ticketprice,
                                $this->config->valuta); ?>
                </div>
                </td>
                <td><?php if ($row->totaltickets == 0){ ?>
                       <div style="text-align:right; padding-top: 5px; padding-right: 2px;">
                            <?php echo JText::_('COM_TICKETMASTER_SOLD_OUT'); ?>
                       </div>
                        <?php } else { ?>    	   
                       <a class="button_rdticketmaster" onclick="init(<?php echo $row->ticketid;?>,2)" style="float:right;">
                          <span><?php echo JText::_('COM_TICKETMASTER_ADD'); ?></span>                      
                       </a>
                       <?php } ?> 
                </td>
            </tr> 
            
			<?php if ( $row->min_ordering != 0 || $row->max_ordering != 0 ) { ?>
            	
            <?php 
				$minimum = str_replace('%%TICKETNAME%%', $row->ticketname, JText::_('COM_TICKETMASTER_MINIMUM_FOR_ORDER'));
				$maximum = str_replace('%%TICKETNAME%%', $row->ticketname, JText::_('COM_TICKETMASTER_MAXIMUM_FOR_ORDER'));
			?>
                <tr class="info">
                   <td colspan="2">
                        <div style="font-size:85%; text-align:center;">
                            <?php echo $minimum; ?> <?php echo $row->min_ordering; ?> || <?php echo $maximum; ?> <?php echo $row->max_ordering; ?>
                        </div>                    
                   </td>
                </tr>
            <?php } ?>                       
            
		 <?php		
        	$k=1 - $k;
        }
        ?> 
        
        </table>          
    
    <?php }else{ ?>

            <table class="table table-striped">               
                <tr>
                    <td><div style="padding-top: 3px;">
                        <input id="qty_<?php echo $this->items->ticketid;?>" type="text" autocomplete="OFF" value="1"  style="text-align:center;" size="1" />
                        &nbsp; <?php echo $this->items->ticketname; ?> - <?php echo showprice($this->config->priceformat ,$this->items->ticketprice,
									$this->config->valuta); ?>
                    </div>
                    </td>
                    <td><?php if ($this->items->totaltickets == 0){ ?>
                           <div style="text-align:right; padding-top: 5px; padding-right: 2px;">
                                <?php echo JText::_('COM_TICKETMASTER_SOLD_OUT'); ?>
                           </div>
                            <?php } else { ?>    	   
                           <a class="button_rdticketmaster" onclick="init(<?php echo $this->items->ticketid;?>,2)" style="float:right;">
                              <span><?php echo JText::_('COM_TICKETMASTER_ADD'); ?></span>                      
                           </a>
                           <?php } ?> 
                    </td>
                </tr>
            </table> 
            
            <?php if ( $this->items->min_ordering != 0 || $this->items->max_ordering != 0 ) { ?>
                  <div style="height:40px; font-size:80%; padding-left:15px; text-align:center;">
                    ** <?php echo JText::_('COM_TICKETMASTER_MINIMUM_FOR_ORDER'); ?> <?php echo $this->items->min_ordering; ?> || 
                    <?php echo JText::_('COM_TICKETMASTER_MAXIMUM_FOR_ORDER'); ?> <?php echo $this->items->max_ordering; ?>
                </div>  
            <?php } ?>       
    
    <?php } ?>

</div>

<?php 
if ($this->ticket->total > 1) {
	$tickets = JText::_('COM_TICKETMASTER_TICKETS');
}else{
	$tickets = JText::_('COM_TICKETMASTER_TICKET');
}	

?>

<div id="ticketmaster_right" style="float:right;">
	<div id="ticketmaster_cart_header">
    	<span class="ticketmaster-header-text">
			<?php echo JText::_('COM_TICKETMASTER_CART_DETAILS'); ?>
        </span>
    </div>
    
    <div id="ticketmaster-cartdetails">
        
        <div id = "cart-information" style="width: 90%; text-align:left; font-size:115%; padding-top: 10px;">
        	
			<?php  if ($this->ticket->total == 0) {
					
					 echo JText::_('COM_TICKETMASTER_EMPTY_CART'); 
					 $visible= 'hidden';
				    
					}else{ 
					
						$visible= 'visible';?>
			
                        <strong><?php echo $this->ticket->total .' '.$tickets; ?> - <?php 
                                echo showprice($this->config->priceformat, $ordertotal, $this->config->valuta); ?> 
                                <?php echo JText::_('COM_TICKETMASTER_IN_CART'); ?>
                        </strong>
            
            <?php } ?>
            
         </div>
     	
       
        <div id = "cart-information" style="font-size:100%; padding-top: 10px; padding-bottom: 10px; width:100%; float:right;">             

            <div id="cart-information-loader-2" style="width:100%; height:20px; margin-top:5px;">
                <div id = "cart-information-loader" style="display: none; margin:0px;" align="center">
                    <img src="components/com_ticketmaster/assets/images/ajaxloader.gif" height="15px" />
                </div>
            </div>
            
			<?php if ($this->items->show_seatplans == 1) { ?>
                
                <a class="button_rdticketmaster" id="seatselection" href="<?php echo $link; ?>" 
                	rel="lightbox[external <?php echo $width+300; ?> <?php echo $height+170; ?>]">
                    <span><?php echo JText::_('COM_TICKETMASTER_CHOOSE_SEAT'); ?></span>                      
                </a>             
			
			<?php } ?>             
               
            <a class="button_rdticketmaster" onClick="location.href='<?php echo $gotocart; ?>'">
                <span><?php echo JText::_('COM_TICKETMASTER_SHOW_CART'); ?></span>                      
            </a>        
        </div> 
        
		<div id="cart-information-loader-1" style="font-size:115%; padding-top: 10px; width:15%; float:right; margin-top:2px; display: none;">
        	<img src="components/com_ticketmaster/assets/images/ajax-loader.gif" height="20px" />
        </div>        
 
    </div> 
    
</div>

<?php if ($this->config->show_venuebox == 1) { ?>

<div id="ticketmaster_right" style="float:right;">

	<div id="ticketmaster_venue_header">
    	<span class="ticketmaster-header-text">
			<?php echo JText::_('COM_TICKETMASTER_VENUE_DETAILS'); ?>
        </span>
    </div>
    
    <div id="ticketmaster-venuedetails">
    	
        <table class="table table-striped">
        	<tr><td><?php echo $this->items->venue; ?></strong></td></tr>
            <tr><td><?php echo $this->items->street; ?></td></tr>
            <tr><td><?php echo $this->items->zipcode; ?></td></tr>
            <tr><td><?php echo $this->items->city; ?></td></tr>
            <tr><td><a href="<?php echo $this->items->website; ?>" target="_blank"><?php echo $this->items->website; ?></a></td></tr>
        </table>
        
		<?php if ($this->config->show_google_maps == 1) { ?>
        
        <div id = "ticketmaster-eventmap" align="center">
			
        	<span class="ticketmaster-maps-text">
        <a href="http://maps.google.com/maps?q=<?php echo $this->items->googlemaps_longitude; ?>,<?php echo $this->items->googlemaps_latitude; ?>&ll=<?php echo $this->items->googlemaps_longitude; ?>,<?php echo $this->items->googlemaps_latitude; ?>&z=<?php echo $this->items->googlemaps_precision; ?>&amp;output=embed" 
        	rel="lightbox[external 800 450]"><?php echo JText::_('COM_TICKETMASTER_VENUE_SHOWMAP'); ?></a>            
         
            </span>
            
        </div>
        <?php } ?>
        
    </div> 
    
</div>

<?php } ?>

<div style="clear:both;"></div>

