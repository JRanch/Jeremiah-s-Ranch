<?php

/****************************************************************
 * @version		1.0.0 ticketmaster $							*
 * @package		ticketmaster									*
 * @copyright	Copyright � 2009 - All rights reserved.			*
 * @license		GNU/GPL											*
 * @author		Robert Dam										*
 * @author mail	info@rd-media.org								*
 * @website		http://www.rd-media.org							*
 ***************************************************************/

## Check if the file is included in the Joomla Framework
defined('_JEXEC') or die ('No Acces to this file!');

## initialize the editor
$document 	= JFactory::getDocument();
$editor 	= JFactory::getEditor();

## Setting the toolbars up here..
$newCat	= ($this->data->clientid < 1);
$text = $newCat ? JText::_( 'COM_TICKETMASTER_ADD' ) : JText::_( 'COM_TICKETMASTER_EDIT' );
JToolBarHelper::title(''.$text.' '.JText::_( 'COM_TICKETMASTER_CLIENT_INFO' ), 'generic.png');
JToolBarHelper::save();
if ($this->data->clientid < 1)  {
	## Cancel the operation
	JToolBarHelper::cancel();
} else {
	## For existing items the button is renamed `close`
	JToolBarHelper::cancel( 'cancel', 'Close' );
};

## Check if this is Joomla 2.5 or 3.0.+
$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');

