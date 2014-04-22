<?php
/****************************************************************
 * @version		1.0.0 ticketmaster $							*
 * @package		ticketmaster									*
 * @copyright	Copyright © 2009 - All rights reserved.			*
 * @license		GNU/GPL											*
 * @author		Robert Dam										*
 * @author mail	info@rd-media.org								*
 * @website		http://www.rd-media.org							*
 ***************************************************************/

## no direct access
defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$document->addStyleSheet('components/com_ticketmaster/assets/component_css.css');

## Setup the toolbars.
JToolBarHelper::title( JText::_( 'COM_TICKETMASTER_EXPORTMANAGER' ), 'generic.png' );

## Include the tooltip behaviour.
JHTML::_('behavior.tooltip', '.hasTip');

## Require specific menu-file.
$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmasterext'.DS.'assets'.DS.'menu.php';
if (file_exists($path)) {
	include_once $path;
}else{
	$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'menu.php';
	include_once $path;
}

## Check if this is Joomla 2.5 or 3.0.+
$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');

if(!$isJ30) {
	## Adding mootools for J!2.5
	JHTML::_('behavior.modal');
	## Include the tooltip behaviour.
	JHTML::_('behavior.tooltip', '.hasTip');
	$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' ); 
	$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/js/bootstrap.js');
}	

?>

  <table class="table table-striped" width="50%">
    <thead>
      <tr>
          <th width="42" height="24"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_DOWNLOAD' ); ?></div></th>
          <th width="357" class="title">
            <div align="left"><?php echo JText::_( 'COM_TICKETMASTER_EVENTNAME' ); ?></div></th>
          <th class="title" width="114"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_EVENT_DATE' ); ?></div></th>
          <th class="title" width="118"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_TICKETS_ADDED' ); ?></div></th>
          <th class="title" width="118"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_TICKETS_SOLD' ); ?></div></th>
          <th class="title" width="118"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_TICKETS_AVAILABLE' ); ?></div></th>
        </tr>
    </thead>
    <?php 
	   
	   $k = 0;
	   for ($i = 0, $n = count($this->items); $i < $n; $i++ ){
		
		## Give give $row the this->item[$i]
		$row        = &$this->items[$i];
		$published 	= JHTML::_('grid.published', $row, $i );
		$tickets_sold = countavailables($row->eventid, $this->sold);
		$tickets_adds = countadded($row->eventid, $this->added);		

	?>
    <tr class="<?php echo "row$k"; ?>">
      <td><div align="center">
          <a href="index.php?option=com_ticketmaster&controller=export&task=export&eventid=<?php echo $row->eventid; ?>">          
              <img src="components/com_ticketmaster/assets/images/download-icon.png" width="16" height="16" />          </a>      
      </div></td>
      <td><div align="left"><?php echo $row->eventname; ?></div></td>
      <td><div align="center"><?php echo date ("d-m-Y", strtotime($row->eventdate)); ?></div></td>
      <td><div align="center"><?php echo $tickets_adds; ?></div></td>
      <td><div align="center"><?php echo $tickets_sold; ?></div></td>
      <td><div align="center"><?php echo $tickets_adds-$tickets_sold; ?></div></td>
      </tr>      
    <?php
	  $k=1 - $k;
	  }
	  ?>
  </table>  
  
<div style="font-weight:bold; font-size:105%;padding-top:15px;padding-bottom:15px; color:#FF0000" align="center">
!! <?php echo JText::_( 'COM_TICKETMASTER_EXPORTNOTES' ); ?> !!
</div>  

<?php 
function countavailables($event, $sold) { 
	
	$availables = '';
	
    for($i = 0; $i < count($sold); $i++) { 
       
	   $counter  = $sold[$i];
	   if ( $event == $counter->eventid ) {
	       $availables = $counter->soldtickets;
       }
	} 
	
    return $availables; 
} 

function countadded($event, $added) { 
	
	$adds = '';
		
    for($i = 0; $i < count($added); $i++) { 
       
	   $tel  = $added[$i];

	   if ( $event == $tel->eventid ) {
	       $adds = $tel->totals;
		   
       }
	   
	} 
	
    return $adds; 
} 
?> 