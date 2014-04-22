<?php
/****************************************************************
 * @version			2.5.5											
 * @package			com_ticketmaster									
 * @copyright		Copyright © 2009 - All rights reserved.			
 * @license			GNU/GPL											
 * @author			Robert Dam										
 * @author mail		info@rd-media.org								
 * @website			http://www.rd-media.org							
 ***************************************************************/


## Check if the file is included in the Joomla Framework
defined('_JEXEC') or die ('No Acces to this file!');

## initialize the editor
$editor =& JFactory::getEditor();

## Getting the tooltip behaviour.
JHTML::_('behavior.tooltip');

$document = JFactory::getDocument();

## Helper file for what you can do.
require_once JPATH_COMPONENT.'/helpers/ticketmaster.php';
$canDo	= ticketmasterHelper::getActions($empty=0);
$user	= JFactory::getUser();

## Include the toolbars for saving.
JToolBarHelper::title(JText::_( 'COM_TICKETMASTER_EMAIL_TEMPLATES' ), 'config.png');
if ($canDo->get('core.admin')) {
	JToolBarHelper::publish();
	JToolBarHelper::unpublish();
	JToolBarHelper::deleteList();
	JToolBarHelper::addNew();
	JToolBarHelper::editList();
}

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

<script>

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
        <th width="40"><div align="center"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?> ) ;" />
        </div></th>
        <th class="title" width="40"><div align="center">EID</div></th>
        <th width="40" class="title"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_TYPE' ); ?></div></th>
        <th width="259" class="title"><div align="left"><?php echo JText::_( 'COM_TICKETMASTER_EMAIL_SUBJECT' ); ?></div></th>
        <th width="420"><div align="left"><?php echo JText::_( 'COM_TICKETMASTER_DESCRIPTION' ); ?></div></th>
        <th width="85"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_UPDATED' ); ?></div></th>
        <th width="50" class="title"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_BCC' ); ?></div></th>
        <th width="50" class="title"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_STATE' ); ?></div></th>
      </tr>
    </thead>
    <?php 
	   
	   $k = 0;
	   for ($i = 0, $n = count($this->items); $i < $n; $i++ ){
		
		$row        = &$this->items[$i];
		
		$published 	= JHTML::_('grid.published', $row, $i );
		$checked    = JHTML::_('grid.id', $i, $row->emailid );
		$option 	= 'com_ticketmaster';		
		$link 		= JRoute::_( 'index.php?option='.$option.'&controller=mail&task=edit&cid[]='.$row->emailid.'' );

	?>
    <tr class="<?php echo "row$k"; ?>">
      <td><div align="center"><?php if ($row->secured == 1) { ?>
      <img src="../administrator/templates/bluestork/images/admin/checked_out.png" width="15" height="15" border="0" />
      <?php } else { echo $checked; } ?></div></td>
      <td><div align="center"><?php echo $row->emailid; ?></div></td>
      <td><div align="center">
	  <?php if ($row->template_type == 1) { 
	  			echo '<span class="badge badge-info" style="font-size:90%;">P</span>'; 
			}else { 
				echo '<span class="badge badge-inverse" style="font-size:90%;">M</span>'; 
			}	
	   ?>      
      </div></td>
      <td><div align="left"><a href="<?php echo $link; ?>"><?php echo $row->mailsubject; ?></a></div></td>
      <td><div align="left"><?php echo $row->description; ?></div></td>
      <td><div align="center"><?php echo date ("d-m-Y", strtotime($row->lastchange)); ?></div></td>
      <td><div align="center"><?php if ($row->receive_bcc == 1) { ?>
          <img src="../administrator/components/com_ticketmaster/assets/images/tick.png" width="15" height="15" border="0" />
      <?php } ?></div></td>
      <td><div align="center"><?php echo $published;?></div></div></td>
    </tr>
    <?php
	  $k=1 - $k;
	  }
	  ?>
  </table>

  <input name = "option" type="hidden" value="com_ticketmaster" />
  <input name = "task" type="hidden" value="" />
  <input name = "boxchecked" type="hidden" value="0"/>
  <input name = "controller" type="hidden" value="mail"/>
 

</form>