if(!$isJ30) {
	## Adding mootools for J!2.5
	JHTML::_('behavior.modal');
	## Include the tooltip behaviour.
	JHTML::_('behavior.tooltip', '.hasTip');
	$document->addScript('http://code.jquery.com/jquery-latest.js');
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
<form action = "index.php" method="POST" name="adminForm" id="adminForm" enctype="multipart/form-data">

<div class="row-fluid">
  <div class="span6">

    <table class="table table-striped">
        <tr>
            <td width="50%">
            <?php echo JText::_( 'COM_TICKETMASTER_YOUR_GENDER' ); ?></label></td>
            <td width="50%"><?php echo $this->lists['gender']; ?></td>
        </tr>    
        <tr>
            <td width="50%">
            <?php echo JText::_( 'COM_TICKETMASTER_FIRSTNAME' ); ?></label></td>
            <td width="50%"><input class="text_area" type="text" name="firstname" id="firstname" size="35" maxlength="150" 
            value="<?php echo $this->data->firstname; ?>" /></td>
        </tr>
        <tr>
            <td width="50%">
            <?php echo JText::_( 'COM_TICKETMASTER_LASTNAME' ); ?></label></td>
            <td width="50%"><input class="text_area" type="text" name="name" id="name" size="35" maxlength="150" 
            value="<?php echo $this->data->name; ?>" />        </td>
        </tr>
        <tr>
            <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_VISITORADDRESS' ); ?></td>
            <td width="50%"><input class="text_area" type="text" name="address" id="address" size="35" maxlength="150" 
            value="<?php echo $this->data->address; ?>" /></td>
        </tr>
        <tr>
          <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_VISITORADDRESS' ); ?></td>
          <td width="50%"><input class="text_area" type="text" name="address2" id="address2" size="35" maxlength="150" 
            value="<?php echo $this->data->address2; ?>" /></td>
        </tr>
        <tr>
          <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_VISITORADDRESS' ); ?></td>
          <td width="50%"><input class="text_area" type="text" name="address3" id="address3" size="35" maxlength="150" 
            value="<?php echo $this->data->address3; ?>" /></td>
        </tr>
        <tr>
            <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_ZIPCODE' ); ?></td>
            <td width="50%"><input class="text_area" type="text" name="zipcode" id="zipcode" size="35" maxlength="35"
            value="<?php echo $this->data->zipcode; ?>" /></td>
        </tr>  
        <tr>
            <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_CITY' ); ?></td>
            <td width="50%"><input class="text_area" type="text" name="city" id="city" size="35" maxlength="150"
            value="<?php echo $this->data->city; ?>" /></td>
        </tr>  
        <tr>
            <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_COUNTRY' ); ?></td>
            <td width="50%"><?php echo $this->lists['country']; ?></td>
        </tr>          
        <tr>
            <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_PHONENUMBER' ); ?></td>
            <td width="50%"><input class="text_area" type="text" name="phonenumber" id="phonenumber" size="35" maxlength="10"
            value="<?php echo $this->data->phonenumber; ?>" /></td>
        </tr>
        <tr>
            <td  width="50%"><?php echo JText::_( 'COM_TICKETMASTER_EMAIL' ); ?></td>
            <td width="50%"><input class="text_area" type="text" name="emailaddress" id="emailaddress" size="35" maxlength="150"
            value="<?php echo $this->data->emailaddress; ?>" /></td>
        </tr>
        <tr>
            <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_DAY_OF_BIRTH' ); ?></td>
            <td width="50%"><input class="text_area" type="text" name="birthday" id="birthday" size="35" maxlength="150"
            value="<?php echo $this->data->birthday; ?>" /></td>
        </tr>    
        <tr>
            <td width="50%"><?php echo JText::_( 'COM_TICKETMASTER_IS_PUBLISHED' ); ?></td>
            <td width="50%"><?php echo $this->lists['published']; ?></td>
        </tr>    
    </table>

    </div>


  <div class="span6">
  
    <div class="alert alert-info">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <h4>Heads Up!</h4>
      <?php echo JText::_( 'COM_TICKETMASTER_IP_ADDRESS' ); ?> <strong><?php echo $this->data->ipaddress; ?></strong>
    </div>
  
	<h4><?php echo JText::_( 'COM_TICKETMASTER_ORDER_HISTORY' ); ?> <?php echo $this->data->name; ?>, <?php echo $this->data->firstname; ?></h4>
    <hr /> 
      <table class="table table-striped" width="100%">
        <thead>
          <tr>
            <th width="32" class="title"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_ORDERID' ); ?></div></th>
            <th class="title" width="261"><div align="left"><?php echo JText::_( 'COM_TICKETMASTER_ORDER_INFO' ); ?></div></th>
            <th class="title" width="75"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_HISTORY' ); ?></div></th>
            <th class="title" width="75"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_TOTAL_TICKETS_2' ); ?></div></th>
            <th class="title" width="95"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_TOTAL_REGULAR_PRICE' ); ?></div></th>
          </tr>
        </thead>
        <?php 
           
           $k = 0;
           for ($i = 0, $n = count($this->items); $i < $n; $i++ ){
            
            ## Give give $row the this->item[$i]
            $row        = &$this->items[$i];
            $published 	= JHTML::_('grid.published', $row, $i );
            $checked    = JHTML::_('grid.id', $i, $row->ordercode );
            $link       = 'index.php?option=com_ticketmaster&controller=ticketbox&task=edit&cid[]='.$row->ordercode;
            $link2      = 'index.php?option=com_ticketmaster&controller=tickets&task=edit&cid[]='.$row->ticketid;
    
        ?>
        <tr class="<?php echo "row$k"; ?>">
          <td><div align="center"><?php echo $row->orderid; ?></div></td>
          <td><div align="left"><strong><?php echo JText::_( 'COM_TICKETMASTER_ORDERCODE' ); ?> <?php echo $row->ordercode; ?></strong></div></td>
          <td><div align="center"><a class="btn btn-mini" href="<?php echo $link;?>"><?php echo JText::_( 'COM_TICKETMASTER_SHOW_ORDER' ); ?></a></div></td>
          <td><div align="center"><?php echo $row->totaltickets; ?></div></td>
          <td><div align="center"><?php echo $this->config->valuta; ?> <?php echo number_format($row->orderprice, 2, ',', ''); ?></div></td>
        </tr>
        <?php
          $k=1 - $k;
          }
          ?>
      </table>
  
  </div>
  
</div>

    
    
<input type="hidden" name="clientid" value="<?php echo $this->data->clientid; ?>" />
<input type="hidden" name="option" value="com_ticketmaster" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="visitors" />
</form>
		
