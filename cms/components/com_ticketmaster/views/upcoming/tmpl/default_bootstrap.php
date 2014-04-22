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

$app 	  = JFactory::getApplication();
$pathway  = $app->getPathway();
$document = JFactory::getDocument();
$pathway->addItem($this->tmpl->mailsubject);

$cssfile = 'components/com_ticketmaster/assets/css-overrides/upcoming-bootstrap.css';

## Check if there is a css override.
if (file_exists($cssfile)) {
    $document->addStyleSheet( $cssfile );
} else {
    $document->addStyleSheet( 'components/com_ticketmaster/assets/css/upcoming-bootstrap.css' );
}

$document->addStyleSheet( 'components/com_ticketmaster/assets/css/component.css' );
include_once( 'components/com_ticketmaster/assets/functions.php' );
jimport( 'joomla.filter.output' );
	
$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');

if($isJ30) {	
	## We are in J3, load the bootstrap!
	jimport('joomla.html.html.bootstrap');
}else{
	$document->addScript('https://code.jquery.com/jquery-latest.js');
	$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' ); 
	$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/js/bootstrap.js');	
}

$button = 'btn btn-small';

?>

<h2 class="contentheading"><?php echo $this->tmpl->mailsubject; ?></h2>
<?php echo $this->tmpl->mailbody; ?>

<?php 
 		 
 $k = 0;
 for ($i = 0, $n = count($this->items); $i < $n; $i++ ){

	
	## Give give $row the this->item[$i]
	$row        = &$this->items[$i];
	$published 	= JHTML::_('grid.published', $row, $i );
	$checked    = JHTML::_('grid.id', $i, $row->ticketid );
	$alias 		= JFilterOutput::stringURLSafe($row->upcomingeventname);
	$link 		= JRoute::_('index.php?option=' . $option . '&view=event&id='.$row->ticketid.':'.$alias);
	$month 		= date ( m, strtotime($row->ticketdate));
	$year 		= date ( Y, strtotime($row->ticketdate));
	
	$tickets_sold = countavailables($row->ticketid, $this->sold);
	$tickets_adds = countadded($row->ticketid, $this->added);	

	?>
	
    <div class="ticktmaster_box_heading">
    	<div style="float:left;">
        	<a href="<?php echo $link; ?>"><?php echo $row->upcomingeventname; ?></a>
        </div>

    </div>
    
    <div class="ticktmaster_box_content">
    	<?php echo $row->eventdescription; ?>
        <div class="ticktmaster_box_content_footer">
        
        <a href="<?php echo $link; ?>" class="<?php echo $button; ?> pull-right"><?php echo JText::_('COM_TICKETMASTER_SHOW_NOW'); ?></a>
		
		<?php if($this->config->show_price_eventlist == 1) { ?>
		
	        <?php if ($row->start_price > 0 && $row->end_price > 0) { ?>
	            <span class="label label-info pull-right" style="padding: 5px; margin-right: 5px;">
					<?php echo JText::_( 'COM_TICKETMASTER_PRICE_RANGE' ); ?> 
						<?php echo showprice($this->config->priceformat ,$row->start_price,$this->config->valuta); ?> - 
	                    <?php echo showprice($this->config->priceformat ,$row->end_price,$this->config->valuta); ?>
	            </span>
	        <?php } ?>
	        
	        <?php if ($row->start_price > 0 && $row->end_price == 0) { ?>
	            <span class="label label-info pull-right" style="padding: 5px; margin-right: 5px;">
					<?php echo JText::_( 'COM_TICKETMASTER_PRICE_STARTS_AT' ); ?> 
						<?php echo showprice($this->config->priceformat ,$row->start_price,$this->config->valuta); ?>
	            </span>
	        <?php } ?>        
         
        <?php } ?> 
        
        <?php if ($this->config->show_quantity_eventlist == 1) { ?>
            <span class="label label-info pull-right" style="padding: 5px;  margin-right: 5px;">
				<?php echo JText::_( 'COM_TICKETMASTER_PLACES_LEFT' ); ?> <?php echo $tickets_adds; ?>
            </span>
        <?php } ?> 
   
        </div>
    </div>
    
  

	<?php $k=1 - $k;
	  }
	?>

   <div align="center"><?php echo $this->pagination->getPagesLinks( ); ?></div>

<?php 
function countavailables($event, $sold) { 
	
	$availables = '';
	
    for($i = 0; $i < count($sold); $i++) { 
       
	   $counter  = $sold[$i];
	   if ( $event == $counter->ticketid ) {
	       $availables = $counter->soldtickets;
       }
	} 
	
    return $availables; 
} 

function countadded($event, $added) { 
	
	$adds = '';
		
    for($i = 0; $i < count($added); $i++) { 
       
	   $tel  = $added[$i];

	   if ( $event == $tel->ticketid ) {
	       $adds = $tel->totals;
		   
       }
	   
	} 
	
    return $adds; 
} 
?> 