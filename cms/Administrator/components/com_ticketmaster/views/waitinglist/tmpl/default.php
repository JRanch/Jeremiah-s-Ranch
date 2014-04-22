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
	$process_button = 'process';
	$process_button = 'refresh';
}else{
	$paid_button 	= 'thumbs-up';
	$process_button = 'user';
	$refresh_button = 'refresh';
}

## Setup the toolbars and functionality.
JToolBarHelper::title( JText::_( 'COM_TICKETMASTER_TICKETBOX' ), 'generic.png' );
JToolBarHelper::divider();
JToolbarHelper::custom( 'confirm', $paid_button, '', JText::_( 'COM_TICKETMASTER_TOOLBAR_CONFIRM' ), true, false);
JToolBarHelper::divider();
JToolbarHelper::custom( 'process', $process_button, '', JText::_( 'COM_TICKETMASTER_TOOLBAR_PROCESS_MANUALLY' ), true, false);
JToolBarHelper::divider();
JToolbarHelper::custom( 'systemcheck', $refresh_button, '', JText::_( 'COM_TICKETMASTER_TOOLBAR_PROCESS_SYSTEM' ), true, false);
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
}	

?>

<div class="alert">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <h4><?php echo JText::_( 'COM_TICKETMASTER_CP_WARNING' ); ?></h4> 
  <?php echo JText::_( 'COM_TICKETMASTER_PROCESSING_MSG' ); ?>
</div>

<form action = "index.php" method="POST" name="adminForm" id="adminForm" class="form-inline">
  <table class="table table-striped" width="100%">
    <thead>
      <tr>
        <th width="33" height="24"><div align="center">
          <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?> ) ;" />
        </div></th>
        <th width="173" class="title">
        <div align="left"><?php echo JText::_( 'COM_TICKETMASTER_EVENTINFORMATION' ); ?></div></th>
        <th class="title" width="261"><div align="left"><?php echo JText::_( 'COM_TICKETMASTER_ORDER_INFO' ); ?></div></th>
        <th class="title" width="75"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_TOTAL_TICKETS_2' ); ?></div></th>
        <th class="title" width="95"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_TOTAL_REGULAR_PRICE' ); ?></div></th>
        <th class="title" width="70"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_PROCESSED' ); ?></div></th>
        <th class="title" width="70"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_ACTIVATED' ); ?></div></th>
      </tr>
    </thead>
    <?php 
	   
	   $k = 0;
	   for ($i = 0, $n = count($this->items); $i < $n; $i++ ){
		
		## Give give $row the this->item[$i]
		$row        = &$this->items[$i];
		$checked    = JHTML::_('grid.id', $i, $row->ordercode );

	?>
    <tr class="<?php echo "row$k"; ?>">
      <td><div align="center"><?php echo $checked; ?></div></td>
      <td><div align="left"><strong><?php echo $row->eventname; ?></strong><br />
      <?php  if ($row->parentname != $row->ticketname) { 
	  	echo $row->parentname.' <em>[ '.$row->ticketname.' ]</em>'; }else{  echo $row->ticketname; 
	   } ?>
        </div></td>
      <td><div align="left">     
          <strong><?php echo JText::_( 'COM_TICKETMASTER_ORDERCODE' ); ?> <?php echo $row->ordercode; ?></strong>
          &nbsp;<em>[&nbsp;<a href="<?php echo $link;?>"><?php echo JText::_( 'COM_TICKETMASTER_SHOW_ORDER' ); ?></a>&nbsp;]</em><br />
          <?php echo $row->name; ?> - <?php echo $row->address; ?> - <?php echo $row->city; ?></div>
      </td>
      <td><div align="center"><?php echo $row->totaltickets; ?></div></td>
      <td><div align="center"><?php echo $this->config->valuta; ?> <?php echo number_format($row->orderprice, 2, ',', ''); ?></div></td>
      <td><div align="center">
      	  <?php if($row->processed == 1) { ?>
		  	  <img src="components/com_ticketmaster/assets/images/tick.png" width="16" height="16" />
		  <?php }else{ ?>
			  <img src="components/com_ticketmaster/assets/images/notice-note.png" width="16" height="16" />
		  <?php } ?>
          </div>
      </td>
      <td><div align="center">
      	  <?php if($row->confirmed == 1) { ?>
		  	  <img src="components/com_ticketmaster/assets/images/tick.png" width="16" height="16" />
		  <?php }else{ ?>
			  <img src="components/com_ticketmaster/assets/images/notice-note.png" width="16" height="16" />
		  <?php } ?>
          </div>
      </td>
    </tr>
    <?php
	  $k=1 - $k;
	  }
	  ?>
  </table>
 
  <table width="100%" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
        <td>
            <div align="center"><?php echo $this->pagination->getListFooter(); ?></div>
        </td>
    </tr>
  </table> 
  
  <input name = "option" type="hidden" value="com_ticketmaster" />
  <input name = "task" type="hidden" value="" />
  <input name = "boxchecked" type="hidden" value="0"/>
  <input name = "controller" type="hidden" value="waitinglist"/>
</form>