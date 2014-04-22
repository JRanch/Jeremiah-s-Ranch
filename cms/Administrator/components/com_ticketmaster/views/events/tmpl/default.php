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

## Setup the toolbars.
JToolBarHelper::title( JText::_( 'COM_TICKETMASTER_CATEGORIES' ), 'generic.png' );
JToolBarHelper::publish();
JToolBarHelper::unpublish();
JToolBarHelper::editList();
JToolBarHelper::addNew();
JToolBarHelper::deleteList();

$document = JFactory::getDocument();

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

<script language="javascript">

jQuery.noConflict();
  jQuery(document).ready(function(jQuery) {
		var oldSrc = '<?php echo JURI::root(true); ?>/administrator/templates/isis/images/admin/tick.png';
		var newSrc = '<?php echo JURI::root(true); ?>/administrator/components/com_ticketmaster/assets/images/tick.png';
		jQuery('img[src="' + oldSrc + '"]').attr('src', newSrc);
  });



</script>
<form action = "index.php" method="POST" name="adminForm" id="adminForm">

  <table class="table table-striped" width="100%">
    <thead>
      <tr>
        <th width="35" height="24"><div align="center">
          <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?> ) ;" />
        </div></th>
        <th width="250" class="title">
        <div align="left"><?php echo JText::_( 'COM_TICKETMASTER_EVENTNAME' ); ?></div></th>
        <th class="title" width="110"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_EVENTDATE' ); ?></div></th>
        <th class="title" width="110"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_SALES_STOP' ); ?></div></th>
        <th class="title" width="110"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_CODE' ); ?></div></th>

        <th class="title" width="110"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_WARNING' ); ?></div></th>
        <th class="title" width="110"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_SOLD_TICKETS' ); ?></div></th>
        <th class="title" width="110"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_AVIL_FOR_EVENT' ); ?></div></th>
        <th class="title" width="100"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_PUBLISHED' ); ?></div></th>
      </tr>
    </thead>
    <?php 
	   
	   $k = 0;
	   for ($i = 0, $n = count($this->items); $i < $n; $i++ ){
		
		## Give give $row the this->item[$i]
		$row        = &$this->items[$i];
		$published 	= JHTML::_('grid.published', $row, $i );
		$checked    = JHTML::_('grid.id', $i, $row->eventid );
		$link = 'index.php?option=com_ticketmaster&controller=events&task=edit&cid='.$row->eventid;
		$tickets_sold = countavailables($row->eventid, $this->sold);
		$tickets_adds = countadded($row->eventid, $this->added);

	?>
    <tr class="<?php echo "row$k"; ?>">
      <td><div align="center"><?php echo $checked; ?></div></td>
      <td><div align="left">
	  	<a href="<?php echo $link; ?>"><?php echo $row->eventname; ?></a></div></td>
      <td><div align="center"><?php echo date ("d-m-Y", strtotime($row->eventdate)); ?></div></td>
      <td>
	       <div align="center"><?php echo date ("d-m-Y" , strtotime($row->closingdate)); ?></div></td>
      <td><div align="center"><?php echo $row->groupname; ?></div></td>
     

      <td width="110"><div align="center"><?php echo $tickets_adds; ?></div>   
  	  </td>
      <td width="110"><div align="center">
	  	<?php if ($tickets_sold == ''){ 
			echo '0'; 
		}else{ 
			echo $tickets_sold; 
		} ?>
        </div></td>
      <td width="110"><div align="center"><?php echo $tickets_adds-$tickets_sold; ?></div></td>
      <td width="100"><div align="center"><?php echo $published; ?></div></td>
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
  <input name = "controller" type="hidden" value="events"/>

  

</form>


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