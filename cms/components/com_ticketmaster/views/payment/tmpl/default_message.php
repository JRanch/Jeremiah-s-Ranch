<?php 
/**
 * @version		1.0.0 ticketmaster $
 * @package		ticketmaster
 * @copyright	Copyright © 2009 - All rights reserved.
 * @license		GNU/GPL
 * @author		Robert Dam
 * @author mail	info@rd-media.org
 * @website		http://www.rd-media.org
 */

## Check if the file is included in the Joomla Framework
defined('_JEXEC') or die ('No Acces to this file!');

$app = JFactory::getApplication();
$pathway = $app->getPathway();
$pathway->addItem(JText::_( 'COM_TICKETMASTER_EVENTS' ), 'index.php?option=com_ticketmaster');
$pathway->addItem(JText::_( 'COM_TICKETMASTER_THANK_YOU' ));

## Obtain user information.
$user = & JFactory::getUser();
$userid = $user->id;
## Get document type and add it.
$document = &JFactory::getDocument();
$document->addStyleSheet( 'components/com_ticketmaster/assets/component.css' );
$document->setTitle( JText::_('COM_TICKETMASTER_THANK_YOU') );

## Getting the global DB session
$session =& JFactory::getSession();
## Gettig the orderid if there is one.
$session->clear('ordercode');


## Check if this is Joomla 2.5 or 3.0.+
$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');

if(!$isJ30) {

	JHTML::_( 'behavior.mootools' );

	if($this->config->load_bootstrap == 1){
		
		## Adding mootools for J!2.5
		JHTML::_('behavior.modal');
		## Include the tooltip behaviour.
		JHTML::_('behavior.tooltip', '.hasTip');
		$document->addScript('http://code.jquery.com/jquery-latest.js');
		$document->addStyleSheet( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/css/bootstrap.css' ); 
		$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/js/bootstrap.js');
		$button = 'btn';
	
	}else{	
		$document->addStyleSheet( 'components/com_ticketmaster/assets/css/bootstrap.css' );
		$button = 'button_rdticketmaster';
	}
		
}else{

	## We are in J3, load the bootstrap!
	$document->addScript( JURI::root(true).'/administrator/components/com_ticketmaster/assets/bootstrap/js/bootstrap.js');	
	jimport('joomla.html.html.bootstrap');
	$button = 'btn';
	
}

?>

<h1><?php echo $this->msg->mailsubject; ?></h1>
<?php echo $this->msg->mailbody; ?>