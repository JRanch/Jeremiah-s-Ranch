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

## Check if the file is included in the Joomla Framework
defined('_JEXEC') or die ('No Acces to this file!');

## initialize the editor
$editor 	= JFactory::getEditor();
$document 	= JFactory::getDocument();

## Include the toolbars for saving.
JToolBarHelper::title(JText::_( 'COM_TICKETMASTER_TRANSACTIONSMANAGER' ), 'module.png');	
JToolBarHelper::editList();
JToolBarHelper::deleteList();

## Require specific menu-file.
$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmasterext'.DS.'assets'.DS.'menu.php';
if (file_exists($path)) {
	include_once $path;
}else{
	$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'menu.php';
	include_once $path;
}

$document->addScript('https://code.jquery.com/jquery-latest.js');
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

<form action = "index.php?option=com_ticketmaster" method="POST" name="adminForm" id="adminForm" >
  <table class="table table-striped" width="100%">
    <thead>
      <tr>
        <th width="24" align="center" valign="middle">
          <div align="center">
            <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?> ) ;" />
          </div></th>
        <th width="25" align="center" valign="middle"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_PID' ); ?></div></th>
        <th width="115"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_DATE' ); ?></div></th>
        <th width="100"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_USER' ); ?></div></th>
        <th width="100"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_ORDERCODE' ); ?></div></th>
        <th width="191"><div align="left"><?php echo JText::_( 'COM_TICKETMASTER_TRANSACTIONID' ); ?></div></th>
        <th width="190"><div align="left"><?php echo JText::_( 'COM_TICKETMASTER_PAYMENT_TYPE' ); ?></div></th>
        <th width="100"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_AMOUNT' ); ?></div></th>
        <th width="200"><div align="left"><?php echo JText::_( 'COM_TICKETMASTER_PAYPAL_EMAIL' ); ?></div></th>
      </tr>
    </thead>
    <?php 
	   
	   $k = 0;
	   for ($i = 0, $n = count($this->items); $i < $n; $i++ ){
		
		$row        = $this->items[$i];
		$checked    = JHTML::_('grid.id', $i, $row->pid );
	
	?>
    <tr class="<?php echo "row$k"; ?>">
      <td align="center" valign="middle"><div align="center"><?php echo $checked; ?></div></td>
      <td align="center" valign="middle"><?php echo $row->pid; ?></td>
      <td><div align="center"><?php echo date ("d-m-Y H:m", strtotime($row->date)); ?></div></td>
      <td><div align="center"><?php echo $row->userid; ?></div></td>
      <td><div align="center"><?php echo $row->orderid; ?></div></td>
      <td><div align="left"><?php echo $row->transid; ?></div></td>
      <td><div align="left"><?php echo $row->type; ?></div></td>
      <td><div align="center"><?php echo $this->data->valuta; ?> <?php echo number_format($row->amount, 2, ',', ' '); ?></div></td>
      <td><div align="left"><?php echo $row->email_paypal; ?></div></td>
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
  <input name ="controller" type="hidden" value="transactions"/>
</form>