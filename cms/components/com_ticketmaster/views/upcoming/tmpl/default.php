<?php 
/************************************************************
 * @version			ticketmaster 2.5.5
 * @package			com_ticketmaster
 * @copyright		Copyright © 2009 - All rights reserved.
 * @license			GNU/GPL
 * @author			Robert Dam
 * @author mail		info@rd-media.org
 * @website			http://www.rd-media.org
 *************************************************************/

## Check if the file is included in the Joomla Framework
defined('_JEXEC') or die ('No Acces to this file!');

## Add the tooltip behaviour.
JHTML::_('behavior.tooltip');
JHTML::_( 'behavior.mootools' );

$app =& JFactory::getApplication();
$pathway =& $app->getPathway();
$pathway->addItem($this->tmpl->mailsubject);

## Get document type and add it.
$document = &JFactory::getDocument();

$cssfile = 'components/com_ticketmaster/assets/css-overrides/upcoming.css';

## Check if there is a css override.
if (file_exists($cssfile)) {
    $document->addStyleSheet( $cssfile );
} else {
    $document->addStyleSheet( 'components/com_ticketmaster/assets/css/upcoming.css' );
}

$document->addStyleSheet( 'components/com_ticketmaster/assets/css/component.css' );
include_once( 'components/com_ticketmaster/assets/functions.php' );
jimport( 'joomla.filter.output' );

## Check if this is Joomla 2.5 or 3.0.+
$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');

if(!$isJ30) {
	
	if($this->config->load_bootstrap == 1){
		## Adding mootools for J!2.5
		JHTML::_('behavior.modal');
		## Include the tooltip behaviour.
		JHTML::_('behavior.tooltip', '.hasTip');
		$document->addScript('/jquery/jquery-1.9.0.min.js');
		$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' ); 
		$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/js/bootstrap.js');
		$button = 'btn btn-small';
	}else{	
		$document->addStyleSheet( 'components/com_ticketmaster/assets/css/bootstrap.css' );
		$button = 'button_rdticketmaster';
	}
}

?>

<h2 class="contentheading"><?php echo $this->tmpl->mailsubject; ?></h2>
<?php echo $this->tmpl->mailbody; ?>
    
<table class="table table-striped">
 
<thead>
    <th width="15%"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_DATE' ); ?></div></th>
    <th width="50%"><?php echo JText::_( 'COM_TICKETMASTER_TM_EVENTS' ); ?></th>
    <th width="15%"><div align="center"><?php echo JText::_( 'COM_TICKETMASTER_PRICE' ); ?></div></th>
    <th width="20%">&nbsp;</th>
</thead> 
         
<?php 
 		 
 $k = 0;
 for ($i = 0, $n = count($this->items); $i < $n; $i++ ){

	
	## Give give $row the this->item[$i]
	$row        = &$this->items[$i];
	$published 	= JHTML::_('grid.published', $row, $i );
	$checked    = JHTML::_('grid.id', $i, $row->ticketid );
	$alias 		= JFilterOutput::stringURLSafe($row->upcomingeventname);
	$link 		= JRoute::_('index.php?option=' . $option . '&view=event&id='.$row->ticketid.':'.$alias);
	$month 		= date ( m, strtotime($row->ticketdate));
	$year 		= date ( Y, strtotime($row->ticketdate));
	
	?>

    <tr>
    	<td><div align="center"><a href="<?php echo $link; ?>"><?php echo date ($this->config->dateformat, strtotime($row->ticketdate)); ?></a></div></td>
        <td><a href="<?php echo $link; ?>"><?php echo $row->upcomingeventname; ?></a></td>
        <td><div align="center"><?php echo showprice($this->config->priceformat ,$row->ticketprice,$this->config->valuta); ?></div></td>
        <td><div align="center"><a class="<?php echo $button; ?>" href="<?php echo $link; ?>"><?php echo JText::_('COM_TICKETMASTER_SHOW_NOW'); ?></a></div></td>
    </tr>    

	<?php

		
	  $k=1 - $k;
	  }
	  ?>
      
     </table> 

 
   <div align="center"><?php echo $this->pagination->getPagesLinks( ); ?></div>
