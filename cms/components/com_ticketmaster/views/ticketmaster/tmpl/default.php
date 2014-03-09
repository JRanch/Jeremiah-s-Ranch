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

jimport( 'joomla.filter.output' );

$app 		= JFactory::getApplication();
$pathway 	= $app->getPathway();
$pathway->addItem(JText::_( 'COM_TICKETMASTER_AVAILABLE_EVENTS' ), 'index.php?option=com_ticketmaster');

## Get document type and add it.
$document = &JFactory::getDocument();

$cssfile = 'components/com_ticketmaster/assets/css-overrides/ticketmaster.css';

## Check if there is a css override.
if (file_exists($cssfile)) {
    $document->addStyleSheet( $cssfile );
} else {
    $document->addStyleSheet( 'components/com_ticketmaster/assets/css/ticketmaster.css' );
}

$document->addStyleSheet( 'components/com_ticketmaster/assets/css/component.css' );

?>

<h2 class="contentheading">
	<?php echo JText::_('COM_TICKETMASTER_AVAILABLE_EVENTS'); ?>
</h2>

<?php 
	for ($ct=0, $i=0, $n = count( $this->items ); $i < $n; $i++) {
	
		$row 	= $this->items[$i];
		$alias 	= JFilterOutput::stringURLSafe($row->eventname);
		$url    = JRoute::_('index.php?option=com_ticketmaster&view=eventlist&id='.$row->eventid.':'.$alias);
		
		## Splitting the text in 2 pieces if needed.
		$split = explode("%%READMORE%%", $row->eventdescription);		
		
		?>
        
        <div id = "ticketmaster_detail_events">		
			<div id = "ticketmaster-eventheader">
            	<span class="ticketmaster-header-text"><?php echo $row->eventname; ?></span>
                <span class="ticketmaster-header-number">( 
              		<?php if ($row->totalevents != 1){
						echo '<a href=\''.$url.'\'>'.$row->totalevents.' '.JText::_('COM_TICKETMASTER_TM_EVENTS').'</a>';
                    }else{ 
                        echo '<a href=\''.$url.'\'>'.$row->totalevents.' '.JText::_('COM_TICKETMASTER_TM_EVENT').'</a>';
                    } ?>  				
                 )</span>
            </div>
            <div id="ticketmaster-eventdetails">
            	<?php echo $split[0]; ?>           
            </div>
            <div id = "ticketmaster-eventbottom">
                <a class="button medium gray" href="<?php echo $url; ?>">
                	<span><?php echo JText::_('COM_TICKETMASTER_AVAILABLE_EVENTS'); ?></span>
                </a>	      
            </div>
        </div>  
 
        
		<?php
    }	
	
?>	