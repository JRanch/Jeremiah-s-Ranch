<?php
/****************************************************************
 * @version		Ticketmaster 2.5.5							
 * @package		ticketmaster									
 * @copyright	Copyright © 2009 - All rights reserved.			
 * @license		GNU/GPL											
 * @author		Robert Dam										
 * @author mail	info@rd-media.org								
 * @website		http://www.rd-media.org							
 ***************************************************************/

## no direct access
defined('_JEXEC') or die('Restricted access');

## Setup the toolbars.
JToolBarHelper::title( JText::_( 'COM_TICKETMASTER_VENUEMANAGER' ), 'generic.png' );
JToolBarHelper::publish();
JToolBarHelper::unpublish();
JToolBarHelper::editList();
JToolBarHelper::addNew();
JToolBarHelper::deleteList();

## Require specific menu-file.
$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmasterext'.DS.'assets'.DS.'menu.php';
if (file_exists($path)) {
	include_once $path;
}else{
	$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'menu.php';
	include_once $path;
}

## Get document type and add it.
$document = JFactory::getDocument();

## Check if this is Joomla 2.5 or 3.0.+
$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');

if(!$isJ30) {
	$document = JFactory::getDocument();
	## Adding mootools for J!2.5
	JHTML::_('behavior.modal');
	## Include the tooltip behaviour.
	JHTML::_('behavior.tooltip', '.hasTip');
	$document->addScript('https://code.jquery.com/jquery-latest.js');
	## Add the fancy lightbox for information fields.
	$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/lightbox/mediabox.js');
	$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/lightbox/mediabox.css' );	
	$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' ); 
	$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/js/bootstrap.js');
}else{
	JHtml::_('bootstrap.framework');
	$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/j3-lightbox/css/colorbox.css' );
	$document->addScript(JURI::root(true).'/administrator/components/com_ticketmaster/assets/j3-lightbox/js/jquery.colorbox.js');
	$document->addScript(JURI::root(true).'/administrator/components/com_ticketmaster/assets/j3-lightbox/js/colorbox.js');	
}
?>
<script>

var JQ = jQuery.noConflict();
	
	JQ(document).ready(function(){
	JQ(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});

});

jQuery.noConflict();
  jQuery(document).ready(function(jQuery) {
		var oldSrc = '<?php echo JURI::root(true); ?>/administrator/templates/isis/images/admin/tick.png';
		var newSrc = '<?php echo JURI::root(true); ?>/administrator/components/com_ticketmaster/assets/images/tick.png';
		jQuery('img[src="' + oldSrc + '"]').attr('src', newSrc);
  });

	
</script>



<form action = "index.php" method="POST" name="adminForm" id="adminForm" >

  <table class="table table-striped" width="100%">
    <thead>
      <tr>
        <th width="42" height="24"><div align="center">
          <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?> ) ;" />
        </div></th>
        <th width="187" class="title"><div align="left"><?php echo JText::_( 'COM_TICKETMASTER_VENUE_NAME' ); ?></div></th>
        <th class="title" width="187"><div align="left"><?php echo JText::_( 'COM_TICKETMASTER_VENUE_ADDRESS' ); ?></div></th>
        <th class="title" width="187"><div align="left"><?php echo JText::_( 'COM_TICKETMASTER_ZIPCITY' ); ?></div></th>
        <th class="title" width="182"><div align="left"><?php echo JText::_( 'COM_TICKETMASTER_URL' ); ?></div></th>
        <th class="title" width="75"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_MAPS' ); ?></div></th>
        <th class="title" width="75"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_STATES' ); ?></div></th>
        <th class="title" width="75"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_COUNTRY' ); ?></div></th>
        <th class="title" width="114"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_PUBLISHED' ); ?></div></th>
      </tr>
    </thead>
    <?php 
	   
	   $k = 0;
	   for ($i = 0, $n = count($this->items); $i < $n; $i++ ){
		
		## Give give $row the this->item[$i]
		$row        = &$this->items[$i];
		$published 	= JHTML::_('grid.published', $row, $i );
		$checked    = JHTML::_('grid.id', $i, $row->id );

	?>
    <tr class="<?php echo "row$k"; ?>">
      <td><div align="center"><?php echo $checked; ?></div></td>
      <td><div align="left"><?php echo $row->venue; ?></div></td>
      <td><div align="left"><?php echo $row->street; ?></div></td>
      <td><div align="left"><?php echo $row->zipcode; ?> <?php echo $row->city; ?></div></td>
      <td><div align="left"><?php echo $row->website; ?></div></td>
      <td><div align="center">
   
        <a class="iframe" href="http://maps.google.com/?q=<?php echo $row->googlemaps_latitude?>,<?php echo $row->googlemaps_longitude; ?>" 
        	rel="lightbox">
            	<img src="<?php echo JURI::base().'components/com_ticketmaster/assets/images/show-now.png'; ?>" border="0" alt="" /></a>              
                 
      </div></td>
      <td><div align="center"><?php echo $row->state; ?></div></td>
      <td><div align="center"><?php echo $row->country; ?></div></td>
      <td width="114"><div align="center"><?php echo $published; ?></div></td>
    </tr>
    <?php
	  $k=1 - $k;
	  }
	  ?>
  </table>  
  
  <input name = "option" type="hidden" value="com_ticketmaster" />
  <input name = "task" type="hidden" value="" />
  <input name = "boxchecked" type="hidden" value="0"/>
  <input name = "controller" type="hidden" value="venues"/>

  

</form>
        