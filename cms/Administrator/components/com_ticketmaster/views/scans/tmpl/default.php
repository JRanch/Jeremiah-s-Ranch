<?php
/****************************************************************
 * @version				Ticketmaster 3.1.0							
 * @package				ticketmaster								
 * @copyright           Copyright © 2009 - All rights reserved.			
 * @license				GNU/GPL										
 * @author				Robert Dam									
 * @author mail         info@rd-media.org								
 * @website				http://www.rd-media.org						
 ***************************************************************/

## Check if the file is included in the Joomla Framework
defined('_JEXEC') or die ('No Acces to this file!');

## initialize the editor
$editor 	= JFactory::getEditor();
$document 	= JFactory::getDocument();

## Include the toolbars for saving.
JToolBarHelper::title(JText::_( 'COM_TICKETMASTER_SCANS' ), 'module.png');	
JToolBarHelper::deleteList();

## Require specific menu-file.
$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmasterext'.DS.'assets'.DS.'menu.php';
if (file_exists($path)) {
	include_once $path;
}else{
	$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'menu.php';
	include_once $path;
}

$document->addScript('http://code.jquery.com/jquery-latest.js');
$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/js/jquery.json-2.2.min.js');

## Check if this is Joomla 2.5 or 3.0.+
$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');

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

<form action = "index.php?option=com_ticketmaster" method="POST" name="adminForm" id="adminForm" class="form-inline">

<table class="table">
<tr>
	<td align="left" width="100%">

	</td>
	<td nowrap="nowrap">
        <input type="text" name="searchbox" id="searchbox" value="<?php echo $this->lists['search'];?>" class="input-medium" placeholder="Search Ordercode.."/> 
		<?php echo $this->lists['result'];?>
        <button onclick="this.form.submit();" class="btn"><?php echo JText::_( 'COM_TICKETMASTER_SEARCH' ); ?></button>
	</td>
</tr>
</table><br/>  

  <table class="table table-striped" width="100%">
    <thead>
      <tr>
        <th width="24" align="center" valign="middle">
          <div align="center">
            <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?> ) ;" />
          </div></th>
        <th width="50"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_SCANID' ); ?></div></th>
        <th width="115"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_DATE' ); ?></div></th>
        <th width="100"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_PHONE_UID' ); ?></div></th>
        <th width="100"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_ORDERCODE' ); ?></div></th>
        <th width="150"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_SCANNED_BARCODE' ); ?></div></th>
        <th width="100"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_SCAN_TYPE' ); ?></div></th>
        <th width="150"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_LOCATION' ); ?></div></th>
        <th width="200"><div align="left"><?php echo JText::_( 'COM_TICKETMASTER_SCAN_RESULT' ); ?></div></th>
      </tr>
    </thead>
    <?php 
	   
	   $k = 0;
	   for ($i = 0, $n = count($this->items); $i < $n; $i++ ){
		
		$row        = $this->items[$i];
		$checked    = JHTML::_('grid.id', $i, $row->scanid );
	
	?>
    <tr class="<?php echo "row$k"; ?>">
      <td align="center" valign="middle"><div align="center"><?php echo $checked; ?></div></td>
      <td align="center" valign="middle"><?php echo $row->scanid; ?></td>
      <td><div align="center"><?php echo date ("d-m-Y H:m", strtotime($row->timestamp)); ?></div></td>
      <td><div align="center"><?php echo $row->uid; ?></div></td>
      <td><div align="center"><?php echo $row->ordercode; ?></div></td>
      <td><div align="center"><?php echo $row->barcode; ?></div></td>
      <td><div align="center"><?php echo $row->type; ?></div></td>
      <td><div align="center"><?php echo $row->location; ?></div></td>
      <td><div align="left"><?php echo scanResult($row->scanresult); ?></div></td>
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
  <input name="option" type="hidden" value="com_ticketmaster" />
  <input name="task" type="hidden" value="" />
  <input name="boxchecked" type="hidden" value="0"/>
  <input name ="controller" type="hidden" value="scans"/>
</form>

<?php function scanResult($result){

	if($result == 100) { $scanResult = '<span class="label label-success" style="margin-right:5px;">'.$result.'</span>'.JText::_( 'COM_TICKETMASTER_SCAN_SUCCESS' ); }
	if($result == 101) { $scanResult = '<span class="label label-important" style="margin-right:5px;">'.$result.'</span>'.JText::_( 'COM_TICKETMASTER_SCAN_BLACKLISTED' ); }
	if($result == 102) { $scanResult = '<span class="label label-important" style="margin-right:5px;">'.$result.'</span>'.JText::_( 'COM_TICKETMASTER_TICKET_WAS_UNPAID' ); }
	if($result == 103) { $scanResult = '<span class="label label-important" style="margin-right:5px;">'.$result.'</span>'.JText::_( 'COM_TICKETMASTER_TICKET_WAS_SCANNED_BEFORE' ); }
	if($result == 104) { $scanResult = '<span class="label label-important" style="margin-right:5px;">'.$result.'</span>'.JText::_( 'COM_TICKETMASTER_UNAUTHORIZED_SCANNER' ); }
	if($result == 105) { $scanResult = '<span class="label label-important" style="margin-right:5px;">'.$result.'</span>'.JText::_( 'COM_TICKETMASTER_NO_BARCODE_FOUND' ); }
	
	return $scanResult;
	
}
?>