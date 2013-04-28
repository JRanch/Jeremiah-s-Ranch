<?php 
/**
 * @version		2.5.4 ticketmaster $ROBERT-20121101
 * @package		Ticketmaster
 * @copyright	Copyright © 2009 - All rights reserved.
 * @license		GNU/GPL
 * @author		Robert Dam
 * @author mail	info@rd-media.org
 * @website		http://www.rd-media.org
 */

## Check if the file is included in the Joomla Framework
defined('_JEXEC') or die ('No Acces to this file!');

## Add the tooltip behaviour.
JHTML::_('behavior.tooltip');

$session =& JFactory::getSession();
## Gettig the orderid if there is one.
$redirect = $session->get('redirect');

## Check if the user is logged in.
$user = & JFactory::getUser();

## Adding the AJAX part
JHTML::script('ajax.js','components/com_ticketmaster/assets/', true);

$app =& JFactory::getApplication();
$pathway =& $app->getPathway();
$pathway->addItem(JText::_( 'COM_TICKETMASTER_MY_TICKETMASTER' ), 'index.php?option=com_ticketmaster&view=myticketmaster');
$pathway->addItem(JText::_( 'COM_TICKETMASTER_MY_ORDERS' ));

## Get document type and add it.
$document = &JFactory::getDocument();

$cssfile = 'components/com_ticketmaster/assets/css-overrides/myorders.css';

## Check if there is a css override.
if (file_exists($cssfile)) {
    $document->addStyleSheet( $cssfile );
} else {
    $document->addStyleSheet( 'components/com_ticketmaster/assets/css/myorders.css' );
}

$document->addStyleSheet( 'components/com_ticketmaster/assets/css/component.css' );
$document->setTitle( JText::_( 'COM_TICKETMASTER_MY_ORDERS' ) );

?>

<h2 class="contentheading"><?php echo JText::_('COM_TICKETMASTER_MY_ORDERS'); ?></h2>	

<div id="cartheading">
	<div id="cart_invoiceid"><?php echo JText::_('COM_TICKETMASTER_MY_ORDERID'); ?></div>
    <div id="cart_date"><?php echo JText::_('COM_TICKETMASTER_DATE'); ?></div>
    <div id="cart_date"><?php echo JText::_('COM_TICKETMASTER_ORDERNUMBER'); ?></div>
    <div id="cart_date"><?php echo JText::_('COM_TICKETMASTER_PAID'); ?></div>
    <div id="cart_date"><?php echo JText::_('COM_TICKETMASTER_TOTAL_PRICE'); ?></div>
    <div id="cart_date"><?php echo JText::_(''); ?></div>
    <div id="cart_action" style="padding-left:4px;"><?php echo JText::_(''); ?></div>
</div>

<?php 
$k = 0;
for ($i = 0, $n = count($this->items); $i < $n; $i++ ){
	
		## Give give $row the this->item[$i]
		$row        = &$this->items[$i];
		$published 	= JHTML::_('grid.published', $row, $i );
		$checked    = JHTML::_('grid.id', $i, $row->orderid );
		$download	= JRoute::_('index.php?option=com_rdsubs&controller=download&task=getinvoice&cid='.(int)$row->invoiceid);
		$item->odd	= $k;

?>

<div id="cartitems">
	
    <div id="cartitems_invoiceid"><?php echo $row->orderid; ?></div>    
    <div id="cartitems_date"><?php echo date ($this->config->dateformat, strtotime($row->orderdate)); ?></div>
    <div id="cartitems_date"><?php echo $row->ordercode; ?></div>
    <div id="cartitems_date"><?php echo $row->totalprice; ?></div>
    <div id="cartitems_date"><?php echo $row->totalprice; ?></div>
    <div id="cartitems_date"></div>
    
    
    <div id="cartitems_action">     

    
    </div>
    
</div>

<?php
  $k=1 - $k;
  }
?>