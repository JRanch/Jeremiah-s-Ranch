<?php

/****************************************************************
 * @version			2.5.5											
 * @package			ticketmaster									
 * @copyright		Copyright © 2009 - All rights reserved.			
 * @license			GNU/GPL											
 * @author			Robert Dam										
 * @author mail		info@rd-media.org								
 * @website			http://www.rd-media.org							
 ***************************************************************/


// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');

$function	= JRequest::getCmd('function', 'jSelectContact');

$document = JFactory::getDocument();
$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' );

?>
<font color="#0033CC" face="Arial, Helvetica, sans-serif">
	<h3><?php echo JText::_('COM_TICKETMASTER_SELECT_TICKETS'); ?></h3>
</font>
<?php echo JText::_('COM_TICKETMASTER_SELECT_EVENTLIST_DESC'); ?>
<br/><br/>
<form action="<?php echo JRoute::_('index.php?option=com_contact&category=category&layout=modal&tmpl=component');?>" method="post" name="adminForm" id="adminForm">
   
  <table class="table table-striped" width="100%">
    <thead>
      <tr>
        <th height="24" colspan="2" class="title"><div align="left"><?php echo JText::_( 'COM_TICKETMASTER_TICKETNAME' ); ?></div></th>
        <th class="title" width="144"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_GROUP' ); ?></div></th>
        <th class="title" width="127"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_AVAILABLE' ); ?></div></th>
        <th class="title" width="129"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_PUBLISHED' ); ?></div></th>
      </tr>
    </thead>
    <?php 
	   
	   $k = 0;
	   for ($i = 0, $n = count($this->items); $i < $n; $i++ ){
		
		## Give give $row the this->item[$i]
		$row        = &$this->items[$i];
		$published 	= JHTML::_('grid.published', $row, $i );
		$checked    = JHTML::_('grid.id', $i, $row->ticketid );
		$link       = 'index.php?option=' .$option. '&controller=tickets&task=edit&cid[]='.$row->ticketid;

	?>
    <tr class="<?php echo "row$k"; ?>">
      <td colspan="2"><div align="left"><strong><?php echo $row->eventname; ?></strong><br />     
      <a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $row->ticketid; ?>', '<?php echo $this->escape(addslashes($row->ticketname)); ?>');">
					  <?php echo $this->escape($row->eventname.' - '.$row->ticketname); ?></a>             	  
      <?php echo JText::_( 'COM_TICKETMASTER_INCLUDING_CHILDS' ); ?></div></td>
      <td><div align="center"><?php echo $row->groupname; ?></div></td>
     
      <td><div align="center"><?php echo $row->totaltickets; ?></div></td>
      <td width="129">
      	  <div align="center"><?php 
			  if ($row->published == 1){ ?>
				   <img src="../administrator/templates/bluestork/images/admin/icon-16-allow.png" width="15" height="15" border="0" />
			   <?php }else{ ?>
				   <img src="../administrator/templates/bluestork/images/admin/publish_r.png" width="15" height="15" border="0" />
			  <?php } ?>
          </div>      </td>
    </tr>
    
 
    
    <?php
	  $k=1 - $k;
	  }
	  ?>
  </table>      
