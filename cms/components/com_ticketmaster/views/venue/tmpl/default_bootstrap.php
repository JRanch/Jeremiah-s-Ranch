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

$app 	 = JFactory::getApplication();
$pathway = $app->getPathway();
$pathway->addItem(JText::_( 'COM_TICKETMASTER_EVENT' ), 'index.php?option=com_ticketmaster&view=venue&id='.$this->data->venue);

## Obtain user information.
$user =  JFactory::getUser();
$userid = $user->id;
## Get document type and add it.
$document = JFactory::getDocument();

$document->addStyleSheet( 'components/com_ticketmaster/assets/css/component.css' );
$cssfile = 'components/com_ticketmaster/assets/css-overrides/venue.css';

## Check if there is a css override.
if (file_exists($cssfile)) {
    $document->addStyleSheet( $cssfile );
} else {
    $document->addStyleSheet( 'components/com_ticketmaster/assets/css/venue.css' );
}

## Adding the lightbox functionality

if ($this->config->load_jquery == 1) {
	$document->addScript('http://code.jquery.com/jquery-latest.js');
}elseif ($this->config->load_jquery == 2) {
	$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/jquery/jquery.js');
}


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
		JHTML::_( 'behavior.mootools' );		
		## Include the tooltip behaviour.
		JHTML::_('behavior.tooltip', '.hasTip');
		$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' ); 
		$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/js/bootstrap.js');
		$button = 'btn btn-small';
	}else{	
		$document->addStyleSheet( 'components/com_ticketmaster/assets/css/bootstrap.css' );
		$button = 'button_rdticketmaster';
	}
}else{
	
	## We are in J3, load the bootstrap!
	jimport('joomla.html.html.bootstrap');
	$button = 'btn';
	
}
?>

<h1><?php echo $this->data->venue; ?></h1>

<div id="ticketmaster_left_side">

      <?php if($this->data->locdescription != '') { echo $this->data->locdescription; ?> <br/><br/> <?php } ?>
    
      <?php echo $this->data->street; ?><br>
      <?php echo $this->data->zipcode; ?> 
      <?php echo $this->data->city; ?><br>
      <a href="<?php echo $this->data->website; ?>" target="_blank"><?php echo $this->data->website; ?></a><br>
      <?php echo $this->data->phone_number; ?><br>
 
    
      <strong><?php echo $this->data->contact_person; ?></strong><br>
      <a href="mailto:<?php echo $this->data->email_address; ?>"><?php echo $this->data->email_address; ?></a>
    
	<?php if ($this->config->show_google_maps == 1) { ?>
        <div id="map-canvas" style="width:100%; height:300px; margin-top:10px;"></div>
    <?php } ?>        

</div>

<div id="ticketmaster_right_side">

    <table class="table">
        
        <thead>
            <th width="75%"><?php echo JText::_( 'COM_TICKETMASTER_TM_EVENTS' ); ?></th>
            <th width="25%"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_PRICE' ); ?></div></th>
        </thead>
    
        <?php 
        $k = 0;
        for ($i = 0, $n = count($this->items); $i < $n; $i++ ){
            
                ## Give give $row the this->item[$i]
                $row        = &$this->items[$i];
                $published 	= JHTML::_('grid.published', $row, $i );
                $checked    = JHTML::_('grid.id', $i, $row->ticketid );
                $alias 		= JFilterOutput::stringURLSafe($this->data->eventname.'-'.$row->ticketname);
                $link 		= JRoute::_('index.php?option=com_ticketmaster&view=event&id='.$row->tid.':'.$alias);
                $item->odd	= $k;
        
        ?>
        
        <tr>
            <td>
            <?php echo date ($this->config->dateformat, strtotime($row->ticketdate)); ?><br/>
            <a href="<?php echo $link; ?>"><?php echo $row->ticketname; ?></a></td>
            <td><div align="center"><?php echo showprice($this->config->priceformat ,$row->ticketprice,$this->config->valuta); ?></div></td>
        </tr>
    
        <?php
          $k=1 - $k;
          }
        ?>           
    
    </table>


</div>

<div style="clear:both; margin-bottom:20px;"></div>

<script type="text/javascript"
  src="https://maps.googleapis.com/maps/api/js?key=<?php echo $this->config->google_maps_key;?>&sensor=false">
</script>

<script type="text/javascript">
	
var position = new google.maps.LatLng(<?php echo $this->data->googlemaps_latitude; ?>, <?php echo $this->data->googlemaps_longitude; ?>);
var marker;
var map;	
	
  function initialize() {
	var mapOptions = {
	  center: new google.maps.LatLng(<?php echo $this->data->googlemaps_latitude; ?>, <?php echo $this->data->googlemaps_longitude; ?>),
	  zoom: 13,
	  mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	var map = new google.maps.Map(document.getElementById("map-canvas"),
		mapOptions);
		
        var marker = new google.maps.Marker({
            position: position,
            map: map,
            title:"<?php echo $this->data->venue; ?>"
        });  
     
        var contentString = '<strong><?php echo $this->data->venue; ?></strong>';
        
        var infowindow = new google.maps.InfoWindow({
            content: contentString
        });
     
        google.maps.event.addListener(marker, 'click', function() {
          infowindow.open(map,marker);
        });
		
  }
  google.maps.event.addDomListener(window, 'load', initialize);
</script>


