<?php
/****************************************************************
 * @version				Ticketmaster 2.5.5							
 * @package				ticketmaster									
 * @copyright			Copyright © 2009 - All rights reserved.			
 * @license				GNU/GPL											
 * @author				Robert Dam										
 * @author mail			info@rd-media.org								
 * @website				http://www.rd-media.org							
 ***************************************************************/

## no direct access
defined('_JEXEC') or die('Restricted access');

## Setup the toolbars.
JToolBarHelper::title( JText::_( 'COM_TICKETMASTER_CLIENTS' ), 'generic.png' );
JToolBarHelper::publish();
JToolBarHelper::unpublish();
JToolBarHelper::editList();
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

<script>

jQuery.noConflict();
  jQuery(document).ready(function(jQuery) {
		var oldSrc = '<?php echo JURI::root(true); ?>/administrator/templates/isis/images/admin/tick.png';
		var newSrc = '<?php echo JURI::root(true); ?>/administrator/components/com_ticketmaster/assets/images/tick.png';
		jQuery('img[src="' + oldSrc + '"]').attr('src', newSrc);
  });

	
</script>

<form action = "index.php" method="POST" name="adminForm" id="adminForm" class="form-inline">
<table>
<tr>
	<td align="left" width="100%">

	</td>
	<td nowrap="nowrap">
		<?php echo JText::_( 'COM_TICKETMASTER_SEARCH' ); ?>
		<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="input" /> 
		<?php echo $this->lists['ordering']; ?>
		<button class="btn" onclick="this.form.submit();"><?php echo JText::_( 'COM_TICKETMASTER_SEARCH' ); ?></button>
		<button class="btn" onclick="document.getElementById('search').value=''; document.getElementById('filter_ordering').value='1'; this.form.submit();"><?php echo JText::_( 'COM_TICKETMASTER_RESET' ); ?></button>
	</td>
</tr>
</table>
  <table class="table table-striped" width="100%">
    <thead>
      <tr>
        <th width="42"><div align="center">
          <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?> ) ;" />
        </div></th>
        <th width="30" class="title"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_CLIENTID' ); ?></div></th>
        <th width="170" class="title"><div align="left"><?php echo JText::_( 'COM_TICKETMASTER_VISITORNAME' ); ?></div></th>
        <th width="170" class="title"><div align="left"><?php echo JText::_( 'COM_TICKETMASTER_ADDRESS' ); ?></div></th>
        <th class="title" width="170"><div align="left"><?php echo JText::_( 'COM_TICKETMASTER_ZIPCODE_CITY' ); ?></div></th>
        <th class="title" width="120"><div align="left"><?php echo JText::_( 'COM_TICKETMASTER_COUNTRY' ); ?></div></th>
        <th class="title" width="120"><div align="left"><?php echo JText::_( 'COM_TICKETMASTER_PHONENUMBER' ); ?></div></th>
        <th class="title" width="170"><div align="left"><?php echo JText::_( 'COM_TICKETMASTER_EMAIL ADDRESS' ); ?></div></th>
        <th class="title" width="91"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_LAST_SEEN' ); ?></div></th>
        <th class="title" width="91"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_REGISTRED' ); ?></div></th>
        <th class="title" width="91"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_PUBLISHED' ); ?></div></th>
      </tr>
    </thead>
    <?php 
	   
	   $k = 0;
	   for ($i = 0, $n = count($this->items); $i < $n; $i++ ){
		
		## Give give $row the this->item[$i]
		$row        = &$this->items[$i];
		$published 	= JHTML::_('grid.published', $row, $i );
		$checked    = JHTML::_('grid.id', $i, $row->clientid );
		$link 		= 'index.php?option=com_ticketmaster&controller=visitors&task=edit&cid='.$row->clientid;

	?>
    <tr class="<?php echo "row$k"; ?>">
      <td><div align="center"><?php echo $checked; ?></div></td>
      <td><div align="center"><?php echo $row->userid; ?></div></td>
      <td><div align="left"><a href="<?php echo $link; ?>"><?php echo $row->name; ?> <em>(<?php echo $row->username; ?>)</em></a></div></td>
      <td><div align="left"><?php echo $row->address; ?></div></td>
      <td><div align="left"><?php echo $row->zipcode; ?> <?php echo $row->city; ?></div></td>
      <td><div align="left"><?php echo $row->country; ?></div></td>
      <td><?php echo $row->phonenumber; ?></div></td>
      <td><div align="left"><?php echo $row->emailaddress; ?></div></td>
      <td width="91"><div align="center"><?php echo date ("d-m-Y", strtotime($row->lastvisitDate)); ?></div></td>
      <td width="91"><div align="center"><?php echo date ("d-m-Y", strtotime($row->registerDate)); ?></div></td>
      <td width="91"><div align="center"><?php echo $published; ?></div></td>
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
  <input name = "controller" type="hidden" value="visitors"/>
  <input name = "filter_order" type="hidden" value="a.clientid"/>
  <input name = "filter_order_Dir" type="hidden" value="ASC"/>

  

</form>
        