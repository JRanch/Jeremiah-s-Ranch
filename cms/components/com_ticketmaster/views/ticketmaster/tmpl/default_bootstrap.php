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

## Check if this is Joomla 2.5 or 3.0.+
$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');

if(!$isJ30) {
	
	if($this->config->load_bootstrap == 1){
		## Adding mootools for J!2.5
		JHTML::_('behavior.modal');
		## Include the tooltip behaviour.
		JHTML::_('behavior.tooltip', '.hasTip');
		$document->addScript('https://code.jquery.com/jquery-latest.js');
		$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' ); 
		$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/js/bootstrap.js');
	}
}

?>

<h2><?php echo JText::_('COM_TICKETMASTER_AVAILABLE_EVENTS'); ?></h2>

<table class="table">

<?php for ($ct=0, $i=0, $n = count( $this->items ); $i < $n; $i++) {
	
		$row 	= $this->items[$i];
		$alias 	= JFilterOutput::stringURLSafe($row->eventname);
		$url    = JRoute::_('index.php?option=com_ticketmaster&view=eventlist&id='.$row->eventid.':'.$alias);		
		
		?>
	
		<tr>
			<td width="5%"><div align="center"><?php echo $i+1; ?></div></td>
			<td><?php echo $row->eventname; ?></td>
			<td>
				<?php if ($row->totalevents != 1){
                        echo '<a href=\''.$url.'\'>'.$row->totalevents.' '.JText::_('COM_TICKETMASTER_TM_EVENTS').'</a>';
                   }else{ 
                        echo '<a href=\''.$url.'\'>'.$row->totalevents.' '.JText::_('COM_TICKETMASTER_TM_EVENT').'</a>';
                } ?>
            
            	<a href="<?php echo $url; ?>" class="btn btn-small pull-right"><?php echo JText::_('COM_TICKETMASTER_AVAILABLE_EVENTS'); ?></a>
            
            </td>
		 </tr>        
	
<?php }	?>

</table>
